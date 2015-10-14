<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Spare_parts extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->load->model('spare_parts_model');
		$this->load->model('human_relations_model');	
		$this->load->model('warehouse_model');
		$this->load->helper('spare_parts_helper');

		$this->db_spare_parts = $this->load->database('spare_parts', TRUE);	
	}

	public function index()
	{
		
		$this->template->view('dashboard');
	}

	public function dashboard_figures()
	{
		
		// critical item count
		$where = "limit_status = 'CRITICAL'";
		$critical_parts_count = $this->spare_parts_model->get_spare_part_stock_level_view_count($where);

		// reserved items count
		$pending_reservation_count = $this->spare_parts_model->get_pending_warehouse_reservations_count();

		$data = array(
			'critical_parts_count' => $critical_parts_count,
			'pending_reservation_count' => $pending_reservation_count
		);
	
		$html = $this->load->view("dashboard_figures",$data,true);

		$this->return_json("1","Display Dashboard Figures", array("html" => $html));	

		return;	
	}

	public function warehouse_critical_inventory()
	{

		$where = "limit_status = 'CRITICAL'";
		// get total records
		$critical_parts_count = $this->spare_parts_model->get_spare_part_stock_level_view_count($where);

		$critical_parts_details = $this->spare_parts_model->get_spare_part_stock_level_view($where, array('rows' => 10, 'offset' => 0), '(stock_limit - good_quantity), description');

		$data = array(
			'critical_parts_details' => $critical_parts_details,
			'critical_parts_count' => $critical_parts_count
		);
	
		$html = $this->load->view("warehouse_critical_inventory",$data,true);

		$this->return_json("1","Display Warehouse Critical Inventory", array("html" => $html));	

		return;	
	}

	public function pending_reservations()
	{
	
		$pending_reservations_count = $this->spare_parts_model->get_pending_warehouse_reservations_count();

		$where = "ORDER BY insert_timestamp DESC LIMIT 20";
		$pending_reservations_details = $this->spare_parts_model->get_pending_warehouse_reservations($where);

		$data = array(
			'pending_reservations_details' => $pending_reservations_details,
			'pending_reservations_count' => $pending_reservations_count
		);
	
		$html = $this->load->view("pending_reservations",$data,true);

		$this->return_json("1","Display Pending Reservations", array("html" => $html));	

		return;	
	}
	
	public function pie_chart()
	{
	
		$data = array(
			);

		$html = $this->load->view("pie_chart",$data,true);

		$this->return_json("1","Display Pie Chart", array("html" => $html));	

		return;	
	}
	
	public function display_mtr($request_code) 
	{
		$this->load->library('mpdf60/mpdf');
		
		$module_code = substr($request_code, 0, 2);

		// get segment name
		$department_module_details = $this->spare_parts_model->get_department_module_by_code($module_code);

		// request_summary
		$request_summary_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
								is_" . $department_module_details->segment_name . " a
							WHERE
							a.request_code = '" . $request_code . "'";

		$request_summary = $this->db_spare_parts->query($request_summary_sql);
		$request_summary = $request_summary->result();		
		$request_summary = $request_summary[0];				
		
		// get request details
		$request_details_sql = "SELECT * FROM
									is_" . $department_module_details->segment_name . "_detail
								WHERE
								" . $department_module_details->segment_name . "_id = '" . $request_summary->id . "'";
		
		$request_details = $this->db_spare_parts->query($request_details_sql);
		$request_details = $request_details->result();								
	
		// get total amount and total quantity
		$total_quantity_amount_sql = "SELECT 
											(SUM(good_quantity) + SUM(bad_quantity)) AS total_items, (SUM(total_amount)) AS total_amount
										FROM 
											is_" . $department_module_details->segment_name . "_detail 
										WHERE " . $department_module_details->segment_name . "_id = " . $request_summary->id . " AND status NOT IN ('CANCELLED', 'DELETED')";

		$total_quantity_amount = $this->db_spare_parts->query($total_quantity_amount_sql);
		$total_quantity_amount = $total_quantity_amount->result();						
		$total_quantity_amount = $total_quantity_amount[0];
		
		$where = "id_number IN (LPAD('" . $request_summary->approved_by . "', 7, 0), LPAD('" . $request_summary->warehouse_approved_by . "', 7, 0))";
		$approvals = $this->human_relations_model->get_employment_information_view($where);

		// get requester details
		$requester = $this->human_relations_model->get_employment_information_view_by_id($request_summary->id_number);

		$warehouse = $this->spare_parts_model->get_warehouse_by_id($request_summary->warehouse_id);

		$data = array(
				'copy' => 'Original',
				'request_code' => $request_code,
				'requester' => $requester,
				'request_summary' => $request_summary,
				'request_details' => $request_details,
				'warehouse' => $warehouse,
				'total_quantity' => $total_quantity_amount->total_items,
				'total_amount' => $total_quantity_amount->total_amount,
				'approvals' => $approvals,
				'department_module_details' => $department_module_details
			);

		$this->mpdf->WriteHTML($this->load->view('mtr_pdf_view',$data,TRUE));			
		$this->mpdf->AddPage();
		$data['copy'] = 'Duplicate';
		$this->mpdf->WriteHTML($this->load->view('mtr_pdf_view',$data,TRUE));
		$this->mpdf->AddPage();
		$data['copy'] = 'Triplicate';
		$this->mpdf->WriteHTML($this->load->view('mtr_pdf_view',$data,TRUE));
		$this->mpdf->Output($request_code, 'I');
		
	}

	public function display_po($request_code) 
	{
		$this->load->library('mpdf60/mpdf');
		
		$module_code = substr($request_code, 0, 2);

		// get segment name
		$department_module_details = $this->spare_parts_model->get_department_module_by_code($module_code);

		// request_summary
		$request_summary_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
								is_" . $department_module_details->segment_name . " a
							WHERE
							a.request_code = '" . $request_code . "'";

		$request_summary = $this->db_spare_parts->query($request_summary_sql);
		$request_summary = $request_summary->result();		
		$request_summary = $request_summary[0];				
		
		// get request details
		$request_details_sql = "SELECT * FROM
									is_" . $department_module_details->segment_name . "_detail
								WHERE
								" . $department_module_details->segment_name . "_id = '" . $request_summary->id . "'";
		
		$request_details = $this->db_spare_parts->query($request_details_sql);
		$request_details = $request_details->result();								
	
		// get total amount and total quantity
		$total_quantity_amount_sql = "SELECT 
											(SUM(good_quantity) + SUM(bad_quantity)) AS total_items, (SUM(total_amount)) AS total_amount
										FROM 
											is_" . $department_module_details->segment_name . "_detail 
										WHERE " . $department_module_details->segment_name . "_id = " . $request_summary->id . " AND status NOT IN ('CANCELLED', 'DELETED')";

		$total_quantity_amount = $this->db_spare_parts->query($total_quantity_amount_sql);
		$total_quantity_amount = $total_quantity_amount->result();						
		$total_quantity_amount = $total_quantity_amount[0];
		
		$where = "id_number IN (LPAD('" . $request_summary->approved_by . "', 7, 0), LPAD('" . $request_summary->warehouse_approved_by . "', 7, 0))";
		$approvals = $this->human_relations_model->get_employment_information_view($where);

		// get requester details
		$requester = $this->human_relations_model->get_employment_information_view_by_id($request_summary->id_number);

		$warehouse = $this->spare_parts_model->get_warehouse_by_id($request_summary->warehouse_id);

		$data = array(
				'copy' => 'Original',
				'request_code' => $request_code,
				'requester' => $requester,
				'request_summary' => $request_summary,
				'request_details' => $request_details,
				'warehouse' => $warehouse,
				'total_quantity' => $total_quantity_amount->total_items,
				'total_amount' => $total_quantity_amount->total_amount,
				'approvals' => $approvals,
			);

		$this->mpdf->WriteHTML($this->load->view('po_pdf_view',$data,TRUE));			
		$this->mpdf->AddPage();
		$data['copy'] = 'Duplicate';
		$this->mpdf->WriteHTML($this->load->view('po_pdf_view',$data,TRUE));
		$this->mpdf->AddPage();
		$data['copy'] = 'Triplicate';
		$this->mpdf->WriteHTML($this->load->view('po_pdf_view',$data,TRUE));
		$this->mpdf->Output($request_code, 'I');
		
	}

	public function get_requester()
	{
		$search_key = $this->input->get_post('search_key');
		$search_key = trim($search_key);

		if (empty($search_key)) 
		{
			$this->return_json("error","Search key is empty.");
			return;
		}
		
		$keys = explode(" ", $search_key);
		$escape_keys = array();
		for ($i = 0; $i < count($keys); $i++)
			array_push($escape_keys, $this->human_relations_model->escape("%".$keys[$i]."%") );
			
		$where_first_name = implode(' OR first_name LIKE ', $escape_keys);
		$where_last_name = implode(' OR last_name LIKE ', $escape_keys);
		
		// check if its a string name or part of a name
		$escaped_search_key1 = $this->human_relations_model->escape($search_key);
		$escaped_search_key2 = $this->human_relations_model->escape('%'.$search_key.'%');
		$where = "is_employed = 1 AND ((complete_name like {$where_first_name}) ".(count($keys) > 1 ? "AND" : "OR")." (complete_name like {$where_last_name})) OR id_number like {$escaped_search_key2}";
		$tmp_employees = $this->human_relations_model->get_employment_information_view($where, array('offset' => 0, 'rows' => 50), "id_number ASC, complete_name ASC");
		
		// 20150723 TODO!!!
		// ================
		//var_dump($where);
		//return;
		// ================

		$employees = array();
		if (count($tmp_employees) == 0)
		{
			// if these is reached then nothing are found.
			$this->return_json("error","Not found.", array('employees' => $employees, 'keys' => $keys));
			return;
		}

		$tmp_position = $this->human_relations_model->get_position();
		$positions = array();
		foreach ($tmp_position as $item)
			$positions[$item->position_id] = $item;

		foreach ($tmp_employees as $mem)
		{
			
			$department_name = "N/A";	
			$position_name = "N/A";
			// get company and department
			$department_details = $this->human_relations_model->get_department_by_id($mem->department_id);
			if (!empty($department_details)) {
				$department_name = $department_details->department_name;
			}

			// get position
			$position_details = $this->human_relations_model->get_position_by_id($mem->position_id);
			if (!empty($position_details)) {
				$position_name = $position_details->position_name;
			}

			// is_employed
			if ($mem->is_employed == 1)
				$is_employed = "YES";
			else
				$is_employed = "NO";

			$employees[$mem->employment_information_id] = array(
				"employment_information_id" => $mem->employment_information_id,
				"id_number" => $mem->id_number,
				"complete_name" => strtoupper($mem->complete_name),
				"company_email_address" => $mem->company_email_address,
				"department_name" => $department_name,
				"position" => $position_name,
				"is_employed" => $is_employed,
			);
		}

		$this->return_json("ok","Ok.", array('employees' => $employees, 'keys' => $keys));
		return;
		
	}

	public function search_item()
	{
		$search_key = $this->input->get_post('search_key');
		$search_key = trim($search_key);
	
		if (empty($search_key)) 
		{
			$this->return_json("error","Item Name is empty.");
			return;
		}

		$keys = explode(" ", $search_key);
		for ($i = 0; $i < count($keys); $i++)
		{
			$escaped_keys[] = mysql_real_escape_string($keys[$i]);
		}

		$key_count = count($escaped_keys);  

		// get possible combinations
		$combinations = array();

		$this->load->library('Math_Combinatorics');
		$combinatorics = new Math_Combinatorics;
		foreach( range(1, count($escaped_keys)) as $subset_size ) {
    		foreach($combinatorics->permutations($escaped_keys, $subset_size) as $p) {
	  			$combinations[sizeof($p)-1][] = $p;
    		}
		}

		$combinations = array_reverse($combinations);

		// exact match search
		$has_exact = false;
		$tmp_items = array();

		foreach($combinations as $comb_group)
		{
			foreach($comb_group as $comb)
			{
				$name = strtoupper(join('', $comb));
				$sql = "
					SELECT * FROM `is_item_view` WHERE
					(REPLACE(`sku`,' ','') LIKE '%{$name}%') OR (REPLACE(`model_name`,' ','') LIKE '%{$name}%') OR (REPLACE(`description`,' ','') LIKE '%{$name}%') OR (REPLACE(`sku`,' ','') LIKE '%{$name}%') ORDER BY sku, description LIMIT 50;
				";
				$query = $this->db_spare_parts->query($sql);
				if(count($query->result_array()) > 0)
				{
					$tmp_items = $query->result_object();
					$has_exact = true;
					break;
				}
			}
			if($has_exact)
			{
				break;
			}
		}
		
		$return_items = array();

		if (count($tmp_items) == 0)
		{
			// if these is reached then nothing are found.
			$this->return_json("error","Not found.", array('items' => $return_items, 'keys' => $keys));
			return;
		}
		
		foreach ($tmp_items as $itm)
		{
			$return_items[$itm->item_id] = array(
				"item_id" => $itm->item_id,
				"sku" => $itm->sku,
				"brand_model" => $itm->brand_name . ' / ' . $itm->model_name,
				"description" => strtoupper($itm->description),
				"srp" => strtoupper($itm->srp),
				"warehouse_name" => strtoupper($itm->warehouse_name),
				"rack_location" => strtoupper($itm->rack_location),
				"bad_quantity" => $itm->bad_quantity,
				"good_quantity" => $itm->good_quantity,

			);
		}
		
		$this->return_json("ok","Ok.", array('items' => $return_items, 'keys' => $keys));
		return;

	}

	public function get_requested_items()
	{
		$search_key = $this->input->get_post('search_key');
		$segment_name = $this->input->get_post('segment_name');
		$request_id = $this->input->get_post('request_id');
		$search_key = trim($search_key);
	
		/*if (empty($search_key)) 
		{
			$this->return_json("error","Item Name is empty.");
			return;

		}*/

		$keys = explode(" ", $search_key);
		for ($i = 0; $i < count($keys); $i++)
		{
			$escaped_keys[] = mysql_real_escape_string($keys[$i]);
		}

		$key_count = count($escaped_keys);  

		// get possible combinations
		$combinations = array();

		$this->load->library('Math_Combinatorics');
		$combinatorics = new Math_Combinatorics;
		foreach( range(1, count($escaped_keys)) as $subset_size ) {
    		foreach($combinatorics->permutations($escaped_keys, $subset_size) as $p) {
	  			$combinations[sizeof($p)-1][] = $p;
    		}
		}

		$combinations = array_reverse($combinations);

		// exact match search
		$has_exact = false;
		$tmp_items = array();

		foreach($combinations as $comb_group)
		{
			foreach($comb_group as $comb)
			{
				$name = strtoupper(join('', $comb));

				$sql = "SELECT 
							a.". $segment_name ."_id as request_id, 
							a.". $segment_name ."_detail_id as request_detail_id, 
							a.item_id, 
							b.sku, 
							b.brand_name, 
							b.model_name, 
							b.description, 
							b.warehouse_name,
							b.rack_location,
							a.srp, 
							a.discount, 
							a.discount_amount, 
							a.good_quantity, 
							CASE WHEN c.good_quantity IS NULL THEN 0 ELSE c.good_quantity END AS reprocessed_good_quantity, 
							(a.good_quantity - (CASE WHEN c.good_quantity IS NULL THEN 0 ELSE c.good_quantity END)) AS remaining_good_quantity, 
							a.bad_quantity, 
							CASE WHEN c.bad_quantity IS NULL THEN 0 ELSE c.bad_quantity END AS reprocessed_bad_quantity,
							(a.bad_quantity - (CASE WHEN c.bad_quantity IS NULL THEN 0 ELSE c.bad_quantity END)) AS remaining_bad_quantity
						FROM 
							is_". $segment_name ."_detail a
						LEFT JOIN
							is_item_view b on a.item_id = b.item_id
						LEFT JOIN
							is_reprocessed_item c on a.". $segment_name ."_detail_id = c.request_detail_id
						WHERE 
							a.". $segment_name ."_id = {$request_id}
						AND 
							a.status NOT IN ('CANCELLED', 'DELETED')
						AND 
							(((a.good_quantity - (CASE WHEN c.good_quantity IS NULL THEN 0 ELSE c.good_quantity END)) <> 0) 
								OR ((a.bad_quantity - (CASE WHEN c.bad_quantity IS NULL THEN 0 ELSE c.good_quantity END)) <> 0))		
						AND
							((REPLACE(b.sku,' ','') LIKE '%{$name}%') OR (REPLACE(b.model_name,' ','') LIKE '%{$name}%') OR (REPLACE(b.description,' ','') LIKE '%{$name}%') OR (REPLACE(b.sku,' ','') LIKE '%{$name}%')) ORDER BY b.sku, b.description LIMIT 50";


				$query = $this->db_spare_parts->query($sql);
				if(count($query->result_array()) > 0)
				{
					$tmp_items = $query->result_object();
					$has_exact = true;
					break;
				}
			}
			if($has_exact)
			{
				break;
			}
		}
		
		$return_items = array();

		if (count($tmp_items) == 0)
		{
			// if these is reached then nothing are found.
			$this->return_json("error","Not found.", array('items' => $return_items, 'keys' => $keys));
			return;
		}
		
		foreach ($tmp_items as $itm)
		{
			$return_items[$itm->item_id] = array(
				"item_id" => $itm->item_id,
				"sku" => $itm->sku,
				"brand_model" => strtoupper($itm->brand_name) . " / " . strtoupper($itm->model_name),
				"description" => strtoupper($itm->description),
				"full_description" => "[".  strtoupper($itm->brand_name) . " / " . strtoupper($itm->model_name) . "]" . strtoupper($itm->description),
				"srp" => strtoupper($itm->srp),
				"warehouse_name" => strtoupper($itm->warehouse_name),
				"rack_location" => strtoupper($itm->rack_location),
				"bad_quantity" => $itm->bad_quantity,
				"remaining_bad_quantity" => $itm->remaining_bad_quantity,
				"good_quantity" => $itm->good_quantity,
				"remaining_good_quantity" => $itm->remaining_good_quantity,
				"discount" => $itm->discount,
				"discount_amount" => $itm->discount_amount,
				"request_detail_id" => $itm->request_detail_id,

			);
		}
		
		$this->return_json("ok","Ok.", array('items' => $return_items, 'keys' => $keys));
		return;

	}











	function get_requester_details()
	{
		$id_number = $this->input->post("id_number");
		$requester_type = $this->input->post("requester_type");

		$details_content = get_requester_details($id_number, $requester_type);
	
		$this->return_json("ok","Get Requester Details", array('html' => $details_content));
		return;	
	}

	function display_request_remarks()
	{
		$request_code = $this->input->post("request_code");
		$remarks = $this->input->post("remarks");
		$segment_name = $this->input->post("segment_name");

		$request_summary_sql = "SELECT * FROM is_" . $segment_name . " WHERE request_code = '" . $request_code . "'";

		$request_summary = $this->db_spare_parts->query($request_summary_sql);
		$request_summary = $request_summary->result();		
		$request_summary = $request_summary[0];		

		$remarks = json_decode($request_summary->remarks);

		//$remarks = (object) $remarks;

		$row = "<table class='table table-condensed table-bordered'>
				<thead>
					<th>Datetime</th>
					<th>Message</th>
				</thead>
				<tbody>";
		if(!empty($remarks))
		{				

			foreach($remarks as $r)
			{	
				$row .= "<tr><td>{$r->datetime}</td><td>{$r->message}</td></tr>";
			}				
		} else {
			$title = "Error View Remarks :: " . $request_code;
			$html = "<p>The is something wrong with the remarks field format.</p";
			$this->return_json("0","Invalid Remarks format", array('html' => $html, 'title' => $title));
		return;
		}

		$row .= "</tbody></table>";
		
		$title = "View Remarks :: " . $request_code;

		$this->return_json("1","Remarks successfully displayed.", array('html' => $row, 'title' => $title));
		return;
	}


	function get_item_details()
	{
		$request_detail_id = $this->input->post("request_detail_id");
		$segment_name = $this->input->post("segment_name");

		$item_details_sql = "SELECT * FROM is_" . $segment_name . "_detail WHERE " . $segment_name . "_detail_id = " . $request_detail_id;
		
		$item_details = $this->db_spare_parts->query($item_details_sql);
		$item_details = $item_details->result();		
		$item_details = $item_details[0];			

		$discount = abs($item_details->discount);

		$item_view_details = $this->spare_parts_model->get_item_view_by_id($item_details->item_id);			
		
		$this->return_json("1","Item Details.", array('item_details' => $item_details, 'item_view_details' => $item_view_details, 'discount' => $discount));
		return;

	}

	function add_new_requester_remarks()
	{
		$request_code = $this->input->post("request_code");
		$remarks = trim($this->input->post("remarks"));
		$segment_name = $this->input->post("segment_name");

		$request_summary_sql = "SELECT * FROM is_" . $segment_name . " WHERE request_code = '" . $request_code . "'";

		$request_summary = $this->db_spare_parts->query($request_summary_sql);
		$request_summary = $request_summary->result();		
		$request_summary = $request_summary[0];		

		$data = json_decode($request_summary->remarks, true);

		$current_datetime = date("Y-m-d H:i:s");		
		array_push($data, array('datetime' => $current_datetime, 'message' => $remarks));

		$remarks = json_encode($data);

		// update table
		$request_update_sql = "UPDATE is_" . $segment_name . " SET remarks = '" . $remarks . "' WHERE request_code = '" . $request_code . "'";
		$this->db_spare_parts->query($request_update_sql);

		$title = "Update Remarks :: " . $request_code;
		$html = "Remarks successfully updated for Request Code : <strong>" . $request_code . "</strong>.";		

		$this->return_json("1","Remarks successfully updated.", array('html' => $html, 'title' => $title));
		return;
	}

	function forward_to_warehouse()
	{
		$request_code = $this->input->post("request_code");		
		$segment_name = $this->input->post("segment_name");
		$is_forwarded = $this->input->post("is_forwarded");

		$request_summary_sql = "SELECT a." . $segment_name . "_id as request_id, a.* FROM is_" . $segment_name . " a WHERE a.request_code = '" . $request_code . "'";

		$request_summary = $this->db_spare_parts->query($request_summary_sql);
		$request_summary = $request_summary->result();		
		$request_summary = $request_summary[0];		

		$department_module_details = $this->spare_parts_model->get_department_module_by_segment_name($segment_name);

		if ($is_forwarded == 0) {

			// one time insert
			$insert_sql = "INSERT INTO 
								tr_warehouse_return
								(department_module_id, 
								transaction_number, 
								request_id, 								
								reprocessed_item_id)
							(SELECT 
								department_module_id, 
								'{$request_code}',
								request_id, 								
								reprocessed_item_id 
							FROM 
								is_reprocessed_item
							WHERE
								department_module_id = {$department_module_details->department_module_id}
							AND
								request_id = {$request_summary->request_id}							
							AND
								action = 'RETURN'
							AND
								status IN ('PENDING'))";

			$this->db_spare_parts->query($insert_sql);

			// update all PENDING items under is_reprocessed_item
			$update_iri_sql = "UPDATE 
									is_reprocessed_item 
								SET 
									status = 'FORWARDED' 
								WHERE 
									department_module_id = " . $department_module_details->department_module_id . "
								AND 
									request_id = " . $request_summary->request_id . "
								AND 
									status IN ('PENDING')
								AND
									action = 'RETURN'";

			$this->db_spare_parts->query($update_iri_sql);

			// update status in tr_warehouse_return
			$data = array(
					"return_status" => 'PENDING',
					"update_timestamp" => date("Y-m-d H:i:s")
				);

			$is_forwarded = 1;

			$title = "Forward To Warehouse :: " . $request_code;
			$html = "You have successfully forwarded the request to warehouse with Request Code : <strong>" . $request_code . "</strong>.";		
			
		} /*else {
			// update all PENDING items under is_reprocessed_item
			$where = "department_module_id " . $department_module_details->department_module_id . "
								AND 
									request_id = " . $request_summary->request_id . "
								AND 
									status = 'FORWARDED'";

			$data_update = array(
					'status' => 'PENDING',
					'update_timestamp' => date("Y-m-d H:i:s")
				);

			$this->spare_parts_model->update_reprocessed_item($data_update, $where);

			$where = "department_module_id " . $department_module_details->department_module_id . "
								AND 
									request_id = " . $request_summary->request_id . "
								AND 
									return_status = 'FORWARDED'";

			$data_update = array(
					'status' => 'PENDING',
					'update_timestamp' => date("Y-m-d H:i:s")
				);	

			// update tr_warehouse_return
			$this->spare_parts_model->update_warehouse_return($data, $where);

			$is_forwarded = 0;

			$title = "Cancel Forward To Warehouse :: " . $request_code;
			$html = "You have cancelled the forwarded request to warehouse with Request Code : <strong>" . $request_code . "</strong>.";		

		}	*/

		$this->return_json("1", "Successfully Forwarded Items to Warehouse" . $title, array('html' => $html, 'title' => $title, 'is_forwarded' => $is_forwarded));
		return;
	}

	public function display_returnslip($request_code) 
	{
		$this->load->library('mpdf60/mpdf');
		
		$module_code = substr($request_code, 0, 2);

		// get department_module_details
		$department_module_details = $this->spare_parts_model->get_department_module_by_code($module_code);

		// request_summary
		$request_summary_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
								is_" . $department_module_details->segment_name . " a
							WHERE
							a.request_code = '" . $request_code . "'";

		$request_summary = $this->db_spare_parts->query($request_summary_sql);
		$request_summary = $request_summary->result();		
		$request_summary = $request_summary[0];				
		
		$where = "action = 'RETURN' AND status IN ('PREPARING', 'PENDING') AND department_module_id = " . $department_module_details->department_module_id . " AND request_id = " . $request_summary->id;
		$reprocessed_item_details = $this->spare_parts_model->get_reprocessed_item($where);

		// get total amount and total quantity
		$total_quantity_amount_sql = "SELECT 
											(SUM(good_quantity) + SUM(bad_quantity)) AS total_items, (SUM(total_amount)) AS total_amount
										FROM 
											is_reprocessed_item 
										WHERE department_module_id = " . $department_module_details->department_module_id . " AND request_id = " . $request_summary->id  . " AND action = 'RETURN' AND status IN ('PREPARING', 'PENDING')";

		$total_quantity_amount = $this->db_spare_parts->query($total_quantity_amount_sql);
		$total_quantity_amount = $total_quantity_amount->result();						
		$total_quantity_amount = $total_quantity_amount[0];

		// get requester details
		$requester = $this->human_relations_model->get_employment_information_view_by_id($request_summary->id_number);

		$warehouse = $this->spare_parts_model->get_warehouse_by_id($request_summary->warehouse_id);

		$data = array(				
				'request_code' => $request_code,
				'requester' => $requester,
				'request_summary' => $request_summary,
				'warehouse' => $warehouse,
				'reprocessed_item_details' => $reprocessed_item_details,
				'department_module_details' => $department_module_details,
				'total_quantity_amount' => $total_quantity_amount,
			);

		$this->mpdf->WriteHTML($this->load->view('returnslip_pdf_view',$data,TRUE));
		$this->mpdf->Output($request_code, 'I');
		
	}

	public function approval()
	{

		$search_status = trim($this->input->get("search_status"));
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";
		$count_is = 0;
		$transfers = "";		

		if (empty($search_status)) {
			$where = "status IN ('FOR APPROVAL', 'APPROVED')";
		} else {

			if ($search_status == 'ALL') {
				$where = "status IN ('FOR APPROVAL', 'APPROVED')";
			} else {
				$where = "status = '". $search_status ."'";
			}
					
			if ($where != NULL) {
				$where = $where . " AND ". $search_by ." LIKE '%" . $search_text . "%'";
			} else {
				$where = $search_by ." LIKE '%" . $search_text . "%'";
			}
		}

		// set pagination data
		$config = array(
				'pagination_url' => "/spare_parts/dealer/approval/",
				'total_items' => $this->spare_parts_model->get_dealer_request_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		$transfers = $this->spare_parts_model->get_dealer_request($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			
		
		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;
		
		$this->template->view('dealer/approval');	
		

	}	


	public function for_approval_confirm()
	{
		$dealer_request_id = $this->input->post("dealer_request_id");
		$dealer_request_code = $this->input->post("dealer_request_code");
		$is_approved = $this->input->post("is_approved");

		$dealer_request = $this->spare_parts_model->get_dealer_request_by_id($dealer_request_id);
	
		if (empty($dealer_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$dealer_request_code}].</p>";
			$title = "Error: Confirm Approval";

			$this->return_json("0","Dealer Request Code not found in DB", array("html" => $html, "title" => $title));			

		} else {

			if ($is_approved == 'yes') {

				$html = "You are about to approve the Dealer Request with Request Code: <strong>" . $dealer_request_code . "</strong>. Do you want to continue?";
			} else {
				$html = "<p>You are about to deny the Dealer Request with Request Code: <strong>" . $dealer_request_code . "</strong>. <br/>
							<div id='reasonremarks-container'>
								<span><strong>Reason/Remarks:</strong></span></br>
								<input id='txt-remarks' style='width:400px;'/><br/>
								<span id='error-reasonremarks' style='color:red;display:none'>Remarks/Reason is required.</span>
							</div>	
							<br/>
							Do you want to continue?</p>";
			}	

			$title = "Confirm Approval :: " . $dealer_request_code;
				
			$data = array (
				'dealer_request_id' => $dealer_request_id,
				'dealer_request_code' => $dealer_request_code,
				'is_approved' => $is_approved
				);	

			$this->return_json("1","Confirm Approval of Dealer Request.",array("html" => $html, "title" => $title, "data" => $data));
		
		}
		
		return;
	}
	
	public function for_approval_proceed()
	{
		$dealer_request_id = $this->input->post("dealer_request_id");
		$dealer_request_code = $this->input->post("dealer_request_code");
		$is_approved = $this->input->post("is_approved");
		$remarks =  $this->input->post("remarks");
		
		$dealer_request = $this->spare_parts_model->get_dealer_request_by_id($dealer_request_id);		

		if (empty($dealer_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$dealer_request_code}].</p>";
			$title = "Error: Proceed Approval";

			$this->return_json("0","Dealer Request Code not found in DB", array("html" => $html, "title" => $title));	
			
		} else {
	
			// change status to DENIED
			$current_datetime = date("Y-m-d H:i:s");
			
			if ($is_approved == 'no') {
				$new_remarks = "[" . $current_datetime . "] " . $remarks . "\n" . $dealer_request->remarks;

				$data = array(
					'status' => "DENIED",
					'approved_by' => $this->user->user_id,
					'remarks' => $new_remarks,
					'approve_timestamp' => $current_datetime
				);

				$html = "You have denied the Dealer Request Code: <strong>{$dealer_request_code}</strong>.";
				$title = "Denied :: " . $dealer_request_code;

				$this->return_json("1","Denied Dealer Request.",array("html" => $html, "title" => $title));		

			} else {
				// change status to APPROVED
				$data = array(
					'status' => "APPROVED",
					'approved_by' => $this->user->user_id,					
					'approve_timestamp' => $current_datetime
				);

				$html = "You have successfully approved the Dealer Request Code: <strong>{$dealer_request_code}</strong>.";
				$title = "Approved :: " . $dealer_request_code;
			}
			
			$where = "dealer_request_id = " . $dealer_request_id;
			$this->spare_parts_model->update_dealer_request($data, $where);
			
			$this->return_json("1","Successful Approval of Dealer Request.",array("html" => $html, "title" => $title));
						
		}	
		return;	
	}


	public function view_details()
	{
		$dealer_request_id = $this->input->post("dealer_request_id");
		$dealer_request_code = $this->input->post("dealer_request_code");
			
		$dealer_request = $this->spare_parts_model->get_dealer_request_by_id($dealer_request_id);		

		if (empty($dealer_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$dealer_request_code}].</p>";
			$title = "Error: View Details";

			$this->return_json("0","Dealer Request Code not found in DB", array("html" => $html, "title" => $title));	
			
		} else {
		
			$data = array(
				'dealer_request' => $dealer_request
			);
		
			$html = $this->load->view("dealer/view_details",$data,true);
			 
			$title = "View Details :: " . $dealer_request_code;
			$this->return_json("1","View Details Dealer Request", array("html" => $html, "title" => $title));
			
		}
			
		return;
	}

	public function load_assign_mtr()
	{
		$request_id = $this->input->post("request_id");
		$request_code = $this->input->post("request_code");		

		$title = "Assign MTR Number :: " . $request_code;
		$html = "<p>Enter a MTR for Request Code : <strong>" . $request_code . "</strong>. <br/>
					<div id='reasonremarks-container'>
						<span><strong>MTR Number:</strong></span></br>
						<input id='txt-mtrnumber' style='width:100px;' maxlength='10' placeholder='1234567890' /><br/>
						<span id='error-mtrnumber' style='color:red;display:none'>MTR Number is required.</span>
					</div>	
					<br/>
					Click Proceed to continue...</p>";
			
		$this->return_json("1","Load Assign MTR Modal.",array("html" => $html, "title" => $title));
		
		return;
	}

	public function check_mtr()
	{
		$request_id = trim($this->input->post("request_id"));
		$request_code = trim($this->input->post("request_code"));	
		$mtr_number = trim($this->input->post("mtr_number"));	

		$return_val = check_mtr($request_code, $mtr_number);
		
		$used_by_request_code = "";
		if (empty($return_val)) {
			$html = "MTR is available.";	
			$this->return_json("1","Available MTR", array("html" => $html));
		} else {
			$used_by_request_code = $return_val[0]->request_code;
			$html = "Sorry, MTR Number was already used by ". $used_by_request_code .". Please try another.";
			$this->return_json("0","Not Available MTR", array("html" => $html, "request_code_mtr_user" => $return_val));
		}

		return;
	}

	public function proceed_assign_mtr()
	{
		$request_id = $this->input->post("request_id");
		$request_code = $this->input->post("request_code");		
		$mtr_number =  trim($this->input->post("mtr_number"));
		
		$result_val = assign_mtr($request_id, $request_code, $mtr_number);

		$html = "You have successfully assigned a MTR Number to the request with Request Code: <strong>{$request_code}</strong>.";
		$title = "Assign MTR Number :: " . $request_code;

		$this->return_json("1","Successful MTR Number Assignment.",array("html" => $html, "title" => $title));

		return;	
	}

	public function load_assign_po()
	{
		$request_id = $this->input->post("request_id");
		$request_code = $this->input->post("request_code");		

		$title = "Assign P.O. Number :: " . $request_code;
		$html = "<p>Enter a Purchase Order Number for Request Code : <strong>" . $request_code . "</strong>. <br/>
					<div id='reasonremarks-container'>
						<span><strong>MTR Number:</strong></span></br>
						<input id='txt-mtrnumber' style='width:100px;' maxlength='10' placeholder='1234567890' /><br/>
						<span id='error-mtrnumber' style='color:red;display:none'>P.O. Number is required.</span>
					</div>	
					<br/>
					Click Proceed to continue...</p>";
			
		$this->return_json("1","Load Assign PO Modal.",array("html" => $html, "title" => $title));
		
		return;
	}

	public function check_po()
	{
		$request_id = trim($this->input->post("request_id"));
		$request_code = trim($this->input->post("request_code"));	
		$mtr_number = trim($this->input->post("mtr_number"));	

		$return_val = check_mtr($request_code, $mtr_number);
		
		$used_by_request_code = "";
		if (empty($return_val)) {
			$html = "P.O. Number is available.";	
			$this->return_json("1","Available PO", array("html" => $html));
		} else {
			$used_by_request_code = $return_val[0]->request_code;
			$html = "Sorry, P.O. Number was already used by ". $used_by_request_code .". Please try another.";
			$this->return_json("0","Not Available PO Number", array("html" => $html, "request_code_mtr_user" => $return_val));
		}

		return;
	}

	public function proceed_assign_po()
	{
		$request_id = $this->input->post("request_id");
		$request_code = $this->input->post("request_code");		
		$mtr_number =  trim($this->input->post("mtr_number"));
		
		$result_val = assign_mtr($request_id, $request_code, $mtr_number);

		$html = "You have successfully assigned a P.O. Number to the request with Request Code: <strong>{$request_code}</strong>.";
		$title = "Assign P.O. Number :: " . $request_code;

		$this->return_json("1","Successful PO Number Assignment.",array("html" => $html, "title" => $title));

		return;	
	}

	
	public function download_check()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));

		// check if start_date and end_date is null
		if (((empty($start_date)) && (empty($start_date))) || (($start_date == NULL) && ($start_date == NULL)))
		{
			$this->return_json("error","Enter both Start Date and End Date.");
			return;
		}

		if ((empty($start_date)) || ($start_date == NULL))
		{
			$this->return_json("error","Enter Start Date.");
			return;
		}

		if ((empty($end_date)) || ($end_date == NULL))
		{
			$this->return_json("error","Enter End Date.");
			return;
		}

		// check if start_date is greater than end_date
		if ($start_date > $end_date)
		{
			$this->return_json("error","Start Date must not exceed End Date.");
			return;
		}

		$current_date = date("Y-n-j");


		// start date must not exceed the current date
		if ($start_date > $current_date)
		{
			$this->return_json("error","Start Date must not exceed Current Date.");
			return;
		}

		$proper_start_date = date('F d, Y', strtotime($start_date));
		$proper_end_date = date('F d, Y', strtotime($end_date));

		// check if query will return records to execute
		$where = "insert_timestamp BETWEEN '$start_date' AND '$end_date'";

		$pending_count = $this->spare_parts_model->get_dealer_request($where);

		if (empty($pending_count))
		{
			$return_html = "<span>No Dealer Request from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.</span>";
			$this->return_json("ok",$return_html);
			return;
		} else
		{
			$return_html = "<span>You are to generate a Dealer Requests Report ranging from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.<br/>Do you want to proceed?</span>";
			$this->return_json("ok",$return_html);
			return;
		}
	}

	public function download_proceed()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));
		
		$current_timestamp = date('Y-m-d H:i:s');

		$return_html = "<span>Request Completed.<br/><br/>You may now download the generated spreadsheet file.</span>";

		$this->return_json("ok",$return_html);
		return;

	}

	function export_xls($start_date,$end_date)
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
		
		try {
			
			// adjusting memory limit to accomodate PHPExcel processing 
			set_time_limit(0); // eliminating the timeout
			ini_set('memory_limit', '512M');

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle("dealer requests")->setDescription("none");
			$start_column_num = 3;

			$worksheet = $objPHPExcel->setActiveSheetIndex(0);

			$where = "insert_timestamp BETWEEN '$start_date' AND '$end_date'";
			$dealer_request_count = $this->spare_parts_model->get_dealer_request_count($where);

			$filename = "dealer_requests_" . str_replace("-", "", $start_date) . "-" . str_replace("-", "", $end_date) . ".xls";

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			
			//set column names
			$worksheet->setCellValue('A1', "Dealer Requests from {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'Request Code');
			$worksheet->setCellValue('B' . $start_column_num, 'Status');
			$worksheet->setCellValue('C' . $start_column_num, 'Dealer ID');
			$worksheet->setCellValue('D' . $start_column_num, 'Agent ID');
			$worksheet->setCellValue('E' . $start_column_num, 'PO Number');
			$worksheet->setCellValue('F' . $start_column_num, 'Remarks');
			$worksheet->setCellValue('G' . $start_column_num, 'Date Created');
			

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($dealer_request_count/$allowed_rows)+1; $prow++)
			{
				$dealer_requests = $this->spare_parts_model->get_dealer_request($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'insert_timestamp ASC');

				foreach ($dealer_requests as $dr)
				{

					$worksheet->setCellValue('A'. $row, $dr->request_code);
					$worksheet->setCellValue('A'. $row, $dr->status);
					$worksheet->setCellValue('C'. $row, $dr->dealer_id);
					$worksheet->setCellValue('D'. $row, $dr->agent_id);
					$worksheet->setCellValue('E'. $row, $dr->purchase_order_number);
					$worksheet->setCellValue('F'. $row, $dr->remarks);
					$worksheet->setCellValue('G'. $row, $dr->insert_timestamp);
					
					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(false);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);
					$worksheet->getColumnDimension('G')->setAutoSize(true);
					$row++;
				}
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit(0);
			
		} catch (Exception $e) {
			exit($e->getMessage());
		}
	}

}
