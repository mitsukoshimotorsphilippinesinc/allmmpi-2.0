<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Information_technology extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db_information_technology = $this->load->database('information_technology', TRUE);
		$this->load->helper("systems_helper");

	}

	public function index()
	{
		
		$this->template->view('dashboard');
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
					SELECT * FROM `rf_repair_hardware` WHERE
					(REPLACE(`repair_hardware_name`,' ','') LIKE '%{$name}%');
				";
				$query = $this->db_information_technology->query($sql);
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
			$return_items[$itm->repair_hardware_id] = array(
				"item_id" => $itm->repair_hardware_id,
				"name" => $itm->repair_hardware_name,
				"description" => $itm->description,
			);
		}
		
		$this->return_json("ok","Ok.", array('items' => $return_items, 'keys' => $keys));
		return;

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

			$image_display = "";
			if ((empty($mem->image_filename)) || ($mem->image_filename == NULL) || (trim($mem->image_filename) == "")) {
				$image_display = "ni_". strtolower($mem->gender) .".png";
			} else {
				$image_display = $mem->image_filename;
			}


			$employees[$mem->employment_information_id] = array(
				"employment_information_id" => $mem->employment_information_id,
				"image_filename" => $image_display,
				"id_number" => $mem->id_number,
				"complete_name" => strtoupper($mem->complete_name),
				"company_email_address" => $mem->company_email_address,
				"department_name" => $department_name,
				"position" => $position_name,
				"gender" => $mem->gender,
				"is_employed" => $is_employed,
				"upload_url" => $upload_url = $this->config->item("media_url") . "/employees",
			);
		}

		$this->return_json("ok","Ok.", array('employees' => $employees, 'keys' => $keys));
		return;
		
	}


	public function get_branch()
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
			
		$where_branch_name = implode(' OR branch_name LIKE ', $escape_keys);		
		
		// check if its a string name or part of a name
		$escaped_search_key1 = $this->human_relations_model->escape($search_key);
		$escaped_search_key2 = $this->human_relations_model->escape('%'.$search_key.'%');
		$where = "is_active = 1 AND branch_name like {$where_branch_name}";
		$tmp_branches = $this->human_relations_model->get_branch($where, array('offset' => 0, 'rows' => 50), "branch_name ASC");
		
		
		$branches = array();
		if (count($tmp_branches) == 0)
		{
			// if these is reached then nothing are found.
			$this->return_json("error","Not found.", array('branches' => $branches, 'keys' => $keys));
			return;
		}

		foreach ($tmp_branches as $mem)
		{
			
			$department_name = "N/A";	
			$position_name = "N/A";
			// get company and department
			$company_details = $this->human_relations_model->get_company_by_id($mem->company_id);
			if (!empty($company_details)) {
				$company_name = $company_details->company_name;
			}
	
			if ($mem->is_active == 1)
				$is_active = "YES";
			else
				$is_active = "NO";

			$branches[$mem->branch_id] = array(
				"branch_id" => $mem->branch_id,				
				"branch_name" => $mem->branch_name,				
				"company_name" => $company_name,				
				"is_active" => $is_active
			);
		}

		$this->return_json("ok","Ok.", array('branches' => $branches, 'keys' => $keys));
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

}