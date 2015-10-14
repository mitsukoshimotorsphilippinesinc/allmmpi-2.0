<?php 
//if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//if (!defined('CRON_SHELL')) exit('This script is accessed thru CRON script only');

class Jobs extends Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model('job_model');
	}
	
	public function index() 
	{
		echo "Process Job Requests...";
	}

	public function process()
	{

		$job_id = abs($this->uri->segment(3));
		if($job_id==0) exit;
		$job = $this->job_model->get_job_by_id($job_id);
		if(empty($job)) exit;

		if($job->job_type_id = 0) exit;

		if($job->status=="COMPLETED") exit;

		$params = (array) json_decode($job->parameters);
		$params['process_timestamp'] = $job->insert_timestamp;
		$current_datetime = date("Y-m-d H:i:s");
		$this->job_model->update_job(array('status'=>"PROCESSING",'processing_timestamp'=>$current_datetime),array('job_id'=>$job->job_id));

        // logs data
        $details_before = array();
        $details_after = array();

		$scripts = explode("|",$job->scripts);
		$script_output_buffer = "";
		foreach($scripts as $s){
			$output = Modules::run($s,$params);
			error_log($output);
			$script_output_buffer .= "[" . $s . "] " . $output . "\n";
		}
		
        $current_datetime = date("Y-m-d H:i:s");
        $this->job_model->update_job(array(
        	'status'=>"COMPLETED",
        	'completed_timestamp'=>$current_datetime,
        	'exceptions'=>$script_output_buffer
        ),array('job_id'=>$job->job_id));

		return;
		
	}

}