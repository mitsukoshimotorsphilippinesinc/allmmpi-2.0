<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {

	public function before()
	{
		parent::before();
	}

	public function index()
	{
		$this->template->view('dashboard');
	}
	
	public function _get_nav($current_system)
	{
		$this->set_system($current_system);
		echo $this->load->view('navigation', null, TRUE);
	}
	
	public function select_facility()
	{
		$facility_id = $this->input->post('facility_id');
		if (empty($facility_id) || !is_numeric($facility_id))
		{
			$this->return_json('error', 'Invalid request.'); //test
			return;
		}
		
		// load the facility model
		$this->load->model('facilities_model');
		$facility = $this->facilities_model->get_facility_by_id($facility_id);
		
		if (empty($facility))
		{
			$this->return_json('error', 'Invalid request.');
			return;
		}
		
		// facility exist
		// save it to our session to be the current selected facility
		ci()->session->set_userdata(array('selected_facility' => $facility));
		
		$this->return_json('ok', 'Ok', array('facility' => $facility));
		return;
	}
}
