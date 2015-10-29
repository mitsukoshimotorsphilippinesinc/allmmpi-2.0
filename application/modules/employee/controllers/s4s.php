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

		$records_per_page = 1;
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

					$html .= "<tr> 
								<td><a href='/employee/s4s/view/{$s->s4s_id}' target='_blank' class='link-elearn' data='{$s->s4s_id}'>{$s->pp_name}</a></td>
								<td>{$s->pp_description}</td>						
								<td>{$s->insert_timestamp}</td>
								<td>{$s->insert_timestamp}</td>
							</tr>";
				}
			}							

			$html .= "</tbody>
					</table>
					</div>
					</div>";
			
		}

		$this->return_json(1, 'Success', array('html' => $html, 'pagination' => $pagination, 'result_count' => $s4s_count . " RESULT/S"));
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
