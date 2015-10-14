<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobs extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("jobs_model");
	}
	
	public function view()
	{
		$jobs = $this->jobs_model->get_jobs();
		$ret = array();
		foreach($jobs as $job)
		{
			$job_type_code = $this->jobs_model->get_job_type_by_job_type_id($job->job_type_id);
			$tmp = new stdClass;
			$tmp->job_id = $job->job_id;
			$tmp->job_type = $job_type_code->job_code;
			$tmp->status = $job->status;
			$tmp->exceptions = $job->exceptions;
			$ret[] = $tmp;
		}
		$this->template->jobs = $ret;
		$this->template->view('jobs');
	}

	public function run_job()
	{
		$job_id = $this->input->post('job_id');

		$job = $this->jobs_model->get_jobs(array('job_id'=>$job_id));
		if(count($job) > 0)
		{
			$root_path = FCPATH;
			exec("/usr/bin/php {$root_path}jobs.php jobs process {$job_id} >> /dev/null 2>&1");
			$this->return_json(1,"SUCCESS");
		}
		else
		{
			$this->return_json(0,"ERROR");
		}
		return;
	}

}