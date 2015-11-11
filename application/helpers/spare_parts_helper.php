<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function return_reserved_items($request_code, $reservation_status, $remarks, $request_detail_id = 0) {
	
	$ci = ci();
	
	$ci->load->model('spare_parts_model');
	//$this->db_spare_parts = $this->load->database('spare_parts', TRUE);	
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	// request_summary
	$request_summary_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
							is_" . $department_module_details->segment_name . " a
						WHERE
						a.request_code = '" . $request_code . "'";

	$request_summary = $ci->db_spare_parts->query($request_summary_sql);
	$request_summary = $request_summary->result();		
	$request_summary = $request_summary[0];				

	$add_condition = "";

	if ($request_detail_id > 0) {
		$add_condition = " AND ". $department_module_details->segment_name ."_detail_id = " . $request_detail_id;
	}

	// get request details
	$request_details_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
								is_" . $department_module_details->segment_name . "_detail a
							WHERE
							a." . $department_module_details->segment_name . "_id = '" . $request_summary->id . "' AND a.status IN ('PENDING')" . $add_condition;

	$request_details = $ci->db_spare_parts->query($request_details_sql);
	$request_details = $request_details->result();		
	
	foreach ($request_details as $rd) {	

		// return good and bad qty per item
		$update_is_item_sql = "UPDATE is_item 
								SET
									good_quantity = good_quantity + {$rd->good_quantity}, bad_quantity = bad_quantity + {$rd->bad_quantity}
								WHERE
									item_id = {$rd->item_id}";
		
		$ci->db_spare_parts->query($update_is_item_sql);

		$current_datetime = date("Y-m-d H:i:s");

		// update status = $reservation_status
		// TODO REMARKS TO JSON
		$complete_remarks = "[" . $current_datetime . "] " . $remarks . "\n" . $rd->remarks;

		$update_detail_status_sql =  "UPDATE is_" . $department_module_details->segment_name . "_detail
										SET
											status = '" . $reservation_status . "',
											update_timestamp = '" . $current_datetime . "',
											remarks = '" . $complete_remarks . "'
										WHERE
											" . $department_module_details->segment_name . "_detail_id = " . $rd->id ;

		
		$ci->db_spare_parts->query($update_detail_status_sql);

	}

	if ($request_detail_id == 0) {
		$current_datetime = date("Y-m-d H:i:s");
		// update status = $reservation_status
		$complete_remarks = "[" . $current_datetime . "] " . $remarks . "\n" . $request_summary->remarks;

		$update_summary_status_sql =  "UPDATE is_" . $department_module_details->segment_name . "
											SET
												status = '" . $reservation_status . "',
												update_timestamp = '" . $current_datetime . "',
												remarks = '" . $complete_remarks . "'
											WHERE
												" . $department_module_details->segment_name . "_id = " . $request_summary->id;

		$ci->db_spare_parts->query($update_summary_status_sql);

		// update tr_warehouse_reservation
		$data = array(
				'status' => $reservation_status,
				'update_timestamp' => $current_datetime
			);

		$ci->spare_parts_model->update_warehouse_reservation($data, "transaction_number = '" . $request_code . "'");
	}	

	return true;

} // end function


function get_items_total_amount_old($request_code) {
	
	$ci = ci();

	$ci->load->model('spare_parts_model');
	
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	// request_summary
	$request_summary_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
							is_" . $department_module_details->segment_name . " a
						WHERE
						a.request_code = '" . $request_code . "'";

	$request_summary = $ci->db_spare_parts->query($request_summary_sql);
	$request_summary = $request_summary->result();		
	$request_summary = $request_summary[0];		

	$request_item_amount_total_sql = "SELECT 
								a." . $department_module_details->segment_name . "_id as id, 
								(SUM(a.good_quantity) + SUM(a.bad_quantity)) AS total_items, 
								(SUM(a.total_amount)) AS total_amount 
							FROM
								is_" . $department_module_details->segment_name . "_detail a
							WHERE
								a." . $department_module_details->segment_name . "_id = '" . $request_summary->id . "' 
							AND 
								a.status NOT IN ('CANCELLED', 'DELETED')";

	$request_item_amount_total = $ci->db_spare_parts->query($request_item_amount_total_sql);
	$request_item_amount_total = $request_item_amount_total->result();						
	$request_item_amount_total = $request_item_amount_total[0];

	return $request_item_amount_total;
}

function get_items_total_amount($request_code) {
	
	$ci = ci();

	$ci->load->model('spare_parts_model');
	
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	// request_summary
	$request_summary_sql = "SELECT a.`request_summary_id` as id, a.* FROM
							is_request_summary a
						WHERE
						a.`request_code` = '" . $request_code . "'";

	$request_summary = $ci->db_spare_parts->query($request_summary_sql);
	$request_summary = $request_summary->result();		
	$request_summary = $request_summary[0];		

	$request_item_amount_total_sql = "SELECT 
								a.`request_summary_id` as id, 
								(SUM(a.`good_quantity`) + SUM(a.`bad_quantity`)) AS total_items, 
								(SUM(a.`total_amount`)) AS total_amount 
							FROM
								is_request_detail a
							WHERE
								a.`request_summary_id` = '" . $request_summary->id . "' 
							AND 
								a.status NOT IN ('CANCELLED', 'DELETED')";

	$request_item_amount_total = $ci->db_spare_parts->query($request_item_amount_total_sql);
	$request_item_amount_total = $request_item_amount_total->result();						
	$request_item_amount_total = $request_item_amount_total[0];

	return $request_item_amount_total;
}

function get_reprocessed_item_total_amount($request_code) {
	
	// 20150728
	// TODO : STILL UNDER DEVELOPMENT

	$ci = ci();

	$ci->load->model('spare_parts_model');
	
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	// request_summary
	$request_summary_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
							is_" . $department_module_details->segment_name . " a
						WHERE
						a.request_code = '" . $request_code . "'";

	$request_summary = $ci->db_spare_parts->query($request_summary_sql);
	$request_summary = $request_summary->result();		
	$request_summary = $request_summary[0];		

	$request_item_amount_total_sql = "SELECT 
								a." . $department_module_details->segment_name . "_id as id, 
								(SUM(a.good_quantity) + SUM(a.bad_quantity)) AS total_items, 
								(SUM(a.total_amount)) AS total_amount 
							FROM
								is_reprocessed_item a
							WHERE
								a.department_module_id = '" . $request_summary->id . "' 
							AND 
								a.request_id = '" . $request_summary->id . "' 
							AND
								a.action IN ('RETURN', 'CHARGE')
							AND
								a.status NOT IN ('CANCELLED', 'DENIED')";

	$request_item_amount_total = $ci->db_spare_parts->query($request_item_amount_total_sql);
	$request_item_amount_total = $request_item_amount_total->result();						
	$request_item_amount_total = $request_item_amount_total[0];

	return $request_item_amount_total;
}


function get_requester_details($id_number, $requester_type, $is_object = 0)
{
	$ci = ci();		
	$ci->load->model('human_relations_model');

	if ($requester_type == 'employee') {
		$requester_details = $ci->human_relations_model->get_employment_information_view_by_id($id_number);

		$is_employed = ($requester_details->is_employed == 1) ? 'Yes' : 'No';
		$email_address = ($requester_details->company_email_address == NULL) ? $requester_details->personal_email_address : $requester_details->company_email_address;
		$contact_number = ($requester_details->mobile_number == NULL) ? $requester_details->phone_number : $requester_details->mobile_number;

		$position_details = $ci->human_relations_model->get_position_by_id($requester_details->position_id);

		$department_details = $ci->human_relations_model->get_department_by_id($requester_details->department_id);

		$department_name = "N/A";
		if (!empty($department_details)) {
			$department_name = $department_details->department_name;
		}

		if ($is_object == 0) {
			$requester_details = "NAME: {$requester_details->complete_name}\nID NUMBER: {$id_number}\nDEPARTMENT: {$department_name}\nPOSITION: {$position_details->position_name}\nIS EMPLOYED: {$is_employed}\nEMAIL: {$email_address}\nCONTACT NUMBER: {$contact_number}\n";
		} else {
			$requester_details = array(
					'complete_name' => $requester_details->complete_name,
					'id_number' => $id_number,
					'department_name' => $department_name,
					'position_name' => $position_details->position_name,
					'is_employed' => $is_employed,
					'email_address' => $email_address,
					'contact_number' => $contact_number,
				);

			$requester_details = (object) ($requester_details);
		}
	}	

	return $requester_details;	
}


function check_mtr($request_code, $mtr_number)
{
	$ci = ci();		
	$ci->load->model('spare_parts_model');
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	// request_summary
	$request_summary_sql = "SELECT a." . $department_module_details->segment_name . "_id as id, a.* FROM
							is_" . $department_module_details->segment_name . " a
						WHERE
						a.request_code = '" . $request_code . "'";

	$request_summary = $ci->db_spare_parts->query($request_summary_sql);
	$request_summary = $request_summary->result();		
	$request_summary = $request_summary[0];				

	// check if mtr_number is already used
	$count_mtr_sql = "SELECT request_code FROM
							is_" . $department_module_details->segment_name . "
						WHERE
						mtr_number = '" . $mtr_number . "'";

	$count_mtr = $ci->db_spare_parts->query($count_mtr_sql);
	$count_mtr = $count_mtr->result();		
	//$count_mtr = $count_mtr[0];									

	return $count_mtr;
}

function assign_mtr($request_id, $request_code, $mtr_number)
{
	$ci = ci();		
	$ci->load->model('spare_parts_model');
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	$current_datetime = date("Y-m-d H:i:s");

	$update_sql = "UPDATE 
						is_". $department_module_details->segment_name ." 
					SET 
						update_timestamp = '{$current_datetime}', 
						mtr_number = '{$mtr_number}' 
					WHERE 
						". $department_module_details->segment_name ."_id = {$request_id}";

	$ci->db_spare_parts->query($update_sql);
	
	return true;					
}

function assign_po($request_id, $request_code, $mtr_number)
{
	$ci = ci();		
	$ci->load->model('spare_parts_model');
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	$current_datetime = date("Y-m-d H:i:s");

	$update_sql = "UPDATE 
						is_". $department_module_details->segment_name ." 
					SET 
						update_timestamp = '{$current_datetime}', 
						purchase_order_number = '{$mtr_number}' 
					WHERE 
						". $department_module_details->segment_name ."_id = {$request_id}";

	$ci->db_spare_parts->query($update_sql);
	
	return true;					
}

function return_items_to_process($request_id, $request_code)
{
	$ci = ci();		
	$ci->load->model('spare_parts_model');
	$ci->db_spare_parts = $ci->load->database('spare_parts', TRUE);

	// identify which module
	$module_code = substr($request_code, 0, 2);
	$department_module_details = $ci->spare_parts_model->get_department_module_by_code($module_code);

	// get all items to module detail table
	$get_items_sql = "SELECT a." . $department_module_details->segment_name . "_detail_id as detail_id, a.* 
						FROM 
							is_". $department_module_details->segment_name ."_detail a
						WHERE 
							a.". $department_module_details->segment_name ."_id = {$request_id}
						AND
							a.status = 'COMPLETED'";

	$query = $ci->db_spare_parts->query($get_items_sql);
	
	if(count($query->result_array()) > 0)
	{
		$items_to_return = $query->result_object();
		
		foreach ($items_to_return as $itr) {
			
			//insert to is_reprocessed_item
			$data = array(
					"department_module_id" =>  $department_module_details->department_module_id,
					"request_id" =>  $request_id,
					"request_detail_id" =>  $itr->detail_id,
					"charge_discount" =>  '0',
					"charge_discount_amount" =>  '0.00',
					"total_amount" =>  $itr->total_amount,
					"good_quantity" => $itr->good_quantity,  
					"bad_quantity" => $itr->bad_quantity, 
					"action" =>  'RETURN',
					"status" => 'PENDING'
				);

			$ci->spare_parts_model->insert_reprocessed_item($data);

			$latest_id = $ci->spare_parts_model->insert_id();

			// insert to tr_warehouse_return
			$data = array(
					"department_module_id" => $department_module_details->department_module_id,
					"transaction_number" => $request_code,
					"request_id" => $request_id,
					"reprocessed_item_id" => $latest_id,
					"return_status" => 'PENDING'
				);

			$ci->spare_parts_model->insert_warehouse_return($data);
		}

		return true;
	} else {
		return false;
	}						
}