<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class S4s extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('asset_model');
		$this->load->model('setting_model');

		$this->load->library('pager2');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{							
		$this->template->current_page = 's4s';
		$this->template->view('s4s/dashboard');
	}

	
	public function get_s4s_list()
	{
		$page = $this->input->post('page');
		$search_data = trim($this->input->post('search_data'));

		if(empty($page)) $page = 1;

		$where = "company_id IN (0, {$this->employee->company_id}) AND branch_id IN (0, {$this->employee->branch_id}) AND department_id IN (0, {$this->employee->department_id}) AND position_id IN (0, {$this->employee->position_id})";

		$add_where = "";
		if (strlen($search_data) > 0) {
			$add_where = " AND (pp_name like '%{$search_data}%' OR pp_description LIKE '%{$search_data}%')";
			$where .= $add_where;
		}

		$s4s_count = $this->human_relations_model->get_s4s_count($where);

		$records_per_page = 30;
		$offset = ($page - 1) * $records_per_page;
        $offset = ($offset < 0 ? 0 : $offset);

		$this->pager2->set_config(array(
            'total_items' => $s4s_count,
            'per_page' => $records_per_page,
            'offset' => $offset,
            'adjacents' => 1,
            'type' => 'ajax'
        ));

        $pagination = $this->pager2->create_links();
       
		$limit = array("rows"=>$records_per_page,"offset"=>$offset);
		$s4s = $this->human_relations_model->get_s4s($where, $limit,'insert_timestamp DESC');

		$html = "";

		if (count($s4s) > 0) {

			foreach($s4s as $a) {			

				$html = "<div class='ui-element'>
						<div>
							<table class='table table-condensed table-striped table-bordered' style='font-size:12px;'>
							<thead>
								<tr>
									<th>Policy / Procedure Name</th>
									<th>Description</th>					
									<th>Date Created</th>
									<th>Date Accepted</th>
								</tr>
							</thead>
							<tbody>";

				if(empty($s4s)) {
					$html .= "<tr><td colspan='4' style='text-align:center;''><strong>No Result</strong></td></tr>";
				} else {
					foreach($s4s as $s) {

						$where = "id_number = {$this->employee->id_number} AND s4s_id = {$s->s4s_id} AND is_accepted = 1";

						// get acceptance_date
						$acceptance_details = $this->human_relations_model->get_s4s_acceptance($where);

						$acceptance_date = "0000-00-00 00:00:00";
						if (count($acceptance_details) > 0) {
							$acceptance_date = $acceptance_details[0]->insert_timestamp;
						} 

						$html .= "<tr> 
									<td><a href='#' class='link-s4s' data='{$s->s4s_id}'>{$s->pp_name}</a></td>
									<td>{$s->pp_description}</td>						
									<td>{$s->insert_timestamp}</td>
									<td><span  class='acceptance-date-{$s->s4s_id}'>{$acceptance_date}</span></td>
								</tr>";
					}
				}							

				$html .= "</tbody>
						</table>
						</div>
						</div>";
				
			}
		} else {
			$html .= "<center><h3>No Policy/Procedure Found.</h3><center>";
		}	

		$this->return_json(1, 'Success', array('html' => $html, 'pagination' => $pagination, 'result_count' => $s4s_count . " RESULT/S"));
		return;
	}

	public function check_acceptance()
	{
		$s4s_id = $this->input->post("s4sId");

		$where = "id_number = {$this->employee->id_number} AND s4s_id = {$s4s_id} AND is_accepted = 1";
				
		$s4s_acceptance_details = $this->human_relations_model->get_s4s_acceptance($where);

		$html = "";

		if (empty($s4s_acceptance_details)) {
			// not yet accepted
			$html .= "<h3>Terms and Conditions</h3>
					<p>
						Put terms and conditions here...
					</p>";
			$is_accepted = 0;
		} else {
			$is_accepted = 1;
		}

		$title = "Acceptance of Policy";

		$this->return_json(1, 'Success', array('html' => $html, 'is_accepted' => $is_accepted, 'title' => $title));
		return;
	}

	public function log_acceptance()
	{
		$s4s_id = $this->input->post("s4s_id");
		$is_accepted = $this->input->post("is_accepted");

		$data = array(
				"id_number" => $this->employee->id_number,
				"s4s_id" => $s4s_id,
				"is_accepted" => $is_accepted,
			);

		$this->human_relations_model->insert_s4s_acceptance($data);

		if ($is_accepted == 1) {
			$html = "You may now view your S4S. Thank you";
		} else {
			$html = "Sorry, you cannot proceed without accepting the Terms and Conditions. Thank you";
		}	

		$created_id = $this->human_relations_model->insert_id();

		$s4s_acceptance_details = $this->human_relations_model->get_s4s_acceptance_by_id($created_id);

		$date_accepted = $s4s_acceptance_details->insert_timestamp;

		$title = "Acceptance of Policy";

		$this->return_json(1, 'Success', array('html' => $html, 'title' => $title, 'date_accepted' => $date_accepted));
		return;

	}

	public function view($course_id = 0) {

		// get course_id data and assets
		$course_details = $this->human_relations_model->get_s4s_by_id($course_id);

		$where = "s4s_id = " . $course_id;
		$asset_details = $this->human_relations_model->get_s4s_asset($where);

		$this->template->course_details = $course_details;
		$this->template->asset_details = $asset_details;
		$this->template->current_page = 's4s';
		$this->template->view('s4s/display_s4s');
	}	
}
