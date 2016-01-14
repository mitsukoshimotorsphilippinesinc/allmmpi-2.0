<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cod extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('human_relations_model');
		$this->load->model('setting_model');

		$this->load->library('pager2');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{							
		$this->template->current_page = 'cod';

		// get latest active COD
		$where = "is_active = 1";
		$cod_details = $this->human_relations_model->get_cod($where, NULL, "insert_timestamp DESC");
		if (empty($cod_details))
			$cod_details = "";
		else 
			$cod_details = $cod_details[0];

		$this->template->cod_details = $cod_details;
		$this->template->view('cod/dashboard');
	}

	public function display_comments()
	{
		$cod_id = abs($this->input->post("_cod_id"));
		$comment = $this->input->post("_comment");

		// insert to am_announcement_message
		$data = array(
				"cod_id" => $cod_id,
				"from_id_number" => $this->employee->id_number,
				"message" => trim($comment)
			);

		$this->human_relations_model->insert_cod_message($data);

		$this->return_json("1", "Ok.");
	}

	public function get_cod_comments() {
		$cod_id = abs($this->input->post("_cod_id"));

		// get s4s messages
		$where = "cod_id = " . $cod_id . " AND (from_id_number = '" . $this->employee->id_number . "' OR to_id_number = '" . $this->employee->id_number . "')";
		$cod_message_details = $this->human_relations_model->get_cod_message($where, NULL, "cod_message_id");

		$html = "";
		if (count($cod_message_details) > 0) {
			foreach ($cod_message_details as $amd) {
				if ($amd->from_id_number == 'n/a') {
					$html .= "<div class='alert alert-success' style='border:1px solid;;margin-bottom:5px;'><strong>ADMIN: </strong>{$amd->message}</div>";
				} else {	
					if ($amd->is_removed == 0) {
						$html .= "<div class='alert alert' style='border:1px solid;;margin-bottom:5px'><strong>ME: </strong>{$amd->message}</div>";
					} else {
						$html .= "<div class='alert alert' style='border:1px solid;;margin-bottom:5px'><strong>ME: </strong><i style='color:#ff1100;'>Your message was removed by Admin.</i></div>";
					}	
				}	
			}
		}

		$this->return_json(1, 'Success', array('html' => $html));
		return;

	}

}
