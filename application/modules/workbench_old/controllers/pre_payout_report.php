<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pre_payout_report extends Base_Controller {
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

		$this->load->model("members_model");
		$this->load->model("jobs_model");
	}
	
	function index()
	{
		echo "Pre-payout report consisting details for payout per group per member per account";
	}
	
	function view()
	{
		$this->template->view("pre_payout_report/group_report");
	}
	
	function get_groups()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		
		//create temp table for all transactions for this payout period
		$drop_temp_sql = "DROP TABLE IF EXISTS `dgo_tmp_pre_payout_group_report`";
		$this->db->query($drop_temp_sql);

		$create_temp_sql = "CREATE TABLE 
								`dgo_tmp_pre_payout_group_report`
							AS 
							(SELECT 
										`t`.`credit_log_id`,
										`a`.`member_id`,
										CONCAT(`a`.`first_name`,' ',`a`.`middle_name`,'. ',`a`.`last_name`) as 'member_name',
										`a`.`group_name`,
										`b`.`account_id`, 
										`a`.`funds`,
										`a`.`gift_cheques`,
										(CASE `a`.`is_auto_payout`
											WHEN 1 THEN 'Yes'
											WHEN 0 THEN 'No'
										END) as 'auto_payout',
										`b`.`sponsor_id`,
										`b`.`upline_id`,
										(CASE `b`.`account_status_id`
											WHEN 1 THEN 'Active'
											WHEN 2 THEN 'Inactive'
											WHEN 3 THEN 'Company'
										END) as 'status',
										`b`.`monthly_maintenance_ctr`,
										`b`.`annual_maintenance_ctr`,
										`b`.`ms_monthly_maintenance_ctr`,
										`b`.`ms_annual_maintenance_ctr`,
										`b`.`left_sp`,
										`b`.`right_sp`,
										`b`.`gc_sp`,
										`b`.`left_vp`,
										`b`.`right_vp`,
										`b`.`gc_vp`,
										`b`.`left_rs`,
										`b`.`right_rs`,
										`b`.`gc_rs`,
										`t`.`transaction_code`,
										`t`.`amount`,
										`t`.`insert_timestamp`
									FROM 
										`tr_member_acct_credit_logs` `t`
									LEFT JOIN
										`cm_member_accounts` `b` 
									ON 
										(`t`.`account_id` = `b`.`account_id`)
									LEFT JOIN
										`cm_members` `a`
									ON
										(`b`.`member_id` = `a`.`member_id`) 
									WHERE
										`t`.`insert_timestamp` BETWEEN '{$start_date}' AND '{$end_date}'
									AND 
										`t`.`amount` > 0)";
		$this->db->query($create_temp_sql);
		
		$member_groups = $this->members_model->get_member_groups();
		
		$html = "<div>";
		$html .= "
			<div class='row-fluid'>
				<div class='span12' style='text-align: center; font-weight: bold;'>Please wait. Generation process may take awhile.</div>
			</div>
			<div style='max-height: 400px; overflow-y:scroll;'>
		";
		foreach($member_groups as $mg)
		{
			$html .= "
			<div class='row-fluid'>
				<div class='span7'>{$mg->group_name}</div>
				<div class='span5'>
					<div class='all-sheets dl-sheets' data-dl='{$mg->group_id}'>
						<div class='label' style='display: inline;'>Pending</div>
					</div>
				</div>
			</div>
			";
		}
		$html .= "</div></div>";
		
		$this->return_json("ok","success",array("html" => $html));
		return;
	}
	
	function start_group_report_download_job()
	{
		$group_id = $this->input->get_post("group_id");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");

		$params = array(
			'group_id' => $group_id,
			'start_date' => $start_date,
			'end_date' => $end_date
		);
		$job_data = array(
			'job_type_id' => 6, // pre-payout
			'parameters' => json_encode($params)
		);
		$this->jobs_model->insert_job($job_data);
		$job_id = $this->jobs_model->insert_id();

		job_exec($job_id);

		$this->return_json("ok","SUCCESS",array('job_id'=>$job_id));
		return;
	}
	
	function check_group_report_job_status()
	{
		$job_id = $this->input->get_post("job_id");

		$job = $this->jobs_model->get_jobs(array(
			'job_id' => $job_id
		));

		if(count($job) > 0)
		{
			$job = $job[0];
			if($job->status == "completed")
			{
				$this->return_json("ok","SUCCESS", array('date_generated'=>$job->insert_timestamp));
			}
			else
			{
				$this->return_json("error","FAIL");
			}
		}
		else
		{
			$this->return_json("error","FAIL");
		}

		return;
	}
	
	public function merge_download_segmented_excel()
	{
		
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);
		$merged_filename = 'group_pre_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '.xlsx';
		
		$params = array(
			'start_date' => $start_date,
			'end_date' => $end_date
		);
		$job_data = array(
			'job_type_id' => 9, // pre-payout
			'parameters' => json_encode($params)
		);
		$this->jobs_model->insert_job($job_data);
		$job_id = $this->jobs_model->insert_id();

		job_exec($job_id);
		
		$this->return_json("ok","SUCCESS",array('job_id'=>$job_id,"filename" => 'group_pre_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '.xlsx'));
		return;
	}
}
