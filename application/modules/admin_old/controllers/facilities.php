<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Facilities extends Systems_Controller 
{
	private $_validation_rule = array(
		array(
			'field' => 'facility_name',
			'label' => 'Facility Name',
			'rules' => 'trim|required|min_length[4]'
		),
		array(
			'field' => 'facility_description',
			'label' => 'Facility Description',
			'rules' => 'trim'
		),
		array(
			'field' => 'facility_address',
			'label' => 'Facility Address',
			'rules' => 'trim'
		),
		array(
			'field' => 'facility_contact_details',
			'label' => 'Facility Contact Details',
			'rules' => 'trim'
		),
		array(
			'field' => 'facility_type_id',
			'label' => 'Facility Type',
			'rules' => 'trim'
		),
	);
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('facilities_model');
	}
	
	public function index() 
	{ 
		$this->template->facilities = $this->facilities_model->get_facilities();
		$this->template->view('facilities/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run()) 
			{
				error_log('valid');
				// insert the new user
				$data = array(
					'facility_name' => set_value('facility_name'),
					'facility_description' => set_value('facility_description'),
					'facility_type_id' => set_value('facility_type_id'),
					'facility_address' => set_value('facility_address'),
					'facility_contact_details' => set_value('facility_contact_details'),
				);
				$this->facilities_model->insert_facility($data);
				
				$insert_id = $this->facilities_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_facilities_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FACILITIES',
					'table_name' => 'is_facilities',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_facilities_log_data);
				
				
				redirect('/admin/facilities');
				return;
			}
		}
		$this->template->view('facilities/add');
		
	}
	
	public function edit($facility_id = 0)
	{
		$facility = $this->facilities_model->get_facility_by_id($facility_id);
		
		if ($_POST and !empty($facility))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run()) 
			{

				// insert the new user
				$data = array(
					'facility_name' => set_value('facility_name'),
					'facility_description' => set_value('facility_description'),
					'facility_type_id' => set_value('facility_type_id'),
					'facility_address' => set_value('facility_address'),
					'facility_contact_details' => set_value('facility_contact_details'),
				);
				$this->facilities_model->update_facility($data, array('facility_id' => $facility_id));
				
				//logging of action
				$data_before = array(
					'facility_name' => $facility->facility_name,
					'facility_description' => $facility->facility_description,
					'facility_type_id' => $facility->facility_type_id,
					'facility_address' => $facility->facility_address,
					'facility_contact_details' => $facility->facility_contact_details,
				);
				
				$details_before = array('id' => $facility_id, 'details' => $data_before);
				$details_before = json_encode($details_before);
				
				$details_after = array('id' => $facility_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$update_facilities_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FACILITIES',
					'table_name' => 'is_facilities',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_facilities_log_data);
				
				redirect('/admin/facilities');
				return;
			}
		}

		$this->template->facility = $facility;	
		$this->template->view('facilities/edit');
		
	}
	
	public function delete($facility_id = 0)
	{
		$facility = $this->facilities_model->get_facility_by_id($facility_id);
		
		if ($_POST and !empty($facility))
		{
			$_facility_id = $this->input->post('facility_id');
			if (!empty($_facility_id)) if ($_facility_id == $facility_id)
			{
				$this->facilities_model->delete_facility(array('facility_id' => $facility_id));
				
				//logging of action
				$data_before = array(
					'facility_name' => $facility->facility_name,
					'facility_description' => $facility->facility_description,
					'facility_type_id' => $facility->facility_type_id,
					'facility_address' => $facility->facility_address,
					'facility_contact_details' => $facility->facility_contact_details,
				);
				
				$details_before = array('id' => $facility_id, 'details' => $data_before);
				$details_before = json_encode($details_before);

				$delete_facilities_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FACILITIES',
					'table_name' => 'is_facilities',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_facilities_log_data);
				
				redirect('/admin/facilities');
				return;
			}
		}
		
		$this->template->facility = $facility;
		$this->template->view('facilities/delete');
		
	}

}
