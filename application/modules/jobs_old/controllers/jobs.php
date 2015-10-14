<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!defined('CRON_SHELL')) exit('This script is accessed thru CRON script only');

class Jobs extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model('jobs_model');
	}
	
	public function index() 
	{
		echo "Process Job Requests...";
	}

	public function process()
	{
		$job_id = abs($this->uri->segment(3));
		if($job_id==0) exit;
		$job = $this->jobs_model->get_job_by_id($job_id);
		if(empty($job)) exit;

		if($job->job_type_id = 0) exit;

		if($job->status=="completed") exit;

		$params = (array) json_decode($job->parameters);
		$params['process_timestamp'] = $job->insert_timestamp;
		$this->jobs_model->update_jobs(array('status'=>"processing"),array('job_id'=>$job->job_id));

        // logs data
        $details_before = array();
        $details_after = array();

        // before process data
        if($job->job_code=="encode_rs"){
        	$this->load->model('cards_model');
        	$details_before = $this->cards_model->get_rs_card_by_card_id($params['card_id']);
        }
		
		$scripts = explode("|",$job->scripts);
		$script_output_buffer = "";
		foreach($scripts as $s){
			$output = Modules::run($s,$params);
			error_log($output);
			$script_output_buffer .= "[" . $s . "] " . $output . "\n";
		}
		
		// after process data
		if($job->job_code=="encode_rs"){
			$this->load->model('tracking_model');
        	$details_after = $this->cards_model->get_rs_card_by_card_id($params['card_id']);
        	$rs_card_logs = array(
				'member_id' => "",
				'module_name' => "ENCODING",
				'table_name' => "is_rs_cards",
				'action' => "UPDATE",
				'details_before' => json_encode($details_before),
				'details_after' => json_encode($details_after),
				'remarks' => "",
				'insert_timestamp' => $params['process_timestamp']
			);
			$this->tracking_model->insert_logs('members', $rs_card_logs);
        }

        $this->jobs_model->update_jobs(array(
        	'status'=>"completed",
        	'exceptions'=>$script_output_buffer
        ),array('job_id'=>$job->job_id));

		return;
		
	}

	public function process_all()
	{
		$jobs = $this->jobs_model->get_jobs(array('status'=>"pending"));
		if(count($jobs) > 0){
			foreach($jobs as $job){
				$params = (array) json_decode($job->parameters);
				$params['process_timestamp'] = $job->insert_timestamp;
				$this->jobs_model->update_jobs(array('status'=>"processing"),array('job_id'=>$job->job_id));

		        // logs data
		        $details_before = array();
		        $details_after = array();

		        // before process data
		        if($job->job_code=="encode_rs"){
		        	$this->load->model('cards_model');
		        	$details_before = $this->cards_model->get_rs_card_by_card_id($params['card_id']);
		        }

				try {
					$scripts = explode("|",$job->scripts);
					foreach($scripts as $s){
						Modules::run($s,$params);
					}
					
					// after process data
					if($job->job_code=="encode_rs"){
						$this->load->model('tracking_model');
			        	$details_after = $this->cards_model->get_rs_card_by_card_id($params['card_id']);
			        	$rs_card_logs = array(
							'member_id' => "",
							'module_name' => "ENCODING",
							'table_name' => "is_rs_cards",
							'action' => "UPDATE",
							'details_before' => json_encode($details_before),
							'details_after' => json_encode($details_after),
							'remarks' => "",
							'insert_timestamp' => $params['process_timestamp']
						);
						$this->tracking_model->insert_logs('members', $rs_card_logs);
			        }

			        $this->jobs_model->update_jobs(array('status'=>"completed"),array('job_id'=>$job->job_id));
			        // $this->jobs_model->delete_jobs(array('job_id'=>$job->job_id));

				} catch (Exception $e) {
					$this->jobs_model->update_jobs(array(
						'status' => "failed",
						'exceptions'=>$e->getMessage()
					),array('job_id'=>$job->job_id));
				}
			}
		}
	}

}