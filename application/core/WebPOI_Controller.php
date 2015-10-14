<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class WebPOI_Controller extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->set_system('webpoi');
		

		if (empty($this->selected_facility) || is_null($this->selected_facility))
		{
			// get default user facility
			$where = array("user_id"=>$this->user->user_id, "is_default"=> 1);
			$default_facility = $this->users_model->get_user_facilities($where);
			$this->selected_facility = $this->facilities_model->get_facility_by_id($default_facility[0]->facility_id);
		}
	}
	
	public function before()
	{
		parent::before();
	}
	
	public function after()
	{
		parent::after();
	}

}