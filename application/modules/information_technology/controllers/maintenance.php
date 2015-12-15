<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maintenance extends Admin_Controller {

	private $_repair_hardware_validation_rule = array(
		array(
			'field' => 'hardware_name',
			'label' => 'Hardware Name',
			'rules' => 'trim|required|is_unique'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim|required'
		)		
	);

	function __construct()
	{
		parent::__construct();		
		$this->load->model('information_technology_model');		
		$this->load->library('pager');			
		$this->load->helper("systems_helper");	
		$this->load->helper("breadcrumb_helper");	

		$this->db_information_technology = $this->load->database('information_technology', TRUE);

	}

	public $segment_name = "maintenance";

	public function index()
	{		
		$this->template->view('maintenance/dashboard');
	}


	// REPAIR HARDWARE
	// ------------------------------------------
	public function repair_hardware()
	{

		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	


		// set pagination data
		$config = array(
		    'pagination_url' => '/information_technology/maintenance/repair_hardware/',
		    'total_items' => $this->information_technology_model->get_repair_hardware_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);
		$this->template->repair_hardware = $this->information_technology_model->get_repair_hardware($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'repair_hardware_name');
		$this->template->view('maintenance/repair_hardware/list');
	}	


	public function add_repair_hardware()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_repair_hardware_validation_rule);
			if ($this->form_validation->run())
			{
				//$this->information_technology_model->update_repair_hardware(array('is_active' => 0),array());
			
				// insert the new results
				$data = array(
					'repair_hardware_name' => strtoupper(set_value('hardware_name')),
					'description' => strtoupper(set_value('description'))					
				);
				$this->information_technology_model->insert_repair_hardware($data);
				
				$insert_id = $this->information_technology_model->insert_id();
				
				$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($insert_id);

				//logging of action			
				$details_before = array('id' => 0, 'details' => array());
				$details_after = array('id' => $insert_id, 'details' => array("repair_details"=>$repair_hardware_details));
				log_to_db("information_technology", $this->user->id_number, "MAINTENANCE-REPAIRS-HARDWARE", "rf_repair_hardware", "INSERT", $details_before, $details_after);

				redirect('/information_technology/maintenance/repair_hardware');
				return;
			}
		}
		
		$this->template->view('information_technology/maintenance/repair_hardware/add');
	}

	public function edit_repair_hardware($repair_hardware_id = 0)
	{
		$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($repair_hardware_id);


		if ($_POST and !empty($repair_hardware_details))
		{
			// post done here
			$this->form_validation->set_rules($this->_repair_hardware_validation_rule);

			if ($this->form_validation->run())
			{				
				// insert the new results
				$data = array(					
					'repair_hardware_name' => strtoupper(set_value('hardware_name')),
					'description' => strtoupper(set_value('description')),					
				);

				$this->information_technology_model->update_repair_hardware($data, array('repair_hardware_id' => $repair_hardware_id));
				
				/*//logging of action
				$details_before = array('id' => $repair_hardware_id, 'details' => array());
				$details_after = array('id' => $repair_hardware_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($repair_hardware_details->$k != $v)
					{
						$details_before['details'][$k] = $repair_hardware_details->$k;
						$details_after['details'][$k] = $v;
					}
				}

				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_result_log_data = array(
					'id_number' => $this->user->id_number,
					'module_name' => 'SPARE PARTS',
					'table_name' => 'rf_repair_hardware',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->information_technology_model->insert_logs('admin', $update_result_log_data);*/
				
				redirect('/information_technology/maintenance/repair_hardware');
				return;
			}
		}

		$this->template->repair_hardware_details = $repair_hardware_details;
		$this->template->view('information_technology/maintenance/repair_hardware/edit');
	}

	public function delete_repair_hardware($repair_hardware_id = 0)
	{
		$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($repair_hardware_id);

		if ($_POST and !empty($repair_hardware_details))
		{
			$_repair_hardware_id = $this->input->post('repair_hardware_id');
			if (!empty($_repair_hardware_id)) if ($_repair_hardware_id == $repair_hardware_id)
			{
				
				$data = array(
					"is_deleted" => 1
				);

				//$this->information_technology_model->update_repair_hardware($data, "repair_hardware_id = {$repair_hardware_id}");

				$this->information_technology_model->delete_repair_hardware(array('repair_hardware_id' => $repair_hardware_id));
				
				//logging of action
				$details_before = array('id' => $repair_hardware_id, 'details' => $repair_hardware_details);
				$details_before = json_encode($details_before);
				
				$delete_result_log_data = array(
					'id_number' => $this->user->id_number,
					'module_name' => 'SPARE PARTS',
					'table_name' => 'rf_repair_hardware',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->information_technology_model->insert_logs('admin', $delete_result_log_data);
				
				redirect('/information_technology/maintenance/repair_hardwares');
				return;
			}
		}

		$this->template->repair_hardware_details = $repair_hardware_details;
		$this->template->view('information_technology/maintenance/repair_hardwares/delete');

	}

	public function view_repair_hardware($repair_hardware_id = 0)
	{
		$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($repair_hardware_id);

		$this->template->repair_hardware_details = $repair_hardware_details;
		$this->template->view('information_technology/maintenance/repair_hardwares/view');

	}

}