<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class S4s extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('asset_model');
		$this->load->model('setting_model');

		$this->db_human_relations = $this->load->database('human_relations', TRUE);

		$this->load->library('pager2');
	}
	
	public function index($segment)
	{		
		$this->page($segment);
	}
	
	public function page($segment, $course_id = 0)
	{							
		$this->template->current_page = 's4s';
		$this->template->segment = $segment;
		if ($segment == "view") {
			$this->view($course_id);
		} else {
			$this->template->view('s4s/dashboard');
		}
	}

	
	public function get_s4s_list()
	{
		$page = $this->input->post('page');
		$segment = $this->input->post('segment');
		$search_data = trim($this->input->post('search_data'));

		if(empty($page)) $page = 1;

		// list of id number that can override s4s
		$in_override_list = strpos($this->setting->s4s_override_id_numbers, $this->employee->id_number);

		// get department_name
		$department_details = $this->human_relations_model->get_department_by_url($segment);

		$where = "is_active_s4s = 1 AND department_id = {$department_details->department_id}";

		if ($in_override_list === FALSE) {
		
			//$where = "company_id IN (0, {$this->employee->company_id}) AND branch_id IN (0, {$this->employee->branch_id}) AND department_id IN (0, {$this->employee->department_id}) AND position_id IN (0, {$this->employee->position_id})";
			$where .= " AND (position_id = {$this->employee->position_id} OR parent_position_id = {$this->employee->position_id}) AND is_active_s4s_position = 1";

			$add_where = "";
			if (strlen($search_data) > 0) {
				$add_where = " AND (pp_name like '%{$search_data}%' OR pp_description LIKE '%{$search_data}%' OR reference_number LIKE '%{$search_data}%')";
				$where .= $add_where;
			}

		}
	
		//$s4s_count = $this->human_relations_model->get_s4s_position_view_count($where);
		// replacement [start]
		$sql = "SELECT 
					*
				FROM 
				 	el_s4s_position_view 
				WHERE	
					{$where}
				GROUP BY
					pp_name
				ORDER BY 
				 	priority_order";

		$query = $this->db_human_relations->query($sql);
		$s4s_view_details = $query->result();			
		$query->free_result();

		$s4s_count = count($s4s_view_details);	
		// replacement [end]

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
		
		//$s4s = $this->human_relations_model->get_s4s_position_view($where, $limit, 'priority_order');
		// replacement [start]
		$sql = "SELECT 
					*
				FROM 
				 	el_s4s_position_view 
				WHERE 	
				 	{$where}
				GROUP BY
					pp_name				
				ORDER BY 
				 	priority_order
				LIMIT 
					{$this->pager2->per_page} 
				OFFSET 
					{$offset}";

		$query = $this->db_human_relations->query($sql);
		$s4s = $query->result();			
		$query->free_result();	
		// replacement [end]

		$html = "";

		if (count($s4s) > 0) {

			foreach($s4s as $a) {			

				$html = "<div class='ui-element'>
						<div>
							<table class='table table-condensed table-striped table-bordered' style='font-size:12px;'>
							<thead>
								<tr>
									<th>Reference Number</th>
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
									<td><a href='#' class='link-s4s' data='{$s->s4s_id}'>{$s->reference_number}</td>
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
			$html .= "<center><h3 class='alert'>No Policy/Procedure Found.</h3><center>";
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

	public function display_comments()
	{
		$s4s_id = $this->input->post("_s4s_id");
		$comment = $this->input->post("_comment");

		// insert to am_announcement_message
		$data = array(
				"s4s_id" => $s4s_id,
				"from_id_number" => $this->employee->id_number,
				"message" => trim($comment)
			);

		$this->human_relations_model->insert_s4s_message($data);

		$this->return_json("1", "Ok.");
	}

	public function get_s4s_comments() {
		$s4s_id = $this->input->post("_s4s_id");

		// get s4s messages
		$where = "s4s_id = " . $s4s_id . " AND (from_id_number = '" . $this->employee->id_number . "' OR to_id_number = '" . $this->employee->id_number . "')";
		$s4s_message_details = $this->human_relations_model->get_s4s_message($where, NULL, "s4s_message_id");

		$html = "";
		if (count($s4s_message_details) > 0) {
			foreach ($s4s_message_details as $amd) {
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
