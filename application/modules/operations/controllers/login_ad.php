<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_ad extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db_operations = $this->load->database('operations', TRUE);	
		$this->load->helper('breadcrumb_helper');
		$this->load->library('pager');
		$this->load->model('asset_model');
	}

	private $_validation_rule = array(
		array(
			'field' => 'ad_name',
			'label' => 'Ad Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim'
		),
		array(
			'field' => 'priority_id',
			'label' => 'Priority Number',
			'rules' => 'trim'
		),
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim'
		)
		
	);

	public function index() 
	{
		$this->listing();
	}
	
	public function listing()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = null;
		} else {
			if($search_by == "ad_name")
			{
				$where = "`ad_name` LIKE LOWER('%{$search_text}%')";
			}
			elseif($search_by == "priority_id")
			{
				$where = "{$search_by} = $search_text";
			}
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}


		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/oprations/login_ad/index/",
		    'total_items' => $this->asset_model->get_employee_login_ad_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		//$this->template->type = $type;

		//$this->template->members = $this->members_model->get_members($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->member_login_ads = $this->asset_model->get_employee_login_ad($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "priority_id, ad_name ASC");
		$this->template->view('login_ad/dashboard');
	}

	public function view_ad($employee_login_ad_id = 0)
	{	
		$featured_employee_login_ad = $this->asset_model->get_employee_login_ad_by_id($employee_login_ad_id);
		
		$this->template->featured_employee_login_ad = $featured_employee_login_ad;
		$this->template->view('login_ad/view_ad');
	}


	public function add_featured_modal()
	{
		
		$html = $this->load->view('operations/login_ad/add_modal',"",TRUE);
			
		echo json_encode(array("status"=>1,"html"=>$html));
	}
	
	public function edit($employee_login_ad_id = 0)
	{
		$featured_employee_login_ad = $this->asset_model->get_employee_login_ad_by_id($employee_login_ad_id);
		
		if ($_POST and !empty($featured_employee_login_ad))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{			
				
				// update member
				$data = array(
					'ad_name' => set_value('ad_name'),
					'description' => set_value('description'),
					'is_active' => set_value('is_active'),
					'priority_id' => set_value('priority_id')
				);

				//var_dump($data);				
				$this->asset_model->update_employee_login_ad($data, array('employee_login_ad_id' => $employee_login_ad_id));

				redirect('/operations/login_ad');
				return;
			}
		}
		
		$this->template->featured_employee_login_ad = $featured_employee_login_ad;
		$this->template->view('login_ad/edit');
	}
	
	public function delete($employee_login_ad_id = 0)
	{
		$employee_login_ad = $this->asset_model->get_employee_login_ad_by_id($employee_login_ad_id);
		
		if ($_POST and !empty($employee_login_ad))
		{
			$_employee_login_ad_id = $this->input->post('employee_login_ad_id');
			if (!empty($_employee_login_ad_id)) if ($_employee_login_ad_id == $employee_login_ad_id)
			{
				//$this->asset_model->delete_employee_login_ad(array('employee_login_ad_id' => $employee_login_ad_id));
				
				//logging of swf_actiongeturl(url, target)
				$details_before = array('id' => $employee_login_ad_id, 'details' => $employee_login_ad);
				$details_before = json_encode($details_before);
				
				/*$delete_member_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'EMPLOYEE LOGIN AD',
					'table_name' => 'sm_members_login_ads',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);*/

				//$this->tracking_model->insert_logs('admin', $delete_member_log_data);
				
				redirect('/operations/login_ad');
				return;
			}
		}
		
		$this->template->featured_employee_login_ad = $employee_login_ad;
		$this->template->view('login_ad/delete');
		
	}

	public function add_ad()
	{
		if($_POST)
		{
			$ad_name = $this->input->post('ad_name');
			$description = $this->input->post('description');
			$priority_id = $this->input->post('priority_id');			
			
			$data = array(
				'ad_name' => $ad_name,
				'description' => $description,
				'priority_id' => $priority_id,				
				'user_id' => $this->user->user_id
			);
			
			$this->asset_model->insert_employee_login_ad($data);
			
			$insert_id = $this->asset_model->insert_id();
			
			/*//logging of action
			$details_after = array('id' => $insert_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$add_member_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'MEMBERS LOGIN ADS',
				'table_name' => 'sm_members_login_ads',
				'action' => 'ADD',
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $add_member_log_data);
			*/
			
			$this->return_json("ok", 'Add New Employee Login Ad', array("id"=>$insert_id)); 
			return;
		}
	}

	public function update_image()
	{
		$filename = $this->input->post('filename');
		$employee_login_ad_id = $this->input->post('employee_login_ad_id');
		
		$data = array("image_filename"=>$filename);
		$where = array(
			'employee_login_ad_id' => $employee_login_ad_id,
		);
		
		$this->asset_model->update_employee_login_ad($data, $where);
		
		$this->return_json('ok','');
	}
	

}