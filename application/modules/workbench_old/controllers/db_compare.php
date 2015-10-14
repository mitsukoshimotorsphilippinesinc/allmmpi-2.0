<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Db_Compare extends  Base_Controller
{

	function __construct() 
	{
  		parent::__construct();

		
	}


	function index()
	{
		//$staging_database = json_decode($this->do_curl('http://vitalc.dev/workbench/db_compare/get_all_db_info/'));
		$staging_database = json_decode($this->do_curl('http://vitalc.dev.gobeyondstudios.com/workbench/db_compare/get_all_db_info/'));
		$live_database = json_decode($this->do_curl('http://vital-c.net/workbench/db_compare/get_all_db_info/'));
		$staging_count = 0;
		$live_count = 0;

		$joined_db = $this->join_db($staging_database, $live_database);

		foreach($joined_db as $key=>$value)
		{
			if(isset($joined_db[$key]['staging']))
				$staging_count++;
			if(isset($joined_db[$key]['live']))
				$live_count++;
		}

		?>

		<table style="position:fixed; border: 2px solid;" cellpadding="0" cellspacing="0">
			<tr>
				<td class="title">
					STAGING DATABASE
				</td>
				<td class="title">
					LIVE DATABASE
				</td>
			</tr>
		</table>
		<div style="height:47px;"> </div>
		<table cellspacing="0" >
			<tr>
				<td >
					Table Count: <?php if(isset($staging_database->db_info->table_count)) echo $staging_count; else echo 'Error on retrieving staging database'; ?>
				</td>
				<td >
					Table Count: <?php if(isset($live_database->db_info->table_count)) echo $live_database->db_info->table_count; else echo 'Error on retrieving live database'; ?>
				</td>
			</tr>
			<?php 
				foreach($joined_db as $key => $value) { 
					
					if(isset($joined_db[$key]['staging']) && isset($joined_db[$key]['live']))
						$background = " background:#FFFFFF;";
					else
						$background = " background:YELLOW;";

			?>
			<tr style="<?php echo $background;?>">
				<td>
					<?php 
						if(isset($joined_db[$key]['staging']))
						{
							echo '<span class="table_name">'.$key.'</span> '; 
							echo '<span class="fields_count">('.$staging_database->db_info->tables->$key->field_count.')</span>';
							echo "<div>";
							foreach($staging_database->db_info->tables->$key->fields as $field_index => $field)
							{	

								if(!isset($live_database->db_info->tables->$key->fields->$field_index) && isset($live_database->db_info->tables->$key))
									echo '<span style="background:#00FF00">';
								else
									echo '<span>'; 
								echo $field->name.' <span class="fields_count">'.$field->type .'</span><br />';
								

								echo '</span>';
							}
							echo "</div>";
						}
					?>
				</td>
				<td>
					<?php 
						if(isset($joined_db[$key]['live']))
						{
							echo '<span class="table_name">'.$key.'</span> ';
							echo '<span class="fields_count">('.$live_database->db_info->tables->$key->field_count .')</span>'; 
							echo "<div>";
							foreach($live_database->db_info->tables->$key->fields as $field_index => $field)
							{
								if(!isset($staging_database->db_info->tables->$key->fields->$field_index) && isset($staging_database->db_info->tables->$key))
									echo '<span style="background:#00FF00">';
								else
									echo '<span>'; 
								echo $field->name.' <span class="fields_count">'.$field->type .'</span><br />';
								if(!isset($staging_database->db_info->tables->$key->fields->$field_index))
									echo '</span>';
							}
							echo "</div>";
						}
					?>
				</td>
			</tr>
			<?php } ?>
		</table>
			<style>
				body {
					font-family: Arial;
					font-size:12px;
					padding:0px;
					margin:0px;
				}
				table { 
					width:100%; 
					font-size:12px;
					border: 2px solid #CCC;

				}
				td { 
					width:50%;
					vertical-align: top;
					border: 2px solid #CCC;
					empty-cells: show;
					padding:10px;
				}
				.title {
					line-height: 20px; 
					text-align:center;
					background: #CCC;
					border:2px solid ;
				}
				.table_name {
					font-weight: bold;
				}
				.fields_count {
					color: blue;
				}
				div { 
					margin-left:20px;
				}
			</style>
		<?php
	}

	function get_all_db_info()
	{	

		if(!$this->input->post('md5_secure') == md5('workbench'))
		{
			echo "DB Compare workbench";
			return;
		}

		$database = $this->input->post('database');	
		$db_info = array();
		$tables = '';

		if(!$database)
		{
			$tables = $this->db->list_tables();
		}
		else
		{	
			$db_other['hostname'] = $this->db->hostname;
			$db_other['username'] = $this->db->username;
			$db_other['password'] = $this->db->password;
			$db_other['database'] = $database;
			$db_other['dbdriver'] = $this->db->dbdriver;
			$db_other['pconnect'] = FALSE;
			$db_other['db_debug'] = TRUE;
			$db_other['cache_on'] = FALSE;
			$db_other['char_set'] = 'utf8';
			$db_other['dbcollat'] = 'utf8_unicode_ci';
			$db_other['swap_pre'] = '';
			$db_other['autoinit'] = TRUE;
			$db_other['stricton'] = FALSE;
			$db_other['failover'] = array();

			$db2 = $this->load->database($db_other,TRUE);
			$tables = $db2->list_tables();
		}

		foreach($tables as $table)
		{
			$tbl_array = array();
		
			if($database != '')
				$fields = $db2->field_data($table);
			else
				$fields = $this->db->field_data($table);
			
			foreach($fields as $field)
			{
				$db_info['db_info']['tables'][$table]['fields'][$field->name] = $field; 
			}
			$db_info['db_info']['tables'][$table]['field_count'] = count($fields); 
		}

		$db_info['db_info']['table_count'] = count($db_info['db_info']['tables']);

		echo json_encode($db_info);

	}

	private function do_curl($url,$database = '')
	{
		if (!function_exists('curl_init')){
	        die('Sorry cURL is not installed!');
	    }
	 
	   	$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('database'=>$database,'md5_secure'=>md5('workbench')));
	    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;	
	}

	private function join_db($staging_database, $live_database)
	{
		$all_tables = array();
		if(isset($staging_database->db_info->tables))
		{
			foreach($staging_database->db_info->tables as $key=>$value)
			{	
				if($this->show_table($key))
					$all_tables[$key]['staging'] = '1';
			}
		}
		if(isset($live_database->db_info->tables))
		{
			foreach($live_database->db_info->tables as $key=>$value)
			{
				if($this->show_table($key))
					$all_tables[$key]['live'] = '1';
			}
		}

		ksort($all_tables,SORT_REGULAR);
		return $all_tables;
	}

	private function show_table($table)
	{
		$table_array[] = 'cm_member_account_commissions_copy';
		$table_array[] = 'cm_member_earnings_per_type_bak_20131127';	
		$table_array[] = 'cm_member_payouts_bak';
		$table_array[] = 'cm_members_before_variance_adjustments';
		$table_array[] = 'ddp_members_zero_gc_commission_20130727';
		$table_array[] = 'dgo_tmp_pre_payout_group_report';
		$table_array[] = 'fv_members_funds_variance_after_process_20130713_20130719';
		$table_array[] = 'gc_crediting_20140111_20140117';
		$table_array[] = 'gc_crediting__';
		$table_array[] = 'gcep_crediting_20140111_20140117';
		$table_array[] = 'gcep_crediting__';
		$table_array[] = 'is_facility_item_backup_0418';
		$table_array[] = 'is_facility_items_backup';
		$table_array[] = 'is_facility_items_backup_0418_v2';
		$table_array[] = 'is_facility_items_backup_20130416';
		$table_array[] = 'pe_member_account_commissions_history_new_all_funds_active';
		$table_array[] = 'pe_member_account_commissions_history_real_member_ids';
		$table_array[] = 'pe_member_commissions_history';
		$table_array[] = 'pe_member_consolidated_commissions_1';
		$table_array[] = 'pe_real_account_member_id_per_payout';
		$table_array[] = 'ph_funds_to_paycard_20130629_20130705_2';
		$table_array[] = 'ph_funds_to_paycard_backup_20130405';
		$table_array[] = 'po_member_commissions_igpsm_view';
		$table_array[] = 'po_member_deduction_conflicts';
		$table_array[] = 'po_member_deductions_20130525_20130531';
		$table_array[] = 'po_member_deductions_20130601_20130607';
		$table_array[] = 'po_member_deductions_20130629_20130705';
		$table_array[] = 'rf_cm_member_duplicates_02142013';
		$table_array[] = 'tr_igpsm_sales_logs_backup_0524';
		$table_array[] = 'tr_member_accounts_logs_2';

		if(substr(strtolower($table),0,4) == 'dnd_' || 
			substr(strtolower($table),0,4) == 'dont'|| 
			substr(strtolower($table),0,4) == 'trh_' || 
			substr(strtolower($table),0,4) == 'ph_m' || 
			substr(strtolower($table),0,4) == 'tmp_' ||
		
			stripos(strtolower($table), 'cm_member_account_pairing_history_') !== false ||
			stripos(strtolower($table), 'cm_member_accounts_history_')  !== false ||
			stripos(strtolower($table), 'cm_member_earnings_per_type_history_')  !== false ||
			stripos(strtolower($table), 'cm_members_history_')  !== false ||
			in_array($table,$table_array)
	
		
			)
		{	
			return false;
		}

		return true;
	}


}

?>