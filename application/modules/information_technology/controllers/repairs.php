<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Repairs extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
	
		$this->load->model('information_technology_model');
		$this->load->model('human_relations_model');		
		$this->load->library('pager');				
		$this->load->helper("breadcrumb_helper");
		$this->load->helper("systems_helper");

		$this->db_information_technology = $this->load->database('information_technology', TRUE);

	}

	public $segment_name = "repairs";

	public function index()
	{		
		$this->template->view('repairs/dashboard');
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

				if (($search_status == 'ALL') || (strlen(trim($search_status)) == 0) || ($search_status == NULL)) {
					$where .= "";
				} else {				
					$where .= " AND overall_status = '{$search_status}'";
				}
				
			}

			if ($search_by == "complete_name") {				
				$employee_details = $this->human_relations_model->get_employment_information_view($sub_where);
				
				$employee_count = count($employee_details);
				
				if ($i >= $employee_count) {
					$in_list .= "-1";
				} else {					
					while ($i < $employee_count) {
						$in_list .= $employee_details[$i]->id_number . ", ";
						$i++;
					}
					$in_list .= "-1";
				
				}

				$where = "id_number IN ({$in_list})";

				if (($search_status == 'ALL') || (strlen(trim($search_status)) == 0) || ($search_status == NULL)) {
					$where .= "";
				} else {				
					$where .= " AND overall_status = '{$search_status}'";
				}
				
			}

			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	

		//var_dump($search_status . '|' . $where);

		// set pagination data
		$config = array(
				'pagination_url' => "/information_technology/repairs/listing/",				
				'total_items' => $this->information_technology_model->get_repair_summary_count($where),
				'per_page' => 30,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$transfers = $this->information_technology_model->get_repair_summary($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "overall_status DESC, insert_timestamp ASC");			
		
		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;
		
		$this->template->view('repairs/listing');	
		
	}	

	public function edit($repair_summary_id = 0)
	{
		$this->add($repair_summary_id);
	}

	public function add($repair_summary_id = 0) 
	{

		$department_module_details = $this->information_technology_model->get_department_module_by_segment_name($this->segment_name);
				
		$repair_summary_details = $this->information_technology_model->get_repair_summary_by_id($repair_summary_id);

		$requester_details = "";
		$department_details = "";
		$position_details = "";

		if (!empty($repair_summary_details)) {

			if ($repair_summary_details->branch_id <> 0) {

			} else if (($repair_summary_details->id_number == NULL) || ($repair_summary_details->id_number == "")) {

				$requester_details = $this->human_relations_model->get_employment_information_view_by_id($repair_summary_details->id_number);
			
				$department_details = $this->human_relations_model->get_department_by_id($requester_details->department_id);
				$position_details = $this->human_relations_model->get_position_by_id($requester_details->position_id);			
			}	

			$this->template->requester_details = $requester_details;
			$this->template->department_details = $department_details;
			$this->template->position_details = $position_details;

			/*// get request items			
			$where = "status NOT IN ('CANCELLED', 'DELETED') AND request_summary_id = " . $repair_summary_id;			
			$repair_summary_detail_details = $this->spare_parts_model->get_request_detail($where);

			$json_items = array();
			for($k = 0;$k<count($repair_summary_detail_details);$k++)
			{
				//$repair_summary_detail_id = $repair_summary_detail_details[$k]->repair_summary_detail_id;
				$repair_summary_detail_id = $repair_summary_detail_details[$k]->request_detail_id;
				
				//$total_amount = $total_amount + ($item_qty[$k]*$item_price[$k]);
				$po_items = array(
						//'repair_summary_detail_id' => $repair_summary_detail_id,
						'request_detail_id' => $repair_summary_detail_id,
						'item_id' => $repair_summary_detail_details[$k]->item_id,
						'srp' => $repair_summary_detail_details[$k]->srp,
						'discount' => $repair_summary_detail_details[$k]->discount,
						'discount_amount' => $repair_summary_detail_details[$k]->discount_amount,
						'good_quantity' => $repair_summary_detail_details[$k]->good_quantity,
						'bad_quantity' => $repair_summary_detail_details[$k]->bad_quantity,
						'total_amount' => $repair_summary_detail_details[$k]->total_amount,
						'remarks' => $repair_summary_detail_details[$k]->remarks,

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
		//	$items_array[$i->item_id] = $i;
		//}

		//$this->template->return_url = $return_url;
		$this->template->items = $items_array;
		$this->template->repair_summary_details = $repair_summary_details;
		$this->template->department_module_details = $department_module_details;		
		$this->template->view('repairs/add');
	}

	public function create_request()
	{
		
		$repair_code = trim($this->input->post("request_code"));
		$item_id = abs($this->input->post("item_id"));
		$quantity = abs($this->input->post("quantity"));
		$description = trim($this->input->post("description"));
		$peripherals = trim($this->input->post("peripherals"));
		$requester_id = trim($this->input->post("requester_id"));
		$repair_status = abs($this->input->post("repair_status"));
		$reported_concern = trim($this->input->post("reported_concern"));
		$tr_number_in = trim($this->input->post("tr_number_in"));
		$requester_type = trim($this->input->post("requester_type"));
		$date_received = trim($this->input->post("date_received"));

		if ($repair_code == '00-00000')
		{
			$dateyear = date("Y");
			$repair_series = substr($dateyear, 2, 2);
			
			$current_datetime = date('Y-m-d H:i:s');						
			
			if ($requester_type == "employee") {
				$sql = "INSERT INTO 
							rs_repair_summary 
							(
								`repair_series`, 
								`repair_number`, 
								`branch_id`, 
								`id_number`, 
								`received_by`, 
								`tr_number_in`, 
								`reported_concern`, 
								`date_received`
							)
		                	(
		                	SELECT 
		                		'{$repair_series}', 
		                		IFNULL(MAX(repair_number) + 1, 1) AS repair_number, 
		                		0,
		                		'{$requester_id}',
		                		'{$this->user->id_number}',
		                        '{$tr_number_in}',
		                        '{$reported_concern}',
		                        '{$date_received}'
		                	FROM 
		                		rs_repair_summary
		                	WHERE 
		                		repair_series = '{$repair_series}'                 	
		                    ORDER BY 
		                    	repair_number DESC
		                	)";
			} else {
				$sql = "INSERT INTO 
						rs_repair_summary 
						(
							`repair_series`, 
							`repair_number`, 
							`branch_id`, 
							`id_number`, 
							`received_by`, 
							`tr_number_in`, 
							`reported_concern`, 
							`date_received`
						)
	                	(
	                	SELECT 
	                		'{$repair_series}', 
	                		IFNULL(MAX(repair_number) + 1, 1) AS repair_number, 	                		
	                		'{$requester_id}',
	                		NULL,
	                		'{$this->user->id_number}',
	                        '{$tr_number_in}',
	                        '{$reported_concern}',
	                        '{$date_received}'
	                	FROM 
	                		rs_repair_summary
	                	WHERE 
	                		repair_series = '{$repair_series}'                 	
	                    ORDER BY 
	                    	repair_number DESC
	                	)";
			}

			$this->db_information_technology->query($sql);	


			$sql = "SELECT 
						LAST_INSERT_ID() AS last_id 
					FROM 
						rs_repair_summary";

			$query = $this->db_information_technology->query($sql);
			$repair_summary = $query->first_row();

			$active_repair_summary_id = $repair_summary->last_id;

	        $sql = "SELECT 
						CONCAT('{$repair_series}', '-', LPAD(repair_number, 5, 0)) AS gen_code
					FROM
						rs_repair_summary                    
	                WHERE
	                	repair_summary_id = " . $active_repair_summary_id;        	

	        $query = $this->db_information_technology->query($sql);
			$repair_code_details = $query->first_row();  

			$repair_code = $repair_code_details->gen_code; 		

			// update repair_code
			$data_update = array(
					'repair_code' => $repair_code
				);

			$where_update = "repair_summary_id = " . $active_repair_summary_id;
		
			$this->information_technology_model->update_repair_summary($data_update, $where_update);

		} else {	
			$active_repair_summary_details = $this->information_technology_model->get_repair_summary_by_code($repair_code);
			$active_repair_summary_id = $active_repair_summary_details->repair_summary_id;
		}	

		// add item to details table
		$data_insert = array(				
				'repair_summary_id' => $active_repair_summary_id,
				'repair_hardware_id' => $item_id,				
				'description' => $description,
				'peripherals' => $peripherals,
				'quantity' => $quantity + 1,
			);
	
		$this->information_technology_model->insert_repair_detail($data_insert);	

		$insert_id = $this->information_technology_model->insert_id();

		// insert initial remark
		 $data_remark = array(
				'repair_detail_id' => $insert_id,
				'repair_status_id' => $repair_status,				
				'remarks' => $reported_concern,
				'created_by' => $this->user->id_number,
			);	

		$this->information_technology_model->insert_repair_remark($data_remark);	

		// get all details
		$where = "repair_summary_id = '{$active_repair_summary_id}'";
		$details = $this->information_technology_model->get_repair_detail($where);
		
		$data = array(
			"details" => $details
		);

		$html_items = $this->load->view("repairs/item_details_view", $data, true);

		
		$html = "<p>Item has been added successfully!</p>";
		$title = "Add Item :: Item Request";

		$this->return_json("1", "Item Successfully Added", array("html" => $html, "title" => $title, "html_items" => $html_items, "repair_code" => $repair_code));
		return;
	}

	public function update($repair_summary_id = 0)
	{
		
		$repair_summary_details = $this->information_technology_model->get_repair_summary_by_id($repair_summary_id);	

		$where = "repair_summary_id = {$repair_summary_id}";
		$repair_details = $this->information_technology_model->get_repair_detail($where);

		$html = "";

		if (empty($repair_details)) {
			var_dump("EMPTY");
		} else {
			
			foreach ($repair_details as $rd) {
				$data = array(
					'repair_detail' => $rd
					);
			
				$html .= $this->load->view("repairs/update_template_view", $data, true);
			}

		}

		$this->template->repair_details = $repair_details;
		
		$this->template->repair_summary_details = $repair_summary_details;
		$this->template->html = $html;
		$this->template->view('repairs/update');
	}


	public function add_remark()
	{
		$repair_status_id = $this->input->post("repair_status_id");
		$remarks = $this->input->post("remarks");
		$repair_detail_id = $this->input->post("repair_detail_id");
		$tr_number_in = $this->input->post("tr_number_in");
		$tr_number_out = $this->input->post("tr_number_out");
		$po_price = $this->input->post("po_price");
		$approval_number = $this->input->post("approval_number");
		$authority_number = $this->input->post("authority_number");
		$proposed_price = $this->input->post("proposed_price");
		$is_branch_expense = $this->input->post("is_branch_expense");

		if ($repair_status_id == 2) {
			// for delivery
			$remarks = $remarks . " <strong>#TR_NUMBER_IN: " . $tr_number_in . "</strong>";
		} else if ($repair_status_id == 10) {
			// for delivery
			$remarks = $remarks . " <strong>#TR_NUMBER_OUT: " . $tr_number_out . "</strong>";
		} else if ($repair_status_id == 7) {
			// for delivery
			$remarks = $remarks . " <br/><strong>
										#APPROVED_AMOUNT: " . $po_price . "<br/>
										#APPROVAL_NUMBER: " . $approval_number . "<br/>
										#AUTHORITY_NUMBER: " . $authority_number . "
									</strong>";
		} else if ($repair_status_id == 6) {
			// for delivery
			$remarks = $remarks . " <strong>#PROPOSED_AMOUNT: " . $proposed_price . "</strong>";
		} 

		$data = array(
				"repair_detail_id" => $repair_detail_id,
				"remarks" => $remarks,
				"repair_status_id" => $repair_status_id,
				"created_by" => $this->user->id_number,			
				"proposed_amount" => $proposed_price,
				"approved_amount" => $po_price,
				"is_branch_expense" => $is_branch_expense,
			);

		$this->information_technology_model->insert_repair_remark($data);

		$where ="repair_detail_id = {$repair_detail_id}";
		$repair_details = $this->information_technology_model->get_repair_detail($where);

		$data = array(
			'repair_detail' => $repair_details[0]	
			);
	
		$remarks_html = $this->load->view("repairs/update_template_view", $data, true);

		// update current_status
		$data = array(
				"current_status_id" => $repair_status_id,
				"tr_number_out" => $tr_number_out,
			);	

		$where = "repair_detail_id = '{$repair_detail_id}'";

		$this->information_technology_model->update_repair_detail($data, $where);

		$repair_detail_details = $this->information_technology_model->get_repair_detail_by_id($repair_detail_id);

		$where = "repair_summary_id = {$repair_detail_details->repair_summary_id}";
		$total_detail_count = $this->information_technology_model->get_repair_detail_count($where);

		// completed
		$where = "repair_summary_id = {$repair_detail_details->repair_summary_id} AND current_status_id = 9";
		$total_completed_count = $this->information_technology_model->get_repair_detail_count($where);

		// received from h.o.
		$where = "repair_summary_id = {$repair_detail_details->repair_summary_id} AND current_status_id = 14";
		$total_closed_count = $this->information_technology_model->get_repair_detail_count($where);

		$overall_status =  "OPEN";
		$update_status = 0;

		if ($total_detail_count == $total_completed_count) {
			// COMPLETED
			$overall_status =  'COMPLETED';
			$update_status = 1;
			
		} else if ($total_detail_count == $total_closed_count) {

			// CLOSED
			$overall_status =  'CLOSED';
			$update_status = 1;
		}

		if ($update_status == 1) {
			$data = array(
					"overall_status" => $overall_status
				);

			$where = "repair_summary_id = {$repair_detail_details->repair_summary_id}";
			$this->information_technology_model->update_repair_summary($data, $where);
		}

		$repair_summary = $this->information_technology_model->get_repair_summary_by_id($repair_detail_details->repair_summary_id);

		// EXPENSES SYSTEM - ON GOING DEV
		// ==============================
		if ($repair_status_id == 7) {

			// check first if repair_summary_id exists
			$expense_summary_details = $this->information_technology_model->get_expense_summary("repair_summary_id = " . $repair_details->repair_summary_id);

			if (empty($expense_summary_details)) {

				// create new entry
				$dateyear = date("Y");
				$expense_series = substr($dateyear, 2, 2);
				
				$current_datetime = date('Y-m-d H:i:s');						
			
				// check if branch or employee
				if ($repair_summmary->branch_id > 0) {
					// BRANCH
					
					$sql = "INSERT INTO 
								es_expense_summary 
								(
									`expense_series`, 
									`expense_number`, 
									`branch_id`, 									
									`approved_by`, 
									`authority_number`, 
									`approval_number`, 
									`requested_by`,
									`created_by`,
									`date_approved`,
									`repair_summary_id`,
								)
			                	(
			                	SELECT 
			                		'{$expense_series}', 
			                		IFNULL(MAX(expense_number) + 1, 1) AS expense_number, 
			                		'{$repair_summmary->branch_id}',
			                		approved_by???,	
			                		'{$authority_number}',	
			                		'{approval_number}',
			                		'{$this->user->id_number}',
			                		'{$this->user->id_number}',			                        			                        
			                        date_approved???,
			                        '{$repair_summmary->branch_id}'
			                	FROM 
			                		rs_expense_summary
			                	WHERE 
			                		expense_series = '{$expense_series}'                 	
			                    ORDER BY 
			                    	expense_number DESC
			                	)";

				} else {
					// EMPLOYEE
					$sql = "INSERT INTO 
								es_expense_summary 
								(
									`expense_series`, 
									`expense_number`, 
									`branch_id`, 									
									`approved_by`, 
									`authority_number`, 
									`approval_number`, 
									`requested_by`,
									`created_by`,
									`date_approved`,
									`repair_summary_id`,
								)
			                	(
			                	SELECT 
			                		'{$expense_series}', 
			                		IFNULL(MAX(expense_number) + 1, 1) AS expense_number, 
			                		'{$repair_summmary->branch_id}',
			                		approved_by???,	
			                		'{$authority_number}',	
			                		'{approval_number}',
			                		'{$this->user->id_number}',
			                		'{$this->user->id_number}',			                        			                        
			                        date_approved???,
			                        '{$repair_summmary->branch_id}'
			                	FROM 
			                		rs_expense_summary
			                	WHERE 
			                		expense_series = '{$expense_series}'                 	
			                    ORDER BY 
			                    	expense_number DESC
			                	)";

										
				}

			}
		}	
		// ==============================



		$html = "<p>Remarks posted successfully!</p>";
		$title = "Add Remarks :: Repairs";

		$this->return_json("1", "Remarks posted sucessfully", array("html" => $html, "title" => $title, "remarks_html" => $remarks_html, "overall_status" => $repair_summary->overall_status));
		return;

	}

	public function remove_item()
	{
		$repair_detail_id = $this->input->post("repair_detail_id");

		$repair_detail_details = $this->information_technology_model->get_repair_detail_by_id($repair_detail_id);

		//logging of action
		$details_before = array('id' => $repair_detail_id, 'details' => array("repair_details"=>$repair_detail_details));
		$details_after = array('id' => $repair_detail_id, 'details' => array());		
		log_to_db("information_technology", $this->user->id_number, "REPAIRS-DEALERS", "rs_repair_detail", "DELETE", $details_before, $details_after);

		$this->information_technology_model->delete_repair_detail($repair_detail_id);		

		$html_items = $this->load->view("repairs/item_details_view", $data, true);

		$html = "<p>Item has been removed successfully!</p>";
		$title = "Remove Item :: Item Request";

		$this->return_json("1", "Item Successfully Removed", array("html" => $html, "title" => $title, "html_items" => $html_items));
		return;
	}

	public function get_item_details_view()
	{

		$repair_summary_id = $this->input->post("repair_summary_id");

		// get all details
		$where = "repair_summary_id = '{$repair_summary_id}'";
		$details = $this->information_technology_model->get_repair_detail($where);
		
		$data = array(
			"details" => $details
		);

		$html_items = $this->load->view("repairs/item_details_view", $data, true);

		$this->return_json("1", "Items", array("html_items" => $html_items));
		return;
	}

	public function reports()
	{
		$this->generate_report();
	}	

	public function generate_report()
	{
		
		$from_date = trim($this->input->get_post('from_date'));
	    $to_date = trim($this->input->get_post('to_date'));
		$filter = trim($this->input->get_post('filter'));
		$branch_id = trim($this->input->get_post('branch_id'));
		
	    $export = $this->input->get_post('export');
	    $where = "";
		$where_conjuction = "";
	    if (empty($from_date)) $from_date = date('Y-m-d');
	    if (empty($to_date)) $to_date = date('Y-m-d');
    
	    //date set
	    $from_dt = $from_date;
	    $to_dt = $to_date;
	    $from_single = "";
	    $from_t = strtotime($from_date);
	    $to_t = strtotime($to_date);
	    if ($from_t !== false) $from_dt = date('Y-m-d', $from_t); 
	    if ($to_t !== false) $to_dt = date('Y-m-d', $to_t); 
       
	    if ($from_t !== false && $to_t !== false)
	      $where .= "WHERE (DATE(c.insert_timestamp) BETWEEN '{$from_dt}' AND '{$to_dt}') ";
	    else if ($from_t !== false && $to_t === false)
	      $where .= "WHERE c.insert_timestamp >= '{$from_dt}'";
	    else if ($from_t === false && $to_t !== false)
	      $where .= "WHERE c.insert_timestamp <= '{$to_dt}'";
	
			
	  	if ($branch_id == -1) {
	  		$where .= " AND branch_id = 0";
	  	} else if ($branch_id > 0){
	  		$where .= " AND branch_id = '{$branch_id}'";
	  	}


	    $data = new ArrayClass(array(
	      'from_date' => $from_date,
	      'to_date' => $to_date,
	      'where' => $where,
	    ));
    
	    // check if to export
	    if ($export == 'excel')
	    {
	      $this->_export($data);
	    }
	    else
	    {
			$display_data = "";
			if($filter == 'yes') {
	      		$display_data = $this->repair_result_view($data);														
			}

			$this->template->search_url = strlen($_SERVER['QUERY_STRING']) > 0 ? '?'.$_SERVER['QUERY_STRING'] : '';
	      	$this->template->from_date = $from_date;
	      	$this->template->to_date = $to_date;
	      	$this->template->display_data = $display_data;			
	      	$this->template->view('repairs/reports/dashboard');
	    }
	}

	public function repair_result_view($data)
	  {
	    $from_date = slugify($data->from_date);
	    $to_date = slugify($data->to_date);
	    $where = $data->where;
	    $current_date = date('Y-m-d');    

	    $sql = "SELECT 
					a.repair_code,
					a.branch_id,
					a.id_number,
					a.received_by,
					a.overall_status,
					a.date_received,
					b.repair_hardware_id,
					b.quantity,
					b.description,
					b.peripherals,
					a.tr_number_in,
					b.tr_number_out,
					c.repair_status_id,
					c.proposed_amount,
					c.approved_amount,
					c.is_branch_expense,
					c.remarks,
					c.created_by,
					c.insert_timestamp,
					a.repair_summary_id
				FROM 
					rs_repair_summary a
				LEFT JOIN 
					rs_repair_detail b on a.repair_summary_id = b.repair_summary_id
				LEFT JOIN 
					rs_repair_remark c on c.repair_detail_id = b.repair_detail_id
				{$where}
				GROUP BY
					a.repair_code	
				ORDER BY 
					a.repair_code, b.repair_hardware_id, c.insert_timestamp";

		$query = $this->db_information_technology->query($sql);
		$report_result = $query->result();		


		$total_records = count($report_result);

		$config = array(
			'pagination_url' => '/information_technology/repairs/generate_report',
			'total_items' => $total_records,
			'per_page' => 30,
			'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		
		//$html = "";
		$html = "<table class='table table-bordered table-condensed'>
			<thead>
				<th>Repair Code</th>
				<th>Overall Status</th>
				<th>Requested By</th>
				<th>Items / Person In-Charge</th>
				<th>Received By</th>	
				<th>TR Number (IN)</th>
				<th>Timestamp - Current State</th>
			</thead>
			<tbody>";

		if(empty($report_result))
		{
			$html .= "<tr><td colspan='7' style='text-align:center'>No Record found.</td></tr>";
		}
		
		foreach($report_result as $t)
		{
			
			$html .= "<tr>
					<td>{$t->repair_code}</td>";
						
			$status_class = strtolower(trim($t->overall_status));			

			$status_class = str_replace(" ", "-", $status_class);
		
			$html .= "<td><span class='label label-" . $status_class . "' >{$t->overall_status}</span></td>";

			if ($t->branch_id <> 0) {
				$requestor_details = $this->human_relations_model->get_branch_by_id($t->branch_id);

				if (count($requestor_details) == 0) {
					$html .= "<td>N/A</td>";
				} else { 
					$html .= "<td>{$requestor_details->branch_name}</td>"; 
				}			

			} else {

				// get requestor details
				$id = str_pad($t->id_number, 7, '0', STR_PAD_LEFT);
				$requestor_details = $this->human_relations_model->get_employment_information_view_by_id($id);

				if (count($requestor_details) == 0) {
					$html .= "<td>N/A</td>";
				} else { 
					$html .= "<td>{$requestor_details->complete_name}</td>"; 
				}			
			}

			// get number of items
			$where = "repair_summary_id = " . $t->repair_summary_id . "";
			$repairs_detail_info = $this->information_technology_model->get_repair_detail($where);
	
			$where = "repair_summary_id = " . $t->repair_summary_id;
			$repairs_details = $this->information_technology_model->get_repair_detail($where);

			$repair_detail_details = $this->information_technology_model->get_repair_detail("repair_summary_id = " . $t->repair_summary_id);				

			$items_html = "<table style='margin-bottom:0px;' class='table table-condensed table-bordered'>
								<thead>
								</thead>
								<tbody>";

			foreach ($repair_detail_details as $rdd) {
				$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($rdd->repair_hardware_id);

				// get person in charge
				$repair_hardware_remark_details = $this->information_technology_model->get_repair_remark("repair_detail_id = '{$rdd->repair_detail_id}'", NULL, "insert_timestamp DESC");

				if (empty($repair_hardware_remark_details)) {
					$repair_summary_details = $this->information_technology_model->get_repair_summary_by_id($rdd->repair_summary_id);

					$pic_details = $this->human_relations_model->get_employment_information_view_by_id($repair_summary_details->received_by);
				} else {
					$pic_details = $this->human_relations_model->get_employment_information_view_by_id($repair_hardware_remark_details[0]->created_by);
				}


				$items_html .= "<tr>
									<td style='width:110px;'>{$repair_hardware_details->repair_hardware_name} x {$rdd->quantity}</td>
									<td>{$pic_details->complete_name}</td>
								</tr>";
										
			}

			$items_html .= "</tbody>
						</table>";

			$html .= "<td>{$items_html}</td>";

			$id = str_pad($t->received_by, 7, '0', STR_PAD_LEFT);
			$received_by_details = $this->human_relations_model->get_employment_information_view_by_id($id);

			if (count($received_by_details) == 0) {
				$html .= "<td>N/A</td>";
			} else { 
				$html .= "<td>{$received_by_details->complete_name}</td>"; 
			}			

			$html .= "<td>{$t->tr_number_in}</td>	
				  	  <td>{$t->insert_timestamp}</td>
				  	</tr>";

		}

	    $html .= "</tbody></table>";
	    $html .= $this->pager->create_links(strlen($_SERVER['QUERY_STRING']) > 0 ? '?'.$_SERVER['QUERY_STRING'] : '');
        
	    return $html; 
	  }


	 public function _export($data)
	  {
		$from_date = slugify($data->from_date);
	    $to_date = slugify($data->to_date);
	    $where = $data->where;
	    $current_date = date('Y-m-d');
    
	    $current_year = date('Y');
	    $current_month = date('m');
	    $current_day = date('d');
	    $date = $current_month . '-' . $current_day . '-' . $current_year;
	    
	    $this->load->library('PHPExcel');
	    $this->load->library('PHPExcel/IOFactory');
	    $objPHPExcel = new PHPExcel();
	    
	     $sql = "SELECT 
					a.repair_code,
					a.branch_id,
					a.id_number,
					a.received_by,
					a.overall_status,
					a.date_received,
					b.repair_detail_id,
					b.repair_hardware_id,
					b.quantity,
					b.description,
					b.peripherals,
					a.tr_number_in,
					b.tr_number_out,
					c.repair_status_id,
					c.proposed_amount,
					c.approved_amount,
					c.is_branch_expense,
					c.remarks,
					c.created_by,
					c.insert_timestamp
				FROM 
					rs_repair_summary a
				LEFT JOIN 
					rs_repair_detail b on a.repair_summary_id = b.repair_summary_id
				LEFT JOIN 
					rs_repair_remark c on c.repair_detail_id = b.repair_detail_id
				{$where}	
				ORDER BY 
					a.repair_code, b.repair_hardware_id, c.insert_timestamp";

		$query = $this->db_information_technology->query($sql);
		$report_result = $query->result();		

	    if (!empty($report_result))
	    {
	      	$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
	      	$start_column_num = 5;
      	
	      	$objPHPExcel->setActiveSheetIndex(0);
	      	$objPHPExcel->getActiveSheet()->setTitle("I.T. REPAIRS");
      
	      	// auto resize columns
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);			
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);			
      
	      	// set column header to bold    
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':R' . $start_column_num)->getFont()->setBold(true);
        
	      	//center column names
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':R' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      
	      	$header = "MITSUKOSHI MOTORS PHILIPPINES INC.";
			$header2 = "I.T. Repairs";
			$header3 = "From " . $from_date . " to " . $to_date;
			$print_date = date('M d, Y H:i:s');
			$print_date_header = " (Printed On: {$print_date})";
			$header3 .= $print_date_header;
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
		    $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
			
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $objPHPExcel->getActiveSheet()->mergeCells('A2:R2');
		    $objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
		
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $objPHPExcel->getActiveSheet()->mergeCells('A3:R3');
		    $objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
        
	      	//set column names
	      	$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'REPAIR CODE');
	      	$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'REQUESTED BY');
	      	$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'OVERALL STATUS');
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'RECEIVED BY');
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'DATE RECEIVED');
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'HARDWARE NAME');
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'QUANTITY');
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $start_column_num, 'DESCRIPTION');
			$objPHPExcel->getActiveSheet()->setCellValue('I' . $start_column_num, 'PERIPHERALS');
			$objPHPExcel->getActiveSheet()->setCellValue('J' . $start_column_num, 'TR NUMBER (IN)');
			$objPHPExcel->getActiveSheet()->setCellValue('K' . $start_column_num, 'TR NUMBER (OUT)');
			$objPHPExcel->getActiveSheet()->setCellValue('L' . $start_column_num, 'STATUS');
			$objPHPExcel->getActiveSheet()->setCellValue('M' . $start_column_num, 'PROPOSED AMOUNT');
			$objPHPExcel->getActiveSheet()->setCellValue('N' . $start_column_num, 'APPROVED AMOUNT');
			$objPHPExcel->getActiveSheet()->setCellValue('O' . $start_column_num, 'IS BRANCH EXPENSE');
			$objPHPExcel->getActiveSheet()->setCellValue('P' . $start_column_num, 'REMARKS');
			$objPHPExcel->getActiveSheet()->setCellValue('Q' . $start_column_num, 'CREATED BY');
			$objPHPExcel->getActiveSheet()->setCellValue('R' . $start_column_num, 'INSERT TIMESTAMP');
        
	      	$objPHPExcel->getActiveSheet()->freezePane('A5');
      
	      	$row = $start_column_num + 1;	      	
	      	$current_repair_code = "";	      	
	      	$switch = 0;
	      	$cell_bgcolor = "FFFF99";
	      	$is_bold = false;
	      	$last_detail_id = "";

	      	foreach ($report_result as $m)
	      	{	        	

	      		if ($m->branch_id == 0) {
	      			$requested_by_details = $this->human_relations_model->get_employment_information_view_by_id($m->id_number);
	      			$requested_by = $requested_by_details->complete_name;
	      		} else {
	      			$requested_by_details = $this->human_relations_model->get_branch_by_id($m->branch_id);
	      			$requested_by = $requested_by_details->branch_name;
	      		}	      	

	        	// received_by
	      		$received_by_details = $this->human_relations_model->get_employment_information_view_by_id($m->received_by);
	      		
	      		// repair_hardware
	      		$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($m->repair_hardware_id);

	        	// repair_status
	      		$repair_status_details = $this->information_technology_model->get_repair_status_by_id($m->repair_status_id);

	        	// is_branch_expense
	      		if ($m->is_branch_expense == 1)
	      			$is_branch_expense = "YES";
	      		else
	      			$is_branch_expense = "NO";

	      		// created_by
	      		$created_by_details = $this->human_relations_model->get_employment_information_view_by_id($m->created_by);


	      		if ($current_repair_code <> $m->repair_code) {
	      			$switch = 1;
	      			if ($cell_bgcolor == "FFFF99") {
	      				$cell_bgcolor = "FFFFFF";	      				
	      			} else 	{
	      				$cell_bgcolor = "FFFF99";	      				
	      			}	      			
	      		} else {
	      			$switch = 0;	      			      			
	      		}

	      		if ($last_detail_id <> $m->repair_detail_id) {
	      			$is_bold = true;
	      		} else {
	      			$is_bold = false;
	      		}

				$last_detail_id = $m->repair_detail_id;
	      		$current_repair_code = $m->repair_code;

	        	$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        	$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        	$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$objPHPExcel->getActiveSheet()
						    ->getStyle('A' . $row . ':R' . $row)
						    ->getFill()
						    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						    ->getStartColor()
						    ->setARGB($cell_bgcolor);

				$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':R' . $row)->getFont()->setBold($is_bold);		    

				$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $m->repair_code);
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $requested_by);
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $m->overall_status);
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $received_by_details->complete_name);
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $m->date_received);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'. $row, $repair_hardware_details->repair_hardware_name, PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $m->quantity);
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $m->description);
				$objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $m->peripherals);
				$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $m->tr_number_in);
				$objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $m->tr_number_out);
				$objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $repair_status_details->repair_status);
				$objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $m->proposed_amount);
				$objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $m->approved_amount);
				$objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $is_branch_expense);
				$objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $m->remarks);
				$objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $created_by_details->complete_name);
				$objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $m->insert_timestamp);

	        	$row++;
	      	}
	    }else {
    
	    }

  		$objPHPExcel->setActiveSheetIndex(0);   
		if($from_date == $to_date)
			$filename_date = $from_date;
		else
	    	$filename_date = $from_date . '_to_' . $to_date;
    
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="it_repairs_'.$filename_date.'.xls"');
	    header('Cache-Control: max-age=0');   
	    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');   
	}

}
