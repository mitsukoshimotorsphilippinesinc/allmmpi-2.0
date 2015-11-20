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
	$ci->load->model($model_name . '_model');

	$details_before = json_encode($details_before);
	$details_after = json_encode($details_after);
	
	$add_result_log_data = array(
		'id_number' => $id_number,
		'module_name' => $module_name,
		'table_name' => $table_name,
		'action' => $action,
		'details_after' => $details_after,
		'remarks' => $remarks,
	);

	$ci->user_model->insert_log($model_name, $add_result_log_data);
}