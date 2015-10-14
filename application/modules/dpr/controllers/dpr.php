<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dpr extends Admin_Controller {


	public function index()
	{
		$this->template->view('dashboard');
	}

	public function form_request()
	{
		$this->template->view('form_request/dashboard');
	}

	public function accountables()
	{
		$this->template->view('form_request/request_accountable_form');	
	}

	public function view_details() {
		$request_detail_id = $this->input->post("request_detail_id");
		$request_code = $this->input->post("request_code");

		$request_summary_details = $this->dpr_model->get_request_summary_by_request_code($request_code);		
		$request_detail_details = $this->dpr_model->get_request_detail_by_id($request_detail_id);		

		if (empty($request_summary_details)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$request_code}].</p>";
			$title = "Error: View Details";

			$this->return_json("0","Request Code not found in DB", array("html" => $html, "title" => $title));	
			
		} else {
		
			$data = array(
				'request_summary_details' => $request_summary_details,
				'request_detail_details' => $request_detail_details
			);
		
			$html = $this->load->view("dpr/view_details",$data, true);
			 
			$title = "View Details :: " . $request_code;
			$this->return_json("1","View Details of Request", array("html" => $html, "title" => $title));
			
		}
			
		return;
	}

}