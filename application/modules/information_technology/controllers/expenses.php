<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expenses extends Admin_Controller {

	private $_expenses_validation_rule = array(
		array(
			'field' => 'branch_dept_type',
			'label' => 'Branch or Department Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'particulars',
			'label' => 'Particulars',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'branch_name',
			'label' => 'Branch Name',
			'rules' => 'trim'
		),
		array(
			'field' => 'department_name',
			'label' => 'Department Name',
			'rules' => 'trim'
		),
		array(
			'field' => 'authority_number',
			'label' => 'Authority Number',
			'rules' => 'trim'
		),
		array(
			'field' => 'approval_number',
			'label' => 'Approval Number',
			'rules' => 'trim'
		),
		array(
			'field' => 'amount',
			'label' => 'Amount',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'expense_signatory_name',
			'label' => 'Signatory Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'requested_by',
			'label' => 'Requester Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'date_approved',
			'label' => 'Approval Date',
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

	public $segment_name = "expenses";

	public function index()
	{		
		$this->template->view('expenses/dashboard');
		//$this->expenses();
	}

	// expenseS
	// ------------------------------------------
	public function listing()
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
		    'pagination_url' => '/information_technology/expenses',
		    'total_items' => $this->information_technology_model->get_expense_view_count($where),
		    'per_page' => 5,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);
		$this->template->expenses = $this->information_technology_model->get_expense_view($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'date_approved');
		$this->template->view('expenses/listing');
	}

	public function add_expense()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_expenses_validation_rule);
			if ($this->form_validation->run())
			{				
				if (set_value('branch_dept_type') == "branch") {
					$branch_name = set_value('branch_name');
					$department_name = 0;
				} else {
					$branch_name = 0;
					$department_name = set_value('department_name');
				}

				// insert the new results
				$data = array(
					'particulars' => strtoupper(set_value('particulars')),	
					'branch_id' => $branch_name,
					'department_id' => $department_name,
					'amount' => set_value('amount'),
					'expense_signatory_id' => set_value('expense_signatory_name'),
					'authority_number' => strtoupper(set_value('authority_number')),
					'approval_number' => strtoupper(set_value('approval_number')),
					'date_approved' => set_value('date_approved'),
					'requested_by' => set_value('requested_by'),
					'created_by' => $this->user->id_number,					

				);

				$this->information_technology_model->insert_expense($data);
				
				//$insert_id = $this->information_technology_model->insert_id();
				
				//logging of action
				// TODO				

				
				redirect('/information_technology/expenses/listing');
				return;
			}
		}
		$this->template->view('information_technology/expenses/add');
	}

	public function edit_expense($expense_id = 0)
	{
		$expense_details = $this->information_technology_model->get_expense_by_id($expense_id);


		if ($_POST and !empty($expense_details))
		{
			// post done here
			$this->form_validation->set_rules($this->_expenses_validation_rule);

			if ($this->form_validation->run())
			{				
				if (set_value('branch_dept_type') == "branch") {
					$branch_name = set_value('branch_name');
					$department_name = 0;
				} else {
					$branch_name = 0;
					$department_name = set_value('department_name');
				}

				// insert the new results
				$data = array(
					'particulars' => strtoupper(set_value('particulars')),	
					'branch_id' => $branch_name,
					'department_id' => $department_name,
					'amount' => set_value('amount'),
					'expense_signatory_id' => set_value('expense_signatory_name'),
					'authority_number' => strtoupper(set_value('authority_number')),
					'approval_number' => strtoupper(set_value('approval_number')),
					'date_approved' => set_value('date_approved'),
					'requested_by' => set_value('requested_by'),
					'created_by' => $this->user->id_number,					

				);

				$this->information_technology_model->update_expense($data, array('expense_id' => $expense_id));
				
				//logging of action
				//TODO
				
				redirect('/information_technology/expenses/listing');
				return;
			}
		}

		$this->template->expense_details = $expense_details;
		$this->template->view('information_technology/expenses/edit');
	}

	public function delete_expense($expense_id = 0)
	{
		$expense_details = $this->information_technology_model->get_expense_view_by_id($expense_id);

		if ($_POST and !empty($expense_details))
		{
			$_expense_id = $this->input->post('expense_id');
			if (!empty($_expense_id)) if ($_expense_id == $expense_id)
			{
				
				$data = array(
					"is_deleted" => 1
				);

				$this->information_technology_model->delete_expense(array('expense_id' => $expense_id));
				
				//logging of action
				// TODO
				
				redirect('/information_technology/expenses/listing');
				return;
			}
		}

		$this->template->expense_details = $expense_details;
		$this->template->view('information_technology/expenses/delete');

	}
}