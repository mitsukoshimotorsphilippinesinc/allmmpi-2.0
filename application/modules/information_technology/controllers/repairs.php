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
				'per_page' => 10,
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

			// get request items			
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

			$this->template->json_items = json_encode($json_items);

		}


		$items = $this->spare_parts_model->get_item(null,null,"sku ASC");
		$items_array = array();
		
		foreach($items as $i)
		{
			$items_array[$i->item_id] = $i;
		}

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
		$tr_number_out = $this->input->post("tr_number_out");

		if ($repair_status_id == 9) {
			// for delivery
			$remarks = $remarks . " <strong>#TR_NUMBER_OUT: " . $tr_number_out . "</strong>";
		} 

		$data = array(
				"repair_detail_id" => $repair_detail_id,
				"remarks" => $remarks,
				"repair_status_id" => $repair_status_id,
				"created_by" => $this->user->id_number,			
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
		$where = "repair_summary_id = {$repair_detail_details->repair_summary_id} AND current_status_id = 8";
		$total_completed_count = $this->information_technology_model->get_repair_detail_count($where);

		// received from h.o.
		$where = "repair_summary_id = {$repair_detail_details->repair_summary_id} AND current_status_id = 11";
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

		$html = "<p>Remarks posted successfully!</p>";
		$title = "Add Remarks :: Repairs";

		$this->return_json("1", "Remarks posted sucessfully", array("html" => $html, "title" => $title, "remarks_html" => $remarks_html, "overall_status" => $overall_status));
		return;

	}

	public function remove_item()
	{
		$repair_detail_id = $this->input->post("repair_detail_id");

		$repair_detail_details = $this->information_technology_model->get_repair_detail_by_id($repair_detail_id);

		//logging of action
		$details_before = array('id' => $repair_detail_id, 'details' => array("repair_details"=>$repair_detail_details));
		$details_after = array('id' => $repair_detail_id, 'details' => array());		
		log_to_db("information_techmology", $this->user->id_number, "REPAIRS-DEALERS", "rs_repair_detail", "DELETE", $details_before, $details_after);

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

		$this->return_json("1", "Item Successfully Removed", array("html_items" => $html_items));
		return;
	}

}
