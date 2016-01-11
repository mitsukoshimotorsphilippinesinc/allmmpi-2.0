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

	public function listing()
	{

		$search_status = trim($this->input->get("search_status"));
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";

		if (($search_text == "") || (empty($search_text)) || ($search_text == NULL)) {
			//$where = NULL;	

			if (($search_status == 'ALL') || (strlen(trim($search_status)) == 0) || ($search_status == NULL)) {
				$where = NULL;
			} else {				
				$where = "overall_status = '{$search_status}'";
			}

		} else {

			$sub_where = "{$search_by} LIKE LOWER('%{$search_text}%')";

			$in_list = "";
				$i = 0;

			if ($search_by == "branch_name") {				
				$branch_details = $this->human_relations_model->get_branch($sub_where);
				
				$branch_count = count($branch_details);
				
				if ($i >= $branch_count) {
					$in_list .= "-1";
				} else {					
					while ($i < $branch_count) {
						$in_list .= $branch_details[$i]->branch_id . ", ";
						$i++;
					}
					$in_list .= "-1";
				
				}

				$where = "branch_id IN ({$in_list})";
			}

			if ($search_by == "department_name") {				
				$department_details = $this->human_relations_model->get_department($sub_where);
				
				$department_count = count($department_details);
				
				if ($i >= $department_count) {
					$in_list .= "-1";
				} else {					
					while ($i < $department_count) {
						$in_list .= $department_details[$i]->department_id . ", ";
						$i++;
					}
					$in_list .= "-1";
				
				}

				$where = "department_id IN ({$in_list})";
			}

			if ($search_by == "approved_by") {				
				$sub_where = "complete_name LIKE LOWER('%{$search_text}%')";

				$expense_signatory_details = $this->information_technology_model->get_expense_signatory($sub_where);
				
				$expense_signatory_count = count($expense_signatory_details);
				
				if ($i >= $expense_signatory_count) {
					$in_list .= "-1";
				} else {					
					while ($i < $expense_signatory_count) {
						$in_list .= $expense_signatory_details[$i]->expense_signatory_id . ", ";
						$i++;
					}
					$in_list .= "-1";
				
				}

				$where = "approved_by IN ({$in_list})";
			}

			if ($search_by == "requested_by") {				
				$sub_where = "complete_name LIKE LOWER('%{$search_text}%')";

				$requested_by_details = $this->human_relations_model->get_employment_information_view($sub_where);
				
				$requested_by_count = count($requested_by_details);
				
				if ($i >= $requested_by_count) {
					$in_list .= "-1";
				} else {					
					while ($i < $requested_by_count) {
						$in_list .= "'" . $requested_by_details[$i]->id_number . "', ";
						$i++;
					}
					$in_list .= "-1";
				
				}

				$where = "requested_by IN ({$in_list})";
			}

			if (($search_status == 'ALL') || (strlen(trim($search_status)) == 0) || ($search_status == NULL)) {
				$where .= "";
			} else {				
				$where .= " AND overall_status = '{$search_status}'";
			}

			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	

		// set pagination data
		$config = array(
				'pagination_url' => "/information_technology/expenses/listing/",				
				'total_items' => $this->information_technology_model->get_expense_summary_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$transfers = $this->information_technology_model->get_expense_summary($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			
		
		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;
		
		$this->template->view('expenses/listing');	
		
	}	

	public function edit($expense_summary_id = 0)
	{
		$this->add($expense_summary_id);
	}

	public function add($expense_summary_id = 0) 
	{

		$department_module_details = $this->information_technology_model->get_department_module_by_segment_name($this->segment_name);
				
		$expense_summary_details = $this->information_technology_model->get_expense_summary_by_id($expense_summary_id);

		$requester_details = "";
		$department_details = "";
		$position_details = "";

		if (!empty($expense_summary_details)) {

			if ($expense_summary_details->branch_id <> 0) {

			} else if (($expense_summary_details->id_number == NULL) || ($expense_summary_details->id_number == "")) {

				$requester_details = $this->human_relations_model->get_employment_information_view_by_id($expense_summary_details->id_number);
			
				$department_details = $this->human_relations_model->get_department_by_id($requester_details->department_id);
				$position_details = $this->human_relations_model->get_position_by_id($requester_details->position_id);			
			}	

			$this->template->requester_details = $requester_details;
			$this->template->department_details = $department_details;
			$this->template->position_details = $position_details;

			/*// get request items			
			$where = "expense_summary_id = " . $expense_summary_id;			
			$expense_summary_detail_details = $this->information_technology_model->get_expense_detail($where);

			$json_items = array();
			for($k = 0;$k<count($expense_summary_detail_details);$k++)
			{
				//$expense_summary_detail_id = $expense_summary_detail_details[$k]->expense_summary_detail_id;
				$expense_summary_detail_id = $expense_summary_detail_details[$k]->expense_detail_id;
				
				//$total_amount = $total_amount + ($item_qty[$k]*$item_price[$k]);
				$po_items = array(
						//'expense_summary_detail_id' => $expense_summary_detail_id,
						'request_detail_id' => $expense_summary_detail_id,
						'item_id' => $expense_summary_detail_details[$k]->item_id,
						'srp' => $expense_summary_detail_details[$k]->srp,
						'discount' => $expense_summary_detail_details[$k]->discount,
						'discount_amount' => $expense_summary_detail_details[$k]->discount_amount,
						'good_quantity' => $expense_summary_detail_details[$k]->good_quantity,
						'bad_quantity' => $expense_summary_detail_details[$k]->bad_quantity,
						'total_amount' => $expense_summary_detail_details[$k]->total_amount,
						'remarks' => $expense_summary_detail_details[$k]->remarks,

				);
				//creates an array of the items that will be json encoded later
				array_push($json_items, $po_items);

			}

			$this->template->json_items = json_encode($json_items);*/

		}


		/*$items = $this->spare_parts_model->get_item(null,null,"sku ASC");*/
		$items_array = array();
		
		//foreach($items as $i)
		//{
	//		$items_array[$i->item_id] = $i;
	//	}

		//$this->template->return_url = $return_url;
		$this->template->items = $items_array;
		$this->template->expense_summary_details = $expense_summary_details;
		$this->template->department_module_details = $department_module_details;		
		$this->template->view('expenses/add');
	}


	public function create_request()
	{
		
		$expense_code = trim($this->input->post("request_code"));
		$item_id = abs($this->input->post("item_id"));
		$quantity = abs($this->input->post("quantity"));
		$amount = $this->input->post("amount");
		$description = trim($this->input->post("description"));		
		$approved_by = abs($this->input->post("approved_by"));
		$requested_by = abs($this->input->post("requested_by"));		
		$approval_number = trim($this->input->post("approval_number"));
		$authority_number = trim($this->input->post("authority_number"));		
		$requester_type = trim($this->input->post("requester_type"));
		$requester_id = trim($this->input->post("requester_id"));
		$date_approved = trim($this->input->post("date_approved"));

		if ($expense_code == '00-00000')
		{
			$dateyear = date("Y");
			$expense_series = substr($dateyear, 2, 2);
			
			$current_datetime = date('Y-m-d H:i:s');						
			
			if ($requester_type == "employee") {

				$employee_details = $this->human_relations_model->get_employment_information_view_by_id($requester_id);

				$sql = "INSERT INTO 
							es_expense_summary 
							(
								`expense_series`, 
								`expense_number`, 
								`branch_id`, 
								`id_number`, 
								`department_id`, 
								`approved_by`, 
								`requested_by`, 
								`approval_number`,
								`authority_number`,
								`date_approved`,
								`created_by`
							)
		                	(
		                	SELECT 
		                		'{$expense_series}', 
		                		IFNULL(MAX(expense_number) + 1, 1) AS expense_number, 		                		
		                		'0',
		                		'{$requester_id}',
		                		'{$employee_details->department_id}',
		                        '{$approved_by}',
		                        '{$requested_by}',
		                        '{$approval_number}',
		                        '{$authority_number}',
		                        '{$date_approved}',
		                        '{$this->user->user_id}'
		                	FROM 
		                		es_expense_summary
		                	WHERE 
		                		expense_series = '{$expense_series}'                 	
		                    ORDER BY 
		                    	expense_number DESC
		                	)";
			} else {
				$sql = "INSERT INTO 
						es_expense_summary 
						(
							`expense_series`, 
							`expense_number`, 
							`branch_id`, 
							`id_number`, 
							`department_id`, 
							`approved_by`, 
							`requested_by`, 
							`approval_number`,
							`authority_number`,
							`date_approved`,
							`created_by`
						)
	                	(
	                	SELECT 
	                		'{$expense_series}', 
	                		IFNULL(MAX(expense_number) + 1, 1) AS expense_number, 	                		
	                		'{$requester_id}',
	                		NULL,
	                		'0',
	                        '{$approved_by}',
	                        '{$requested_by}',
	                        '{$approval_number}',
	                        '{$authority_number}',
	                        '{$date_approved}',
	                        '{$this->user->user_id}'
	                	FROM 
	                		es_expense_summary
	                	WHERE 
	                		expense_series = '{$expense_series}'                 	
	                    ORDER BY 
	                    	expense_number DESC
	                	)";
			}

			$this->db_information_technology->query($sql);	


			$sql = "SELECT 
						LAST_INSERT_ID() AS last_id 
					FROM 
						es_expense_summary";

			$query = $this->db_information_technology->query($sql);
			$expense_summary = $query->first_row();

			$active_expense_summary_id = $expense_summary->last_id;

	        $sql = "SELECT 
						CONCAT('{$expense_series}', '-', LPAD(expense_number, 5, 0)) AS gen_code
					FROM
						es_expense_summary                    
	                WHERE
	                	expense_summary_id = " . $active_expense_summary_id;        	

	        $query = $this->db_information_technology->query($sql);
			$expense_code_details = $query->first_row();  

			$expense_code = $expense_code_details->gen_code; 		

			// update expense_code
			$data_update = array(
					'expense_code' => $expense_code
				);

			$where_update = "expense_summary_id = " . $active_expense_summary_id;
		
			$this->information_technology_model->update_expense_summary($data_update, $where_update);

		} else {	
			$active_expense_summary_details = $this->information_technology_model->get_expense_summary_by_code($expense_code);
			$active_expense_summary_id = $active_expense_summary_details->expense_summary_id;
		}	

		// add item to details table
		$data_insert = array(				
				'expense_summary_id' => $active_expense_summary_id,
				'repair_hardware_id' => $item_id,				
				'description' => $description,				
				'quantity' => $quantity + 1,
				'amount' => $amount,
			);
	
		$this->information_technology_model->insert_expense_detail($data_insert);	

		$insert_id = $this->information_technology_model->insert_id();


		// get all details
		$where = "expense_summary_id = '{$active_expense_summary_id}'";
		$details = $this->information_technology_model->get_expense_detail($where);
		
		$data = array(
			"details" => $details
		);

		$html_items = $this->load->view("expenses/item_details_view", $data, true);

		
		$html = "<p>Item has been added successfully!</p>";
		$title = "Add Item :: Item Request";

		$this->return_json("1", "Item Successfully Added", array("html" => $html, "title" => $title, "html_items" => $html_items, "expense_code" => $expense_code));
		return;
	}

	public function get_item_details_view()
	{

		$expense_summary_id = $this->input->post("expense_summary_id");

		// get all details
		$where = "expense_summary_id = '{$expense_summary_id}'";
		$details = $this->information_technology_model->get_expense_detail($where);
		
		$data = array(
			"details" => $details
		);

		//var_dump($details);

		$html_items = $this->load->view("expenses/item_details_view", $data, true);

		$this->return_json("1", "Items", array("html_items" => $html_items));
		return;
	}




	public function delete($expense_summary_id = 0)
	{
		/*$expense_details = $this->information_technology_model->get_expense_view_by_id($expense_id);

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
*/
	}
}