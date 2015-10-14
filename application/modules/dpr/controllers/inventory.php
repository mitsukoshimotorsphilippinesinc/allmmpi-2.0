<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
	
		$this->load->model('dpr_model');
		$this->load->model('human_relations_model');
		$this->load->library('pager');				
		$this->load->helper("breadcrumb_helper");

	}

	public function index()
	{		
		$this->template->view('inventory/dashboard');	
	}

	public function main()
	{		
		$search_status = trim($this->input->get("search_status"));
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";
		$count_is = 0;
		$transfers = "";
		$request_search_by = "";		

		if ($search_by == 'name') {
			$request_search_by = "id_number";

			// get all personal_information_id in pm_personal_information
			$where = "complete_name LIKE '%" . $search_text . "%'";
			$personal_information_details = $this->human_relations_model->get_personal_information($where, NULL, NULL, "personal_information_id, complete_name");

			$where_id_numbers = "";
			$count_id_num = 0;
			// get the id_numbers within the personal_information_id results above
			if (count($personal_information_details) > 0) {
				foreach ($personal_information_details as $pid) {
					
					$employment_information_details = $this->human_relations_model->get_employment_information("personal_information_id = ". $pid->personal_information_id);
					
					if (count($employment_information_details) > 0) {
						foreach ($employment_information_details as $eid) {
							if ($count_id_num == 0)
								$where_id_numbers = "'" . $eid->id_number . "'";
							else 		
								$where_id_numbers = $where_id_numbers . ", '" . $eid->id_number . "'";

							$count_id_num++;
						}
					}
				}	
			}
		} 

		if (empty($search_status)) {			
			$where = "";
		} else {

			if ($search_status == 'ALL') {				
				$where = "";
			} else {
				$where = "status = '". $search_status ."'";
			}
				
			if ($where != NULL) {
				if ($search_by == 'name')
					$where = $where . " AND ". $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = $where . " AND ". $search_by ." LIKE '%" . $search_text . "%'";
			} else {
				if ($search_by == 'name')
					$where = $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = $search_by ." LIKE '%" . $search_text . "%'";
			}
		}	

		// set pagination data
		$config = array(
				'pagination_url' => "/dpr/inventory/main/",
				'total_items' => $this->dpr_model->get_booklet_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		$inventory = $this->dpr_model->get_booklet($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "booklet_series, insert_timestamp");			
		
		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->inventory = $inventory;
		
		$this->template->view('inventory/main');	
	}


	public function  assign_to_branch($booklet_id = 0)
	{
		$booklet_details = $this->dpr_model->get_booklet_by_id($booklet_id);	


		if ($booklet_details->branch_id == 0) {
			// non-accouontable			
		} else {
			// accountable forms

		}

		$where = "";
		$branch_rack_location_view_details = $this->dpr_model->get_branch_rack_location_view($where);

		$this->template->branch_rack_location_view_details = $branch_rack_location_view_details;
		$this->template->booklet_details = $booklet_details;
		$this->template->view('inventory/assign_to_branch');
	}
}
