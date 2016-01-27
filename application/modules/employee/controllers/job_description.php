<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_description extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('human_relations_model');
		$this->load->model('setting_model');

		$this->load->library('pager2');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{							
		$this->template->current_page = 'job_description';

		// get parent position_id
		$position_details = $this->human_relations_model->get_position_by_id($this->employee->position_id);

		$position_id = 0;

		if (empty($position_details)) {
			// error
		} else {
			$position_id = ($position_details->parent_position_id == 0) ? $position_id = $position_details->position_id : $position_id = $position_details->parent_position_id;

			$job_description_asset_details = $this->human_relations_model->get_job_description_asset(array("position_id" => $position_id, "is_active" => 1));
			$this->template->position_details = $position_details;
			
			if (!empty($job_description_asset_details)) {
				$this->template->job_description_asset_details = $job_description_asset_details[0];
			} else {
				$this->template->job_description_asset_details = "";
			}
		}
			
		$this->template->view('job_description/dashboard');
	}
}
