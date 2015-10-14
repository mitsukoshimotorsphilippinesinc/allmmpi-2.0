<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounts extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('members_model');		
		$this->load->model('tracking_model');
		$this->load->model('contents_model');
	}
	
	public function index() 
	{
		echo "Account Jobs.";
	}

	public function reset_pairs()
	{
		// reset pts for flushout
		echo "checking for flushouts..\n";
		// get smallest max pair
		$l_ach = $this->contents_model->get_member_achievements(null, array('rows' => 1, 'offset' => 0), 'max_pairs ASC');
		$min_max_pair = $l_ach[0]->max_pairs;

		$member_account_pairings = $this->members_model->get_member_account_pairing("pair_count >= " . $min_max_pair);
		foreach($member_account_pairings as $pairing_ent)
		{
			echo ".";
			// get member's max pair
			$member_achievement = $this->contents_model->get_featured_members(array('member_id' => $pairing_ent->member_id));
			$achievement_id = (sizeof($member_achievement) > 0)?$member_achievement[0]->achievement_id:0; // 0 = regular
			$achievement = $this->contents_model->get_member_achievements(array('member_achievement_id' => $achievement_id));
			$achievement = $achievement[0];

			if($pairing_ent->pair_count >= $achievement->max_pairs) //flushout
			{
				$this->members_model->update_member_account_pairing(array(
					'left_count' => 0,
					'right_count' => 0
				), array(
					'pairing_id' => $pairing_ent->pairing_id
				));
			}
		}
		
		if ($this->settings->auto_reset_pairing_counters)
		{
			// get the 1000 accounts and iterate until all accounts pairing counters have been reset to 0
			$max_no_of_accounts_per_loop = 10000;
            
            $where = "pair_count > 0  OR flushout > 0  OR gc_count > 0";
			//$where = "pair_count > 0  OR flushout > 0  OR gc_pair > 0";
			$limit = array("rows"=>$max_no_of_accounts_per_loop,"offset"=>0);
			$fields = "pairing_id";
			do
			{
				$account_pairings = $this->members_model->get_member_account_pairing($where, $limit, NULL, $fields);
				$count = count($account_pairings);
				$pairings = array();
            
				if($count > 0) 
				{
					foreach ($account_pairings as $a) $pairings[] = "'{$a->pairing_id}'";
					$_pairings = implode(",", $pairings);
					$condition = "pairing_id IN ({$_pairings})";
            
					// $data = array(
					// 	'pair_count' => 0,
					// 	'flushout' => 0,
					// 	'gc_pair' => 0
					// );
					$data = array(
						'pair_count' => 0,
						'flushout' => 0,
						'gc_count' => 0
					);
            
					$this->members_model->update_member_account_pairing($data, $condition);
				}
            
			} while($count > 0);
			
			// $where = "pairs_sp > 0  OR gc_sp > 0  OR flushout_sp > 0  OR pairs_vp > 0  OR gc_vp > 0  OR flushout_vp > 0  OR pairs_rs > 0  OR gc_rs > 0  OR flushout_rs > 0";
			// $limit = array("rows"=>$max_no_of_accounts_per_loop,"offset"=>0);
			// $fields = "account_id";

			// do 
			// {
			// 	$member_accounts = $this->members_model->get_member_accounts($where,$limit,NULL,$fields);						
			// 	$count = count($member_accounts);
			// 	$accounts = array();		

			// 	if($count>0) 
			// 	{
			// 		foreach ($member_accounts as $a) $accounts[] = "'{$a->account_id}'";

			// 		$_accounts = implode(",",$accounts);

			// 		$condition = "account_id IN ({$_accounts})";

			// 		$data = array(
			// 			"pairs_sp"=>0,
			// 			"gc_sp"=>0,
			// 			"flushout_sp"=>0,
			// 			"pairs_vp"=>0,
			// 			"gc_vp"=>0,
			// 			"flushout_vp"=>0,
			// 			"pairs_rs"=>0,
			// 			"gc_rs"=>0,
			// 			"flushout_rs"=>0
			// 		);

			// 		$this->members_model->update_member_accounts($data,$condition);
			// 	}
				
			// 	$limit['offset'] = $count;

			// } while ($count>0);			
		}		
	}
	
	public function reset_maintenance_counters()
	{
		// SET Monthly Maintenance Counters to 0 
		$sql = "
			UPDATE 
				cm_member_accounts 
			SET 
				monthly_maintenance_ctr = 0, ms_monthly_maintenance_ctr = 0";
		
		$this->db->query($sql);		
	}


	public function update_last_encashment_timestamp()
	{		
		$sql = "
			UPDATE rf_settings SET value = (SELECT end_date FROM po_payout_periods 
			WHERE status = 'COMPLETED' 
			AND payout_type = 'IGPSM' 
			AND is_official = 1 
			ORDER BY end_date DESC LIMIT 1)
			WHERE slug = 'last_encashment_timestamp'";
		
		$this->db->query($sql);		
	}
	
	public function check_monthly_maintenance()
	{
		if ($this->settings->auto_monthly_maintenance)
		{
			// TODO: logs changes
			
			// SET TO INACTIVE - process all active accounts where monthly maintenance<2 or annual maintenance<4
			$sql = "
				UPDATE 
					cm_member_accounts 
				SET 
					account_status_id = 2
				WHERE 
					account_status_id = 1 
				AND 
					(monthly_maintenance_ctr + ms_monthly_maintenance_ctr) < {$this->settings->monthly_maintenance} 					
				AND
					date(insert_timestamp) < (select concat(date_format(date_sub(now(), interval 1 month), '%Y-%m'),'-16'))";
			
			$this->db->query($sql);
		}		
	}

	public function check_annual_maintenance()
	{
		if ($this->settings->auto_annual_maintenance)
		{
            // get accounts
            $sql = "
                SELECT 
                    member_id, 
                    account_id, 
                    sponsor_id, 
                    upline_id, 
                    monthly_maintenance_ctr, 
                    ms_monthly_maintenance_ctr,
                    annual_maintenance_ctr, 
                    ms_annual_maintenance_ctr, 
                    insert_timestamp 
                FROM 
                    cm_member_accounts 
                WHERE 
                    account_type_id <> 3 
                AND    
                    DATE(insert_timestamp) like CONCAT('%', DATE_FORMAT(DATE_SUB(now(), interval 1 day), '-%m-%d'))  
                AND 
                    DATE(insert_timestamp) <> DATE_FORMAT(DATE_SUB(now(), interval 1 day), '%Y-%m-%d') 
                ";
            
            $accounts_list_query = $this->db->query($sql);
			$member_account_details = $accounts_list_query->result();
		
            foreach($member_account_details as $mad) {
                // check if member meets the monthly maintenance condition
                if (($mad->monthly_maintenance_ctr + $mad->ms_mothly_maintenance_ctr) < $this->settings->monthly_maintenance) {
                    
                     // set to INACTIVE
                        $sql ="
                            UPDATE
                                cm_member_accounts 
                            SET 
                                account_status_id = 2
                            WHERE 
                                member_id = {$mad->member_id}
                            AND
                                account_id = {$mad->account_id}             
                        ";
                        
                   	    $this->db->query($sql);
                        
                } else {     
                    
                    // check if member meets the annual maintenance condition
                    if (($mad->annual_maintenance_ctr + $mad->ms_annual_maintenance_ctr) < $this->settings->annual_maintenance) {
                        
                        // set to INACTIVE
                        $sql ="
                            UPDATE
                                cm_member_accounts 
                            SET 
                                account_status_id = 2
                            WHERE 
                                member_id = {$mad->member_id}
                            AND
                                account_id = {$mad->account_id}             
                        ";
                        
                   	    $this->db->query($sql);
                        
                    } else {
                         // set to ACTIVE
                        $sql ="
                            UPDATE
                                cm_member_accounts 
                            SET 
                                account_status_id = 1
                            WHERE 
                                member_id = {$mad->member_id}
                            AND
                                account_id = {$mad->account_id}             
                        ";
                        
                   	    $this->db->query($sql);
                        
                    }                                 
                }                
            }
        

			// TODO: logs changes

			// check for members whose date registered the same day as yesterday
			
			/*// SET TO INACTIVE - process all active accounts where monthly maintenance<2 or annual maintenance<4
			$sql = "
				UPDATE 
					cm_member_accounts 
				SET 
					account_status_id = 2
				WHERE 
					account_status_id = 1 
				AND 
					(MONTH(insert_timestamp + INTERVAL 1 DAY) = MONTH(CURRENT_DATE)
					AND 
					DAY(insert_timestamp + INTERVAL 1 DAY) = DAY(CURRENT_DATE)) 
				AND 
					(monthly_maintenance_ctr < {$this->settings->monthly_maintenance} 
					OR 
					annual_maintenance_ctr < {$this->settings->annual_maintenance})";
			
			$this->db->query($sql);*/
		}
		
	}
	
	// every 12mn
	public function backup_member_accounts() 
	{
		$sql = "INSERT INTO cm_member_accounts_history (member_id,account_id,sponsor_id,upline_id,account_type_id,account_status_id,node_address, uni_node,monthly_maintenance_ctr,annual_maintenance_ctr,ms_monthly_maintenance_ctr,ms_annual_maintenance_ctr,cd_amount,node_type) 
		(SELECT member_id,account_id,sponsor_id,upline_id,account_type_id,account_status_id,node_address, uni_node,monthly_maintenance_ctr,annual_maintenance_ctr,ms_monthly_maintenance_ctr,ms_annual_maintenance_ctr,cd_amount,node_type FROM cm_member_accounts)";
		
		$this->db->query($sql);
		
		$data = array(
			"module_name"=>"DAILY BACKUP (cm_member_accounts)",
			"table_name"=>"cm_member_accounts_history",
			"action"=>"INSERT cm_member_accounts_history - SELECT cm_member_accounts",			
			"details"=>$sql
			
		);
		$this->tracking_model->insert_cron_logs($data);	
	}
	
	public function backup_monthly_cm_member_accounts_history() {
		// get previous month date
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_accounts_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			
			// create table cm_members_history_{suffix_date} as select 		
			$sql = "CREATE TABLE cm_member_accounts_history_{$suffixdate} AS 
					SELECT	history_id,member_id,account_id,sponsor_id,upline_id,account_type_id,account_status_id,node_address, uni_node,monthly_maintenance_ctr,annual_maintenance_ctr,ms_monthly_maintenance_ctr,ms_annual_maintenance_ctr,cd_amount,node_type,insert_timestamp
					FROM 
						cm_member_accounts_history
					WHERE
						date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'
						";
		
			$this->db->query($sql);
			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_member_accounts_history)",
				"table_name"=>"cm_member_accounts_history_{$suffixdate}",
				"action"=>"CREATE TABLE - SELECT cm_member_accounts_history",			
				"details"=>$sql
				
			);
			$this->tracking_model->insert_cron_logs($data);
            
            // 20140226 email to backup_history_tables_email_group
			$params = array(
				"table_name"=>"cm_member_accounts_history_{$suffixdate}",
				"current_timestamp"=>date("Y-m-d H:i:s")
			);
            
            $email_group = $this->settings->backup_history_tables_email_group;
			$emails = explode(',',$email_group);

			foreach($emails as $email) {
    			$data = array(
    				"email"=>$email,
    				"type"=>"backup_history_table_generic_email",
    				"params"=>$params,
                    "title"=>"Backup History Table (cm_member_accounts_history) Generic Email"
    			);
    
    			//send email to user
    			Modules::run('jobs/notifications/send_email',$data); 
            }
		
		} else {
			// log to tracking table			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_member_accounts_history)",
				"table_name"=>"cm_member_accounts_history_{$suffixdate}",
				"action"=>"NONE - Table Already Exists",			
				"details"=>"n/a"
				
			);
			$this->tracking_model->insert_cron_logs($data);
		}
		
	}
	

	public function check_and_delete_old_records_cm_member_accounts_history()
	{	
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_accounts_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			// process backup_monthly_cm_members_history
			$this->backup_monthly_cm_member_accounts_history();
			return;
			
		} else {
						
			$sql = "SELECT COUNT(*) AS cnt FROM cm_member_accounts_history_{$suffixdate}";
			
			$query = $this->db->query($sql);
			$table_archive = $query->first_row();
			
			// get count from cm_members_history
			$sql = "SELECT COUNT(*) AS cnt FROM cm_member_accounts_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
			
			$query = $this->db->query($sql);
			$table_live = $query->first_row();
			
			// check if live table != 0
			if ($table_live->cnt != 0) {
			
				if ($table_archive->cnt ==  $table_live->cnt) {										
					
					// delete old records to live table
					$sql = "DELETE FROM cm_member_accounts_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
					$query = $this->db->query($sql);	
						
					// log to tracking table					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_member_accounts_history)",
						"table_name"=>"cm_member_accounts_history",
						"action"=>"DELETE FROM cm_member_accounts_history - Old Records Have Been Deleted.",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					return;
					
				} else {				
					$sql = "DROP TABLE cm_member_accounts_history_{$suffixdate}";
					$query = $this->db->query($sql);	
					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_member_accounts_history)",
						"table_name"=>"cm_member_accounts_history_{$suffixdate}",
						"action"=>"DROP TABLE cm_member_accounts_history_{$suffixdate} - Run backup_monthly_cm_member_accounts_history().",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					
					// process backup_monthly_cm_members_history
					$this->backup_monthly_cm_member_accounts_history();
					
					return;
				}			
			} else {
				$data = array(
					"module_name"=>"CHECK AND DELETE (cm_member_accounts_history)",
					"table_name"=>"cm_member_accounts_history",
					"action"=>"NONE - No Record Found on Live Table",
					"details"=>"n/a"						
				);
				
				$this->tracking_model->insert_cron_logs($data);	
				
			}			
		}
		return;
		
	}
	
	// START ==========================
	// cm_member_account_pairing backup
	// every 12:01 AM
	public function backup_member_account_pairing() 
	{
		$sql = "INSERT INTO cm_member_account_pairing_history (member_id,account_id,card_type_id,left_count,right_count,total_left,total_right,pair_count,flushout,gc_count,gc_pair,updated_timestamp,insert_timestamp) 
		(SELECT member_id,account_id,card_type_id,left_count,right_count,total_left,total_right,pair_count,flushout,gc_count,gc_pair,updated_timestamp,insert_timestamp FROM cm_member_account_pairing)";
		
		$this->db->query($sql);
		
		$data = array(
			"module_name"=>"DAILY BACKUP (cm_member_account_pairing)",
			"table_name"=>"cm_member_account_pairing_history",
			"action"=>"INSERT cm_member_account_pairing_history - SELECT cm_member_account_pairing",			
			"details"=>$sql
			
		);
		$this->tracking_model->insert_cron_logs($data);	
	}
	
	public function backup_monthly_cm_member_account_pairing_history() {
		// get previous month date
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_account_pairing_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			
			// create table cm_member_account_pairing_history_{suffix_date} as select 		
			$sql = "CREATE TABLE cm_member_account_pairing_history_{$suffixdate} AS 
					SELECT	*
					FROM 
						cm_member_account_pairing_history
					WHERE
						date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'
						";
		
			$this->db->query($sql);
			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_member_account_pairing_history)",
				"table_name"=>"cm_member_account_pairing_history_{$suffixdate}",
				"action"=>"CREATE TABLE - SELECT cm_member_account_pairing_history_{$suffixdate}",			
				"details"=>$sql
				
			);
			$this->tracking_model->insert_cron_logs($data);
		
            // 20140226 email to backup_history_tables_email_group
			$params = array(
				"table_name"=>"cm_member_account_pairing_history_{$suffixdate}",
				"current_timestamp"=>date("Y-m-d H:i:s")
			);
            
            $email_group = $this->settings->backup_history_tables_email_group;
			$emails = explode(',',$email_group);

			foreach($emails as $email) {
    			$data = array(
    				"email"=>$email,
    				"type"=>"backup_history_table_generic_email",
    				"params"=>$params,
                    "title"=>"Backup History Table (cm_member_account_pairing_history) Generic Email"
    			);
    
    			//send email to user
    			Modules::run('jobs/notifications/send_email',$data);    
            } 
		} else {
			// log to tracking table			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_member_account_pairing_history)",
				"table_name"=>"cm_member_account_pairing_history_{$suffixdate}",
				"action"=>"NONE - Table Already Exists",			
				"details"=>"n/a"				
			);
			$this->tracking_model->insert_cron_logs($data);
		}
	}

	public function check_and_delete_old_records_cm_member_account_pairing_history()
	{	
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_account_pairing_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			// process backup_monthly_cm_members_history
			$this->backup_monthly_cm_member_account_pairing_history();
			return;
			
		} else {
						
			$sql = "SELECT COUNT(*) AS cnt FROM cm_member_account_pairing_history_{$suffixdate}";
			
			$query = $this->db->query($sql);
			$table_archive = $query->first_row();
			
			// get count from cm_members_history
			$sql = "SELECT COUNT(*) AS cnt FROM cm_member_account_pairing_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
			
			$query = $this->db->query($sql);
			$table_live = $query->first_row();
			
			// check if live table != 0
			if ($table_live->cnt != 0) {
			
				if ($table_archive->cnt ==  $table_live->cnt) {										
					
					// delete old records to live table
					$sql = "DELETE FROM cm_member_account_pairing_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
					$query = $this->db->query($sql);	
						
					// log to tracking table					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_member_account_pairing_history)",
						"table_name"=>"cm_member_account_pairing_history",
						"action"=>"DELETE FROM cm_member_account_pairing_history - Old Records Have Been Deleted.",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					return;
					
				} else {				
					$sql = "DROP TABLE cm_member_account_pairing_history_{$suffixdate}";
					$query = $this->db->query($sql);	
					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_member_account_pairing_history)",
						"table_name"=>"cm_member_account_pairing_history_{$suffixdate}",
						"action"=>"DROP TABLE cm_member_account_pairing_history_{$suffixdate} - Run backup_monthly_cm_member_account_pairing_history_().",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					
					// process backup_monthly_cm_members_history
					$this->backup_monthly_cm_member_account_pairing_history();
					
					return;
				}			
			} else {
				$data = array(
					"module_name"=>"CHECK AND DELETE (cm_member_account_pairing_history)",
					"table_name"=>"cm_member_account_pairing_history",
					"action"=>"NONE - No Record Found on Live Table",
					"details"=>"n/a"						
				);
				
				$this->tracking_model->insert_cron_logs($data);	
				
			}			
		}
		return;
		
	}	
	// cm_member_account_pairing backup
	// END ============================
	
	
	// START ==========================
	// cm_member_earnings_per_type backup
	// every 12:01 AM
	public function backup_member_earnings_per_type() 
	{
		$sql = "INSERT INTO cm_member_earnings_per_type_history (member_id,account_id,type_id,pairing_bonus,gift_cheque) 
		(SELECT member_id,account_id,type_id,pairing_bonus,gift_cheque FROM cm_member_earnings_per_type)";
		
		$this->db->query($sql);
		
		$data = array(
			"module_name"=>"DAILY BACKUP (cm_member_earnings_per_type)",
			"table_name"=>"cm_member_earnings_per_type_history",
			"action"=>"INSERT cm_member_earnings_per_type_history - SELECT cm_member_earnings_per_type",			
			"details"=>$sql
			
		);
		$this->tracking_model->insert_cron_logs($data);	
	}
	
	public function backup_monthly_cm_member_earnings_per_type_history() {
		// get previous month date
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_earnings_per_type_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			
			// create table cm_member_earnings_per_type_history_{suffix_date} as select 		
			$sql = "CREATE TABLE cm_member_earnings_per_type_history_{$suffixdate} AS 
					SELECT	*
					FROM 
						cm_member_earnings_per_type_history
					WHERE
						date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'
						";
		
			$this->db->query($sql);
			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_member_earnings_per_type_history)",
				"table_name"=>"cm_member_earnings_per_type_history_{$suffixdate}",
				"action"=>"CREATE TABLE - SELECT cm_member_earnings_per_type_history_{$suffixdate}",			
				"details"=>$sql
				
			);
			$this->tracking_model->insert_cron_logs($data);
		
            // 20140226 email to backup_history_tables_email_group
			$params = array(
				"table_name"=>"cm_member_earnings_per_type_history_{$suffixdate}",
				"current_timestamp"=>date("Y-m-d H:i:s")
			);
            
            $email_group = $this->settings->backup_history_tables_email_group;
			$emails = explode(',',$email_group);

			foreach($emails as $email) {
    			$data = array(
    				"email"=>$email,
    				"type"=>"backup_history_table_generic_email",
    				"params"=>$params,
                    "title"=>"Backup History Table (cm_member_earnings_per_type_history) Generic Email"
    			);
    
    			//send email to user
    			Modules::run('jobs/notifications/send_email',$data); 
            }
        
		} else {
			// log to tracking table			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_member_earnings_per_type_history)",
				"table_name"=>"cm_member_earnings_per_type_history_{$suffixdate}",
				"action"=>"NONE - Table Already Exists",			
				"details"=>"n/a"				
			);
			$this->tracking_model->insert_cron_logs($data);
		}
	}	
	
	public function check_and_delete_old_records_cm_member_earnings_per_type_history()
	{	
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_earnings_per_type_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			// process backup_monthly_cm_members_history
			$this->backup_monthly_cm_member_earnings_per_type_history();
			return;
			
		} else {
						
			$sql = "SELECT COUNT(*) AS cnt FROM cm_member_earnings_per_type_history_{$suffixdate}";
			
			$query = $this->db->query($sql);
			$table_archive = $query->first_row();
			
			// get count from cm_members_history
			$sql = "SELECT COUNT(*) AS cnt FROM cm_member_earnings_per_type_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
			
			$query = $this->db->query($sql);
			$table_live = $query->first_row();
			
			// check if live table != 0
			if ($table_live->cnt != 0) {
			
				if ($table_archive->cnt ==  $table_live->cnt) {										
					
					// delete old records to live table
					$sql = "DELETE FROM cm_member_earnings_per_type_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
					$query = $this->db->query($sql);	
						
					// log to tracking table					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_member_earnings_per_type_history)",
						"table_name"=>"cm_member_earnings_per_type_history",
						"action"=>"DELETE FROM cm_member_earnings_per_type_history - Old Records Have Been Deleted.",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					return;
					
				} else {				
					$sql = "DROP TABLE cm_member_earnings_per_type_history_{$suffixdate}";
					$query = $this->db->query($sql);	
					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_member_earnings_per_type_history)",
						"table_name"=>"cm_member_earnings_per_type_history_{$suffixdate}",
						"action"=>"DROP TABLE cm_member_earnings_per_type_history_{$suffixdate} - Run backup_monthly_cm_member_earnings_per_type_history_().",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					
					// process backup_monthly_cm_members_history
					$this->backup_monthly_cm_member_earnings_per_type_history();
					
					return;
				}			
			} else {
				$data = array(
					"module_name"=>"CHECK AND DELETE (cm_member_earnings_per_type_history)",
					"table_name"=>"cm_member_earnings_per_type_history",
					"action"=>"NONE - No Record Found on Live Table",
					"details"=>"n/a"						
				);
				
				$this->tracking_model->insert_cron_logs($data);	
				
			}			
		}
		return;
		
	}	
	
	// cm_member_account_pairing backup
	// END =====================
	
	
	// cm_member details backup (set every 6am, 6pm)
	public function backup_member_details() 
	{
		$sql = "INSERT INTO cm_members_history (
					member_id,
					email,
					mobile_number,
					service_depot,
					rf_id,
					metrobank_paycard_number,
					group_id,
					group_name,
					is_email_verified,
					is_paycard_verified,
					is_rf_id_verified,
					is_mobile_verified,
					is_auto_payout,
					is_paycard_corpo,
					is_active,
					is_on_hold,
					funds,
					gift_cheques,
					gcep
				) 
				(SELECT member_id,
					email,
					mobile_number,
					service_depot,
					rf_id,
					metrobank_paycard_number,
					group_id,
					group_name,
					is_email_verified,
					is_paycard_verified,
					is_rf_id_verified,
					is_mobile_verified,
					is_auto_payout,
					is_paycard_corpo,
					is_active,
					is_on_hold,
					funds,
					gift_cheques,
					gcep
				FROM 
					cm_members
				)";
		
		$this->db->query($sql);	
		
		$data = array(
			"module_name"=>"DAILY BACKUP (cm_members)",
			"table_name"=>"cm_members_history",
			"action"=>"INSERT cm_members_history - SELECT cm_members",			
			"details"=>$sql
			
		);
		$this->tracking_model->insert_cron_logs($data);
	}
	
	
	// archive every first day of the month
	public function backup_monthly_cm_members_history()
	{
		// get previous month date
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_members_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			
			// create table cm_members_history_{suffix_date} as select 		
			$sql = "CREATE TABLE cm_members_history_{$suffixdate} AS 
					SELECT
						history_id,
						member_id,
						email,
						mobile_number,
						service_depot,
						rf_id,
						metrobank_paycard_number,
						group_id,
						group_name,
						is_email_verified,
						is_paycard_verified,
						is_rf_id_verified,
						is_mobile_verified,
						is_auto_payout,
						is_paycard_corpo,
						is_active,
						is_on_hold,
						funds,
						gift_cheques,
						gcep,
						insert_timestamp
					FROM 
						cm_members_history
					WHERE
						date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'
						";
		
			$this->db->query($sql);
			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_members_history)",
				"table_name"=>"cm_members_history_{$suffixdate}",
				"action"=>"CREATE TABLE - SELECT cm_members_history",			
				"details"=>$sql
				
			);
			$this->tracking_model->insert_cron_logs($data);
		
            // 20140226 email to backup_history_tables_email_group
			$params = array(
				"table_name"=>"cm_members_history_{$suffixdate}",
				"current_timestamp"=>date("Y-m-d H:i:s")
			);
            
            $email_group = $this->settings->backup_history_tables_email_group;
			$emails = explode(',',$email_group);

			foreach($emails as $email) {
    			$data = array(
    				"email"=>$email,
    				"type"=>"backup_history_table_generic_email",
    				"params"=>$params,
                    "title"=>"Backup History Table (cm_members_history) Generic Email"
                    
    			);
    
    			//send email to user
    			Modules::run('jobs/notifications/send_email',$data); 
            }
        
		} else {
			// log to tracking table			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (cm_members_history)",
				"table_name"=>"cm_members_history_{$suffixdate}",
				"action"=>"NONE - Table Already Exists",			
				"details"=>"n/a"
				
			);
			$this->tracking_model->insert_cron_logs($data);
		}
		
	}
	
	// delete old records if backup was created properly
	public function check_and_delete_old_records_cm_members_history()
	{	
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_members_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			// process backup_monthly_cm_members_history
			$this->backup_monthly_cm_members_history();
			return;
			
		} else {
						
			$sql = "SELECT COUNT(*) AS cnt FROM cm_members_history_{$suffixdate}";
			
			$query = $this->db->query($sql);
			$table_archive = $query->first_row();
			
			// get count from cm_members_history
			$sql = "SELECT COUNT(*) AS cnt FROM cm_members_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
			
			$query = $this->db->query($sql);
			$table_live = $query->first_row();
			
			// check if live table != 0
			if ($table_live->cnt != 0) {
			
				if ($table_archive->cnt ==  $table_live->cnt) {										
					
					// delete old records to live table
					$sql = "DELETE FROM cm_members_history WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
					$query = $this->db->query($sql);	
						
					// log to tracking table					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_members_history)",
						"table_name"=>"cm_members_history",
						"action"=>"DELETE FROM cm_members_history - Old Records Have Been Deleted.",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					return;
					
				} else {				
					$sql = "DROP TABLE cm_members_history_{$suffixdate}";
					$query = $this->db->query($sql);	
					
					$data = array(
						"module_name"=>"CHECK AND DELETE (cm_members_history)",
						"table_name"=>"cm_members_history_{$suffixdate}",
						"action"=>"DROP TABLE cm_members_history_{$suffixdate} - Run backup_monthly_cm_members_history().",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					
					// process backup_monthly_cm_members_history
					$this->backup_monthly_cm_members_history();
					
					return;
				}			
			} else {
				$data = array(
					"module_name"=>"CHECK AND DELETE (cm_members_history)",
					"table_name"=>"cm_members_history",
					"action"=>"NONE - No Record Found on Live Table",
					"details"=>"n/a"						
				);
				
				$this->tracking_model->insert_cron_logs($data);	
				
			}			
		}
		return;
		
	}

	// backup tr_member_logs (monthly)
	public function backup_monthly_tr_member_logs() {
		// get previous month date
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'tr_member_logs_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			
			// create table cm_members_history_{suffix_date} as select 		
			$sql = "CREATE TABLE tr_member_logs_{$suffixdate} AS 
					SELECT	*
					FROM 
						tr_member_logs
					WHERE
						date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'
						";
		
			$this->db->query($sql);
			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (tr_member_logs)",
				"table_name"=>"tr_member_logs_{$suffixdate}",
				"action"=>"CREATE TABLE - SELECT tr_member_logs",			
				"details"=>$sql
				
			);
			$this->tracking_model->insert_cron_logs($data);
            
            // 20140226 email to backup_history_tables_email_group
			$params = array(
				"table_name"=>"tr_member_logs_{$suffixdate}",
				"current_timestamp"=>date("Y-m-d H:i:s")
			);
            
            $email_group = $this->settings->backup_history_tables_email_group;
			$emails = explode(',',$email_group);

			foreach($emails as $email) {
    			$data = array(
    				"email"=>$email,
    				"type"=>"backup_history_table_generic_email",
    				"params"=>$params,
                    "title"=>"Backup Tracking Table (tr_member_logs) Generic Email"
    			);
    
    			//send email to user
    			Modules::run('jobs/notifications/send_email',$data); 
            }
		
		} else {
			// log to tracking table			
			$data = array(
				"module_name"=>"MONTHLY BACKUP (tr_member_logs)",
				"table_name"=>"tr_member_logs_{$suffixdate}",
				"action"=>"NONE - Table Already Exists",			
				"details"=>"n/a"
				
			);
			$this->tracking_model->insert_cron_logs($data);
		}
		
	}
	

	public function check_and_delete_old_records_tr_member_logs()
	{	
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'tr_member_logs_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			$this->backup_monthly_tr_member_logs();
			return;
			
		} else {
						
			$sql = "SELECT COUNT(*) AS cnt FROM tr_member_logs_{$suffixdate}";
			
			$query = $this->db->query($sql);
			$table_archive = $query->first_row();
			
			// get count from cm_members_history
			$sql = "SELECT COUNT(*) AS cnt FROM tr_member_logs WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
			
			$query = $this->db->query($sql);
			$table_live = $query->first_row();
			
			// check if live table != 0
			if ($table_live->cnt != 0) {
			
				if ($table_archive->cnt ==  $table_live->cnt) {										
					
					// delete old records to live table
					$sql = "DELETE FROM tr_member_logs WHERE date(insert_timestamp) LIKE '{$suffix_date->suffixdate}%'";
					$query = $this->db->query($sql);	
						
					// log to tracking table					
					$data = array(
						"module_name"=>"CHECK AND DELETE (tr_member_logs)",
						"table_name"=>"tr_member_logs",
						"action"=>"DELETE FROM tr_member_logs - Old Records Have Been Deleted.",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					return;
					
				} else {				
					$sql = "DROP TABLE tr_member_logs_{$suffixdate}";
					$query = $this->db->query($sql);	
					
					$data = array(
						"module_name"=>"CHECK AND DELETE (tr_member_logs)",
						"table_name"=>"tr_member_logs_{$suffixdate}",
						"action"=>"DROP TABLE tr_member_logs_{$suffixdate} - Run backup_monthly_tr_member_logs().",			
						"details"=>$sql						
					);
					
					$this->tracking_model->insert_cron_logs($data);	
					
					$this->backup_monthly_tr_member_logs();
					
					return;
				}			
			} else {
				$data = array(
					"module_name"=>"CHECK AND DELETE (tr_member_logs)",
					"table_name"=>"tr_member_logs",
					"action"=>"NONE - No Record Found on Live Table",
					"details"=>"n/a"						
				);
				
				$this->tracking_model->insert_cron_logs($data);	
				
			}			
		}
		return;
		
	}
	
	public function igpsm_sales()
	{
		$this->load->model('items_model');
		$this->load->model('facilities_model');
		$this->load->model('payment_model');
		$this->load->model('logs_model');
		
		$from_date = date('Y-m-d', strtotime('yesterday'));
		$to_date = date('Y-m-d', strtotime('yesterday'));
		
		$igpsm_array = array();
		$non_igpsm_array = array();
		$igpsm_groups_array = array();
		
		//get those transactions with cards
		/*$transactions_query = "SELECT DISTINCT(b.transaction_id) FROM tr_cards_logging a, is_payment_transactions b, is_payment_transaction_details c WHERE a.transaction_id = b.transaction_id AND a.transaction_id = c.transaction_id AND c.payment_method NOT IN ('giftcheque', 'onlinegiftcheque') and a.type IN ('SP', 'RS') and (b.status = 'COMPLETED' OR b.status = 'RELEASED') AND (DATE(b.completed_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ORDER BY b.transaction_id";*/
		$transactions_query = "SELECT DISTINCT(a.transaction_id) FROM is_payment_transactions a, is_payment_transaction_products b, is_payment_transaction_details c WHERE a.transaction_id = b.transaction_id AND b.package_product_id = 0 AND b.is_product_rebate = 0 AND b.product_id IN (SELECT product_id FROM is_products_view WHERE is_igpsm = 1) AND a.transaction_id = c.transaction_id AND c.payment_method NOT IN ('giftcheque', 'onlinegiftcheque', 'gcep') AND a.status IN ('COMPLETED', 'RELEASED') AND (DATE(a.completed_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ORDER BY b.transaction_id";
		$query = $this->db->query($transactions_query);
		$result = $query->result();
		$result_transaction_ids = array();
		if(!empty($result))
		{
			foreach($result as $r) //per transaction
			{
				$transaction_id = $r->transaction_id;
				array_push($result_transaction_ids, $transaction_id);
				//get related products
				$products_query = "SELECT a.product_id, a.quantity, a.price, b.product_name FROM is_payment_transaction_products a, is_products_view b WHERE a.transaction_id = {$transaction_id} and a.product_id = b.product_id and a.package_product_id = 0 and a.is_product_rebate = 0 ORDER BY a.quantity DESC";
				$prod_query = $this->db->query($products_query);
				$products_result = $prod_query->result();
				
				//obtained product id, qty, name
				if(!empty($products_result))
				{
					foreach($products_result as $res) //per product per transaction
					{
						//get transaction
						$transaction_info = $this->payment_model->get_payment_transaction_by_id($transaction_id);
						$facility_id = $transaction_info->facility_id;
						
						//check per product if it produces a card
						$product_id = $res->product_id;
						$price = $res->price;
						$product = $this->items_model->get_product_by_id($product_id);
						$product_type = $product->product_type_id;
						$product_line = $product->product_line_id;
						
						$product_card = $this->items_model->get_product_cards(array('product_id' => $product_id));
						//currently, we assume that an item can only be involved in one grouping
						
						$quantity_bought = $res->quantity;
						
						if(empty($product_card)) //does not count for igpsm sales
						{
							//insert product count in Non-IGPSM Sales
							
							//check non-igpsm array if item and facility already exists
							if(isset($non_igpsm_array[$product_id][$facility_id])) 
							{
								//product already in array, update quantities
								$product_sale_array = $non_igpsm_array[$product_id][$facility_id];
								
								$price_to_use = $quantity_bought * $price;
								$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
								$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
								$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";

								$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
							}
							else
							{
								$price_to_use = $quantity_bought * $price;
								
								$product_sale_info = array(
									'product_id' => $product_id,
									'qty' => $quantity_bought,
									'amount' => $price_to_use,
									'facility_id' => $facility_id,
									'product_type_id' => $product_type,
									'product_line_id' => $product_line,
									'transaction_ids' => "{$transaction_id}",
									'sales_type' => 'NON-IGPSM'
								);
								$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
							}
						}
						else //counts for igpsm sales
						{
							$product_card = $product_card[0];
							$group_product_ids = $product_card->group_product_ids;
							$qty_needed = $product_card->qty_needed;
							$qty_counted = $product_card->qty_counted;
							
							$igpsm_package_query = "SELECT product_type_id FROM rf_product_types WHERE is_package = 1 AND is_igpsm != 0";
							$package_query = $this->db->query($igpsm_package_query);
							$igpsm_package_type_ids = $package_query->result();
							$package_type_ids = array();

							//create igpsm package ids array
							foreach($igpsm_package_type_ids as $i)
								array_push($package_type_ids, $i->product_type_id);

							$group_product_ids = $product_card->group_product_ids;
							
							//check if product will not release card is member, i.e., not starter or value packs
							if(!in_array($product_type, $package_type_ids))
							{
								//check if bought by member
								if($transaction_info->member_id == 0)
								{
									//include product in non-igpsm sales
									
									//check non-igpsm array if item already exists
									if(isset($non_igpsm_array[$product_id][$facility_id])) 
									{
										//product already in array, update quantities
										$product_sale_array = $non_igpsm_array[$product_id][$facility_id];
										
										$price_to_use = $quantity_bought * $price;
										$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
										$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
										$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";

										$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
									}
									else
									{
										$price_to_use = $quantity_bought * $price;
										
										$product_sale_info = array(
											'product_id' => $product_id,
											'qty' => $quantity_bought,
											'amount' => $price_to_use,
											'facility_id' => $facility_id,
											'product_type_id' => $product_type,
											'product_line_id' => $product_line,
											'transaction_ids' => "{$transaction_id}",
											'sales_type' => 'NON-IGPSM'
										);
										$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
									}								
									continue;
								}
							}
							//else continue
							//check for inclusion in IGPSM Sales
							
							if($product_id == $group_product_ids) //single item, no group
							{
								if($quantity_bought < $qty_needed)
								{
									//automatically count for non-igpsm sales
									//check non-igpsm array if item already exists
									if(isset($non_igpsm_array[$product_id][$facility_id])) 
									{
										//product already in array, update quantities
										$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

										$price_to_use = $quantity_bought * $price;
										$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
										$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
										$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";

										$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
									}
									else
									{
										$price_to_use = $quantity_bought * $price;
										
										$product_sale_info = array(
											'product_id' => $product_id,
											'qty' => $quantity_bought,
											'amount' => $price_to_use,
											'facility_id' => $facility_id,
											'product_type_id' => $product_type,
											'product_line_id' => $product_line,
											'transaction_ids' => "{$transaction_id}",
											'sales_type' => 'NON-IGPSM'
										);
										$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
									}
								}
								else
								{
									//might need to handle 4-in-1 on single product
									
									//divide
									$qty_to_deduct = $quantity_bought % $qty_needed;
									$qty_to_count = $quantity_bought - $qty_to_deduct;
									
									//count for IGPSM
								
									//check igpsm array if item already exists
									if(isset($igpsm_array[$product_id][$facility_id])) 
									{
										//product already in array, update quantities
										$product_sale_array = $igpsm_array[$product_id][$facility_id];
										
										$price_to_use = $qty_to_count * $price;
										$product_sale_array['qty'] = $product_sale_array['qty'] + $qty_to_count;
										$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
										$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";
										
										$igpsm_array[$product_id][$facility_id] = $product_sale_array;
									}
									else
									{
										$price_to_use = $qty_to_count * $price;
										
										$product_sale_info = array(
											'product_id' => $product_id,
											'qty' => $qty_to_count,
											'amount' => $price_to_use,
											'facility_id' => $facility_id,
											'product_type_id' => $product_type,
											'product_line_id' => $product_line,
											'transaction_ids' => "{$transaction_id}",
											'sales_type' => 'IGPSM'
										);
										$igpsm_array[$product_id][$facility_id] = $product_sale_info;
									}
									
									if($qty_to_deduct != 0) //other items go to non-igpsm sales
									{
										//check non-igpsm array if item already exists
										if(isset($non_igpsm_array[$product_id][$facility_id])) 
										{
											//product already in array, update quantities
											$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

											$price_to_use = $qty_to_deduct * $price;
											$product_sale_array['qty'] = $product_sale_array['qty'] + $qty_to_deduct;
											$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
											$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";

											$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
										}
										else
										{
											$price_to_use = $qty_to_deduct * $price;
											
											$product_sale_info = array(
												'product_id' => $product_id,
												'qty' => $qty_to_deduct,
												'amount' => $price_to_use,
												'facility_id' => $facility_id,
												'product_type_id' => $product_type,
												'product_line_id' => $product_line,
												'transaction_ids' => "{$transaction_id}",
												'sales_type' => 'NON-IGPSM'
											);
											$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
										}
									}
								}
							}
							else //part of group
							{
								$existing_group = in_array($group_product_ids, $igpsm_groups_array);
								if(!($existing_group))
									array_push($igpsm_groups_array, $group_product_ids);
								else
									continue;
							}
						}
					}
					//do groups here
					if(!empty($igpsm_groups_array))
					{
						foreach($igpsm_groups_array as $g)
						{
							$group_product_ids_array = explode(",", $g);
							//foreach($group_product_ids_array as $i)
							//{
								//get transaction products
								$transaction_products_where = "transaction_id = {$transaction_id} AND product_id IN ({$g}) AND package_product_id = '0'";
								$transaction_products = $this->payment_model->get_payment_transaction_products($transaction_products_where, "", "quantity DESC");
								
								if(!empty($transaction_products))
								{
									/*//get sum of transaction products involved
									$transaction_products_sum = $this->payment_model->get_payment_transaction_products($transaction_products_where, "", "", "SUM(quantity) as sum");
									$sum =  $transaction_products_sum[0]->sum;*/
									
									$sum = 0;
									//get sum of products, plus with modifications based on qty_counted
									foreach($transaction_products as $t)
									{
										$qty_bought = $t->quantity;
										$product_card = $this->items_model->get_product_cards(array('product_id' => $t->product_id));
										$product_card = $product_card[0];
										$qty_counted = $product_card->qty_counted;
										
										$qty_bought_counted = $qty_bought * $qty_counted;
										$sum += $qty_bought_counted;
										
										//order items by $qty_bought_counted
										$t->qty_bought_counted = $qty_bought_counted;
									}
									$qty_count_array= array();
									foreach($transaction_products as $k => $t)
										$qty_count_array[$k] = $t->qty_bought_counted;
									
									array_multisort($qty_count_array, SORT_DESC, $transaction_products);
									
									$product_card = $this->items_model->get_product_cards(array('group_product_ids' => $g));
									$product_card = $product_card[0];
									$qty_needed = $product_card->qty_needed;
									//$qty_counted = $product_card->qty_counted;
									
									$remainder = $sum % $qty_needed;

									$target_igpsm = $sum - $remainder;
									$target_non_igpsm = $remainder;
									
									if($target_igpsm <= 0) $target_igpsm = 0;
									
									foreach($transaction_products as $t)
									{
										$product_id = $t->product_id;
										$qty_bought = $t->quantity;
										$price = $t->price;
										
										$product = $this->items_model->get_product_by_id($product_id);
										$product_type = $product->product_type_id;
										$product_line = $product->product_line_id;
										
										$product_card_single = $this->items_model->get_product_cards(array('product_id' => $t->product_id));
										$product_card_single = $product_card_single[0];
										$qty_counted = $product_card->qty_counted;
										
										if($qty_bought < $target_igpsm) //not enough
										{	
											//check igpsm array if item already exists
											if(isset($igpsm_array[$product_id][$facility_id])) 
											{
												//product already in array, update quantities
												$product_sale_array = $igpsm_array[$product_id][$facility_id];
												
												$price_to_use = $qty_bought * $price;

												$product_sale_array['qty'] = $product_sale_array['qty'] + $qty_bought;
												$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
												$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";

												$igpsm_array[$product_id][$facility_id] = $product_sale_array;
											}
											else
											{
												$price_to_use = $qty_bought * $price;
												
												$product_sale_info = array(
													'product_id' => $product_id,
													'qty' => $qty_bought,
													'amount' => $price_to_use,
													'facility_id' => $facility_id,
													'product_type_id' => $product_type,
													'product_line_id' => $product_line,
													'transaction_ids' => "{$transaction_id}",
													'sales_type' => 'IGPSM'
												);
												$igpsm_array[$product_id][$facility_id] = $product_sale_info;
											}
											
											//update target igpsm to target - (bought * counted)
											$target_igpsm -= ($qty_bought * $qty_counted);
											if($target_igpsm <= 0) 
												$target_igpsm = 0;
										}
										elseif($qty_bought >= $target_igpsm) //enough or above
										{
											if($target_igpsm != 0)
											{
												//check igpsm array if item already exists
												if(isset($igpsm_array[$product_id][$facility_id])) 
												{
													//product already in array, update quantities
													$product_sale_array = $igpsm_array[$product_id][$facility_id];

													$price_to_use = $target_igpsm * $price;
													$product_sale_array['qty'] = $product_sale_array['qty'] + $target_igpsm;
													$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
													$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";

													$igpsm_array[$product_id][$facility_id] = $product_sale_array;
												}
												else
												{
													$price_to_use = $target_igpsm * $price;
													
													$product_sale_info = array(
														'product_id' => $product_id,
														'qty' => $target_igpsm,
														'amount' => $price_to_use,
														'facility_id' => $facility_id,
														'product_type_id' => $product_type,
														'product_line_id' => $product_line,
														'transaction_ids' => "{$transaction_id}",
														'sales_type' => 'IGPSM'
													);
													$igpsm_array[$product_id][$facility_id] = $product_sale_info;
												}
											}
											
											$to_non_igpsm = $qty_bought - $target_igpsm;
											if($to_non_igpsm > 0)
											{
												//check non-igpsm array if item already exists
												if(isset($non_igpsm_array[$product_id][$facility_id])) 
												{
													//product already in array, update quantities
													$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

													$price_to_use = $to_non_igpsm * $price;
													$product_sale_array['qty'] = $product_sale_array['qty'] + $to_non_igpsm;
													$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
													$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$transaction_id}";

													$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
												}
												else
												{
													$price_to_use = $to_non_igpsm * $price;
													$product_sale_info = array(
														'product_id' => $product_id,
														'qty' => $to_non_igpsm,
														'amount' => $price_to_use,
														'facility_id' => $facility_id,
														'product_type_id' => $product_type,
														'product_line_id' => $product_line,
														'transaction_ids' => "{$transaction_id}",
														'sales_type' => 'NON-IGPSM'
													);
													$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
												}
											}
										}
									}
								}
							//}
						}
					}
				}
			}
		}
		
		//do query for transactions with no cards released, therefore automatically making them non-igpsm
		//$result has the transactions ids of those with cards, therefore place in NOT IN
		$non_igpsm_transactions_where = "";
		if(!empty($result)) 
		{
			$result_transaction_ids = implode(",", $result_transaction_ids);
			$non_igpsm_transactions_where = " AND a.transaction_id NOT IN ({$result_transaction_ids})";
		}
		
		$non_igpsm_transactions_query = "SELECT a.* FROM is_payment_transactions a, is_payment_transaction_details b  WHERE (DATE(a.completed_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') AND a.employee_id = 0 AND a.transaction_id = b.transaction_id AND b.payment_method NOT IN ('giftcheque', 'onlinegiftcheque', 'gcep') {$non_igpsm_transactions_where}";
		$query = $this->db->query($non_igpsm_transactions_query);
		$non_igpsm_result = $query->result();
		
		if(!empty($non_igpsm_result))
		{
			//process
			foreach($non_igpsm_result as $r)
			{
				$transaction_products_where = array(
					'transaction_id' => $r->transaction_id,
					'package_product_id' => 0,
					'is_product_rebate' => 0
				);
				$transaction_products = $this->payment_model->get_payment_transaction_products($transaction_products_where);
				$facility_id = $r->facility_id;
				if(!empty($transaction_products))
				{
					foreach($transaction_products as $p)
					{
						$product_id = $p->product_id;
						$price = $p->price;
						$product = $this->items_model->get_product_by_id($product_id);
						$product_type = $product->product_type_id;
						$product_line = $product->product_line_id;
						$quantity_bought = $p->quantity;
						
						if(isset($non_igpsm_array[$product_id][$facility_id])) 
						{
							//product already in array, update quantities
							$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

							$price_to_use = $quantity_bought * $price;
							$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
							$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
							$product_sale_array['transaction_ids'] = $product_sale_array['transaction_ids'] . ", {$r->transaction_id}";

							$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
						}
						else
						{
							$price_to_use = $quantity_bought * $price;
							
							$product_sale_info = array(
								'product_id' => $product_id,
								'qty' => $quantity_bought,
								'amount' => $price_to_use,
								'facility_id' => $facility_id,
								'product_type_id' => $product_type,
								'product_line_id' => $product_line,
								'transaction_ids' => "{$r->transaction_id}",
								'sales_type' => 'NON-IGPSM'
							);
							$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
						}
					
					}
				}
			}
		}
		//insert igpsm sales and non-igpsm sales into table
		//print_r($igpsm_array);
		//print_r($non_igpsm_array);
		foreach($igpsm_array as $per_item)
		{
			foreach($per_item as $item)
			{
				$item['sales_date'] = $from_date;
				$this->logs_model->insert_igpsm_sales_logs($item);
			}
		}
		
		foreach($non_igpsm_array as $per_item)
		{
			foreach($per_item as $item)
			{
				$item['sales_date'] = $from_date;
				$this->logs_model->insert_igpsm_sales_logs($item);
			}
		}
	}
	
	public function inventory_daily_reports() {
	
		$this->load->model('facilities_model');
		
		$current_date = date('Y-m-d', strtotime('yesterday'));
		
		$facilities = $this->facilities_model->get_facilities();
		foreach($facilities as $f)
		{
			$facility_items = $this->facilities_model->get_facility_items_by_facility_id($f->facility_id);
			foreach ($facility_items as $i)
			{
                $item_name = addslashes($i->item_name);
				$sql = "INSERT INTO rt_facility_items (facility_id, item_id, item_name, qty, qty_pending, unit_id, item_date) VALUES({$i->facility_id}, {$i->item_id}, '{$item_name}', {$i->qty}, {$i->qty_pending}, {$i->unit_id}, '{$current_date}')";
				$this->db->query($sql);
			}
		}
	}
	
	public function payout_period_checker_igpsm() {
			
		$this->load->model('payout_model');
		
		// check if there is already and exisiting payout period for this week
		$where = "now() BETWEEN start_date AND end_date AND is_official = 1 AND payout_type = 'IGPSM'";
		$pp_count = $this->payout_model->get_payout_periods_count($where);
		
		if ($pp_count <= 0) {
			// no current schedule, get last completed payout period
			$query = "SELECT * FROM po_payout_periods WHERE status = 'COMPLETED' AND payout_type = 'IGPSM' AND is_official = 1 ORDER BY end_date DESC LIMIT 1";
			$pp_query = $this->db->query($query);
			$pp_result = $pp_query->result();
		
			$last_completed_payout_end_date = $pp_result[0]->end_date;
			
			// set start_date	
			$query = "SELECT DATE_ADD('{$last_completed_payout_end_date}', INTERVAL +1 SECOND) AS startdate";
			$start_date_query = $this->db->query($query);
			$start_date_result = $start_date_query->result();
			
			//set end date (first incoming friday)
			$query = "SELECT CAST(CONCAT(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL (6 - DAYOFWEEK(NOW())) DAY), '%Y-%m-%d'), ' 23:59:59') AS datetime) AS enddate";
			$end_date_query = $this->db->query($query);
			$end_date_result = $end_date_query->result();
		
			// insert to po_payout_period
			$data = array(
				'start_date' => $start_date_result[0]->startdate,
				'end_date' => $end_date_result[0]->enddate,
				'is_official' => '1',
				'status' => 'ACTIVE',
				'payout_type' => 'IGPSM'
			);
			
			$this->payout_model->insert_payout_period($data);
			
			$data = array(
						"module_name"=>"SET PAYOUT PERIOD - IGPSM",
						"table_name"=>"po_payout_periods",
						"action"=>"INSERT po_payout_periods - NEXT IGPSM",			
						"details"=>$start_date_result[0]->startdate . "|" . $end_date_result[0]->enddate
						
					);
					
			$this->tracking_model->insert_cron_logs($data);	
			
		}
	}
	
	public function payout_period_checker_unilevel() {
			
		$this->load->model('payout_model');
		
		// check if there is already and exisiting payout period for this week
		$where = "(SELECT DATE_ADD(now(), INTERVAL +20 DAY)) BETWEEN start_date AND end_date AND is_official = 1 AND payout_type = 'UNILEVEL'";
		$pp_count = $this->payout_model->get_payout_periods_count($where);
		
		if ($pp_count <= 0) {
			// set the next unilevel payout schedule
			$query = "SELECT DATE_SUB(LAST_DAY(DATE_ADD(NOW(), INTERVAL 1 MONTH)), INTERVAL DAY(LAST_DAY(DATE_ADD(NOW(), INTERVAL 1 MONTH)))-1 DAY) AS firstday_nextmonth,
						LAST_DAY(DATE_ADD(NOW(), INTERVAL 1 MONTH)) AS lastday_nextmonth";
			$pp_query = $this->db->query($query);
			$pp_result = $pp_query->result();
		
			$next_unilevel_start_date = $pp_result[0]->firstday_nextmonth . " 00:00:00";
			$next_unilevel_end_date = $pp_result[0]->lastday_nextmonth . " 23:59:59";
		
			// insert to po_payout_period
			$data = array(
				'start_date' => $next_unilevel_start_date,
				'end_date' => $next_unilevel_end_date,
				'is_official' => '1',
				'status' => 'ACTIVE',
				'payout_type' => 'UNILEVEL'
			);
			
			$this->payout_model->insert_payout_period($data);	

			$data = array(
						"module_name"=>"SET PAYOUT PERIOD - UNILEVEL",
						"table_name"=>"po_payout_periods",
						"action"=>"INSERT po_payout_periods - NEXT Unilevel",			
						"details"=>$next_unilevel_start_date . "|" . $next_unilevel_end_date
						
					);
					
			$this->tracking_model->insert_cron_logs($data);				
		}
	}
    
    public function delete_monthly_history_tables() {	
		
        
        $dropped_tablenames = "";
        
        // get suffix for last month
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		
        // check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_members_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if ($table_exists) {
		  
            // cm_member_accounts_history
            $sql = "DROP TABLE cm_members_history_{$suffixdate}";
            $query = $this->db->query($sql);	
    		
            $dropped_tablenames = 'cm_members_history_' . $suffixdate;
            
            // log to tracking table					
            $data = array(
            	"module_name"=>"CHECK AND DROP (cm_members_history_{$suffixdate})",
            	"table_name"=>"cm_members_history_{$suffixdate}",
            	"action"=>"DROP cm_members_history_{$suffixdate}",			
            	"details"=>$sql						
            );
            
            $this->tracking_model->insert_cron_logs($data);			           			
		}
        
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_accounts_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if ($table_exists) {
		  
            // cm_member_accounts_history
            $sql = "DROP TABLE cm_member_accounts_history_{$suffixdate}";
            $query = $this->db->query($sql);	
    		
            if (strlen(trim($dropped_tablenames)) > 0) {
                $dropped_tablenames = $dropped_tablenames .', cm_member_accounts_history_' . $suffixdate;
            } else {
                $dropped_tablenames = 'cm_member_accounts_history_' . $suffixdate;
            }
            
            // log to tracking table					
            $data = array(
            	"module_name"=>"CHECK AND DROP (cm_member_accounts_history_{$suffixdate})",
            	"table_name"=>"cm_member_accounts_history_{$suffixdate}",
            	"action"=>"DROP cm_member_accounts_history_{$suffixdate}",			
            	"details"=>$sql						
            );
            
            $this->tracking_model->insert_cron_logs($data);			           			
		}
        
        // check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_account_pairing_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if ($table_exists) {
		  
            // cm_member_accounts_history
            $sql = "DROP TABLE cm_member_account_pairing_history_{$suffixdate}";
            $query = $this->db->query($sql);	
    		
            if (strlen(trim($dropped_tablenames)) > 0) {
                $dropped_tablenames = $dropped_tablenames .', cm_member_account_pairing_history_' . $suffixdate;
            } else {
                $dropped_tablenames = 'cm_member_account_pairing_history_' . $suffixdate;
            }
            
            // log to tracking table					
            $data = array(
            	"module_name"=>"CHECK AND DROP (cm_member_account_pairing_history_{$suffixdate})",
            	"table_name"=>"cm_member_account_pairing_history_{$suffixdate}",
            	"action"=>"DROP cm_member_account_pairing_history_{$suffixdate}",			
            	"details"=>$sql						
            );
            
            $this->tracking_model->insert_cron_logs($data);			           			
		}
        
		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'cm_member_earnings_per_type_history_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();

        if ($table_exists) {
		  
            // cm_member_accounts_history
            $sql = "DROP TABLE cm_member_earnings_per_type_history_{$suffixdate}";
            $query = $this->db->query($sql);	
    		
            if (strlen(trim($dropped_tablenames)) > 0) {
                $dropped_tablenames = $dropped_tablenames .', cm_member_earnings_per_type_history_' . $suffixdate;
            } else {
                $dropped_tablenames = 'cm_member_earnings_per_type_history_' . $suffixdate;
            }
            
            // log to tracking table					
            $data = array(
            	"module_name"=>"CHECK AND DROP (cm_member_earnings_per_type_history_{$suffixdate})",
            	"table_name"=>"cm_member_earnings_per_type_history_{$suffixdate}",
            	"action"=>"DROP cm_member_earnings_per_type_history_{$suffixdate}",			
            	"details"=>$sql						
            );
            
            $this->tracking_model->insert_cron_logs($data);			           			
		}        

		// check if archive history table exists
		$sql = "SHOW TABLES LIKE 'tr_member_logs_{$suffixdate}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();

        if ($table_exists) {
		  
            // cm_member_accounts_history
            $sql = "DROP TABLE tr_member_logs_{$suffixdate}";
            $query = $this->db->query($sql);	
    		
            if (strlen(trim($dropped_tablenames)) > 0) {
                $dropped_tablenames = $dropped_tablenames .', tr_member_logs_' . $suffixdate;
            } else {
                $dropped_tablenames = 'tr_member_logs_' . $suffixdate;
            }
            
            // log to tracking table					
            $data = array(
            	"module_name"=>"CHECK AND DROP (tr_member_logs_{$suffixdate})",
            	"table_name"=>"tr_member_logs_{$suffixdate}",
            	"action"=>"DROP tr_member_logs_{$suffixdate}",			
            	"details"=>$sql						
            );
            
            $this->tracking_model->insert_cron_logs($data);			           			
		}        

		// po_member_accounts tables and history
		$sql = "SELECT table_name as po_tablename FROM INFORMATION_SCHEMA.TABLES
					WHERE table_schema = 'vitalc_db'
  					AND table_name LIKE 'po_member_accounts_201405%'";
				
		$query = $this->db->query($sql);
		$tables_po = $query->result();

		if (!(empty($tables_po))) {
			foreach($tables_po as $tpo) {
				$dropped_tablenames = $dropped_tablenames .', ' . $tpo->po_tablename;

				// delete table
				$sql = "DROP TABLE IF EXISTS `{$tpo->po_tablename}`";
				$this->db->query($sql); 	
			}
		}

		if (strlen(trim($dropped_tablenames)) > 0) {
        
            $params = array(
    				"table_name"=>$dropped_tablenames,
    				"current_timestamp"=>date("Y-m-d H:i:s")
    			);
                
            $email_group = $this->settings->backup_history_tables_email_group;
    		$emails = explode(',',$email_group);
    
    		foreach($emails as $email) {
    			$data = array(
    				"email"=>$email,
    				"type"=>"drop_history_tables_generic_email",
    				"params"=>$params,
                    "title"=>"Drop History Tables Generic Email"
    			);
    
    			//send email to user
    			Modules::run('jobs/notifications/send_email',$data); 
            }
        }    
	}	

	public function get_status_of_member_accounts() {
		/*// get previous month date
		$sql = "SELECT CONCAT(date_format(date_sub(now(), interval 1 month), '%Y-%m'))  AS suffixdate";
		
		$query = $this->db->query($sql);
		$suffix_date = $query->first_row();
			
		$suffixdate = str_replace('-', '', $suffix_date->suffixdate);
		*/

		// check po_payout_periods if now() = end_date + 1 minute
		$sql = "SELECT * FROM po_payout_periods 
				WHERE 
				DATE_FORMAT(DATE_ADD(end_date, INTERVAL 1 MINUTE), '%Y-%m-%d %H:%i') = DATE_FORMAT(now(), '%Y-%m-%d %H:%i')";
		
		// testing purposes
		//$sql = "SELECT * FROM po_payout_periods 
		//		WHERE 
		//		DATE_FORMAT(DATE_ADD(end_date, INTERVAL 1 MINUTE), '%Y-%m-%d %H:%i') = DATE_FORMAT('2014-05-17 00:00:01', '%Y-%m-%d %H:%i')";		

		$query = $this->db->query($sql);
		$present_payout = $query->first_row();

		if (empty($present_payout)) {

			// no scheduled payout
			echo "No payout report to generate at this moment.";
			return;

		} else {

			$enddatetime_suffix = date("Ymd_Hi", strtotime($present_payout->end_date));

			// check if archive history table exists
			$sql = "SHOW TABLES LIKE 'po_member_accounts_{$enddatetime_suffix}'";
					
			$query = $this->db->query($sql);
			$table_exists = $query->first_row();
			
			if (empty($table_exists)) {
				
				// create table cm_members_history_{suffix_date} as select 		
				$sql = "CREATE TABLE `po_member_accounts_{$enddatetime_suffix}` (
							  `member_account_id` int(11) NOT NULL AUTO_INCREMENT,
							  `member_id` int(11) DEFAULT NULL,
							  `member_user_account_id` int(11) DEFAULT NULL,
							  `account_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
							  `sponsor_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
							  `upline_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
							  `account_type_id` int(11) DEFAULT NULL,
							  `account_status_id` int(11) DEFAULT NULL,
							  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							  PRIMARY KEY (`member_account_id`),
							  UNIQUE KEY `account_id` (`account_id`),
							  KEY `member_id` (`member_id`),
							  KEY `sponsor_id` (`sponsor_id`),
							  KEY `upline_id` (`upline_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
						";
			
				$this->db->query($sql);
				
				$data = array(
					"module_name"=>"CREATE TABLE (po_member_accounts_{$enddatetime_suffix})",
					"table_name"=>"po_member_accounts",
					"action"=>"CREATE TABLE - po_member_accounts",			
					"details"=>$sql
					
				);
				$this->tracking_model->insert_cron_logs($data);

				// populate po_member_accounts
				$sql = "INSERT INTO `po_member_accounts_{$enddatetime_suffix}` (
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id`)
						(
						SELECT 
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id` 
						FROM 
							`cm_member_accounts`
						)";

				$this->db->query($sql);

				// log to tracking table
	           	$data = array(
					"module_name"=>"POPULATE TABLE (get status of member accounts)",
					"table_name"=>"po_member_accounts_{$enddatetime_suffix}",
					"action"=>"INSERT - po_member_accounts_{$enddatetime_suffix}, SELECT cm_member_accounts_{$enddatetime_suffix}",			
					"details"=>$sql
					
				);
				$this->tracking_model->insert_cron_logs($data);

				// insert into po_member_accounts_history
				$sql = "INSERT INTO `po_member_accounts_history` (
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id`)
						(
						SELECT 
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id` 
						FROM 
							`cm_member_accounts`
						)";

				$this->db->query($sql);

				// log to tracking table
	           	$data = array(
					"module_name"=>"POPULATE TABLE (get status of member accounts)",
					"table_name"=>"po_member_accounts_history",
					"action"=>"INSERT - po_member_accounts_history, SELECT cm_member_accounts",			
					"details"=>$sql
					
				);
				$this->tracking_model->insert_cron_logs($data);

			} else {

				// truncate table
				$sql = "TRUNCATE TABLE `po_member_accounts_{$enddatetime_suffix}`";
				$this->db->query($sql); 	

				// populate po_member_accounts
				$sql = "INSERT INTO `po_member_accounts_{$enddatetime_suffix}` (
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id`)
						(
						SELECT 
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id` 
						FROM 
							`cm_member_accounts`
						)";

				$this->db->query($sql);
			
				// log to tracking table
	           	$data = array(
					"module_name"=>"POPULATE TABLE (get_status_of_member_accounts)",
					"table_name"=>"po_member_accounts",
					"action"=>"INSERT - po_member_accounts_{$enddatetime_suffix}, SELECT cm_member_accounts_{$enddatetime_suffix}",			
					"details"=>$sql
					
				);
				$this->tracking_model->insert_cron_logs($data);

				// populate po_member_accounts_history
				$sql = "INSERT INTO `po_member_accounts_history` (
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id`)
						(
						SELECT 
							`member_id`, 
							`member_user_account_id`, 
							`account_id`, 
							`sponsor_id`, 
							`upline_id`, 
							`account_type_id`, 
							`account_status_id` 
						FROM 
							`cm_member_accounts`
						)";

				$this->db->query($sql);
			
				// log to tracking table
	           	$data = array(
					"module_name"=>"POPULATE TABLE (get status of member accounts)",
					"table_name"=>"po_member_accounts_history",
					"action"=>"INSERT - po_member_accounts_history, SELECT cm_member_accounts",			
					"details"=>$sql
					
				);
				$this->tracking_model->insert_cron_logs($data);
		
			}
		}	
	}
    
}