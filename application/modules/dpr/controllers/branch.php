<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Branch extends Admin_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('dpr_model');
		$this->load->library('pager');			
		$this->load->helper("breadcrumb_helper");	

		$this->db_dpr = $this->load->database('dpr', TRUE);

	}

	public function index()
	{
		$this->template->view('/branch/dashboard');
	}

	public function releasing_of_form()
	{
		$all_record = "";
		$where = "";
		$search_url = "";
		$search_status = trim($this->input->get("branch_option"));
		$search_by = trim($this->input->get("tr_number"));

		if (($search_status == "0") ||  ($search_status == "")) {
			if (!($search_by) == ""){
				$where = "tr_number = '{$search_by}'";	
			}else{
				$where = "";
			}
		}else if (!($search_status == "0")) {
			if (!($search_by) == ""){
				$where = "branch_id = '{$search_status}' and tr_number = '{$search_by}'";	
			}else{
				$where = "branch_id = '{$search_status}'";
			}
		}
		//var_dump($where);
		$config = array(
				'pagination_url' => "/dpr/branch/releasing/",
				'total_items' => $this->dpr_model->get_release_summary_count($where),
				'per_page' => 10,
				'uri_segment' => 4,);
		
		$this->pager->set_config($config);
		
		$all_record = $this->dpr_model->get_release_summary($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "release_summary_id ASC");
		//var_dump($all_record);
		$this->template->all_record = $all_record;
		
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_url = $search_url;

		$this->template->view('/branch/transaction/releasing');
	}

	public function add_new_tr()
	{
		$this->template->view('dpr/branch/transaction/add_tr');	
	}

	public function add_new_booklet()
	{
		$branch_id = $this->input->post('branch_id');
		$form_type_id = $this->input->post('form_type_id');
		$tr_number = $this->input->post('tr_number');
		$booklet_number_from = $this->input->post('booklet_number_from');
		$booklet_number_to = $this->input->post('booklet_number_to');
		$borrowed_branch_id = $this->input->post('borrowed_branch_id');
		$is_borrowed = $this->input->post('check_borrowed');


		$where = "tr_number = {$tr_number}";
		$release_summary_last = $this->dpr_model->get_release_summary($where,null,'release_summary_id DESC');

		$booklet_code = str_pad($form_type_id, 2,"0",STR_PAD_LEFT) . str_pad($branch_id, 3,"0",STR_PAD_LEFT);
		$where = "(booklet_series between '{$booklet_number_from}' and '{$booklet_number_to}') and booklet_code ='{$booklet_code}'";
		$booklet_inventory = $this->dpr_model->get_booklet($where,null,'booklet_id DESC');

		if (count($booklet_inventory) <= 0){
			$this->return_json("0","Not enough inventory.");			
			return;
		}

		if (count($release_summary_last) <= 0){
			//var_dump($release_summary_last);
			$data_summary=array(
			'tr_number' => $tr_number,
			'branch_id' => $branch_id,
			);
			$this->dpr_model->insert_release_summary($data_summary);
			$last_insert_id = $this->dpr_model->insert_id();
		}else{
			$last_insert_id = $release_summary_last[0]->release_summary_id;
		}

		//var_dump($where);
		foreach ($booklet_inventory as $bi){
			//var_dump($bi->booklet_id);
			$data_detail=array(
			'release_summary_id' => $last_insert_id,
			'booklet_id' => $bi->booklet_id,
			'is_borrowed' => $is_borrowed,
			'borrowed_branch_id' => $borrowed_branch_id,
			'remarks' => ""

			);
			$this->dpr_model->insert_release_detail($data_detail);
		}

		$this->return_json("1","Add new booklet successfully.");
	}

	public function refresh_list_details()
	{

		$tr_number = $this->input->post('tr_number');
		$where = "tr_number = '{$tr_number}'";

		$release_summary_last = $this->dpr_model->get_release_summary($where);
		//var_dump($release_summary_last[0]->release_summary_id);

		$where1 = "release_summary_id = '{$release_summary_last[0]->release_summary_id}'";
		
		$release_detail_list = $this->dpr_model->get_release_detail($where1,null,'release_detail_id DESC');

		$html = "";


       	foreach ($release_detail_list as $rdl) {

			$booklet_id = $rdl->booklet_id;
			$borrowed_by = $rdl->borrowed_branch_id;
			$release_detail_id = $rdl->release_detail_id;
			$booklet_inventory = $this->dpr_model->get_booklet_by_id($booklet_id);

			$form_type_id = substr($booklet_inventory->booklet_code, 0,2);
//       						var_dump($form_type_id);
			$form_info = $this->dpr_model->get_form_type_by_id($form_type_id);
			$branch_info = $this->human_relations_model->get_branch_by_id($borrowed_by);
			$borrowed_name = "";
			if (empty($branch_info)){
				$borrowed_name = "";
			}else{
				$borrowed_name = "{$branch_info->branch_name}";
			}	
		 	$html .= "<tr>			
			<td>{$form_info->name}</td>
			<td>{$booklet_inventory->booklet_number}</td>
			<td>{$booklet_inventory->series_from}</td>
			<td>{$booklet_inventory->series_to}</td>
			<td>$borrowed_name</td>		
			<td>{$rdl->remarks}</td>
			<td><a class = 'btn delete_item' data = '{$release_detail_id}'>Delete</a></td>
			</tr>";

		};

		$this->return_json("1","Add new Booklet successfully.",array('html' => $html));

	}

	public function delete_booklet()
	{
		$release_detail_id = $this->input->post('release_detail_id');
		
		$where = "release_detail_id = '{$release_detail_id}'";
		$delete_item = $this->dpr_model->delete_release_detail($where);

		$this->return_json("1","Delete booklet Successfully.");
	}

	public function cancel_update_summary_release()
	{
		$release_summary_id = $this->input->post('release_summary_id');
		$where = "release_summary_id = '{$release_summary_id}'";
		$data = array(
			'status' => "CANCELLED"
		);
	
		$this->dpr_model->update_release_summary($data,$where);	
		
		$this->return_json("1","Update Successfully");

	}

	public function view_release_detail($release_summary_id = 0)
	{
		$where = "release_summary_id = {$release_summary_id}";
		$record_summary = $this->dpr_model->get_release_summary($where);

		$where = "release_summary_id = '{$record_summary[0]->release_summary_id}'";
		$record_detail = $this->dpr_model->get_release_detail($where,null,'release_detail_id DESC');

		$where = "branch_id = '{$record_summary[0]->branch_id}'";
		$branch_list=$this->human_relations_model->get_branch($where,null,'branch_name ASC');

		$this->template->tr_number = $record_summary[0]->tr_number;
		$this->template->branch_name = $branch_list[0]->branch_name;
		$this->template->record_detail = $record_detail;
		$this->template->release_summary_id = $release_summary_id;

		$this->template->view('branch/transaction/view_release_detail');

	}
	public function monitoring_of_form()
	{
		$this->template->view('branch/monitoring/monitoring_view');		
	}
}