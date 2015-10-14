<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voucher_types extends Systems_Controller 
{
	
	
	function __construct() 
	{
  		parent::__construct();
		//set navigation
		$this->set_navigation('voucher_types');
		// load pager library
		$this->load->library('pager');
		//load models used by this controller
		$this->load->model('vouchers_model');
		
	}
	
	public function index() 
	{ 
		$this->view();
	}
	
	public function view()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/voucher_types/index/',
		    'total_items' => $this->vouchers_model->get_is_voucher_types_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		
		$this->template->voucher_types = $this->vouchers_model->get_is_voucher_types($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('voucher_types/list');

	}
	
	public function add()	{
	
		$data = "";
		$html = $this->load->view('admin/voucher_types/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_add() {
		$_voucher_type_code = trim($this->input->post('_voucher_type_code'));
		$_voucher_type_name = trim($this->input->post('_voucher_type_name'));
		$_description = $this->input->post('_description');
			
		$_voucher_type_code = strtoupper($_voucher_type_code);
		$_voucher_type_name = strtoupper($_voucher_type_name);
		$_description = strtoupper($_description);
		
		// check if voucher_type_code exists in table
		$voucher_type_code = $this->vouchers_model->get_is_voucher_type_by_code($_voucher_type_code);
		
		if (count($voucher_type_code) > 0)  {
			// existing code
			$html = "<p>
						<label>Sorry, the Code already exists. See the details below:</label>
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Code</strong></label></td>
									<td><label style='color:#990000;'>{$voucher_type_code->code}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Name</strong></label></td>
									<td><label style='color:#990000;'>{$voucher_type_code->name}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Description</strong></label></td>
									<td><label style='color:#990000;'>{$voucher_type_code->description}</label></td>		
								</tr>					
							</tbody>
						</table>
					</p>";
					
			echo json_encode(array("status"=>"0","html"=>$html));
			
		} else { 					
			$html = "<p><label>You are about to add a new Voucher Type with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:100px;'><label><strong>Code</strong></label></td>
								<td><label class=''>{$_voucher_type_code}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Name</strong></label></td>
								<td><label class=''>{$_voucher_type_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Description</strong></label></td>
								<td><label class=''>{$_description}</label></td>		
							</tr>					
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			echo json_encode(array("status"=>"1","html"=>$html));
		}
		
		return;
		
	}
	
	public function add_voucher_type() {
		$_voucher_type_code = $this->input->post('_voucher_type_code');
		$_voucher_type_name = $this->input->post('_voucher_type_name');
		$_description = $this->input->post('_description');

		$_voucher_type_code = strtoupper($_voucher_type_code);
		$_voucher_type_name = strtoupper($_voucher_type_name);
		$_description = strtoupper($_description);
	
		$data = array(
			'code' => $_voucher_type_code,
			'name' => $_voucher_type_name,
			'description' => $_description
		);
		$this->vouchers_model->insert_is_voucher_types($data);
		
		$table_id = $this->vouchers_model->insert_id();
		
		//logging of action
		$details_after = array('id' => $table_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_vouchers_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'MEMBER VOUCHER TYPES',
			'table_name' => 'cm_member_voucher_types',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $add_vouchers_log_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}

	public function edit()
	{
		$_voucher_type_id = $this->input->post('_voucher_type_id');
				
		$voucher_type = $this->vouchers_model->get_is_voucher_type_by_id($_voucher_type_id);
	
		$data = array(
			'voucher_type' => $voucher_type
		);

		$html = $this->load->view('admin/voucher_types/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;				
	}
	
	public function confirm_edit() {
		$_voucher_type_code = $this->input->post('_voucher_type_code');
		$_voucher_type_name = $this->input->post('_voucher_type_name');
		$_description = $this->input->post('_description');
		
		$_voucher_type_code = strtoupper($_voucher_type_code);
		$_voucher_type_name = strtoupper($_voucher_type_name);
		$_description = strtoupper($_description);
			
		$html = "<p><label>You are about to edit a Voucher Type with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Code</strong></label></td>
							<td><label class=''>{$_voucher_type_code}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_voucher_type_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Description</strong></label></td>
							<td><label class=''>{$_description}</label></td>		
						</tr>					
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_voucher_type() {
		
		$_voucher_type_code = $this->input->post('_voucher_type_code');
		$_voucher_type_name = $this->input->post('_voucher_type_name');
		$_description = $this->input->post('_description');
		$_voucher_type_id = $this->input->post('_voucher_type_id');

		$_voucher_type_code = strtoupper($_voucher_type_code);
		$_voucher_type_name = strtoupper($_voucher_type_name);
		$_description = strtoupper($_description);
		
		$voucher_type = $this->vouchers_model->get_is_voucher_type_by_id($_voucher_type_id);
		
		// insert the new user
		$data = array(
			'code' => $_voucher_type_code,
			'name' => $_voucher_type_name,
			'description' => $_description		
		);
		
		$this->vouchers_model->update_is_voucher_types($data, array('voucher_type_id' => $_voucher_type_id));
		
		//logging of action
		$data_before = array(
			'code' => $voucher_type->code,
			'name' => $voucher_type->name,
			'description' => $voucher_type->description
		);
		$details_before = array('id' => $_voucher_type_id, 'details' => $data_before);
		$details_before = json_encode($details_before);
		
		$details_after = array('id' => $_voucher_type_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_vouchers_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'MEMBER VOUCHER TYPES',
			'table_name' => 'cm_member_voucher_types',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_vouchers_log_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}
		
			
	public function delete() {
		$_voucher_type_id = $this->input->post('_voucher_type_id');
		
		$voucher_type = $this->vouchers_model->get_is_voucher_type_by_id($_voucher_type_id);
	
		$data = array(
			'voucher_type' => $voucher_type
		);

		$html = $this->load->view('admin/voucher_types/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}
	
	public function confirm_delete() {
		$_voucher_type_id = $this->input->post('_voucher_type_id');
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_voucher_type() {	
		$_voucher_type_id = $this->input->post('_voucher_type_id');
		
		$voucher_type = $this->vouchers_model->get_is_voucher_type_by_id($_voucher_type_id);
			
		if (!empty($voucher_type)) {
			
			$this->vouchers_model->delete_is_voucher_types(array('voucher_type_id' => $_voucher_type_id));
			
			//logging of action
			$data_before = array(
				'code' => $voucher_type->code,
				'name' => $voucher_type->name,
				'description' => $voucher_type->description
			);
			$details_before = array('id' => $_voucher_type_id, 'details' => $data_before);
			$details_before = json_encode($details_before);

			$delete_vouchers_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'MEMBER VOUCHER TYPES',
				'table_name' => 'cm_member_voucher_types',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $delete_vouchers_log_data);
			
			echo json_encode(array("status"=>"1"));
			return;			
		} else {
			echo json_encode(array("status"=>"0","message"=>"Voucher Type ID not found."));
			return;
		}	
	}
	
	public function excel_view(){
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$start_column_num = 1;
 
        $objPHPExcel->setActiveSheetIndex(0);
		
		$voucher_types = $this->vouchers_model->get_is_voucher_types();
		
		// auto resize columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		
		//show timestamp
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'List of Voucher Types as of '.date("Y-m-d H:i:s"));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$start_column_num)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$start_column_num.':F'.$start_column_num);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$start_column_num.':F'.$start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$start_column_num++;
		
		// set column header to bold		
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'ID');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'Code');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Name');
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'Description');
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Date Created');

		$row = $start_column_num + 1;
		foreach ($voucher_types as $vt)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $vt->voucher_type_id);
			$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $vt->code);
			$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $vt->name);
			$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $vt->description);
			$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $vt->insert_timestamp);
			
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$row++;
		}
			
		$current_year = date('Y');
		$current_month = date('m');
		$current_day = date('d');
		$date = $current_month . $current_day . $current_year;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="list_of_voucher_types_'.$date.'.xls"');
		header('Cache-Control: max-age=0');		

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

	}

}
