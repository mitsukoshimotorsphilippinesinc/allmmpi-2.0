<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Form_request extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('human_relations_model');
		$this->load->library('pager');
		$this->db_dpr = $this->load->database('dpr', TRUE);
		$this->load->helper('breadcrumb_helper');
	}

	public function index()
	{

		$this->template->view('dashboard');
	}

	public function view_accountable_details($request_summary_id = 0)
	{
		$where = "request_summary_id = {$request_summary_id}";
		$record_summary = $this ->dpr_model->get_request_summary($where);
		$this->template->record_summary = $record_summary;
		$this->template->request_code = $record_summary[0]->request_code;
		
		$record_detail = $this ->dpr_model->get_request_detail($where,null,'request_detail_id DESC');
		$this->template->record_detail = $record_detail;

		$this->template->view('form_request/accountable_view_details');

	}

	public function view_non_accountable_details($request_summary_id = 0)
	{
		$where = "request_summary_id = {$request_summary_id}";
		$record_summary = $this ->dpr_model->get_request_summary($where);
		$this->template->record_summary = $record_summary;
		$this->template->request_code = $record_summary[0]->request_code;

		$record_detail = $this ->dpr_model->get_request_detail($where,null,'request_detail_id DESC');
		$this->template->record_detail = $record_detail;

		$this->template->view('form_request/non_accountable_view_details');

	}

	public function accountables()
	{

		$all_record = "";
		$where = "is_accountable = 1";
		$search_url = "";
		$search_status = trim($this->input->get("status_option"));
		$search_by = trim($this->input->get("txtsearch"));

		if (($search_status == "All") || ($search_status == "")){
			if (!($search_by) == ""){
				$where="request_code = '{$search_by}' and is_accountable = 1";	
			}
		}else{

			if (($search_by) == "") {
				$where="status = '{$search_status}' and is_accountable = 1";	
			}else{
				$where="status = '{$search_status}' and request_code = '{$search_by}' and is_accountable = 1";
			}
		}
		$config = array(
				'pagination_url' => "/dpr/form_request/accountables/",
				'total_items' => $this->dpr_model->get_request_summary_count($where),
				'per_page' => 10,
				'uri_segment' => 4,);
		
		$this->pager->set_config($config);
		
		$all_record = $this->dpr_model->get_request_summary($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			

		
		$this->template->all_record = $all_record;

		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_url = $search_url;

		$this->template->view('form_request/request_accountable_form');	


	}

	public function non_accountables()
	{
		$all_record = "";
		$where = "is_accountable = 0";
		$search_url = "";
		$search_status = trim($this->input->get("status_option"));
		$search_by = trim($this->input->get("txtsearch"));

		//var_dump($search_status);
		if (($search_status == "All") || ($search_status == "")){
			if (!($search_by) == ""){
				$where="request_code = '{$search_by}' and is_accountable = 0";	
			}
		}else{

			if (($search_by) == "") {
				$where="status = '{$search_status}' and is_accountable = 0";	
			}else{
				$where="status = '{$search_status}' and request_code = '{$search_by}' and is_accountable = 0";
			}
		}
		
		$config = array(
				'pagination_url' => "/dpr/form_request/non_accountables/",
				'total_items' => $this->dpr_model->get_request_summary_count($where),
				'per_page' => 10,
				'uri_segment' => 4,);
		
		$this->pager->set_config($config);
		
		$all_record = $this->dpr_model->get_request_summary($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			

		
		$this->template->all_record = $all_record;

		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_url = $search_url;
		$this->template->view('form_request/request_non_accountable_form');	
	}

	public function add_new_accountables()
	{
		$this->template->view('form_request/add_request_accountable_form');	
	}

	public function add_new_non_accountables()
	{
		$this->template->view('form_request/add_request_non_accountable_form');	
	}

	public function add_new_item()
	{
		$branch_id = $this->input->post('branch_id');
		$last_serial_number = $this->input->post('last_serial_number');
		$form_type_id = $this->input->post('form_type_id');
		$quantity = $this->input->post('quantity');
		$printing_press_id = $this->input->post('printing_press_id');
		$request_code_current = trim($this->input->post('request_code'));
		
		if ($request_code_current==""){

			$current_year = date("Y");

			$current_year = substr($current_year, 2);

			$where = "request_year = {$current_year}";
			$request_summary_last = $this->dpr_model->get_request_summary($where,null,'request_series DESC');
			//var_dump($request_summary_last[0]->request_series);
			if (count($request_summary_last) <= 0){
				$request_series = 1;
			}else{
				$request_series = $request_summary_last[0]->request_series + 1;
			}

			$current_month_day = date("md");
			$request_code = "TRD" . $current_month_day . $current_year . "-" . str_pad($request_series, 3,"0",STR_PAD_LEFT);

			$data_summary=array(
			'request_year' => $current_year,
			'request_series' => $request_series,
			'request_code' => $request_code,
			'is_accountable' => "1"
			);
			$this->dpr_model->insert_request_summary($data_summary);
			$last_insert_id = $this->dpr_model->insert_id();
		}else{
			$request_code = $request_code_current;
			$where = "request_code = '{$request_code}'";
			$request_summary_info = $this->dpr_model->get_request_summary($where);
			$last_insert_id = $request_summary_info[0]->request_summary_id;
		}

		$data_detail=array(
			'branch_id' => $branch_id,
			'last_serial_number' => $last_serial_number,
			'form_type_id' => $form_type_id,
			'quantity' => $quantity,
			'printing_press_id' => $printing_press_id,
			'request_summary_id' => $last_insert_id
			);
		$this->dpr_model->insert_request_detail($data_detail);

		$this->return_json("1","Add new item successfully.",array('request_code' => $request_code));
	}

	public function refresh_list_details()
	{
		$request_code = $this->input->post('request_code');
		$where = "request_code = '{$request_code}'";
		$request_summary_last = $this->dpr_model->get_request_summary($where);
		//var_dump($request_summary_last[0]->request_summary_id);

		$where = "request_summary_id = '{$request_summary_last[0]->request_summary_id}'";
		$request_summary_last = $this->dpr_model->get_request_detail($where);

       	$html = "";


       	foreach ($request_summary_last as $rsl) {
       						$branch_id = $rsl->branch_id;
       						$form_type_id = $rsl->form_type_id;
       						$printing_press_id = $rsl->printing_press_id;
       						$request_detail_id = $rsl->request_detail_id;

       						$last_series = $rsl->last_serial_number;
       						$quantity = $rsl->quantity;
       						$status = $rsl->status;

							$branch_info = $this->human_relations_model->get_branch_by_id($branch_id);
							$form_info = $this->dpr_model->get_form_by_id($form_type_id);
							$printing_press_info = $this->dpr_model->get_printing_press_by_id($printing_press_id);
       					   

       					 	$html .= "<tr>			
								<td>{$branch_info->branch_name}</td>
								<td>{$branch_info->tin}</td>
								<td>{$form_info->name}</td>
								<td>{$last_series}</td>
								<td>{$form_info->pieces_per_booklet}</td>
								<td>{$quantity}</td>		
								<td>{$printing_press_info->complete_name}</td>
								<td>{$status}</td>
								<td><a class = 'btn delete_item' data = '{$request_detail_id}'>Delete</a></td>
				  			</tr>";			

       					};
       					
       					$this->return_json("1","Add new item successfully.",array('html' => $html));
       			
	}

	public function delete_item()
	{
		$request_detail_id = $this->input->post('request_detail_id');
		
		$where = "request_detail_id = '{$request_detail_id}'";
		$delete_item = $this->dpr_model->delete_request_detail($where);

		$this->return_json("1","Delete Item Successfully.");
	}

	public function refresh_list_details_na()
	{
		$request_code = $this->input->post('request_code');
		$where = "request_code = '{$request_code}'";
		$request_summary_last = $this->dpr_model->get_request_summary($where);

		$where = "request_summary_id = '{$request_summary_last[0]->request_summary_id}'";
		$request_summary_last = $this->dpr_model->get_request_detail($where);

       	$html = "";


       	foreach ($request_summary_last as $rsl) {
       						$form_type_id = $rsl->form_type_id;
       						$printing_press_id = $rsl->printing_press_id;
       						$request_detail_id = $rsl->request_detail_id;

       						$last_series = $rsl->last_serial_number;
       						$quantity = $rsl->quantity;
       						$status = $rsl->status;

							$form_info = $this->dpr_model->get_form_by_id($form_type_id);
							$printing_press_info = $this->dpr_model->get_printing_press_by_id($printing_press_id);
       					   

       					 	$html .= "<tr>			
								<td>{$form_info->name}</td>
								<td>{$last_series}</td>
								<td>{$form_info->pieces_per_booklet}</td>
								<td>{$quantity}</td>		
								<td>{$printing_press_info->complete_name}</td>
								<td>{$status}</td>
								<td><a class = 'btn delete_item' data = '{$request_detail_id}'>Delete</a></td>
				  			</tr>";			

       					};
       					
       					$this->return_json("1","Add new item successfully.",array('html' => $html));
       			
	}

	public function add_new_item_na()
	{
		$last_serial_number = $this->input->post('last_serial_number');
		$form_type_id = $this->input->post('form_type_id');
		$quantity = $this->input->post('quantity');
		$printing_press_id = $this->input->post('printing_press_id');
		$request_code_current = trim($this->input->post('request_code'));
		
		if ($request_code_current==""){

			$current_year = date("Y");

			$current_year = substr($current_year, 2);

			$where = "request_year = {$current_year}";
			$request_summary_last = $this->dpr_model->get_request_summary($where,null,'request_series DESC');
			//var_dump($request_summary_last[0]->request_series);
			if (count($request_summary_last) <= 0){
				$request_series = 1;
			}else{
				$request_series = $request_summary_last[0]->request_series + 1;
			}

			$current_month_day = date("md");
			$request_code = "TRD" . $current_month_day . $current_year . "-" . str_pad($request_series, 3,"0",STR_PAD_LEFT);

			$data_summary=array(
			'request_year' => $current_year,
			'request_series' => $request_series,
			'request_code' => $request_code,
			'is_accountable' => "0"
			);
			$this->dpr_model->insert_request_summary($data_summary);
			$last_insert_id = $this->dpr_model->insert_id();
		}else{
			$request_code = $request_code_current;
			$where = "request_code = '{$request_code}'";
			$request_summary_info = $this->dpr_model->get_request_summary($where);
			$last_insert_id = $request_summary_info[0]->request_summary_id;
		}

		$data_detail=array(
			'last_serial_number' => $last_serial_number,
			'form_type_id' => $form_type_id,
			'quantity' => $quantity,
			'printing_press_id' => $printing_press_id,
			'request_summary_id' => $last_insert_id
			);
		$this->dpr_model->insert_request_detail($data_detail);

		$this->return_json("1","Add new item successfully.",array('request_code' => $request_code));
	}

	public function view_update_accountable()
	{
		$request_detail_id = $this->input->post('request_detail_id');
		$where = "request_detail_id = '{$request_detail_id}'";
		$request_detail_update = $this->dpr_model->get_request_detail($where);

		$html = "
		<table id = 'update_list' class='table table-striped table-bordered'>
		<thead>
			<tr>			
				<th>Send ATP</th>
				<th>Received ATP</th>
				<th>Faxed to Printer</th>
				<th>Rec From Printer</th>
				<th>Send For Stamping</th>
				<th>Rec from Stamping</th>		
				<th>Date Delivered</th>
			</tr>
		</thead>
		<tbody>";
		
		foreach ($request_detail_update as $rdu) {
			$send_atp = $rdu->send_atp;
			$received_atp = $rdu->receive_atp;
			$faxed_to_printer = $rdu->faxed_to_printer;
			$received_from_printer = $rdu->received_from_printer;
			$send_for_stamping = $rdu->send_for_stamping;
			$received_from_stamping = $rdu->received_from_stamping;
			$date_delivered = $rdu->date_delivered;

			if ($send_atp == "0000-00-00 00:00:00"){
				$send_atp = "N/A";
			}
			if ($received_atp == "0000-00-00 00:00:00"){
				$received_atp = "N/A";
			}
			if ($faxed_to_printer == "0000-00-00 00:00:00"){
				$faxed_to_printer = "N/A";
			}
			if ($received_from_printer == "0000-00-00 00:00:00"){
				$received_from_printer = "N/A";
			}
			if ($send_for_stamping == "0000-00-00 00:00:00"){
				$send_for_stamping = "N/A";
			}
			if ($received_from_stamping == "0000-00-00 00:00:00"){
				$received_from_stamping = "N/A";
			}
			if ($date_delivered == "0000-00-00 00:00:00"){
				$date_delivered = "N/A";
			}

			$html .="<tr>			
				<td>{$send_atp}</td>
				<td>{$received_atp}</td>
				<td>{$faxed_to_printer}</td>
				<td>{$received_from_printer}</td>
				<td>{$send_for_stamping}</td>
				<td>{$received_from_stamping}</td>
				<td>{$date_delivered}</td>
			</tr>";
		}
			
		$html .= "</tbody></table>";

		$sql = "SELECT COLUMN_NAME as col_name, ORDINAL_POSITION, COLUMN_DEFAULT
				FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE TABLE_SCHEMA = 'dpr' AND TABLE_NAME = 'tr_request_detail'
				AND DATA_TYPE = 'timestamp' 
				AND COLUMN_DEFAULT = '0000-00-00 00:00:00'
				AND COLUMN_NAME NOT IN ('date_delivered', 'update_timestamp')
				ORDER BY ORDINAL_POSITION;";

		$detail_update_column = $this->db_dpr->query($sql);
		$detail_update_column = $detail_update_column->result(); 
		
		$has_pending = 0;

		foreach ($detail_update_column as $duc){
			
			$sql_test = "SELECT ". $duc->col_name ." AS timestamp_col FROM tr_request_detail WHERE request_detail_id = {$request_detail_id}";
			$detail_test = $this->db_dpr->query($sql_test);
			$detail_test = $detail_test->result(); 
			$detail_test = $detail_test[0]; 

			if ($detail_test->timestamp_col == '0000-00-00 00:00:00') {
				$has_pending = 1;
				break;
			}

		}

		if ($has_pending == 1)
			$current_col_name = $duc->col_name;
		else
			$current_col_name = "NONE";

		//var_dump($current_col_name);
		$current_col_name_unformat = $current_col_name;
		$current_col_name = strtoupper(str_replace("_", " ", $current_col_name));
		
		$this->return_json("1","View Update...",array('html' => $html,'current_col_name' => $current_col_name, 'current_col_name_unformat' => $current_col_name_unformat, 'request_detail_status' => $request_detail_update[0]->status));
	}

	public function proceed_update_request_detail()
	{
		$request_detail_id = $this->input->post('request_detail_id');
		$current_col_name_unformat = $this->input->post('current_col_name_unformat');
		$where = "request_detail_id = '{$request_detail_id}'";
		$data = array(
			$current_col_name_unformat => $current_datetime = date("Y-m-d H:i:s"));

		if ($current_col_name_unformat == "received_from_stamping") {
			$data['status'] = "COMPLETED";
		}

		$this->dpr_model->update_request_detail($data,$where);

		$this->return_json("1","Update Successfully");
	}
	public function update_summary_request()
	{
		$request_summary_id = $this->input->post('request_summary_id');
		$where = "request_summary_id = '{$request_summary_id}'";
		$data = array(
			'status' => "COMPLETED");

		$this->dpr_model->update_request_summary($data,$where);	

		$this->dpr_model->update_request_detail($data,$where);	
		
		$this->return_json("1","Update Successfully");

	}

	public function cancel_update_summary_request()
	{
		$request_summary_id = $this->input->post('request_summary_id');
		$where = "request_summary_id = '{$request_summary_id}'";
		$data = array(
			'status' => "CANCELLED");

		$this->dpr_model->update_request_summary($data,$where);	

		$this->dpr_model->update_request_detail($data,$where);	
		
		$this->return_json("1","Update Successfully");

	}
}	