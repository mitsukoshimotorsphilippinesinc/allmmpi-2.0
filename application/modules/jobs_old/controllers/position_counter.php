<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Position_counter extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model('settings_model');
  		$this->load->model('members_model');
        ini_set('memory_limit', '2000M');
	}

	// 1 	-> ~405 accts/min :: ~70% cpu 			4:16
	// 3 	-> ~507 accts/min :: ~75% cpu 			
	// 30 	-> ~547 accts/min :: ~85% cpu 			3:10
	// 60 	-> ~510 accts/min :: ~78% cpu boom!
	// 100 	-> ~638 accts/min :; ~85% cpu boom!

	private $cnt_table = "cm_member_accounts_position_count";
	private $max_process_count = 20;
	private $reset_timeout = 5;

	public function test()
	{
		echo "test position counter";
		exit();
	}

	public function populate_table()
	{
		$position_count_timestamp = $this->settings_model->get_setting_by_slug('position_count_timestamp');
		$position_count_status = $this->settings_model->get_setting_by_slug('position_count_status');

		if(!isset($position_count_timestamp) || !isset($position_count_status))
		{
			$this->return_json('error','settings not found');
			return;
		}

		if($position_count_status != "ready")
		{
			$this->return_json('error', 'currently processing');
			return;
		}

		echo "SET PROCESSING STATUS\n";
		$this->settings_model->update_settings(array(
			'value' => 'processing'
		), array(
			'slug' => 'position_count_status'
		));

		echo "TRUNCATING TABLE\n";
		$sql = "TRUNCATE TABLE cm_member_accounts_position_count";
		$this->db->query($sql);		

		echo "CALL SPAWNER\n";
		$root_path = FCPATH;
		exec("/usr/bin/php {$root_path}jobs.php jobs position_counter spawner >> /dev/null 2>&1");

		$this->return_json('ok', 'processing has started');
	}

	public function ending()
	{
		echo "SET PROCESSING STATUS\n";
		$this->settings_model->update_settings(array(
			'value' => 'ready'
		), array(
			'slug' => 'position_count_status'
		));

		echo "SET POSITION COUNT TIMESTAMP\n";
		$this->settings_model->update_settings(array(
			'value' => date('Y-m-d H:i:s')
		), array(
			'slug' => 'position_count_timestamp'
		));
		exit();
	}

	public function spawner()
	{
		//$start_time = time();
		$offset_count = 0;
		$base_wait_time = 5000;
		$wait_time = $base_wait_time;
		echo "spawner started...";
		$root_path = FCPATH;
		while(true) {
			if($this->is_processing_count() < $this->max_process_count) {
				$wait_time = $base_wait_time;
				$account_id = $this->get_account_to_process($offset_count);
				if(!$account_id) {
					echo "no more account to process..ending\n";
					break;
				}
				$offset_count++;

				echo "create counter entry for {$account_id}...\n";
				$sql = "
				INSERT INTO 
					{$this->cnt_table}
				(
					`account_id`,
					`left_cnt`,
					`right_cnt`,
					`is_processing`
				)
				VALUES
				(
					{$account_id},
					0,
					0,
					1
				)
				";
				$query = $this->db->query($sql);

				echo "spawning zombie for {$account_id}...\n";
				$output = null;
				exec("/usr/bin/php {$root_path}jobs.php jobs position_counter zombie {$account_id} >> {$root_path}position_counter_log.log &", &$output);

				// garbage collect
				$sql = null;
				$account_id = null;
				$output = null;
				$query = null;
				unset($sql);
				unset($account_id);
				unset($output);
				unset($query);

				//usleep(1000);
			} else {
				// break;

				echo "queue at max... waiting... \n";
				$this->reset_entries();
				//usleep($wait_time);
				$wait_time+=$wait_time;

				usleep($wait_time);
			}
		}

		//$end_time = time();
		//$total_time = $end_time - $start_time;
		//echo "TOTAL PROCESS TIME: " . $total_time . "secs\n";
		//echo "spawner dying........\n";
		exec("/usr/bin/php {$root_path}jobs.php jobs position_counter ending >> /dev/null 2>&1");
		exit();
	}

	public function zombie($account_id)
	{
		echo "zombie {$account_id}: started processing \n";

		// start processing
		$start = time();
		$sql = "
		UPDATE
			{$this->cnt_table}
		SET
			start_timestamp = {$start}
		WHERE
			account_id = {$account_id}
		";
		$query = $this->db->query($sql);

		// count downlines
		$counts = $this->downline_count($account_id);
		$sql = "
		UPDATE
			{$this->cnt_table}
		SET
			left_cnt = {$counts[0]},
			right_cnt = {$counts[1]},
			is_processing = 0
		WHERE
			account_id = {$account_id}
		";
		$query = $this->db->query($sql);

		// end processing
		$end = time();
		$total = $end - $start;
		$sql = "
		UPDATE
			{$this->cnt_table}
		SET
			end_timestamp = {$end},
			total_timestamp = {$total}
		WHERE
			account_id = {$account_id}
		";
		$this->db->query($sql);

		echo "zombie {$account_id}: done processing.. dying..\n";

		$root_path = FCPATH;
		$output = null;
		//exec("/usr/bin/php {$root_path}jobs.php jobs position_counter minispawner >> {$root_path}position_counter_spawner_log.log &");
		exit();
	}

	public function downline_count($account_id)
	{
		$left = 0;
		$right = 0;
		$account = $this->members_model->get_member_accounts_by_account_ids(array($account_id));
		if(count($account) > 0) {
			$account = $account[0];
			$left  = $this->members_model->get_downline_count_by_node_address($account->node_address, 'l');
			$right  = $this->members_model->get_downline_count_by_node_address($account->node_address, 'r');
			return array($left, $right);
		}
		return false;
	}

	private function reset_entries()
	{
		$root_path = FCPATH;
		$now = date('Y-m-d h:i:s', time()+60);
		$sql = "
		SELECT
			*
		FROM
			{$this->cnt_table}
		WHERE
			is_processing = 1
		AND
			insert_timestamp > '{$now}'
		";
		//echo "\nRESET SQL\n" . $sql . "\n";
		$query = $this->db->query($sql);
		$result = $query->result();
		//print_r($result);
		foreach($result as $v)
		{
			$sql = "
			UPDATE
				{$this->cnt_table}
			SET
				is_processing = 0
			WHERE
				account_id = {$v->account_id}
			";
			$query = $this->db->query($sql);

			echo "reseting zombie for {$v->account_id}...\n";
			$output = null;
			exec("/usr/bin/php {$root_path}jobs.php jobs position_counter zombie {$v->account_id} >> {$root_path}position_counter_log.log &", &$output);
			sleep(1);
		}
		$now = null;
		$sql = null;
		$query = null;
		$result = null;
		unset($now);
		unset($sql);
		unset($query);
		unset($result);
	}

	private function is_processing_count()
	{
		$sql = "SELECT COUNT(1) AS 'cnt' FROM {$this->cnt_table} WHERE is_processing = 1";
		$query = $this->db->query($sql);
		$result = $query->result();

		$sql = null;
		$query = null;
		unset($sql);
		unset($query);

		return $result[0]->cnt;
	}

	private function get_account_to_process($offset_count)
	{
		$sql = "
		SELECT
			account_id
		FROM
			cm_member_accounts
		LIMIT {$offset_count},1;
		";
		$query = $this->db->query($sql);
		$result = $query->result();

		$sql = null;
		$query = null;
		unset($sql);
		unset($query);

		if(count($result) == 0) return false;
		return $result[0]->account_id;
	}
}