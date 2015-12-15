<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_department_module_id()
{
	
	$ci = ci();
	
	$ci->db_human_relations = $ci->load->database("human_relations", TRUE);	
	$ci->db_mmpi = $ci->load->database("default", TRUE);	

	$department_details_sql = "SELECT * FROM rf_department WHERE url = '{$ci->uri->segment(1)}'";

	$department_details = $ci->db_human_relations->query($department_details_sql);
	$department_details = $department_details->result();		
	$department_details = $department_details[0];				

	$department_module_details_sql = "SELECT * FROM rf_department_module WHERE department_id = '{$department_details->department_id}' AND segment_name = '{$ci->uri->segment(2)}'";

	//var_dump($department_module_details_sql);

	$department_module_details = $ci->db_mmpi->query($department_module_details_sql);
	$department_module_details = $department_module_details->result();		
	$department_module_details = $department_module_details[0];				
	
	return $department_module_details->department_module_id;
}

function get_segment_name()
{
	$ci = ci();

	return $ci->uri->segment(2);	
}


function log_to_db($model_name, $id_number, $module_name, $table_name, $action, $details_before = NULL, $details_after = NULL, $remarks = NULL)
{
	$ci = ci();
	//$ci->load->model($model_name . '_model');

	$ci->db_database = $ci->load->database($model_name, TRUE);
	
	$details_before = json_encode($details_before);
	$details_after = json_encode($details_after);
	
	$tableis = "admin";
	
	if ($model_name == "default") {
		$tableis = "user";
	} 
	
	$sql_insert = "INSERT INTO tr_". $tableis ."_log(`id_number`, `module_name`, `table_name`, `action`, `details_before`, `details_after`, `remarks`)
					VALUES ('{$id_number}', '{$module_name}', '{$table_name}', '{$action}', '{$details_before}', '{$details_after}', '{$remarks}')";

	$ci->db_database->query($sql_insert);	

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
	} else if ($requester_type == 'branch') {

		$requester_details = $ci->human_relations_model->get_branch_by_id($id_number);

		$is_active = ($requester_details->is_active == 1) ? 'Yes' : 'No';
		
		$company_details = $ci->human_relations_model->get_company_by_id($requester_details->company_id);

		if ($is_object == 0) {
			$requester_details = "NAME: {$requester_details->branch_name}\nBRANCH ID: {$id_number}\nCOMPANY NAME: {$company_details->company_name}\nIS ACTIVE: {$is_active}\n";
		} else {
			$requester_details = array(
					'complete_name' => $requester_details->branch_name,
					'branch_id' => $id_number,					
					'company_name' => $company_details->company_name,
					'is_active' => $is_active,					
				);

			$requester_details = (object) ($requester_details);
		}

	}	

	return $requester_details;	
}

