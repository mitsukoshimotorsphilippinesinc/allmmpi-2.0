<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_types extends Systems_Controller 
{
	
	
	function __construct() 
	{
  		parent::__construct();
		//set navigation
		$this->set_navigation('card_types');
		// load pager library
		$this->load->library('pager');
		//load models used by this controller
		$this->load->model('cards_model');
		
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
		    'pagination_url' => '/admin/card_types/index/',
		    'total_items' => $this->cards_model->get_card_types_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		
		$this->template->card_types = $this->cards_model->get_card_types($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('card_types/list');

	}
	
	public function add()	{
	
		$data = "";
		$html = $this->load->view('admin/card_types/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_add() {
		$_card_type_code = trim($this->input->post('_card_type_code'));
		$_card_type_name = trim($this->input->post('_card_type_name'));
		$_description = $this->input->post('_description');
		$_sales_package_option = abs($this->input->post('_sales_package_option'));
		$_series_number = abs($this->input->post('_series_number'));
			
		$_card_type_code = strtoupper($_card_type_code);
		$_card_type_name = strtoupper($_card_type_name);
		$_description = strtoupper($_description);
				
		// check if card_type_code exists in table
		$card_type_code = $this->cards_model->get_card_type_by_code($_card_type_code);
				
		if (count($card_type_code) > 0)  {
			// existing code			
			$card_series = "";

			// get current series of card type
			$where = "card_type_id = '{$card_type_code->card_type_id}'";
			$card_series_details = $this->cards_model->get_card_series($where,null,"series_number");

			if (count($card_series_details) == 0) {
				$card_series = "NONE";
			} else {	
				$cnt = 1;									
				foreach ($card_series_details as $csd) {
					if ($cnt == 1) {
						$card_series = $csd->series_number; 
					} else {
						$card_series = $card_series . ", " . $csd->series_number;
					}
					$cnt++;
				}
			}
			
			if ($card_type_code->is_package == 0) {
				$is_package = "Sales Card";
			} else {
				$is_package = "Package Card";
			}
			
			$html = "<p>
						<label>Sorry, the Code already exists. See the details below:</label>
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Code</strong></label></td>
									<td><label style='color:#990000;'>{$card_type_code->code}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Name</strong></label></td>
									<td><label style='color:#990000;'>{$card_type_code->name}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Card Series</strong></label></td>
									<td><label style='color:#990000;'>{$card_series}</label></td>		
								</tr>	
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Type</strong></label></td>
									<td><label style='color:#990000;'>{$is_package}</label></td>		
								</tr>				
							</tbody>
						</table>
						<br/>
						<label>If you want to add a new Card Series for this Card Type, click the Edit button on the Action field.</label>
					</p>";
					
			echo json_encode(array("status"=>"0","html"=>$html));
			
		} else { 	
			
			$is_package = "Package Card";
			if ($_sales_package_option == 0) {
				$is_package = "Sales Card";
			}
					
			if ($_series_number < 10) {
				$_series_number = "0" . $_series_number;
			}		
							
			$html = "<p><label>You are about to add a new Card Type with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:100px;'><label><strong>Code</strong></label></td>
								<td><label class=''>{$_card_type_code}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Name</strong></label></td>
								<td><label class=''>{$_card_type_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Card Series</strong></label></td>
								<td><label class=''>{$_series_number}</label></td>		
							</tr>	
							<tr>
								<td style='width:100px;'><label><strong>Type</strong></label></td>
								<td><label>{$is_package}</label></td>		
							</tr>				
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			echo json_encode(array("status"=>"1","html"=>$html));
		}
		
		return;
		
	}
	
	public function add_card_type() {
		$_card_type_code = trim($this->input->post('_card_type_code'));
		$_card_type_name = trim($this->input->post('_card_type_name'));
		$_description = $this->input->post('_description');
		$_sales_package_option = abs($this->input->post('_sales_package_option'));
		$_series_number = abs($this->input->post('_series_number'));

		$_card_type_code = strtoupper($_card_type_code);
		$_card_type_name = strtoupper($_card_type_name);
		$_description = strtoupper($_description);
	
	
		// insert to card_type table
		$data = array(
			'code' => $_card_type_code,
			'name' => $_card_type_name,
			'description' => $_description,
			'is_package' => $_sales_package_option
		);
		$this->cards_model->insert_card_types($data);
		
		$type_id = $this->cards_model->insert_id();
		
		//logging of action
		$details_after = array('id' => $type_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_card_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARDS TYPES',
			'table_name' => 'rf_card_types',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => "",
		);
		
		$this->tracking_model->insert_logs('admin', $add_card_log_data);
		
		// insert to card_series table
		$data = array(
			'series_number' => str_pad($_series_number,2,0, STR_PAD_LEFT),
			'card_type_id' => $type_id
		);
		$this->cards_model->insert_card_series($data);

		$series_id = $this->cards_model->insert_id();
		
		//logging of action
		$details_after = array('id' => $series_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_card_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARDS SERIES',
			'table_name' => 'rf_card_series',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => "",
		);
		
		$this->tracking_model->insert_logs('admin', $add_card_log_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}

	public function edit()
	{
		$_card_type_id = $this->input->post('_card_type_id');
				
		$card_type = $this->cards_model->get_card_type_by_id($_card_type_id);
	
		$data = array(
			'card_type' => $card_type
		);

		$html = $this->load->view('admin/card_types/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;				
	}
	
	public function confirm_edit() {
		$_card_type_code = $this->input->post('_card_type_code');
		$_card_type_name = $this->input->post('_card_type_name');
		$_description = $this->input->post('_description');
		$_sales_package_option = abs($this->input->post('_sales_package_option'));
		
		$is_package = "Package Card";
		if ($_sales_package_option == 0) {
			$is_package = "Sales Card";
		}
		
		$_card_type_code = strtoupper($_card_type_code);
		$_card_type_name = strtoupper($_card_type_name);
		$_description = strtoupper($_description);
			
		$html = "<p><label>You are about to edit a Card Type with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Code</strong></label></td>
							<td><label class=''>{$_card_type_code}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_card_type_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Description</strong></label></td>
							<td><label class=''>{$_description}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Type</strong></label></td>
							<td><label>{$is_package}</label></td>		
						</tr>					
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_card_type() {
		
		$_card_type_code = $this->input->post('_card_type_code');
		$_card_type_name = $this->input->post('_card_type_name');
		$_description = $this->input->post('_description');
		$_card_type_id = $this->input->post('_card_type_id');
		$_sales_package_option = abs($this->input->post('_sales_package_option'));

		
		$_card_type_code = strtoupper($_card_type_code);
		$_card_type_name = strtoupper($_card_type_name);
		$_description = strtoupper($_description);

		// insert the new data
		$data = array(
			'code' => $_card_type_code,
			'name' => $_card_type_name,
			'description' => $_description,		
			'is_package' => $_sales_package_option,
		);
		
		$card_type = $this->cards_model->get_card_type_by_id($_card_type_id);
		
		$this->cards_model->update_card_types($data, array('card_type_id' => $_card_type_id));
		
		$data_before = array(
			'code' => $card_type->code,
			'name' => $card_type->name,
			'description' => $card_type->description,
			'is_package' => $card_type->is_package
		);
		
		//logging of action
		$details_before = array('id' => $_card_type_id, 'details' => $data_before);
		$details_before = json_encode($details_before);
		
		$details_after = array('id' => $_card_type_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_card_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARDS TYPES',
			'table_name' => 'rf_card_types',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);
		
		$this->tracking_model->insert_logs('admin', $update_card_log_data);
			
		echo json_encode(array("status"=>"1"));
		return;		
	}
		
			
	public function delete() {
		$_card_type_id = $this->input->post('_card_type_id');
		
		$card_type = $this->cards_model->get_card_type_by_id($_card_type_id);
	
		$data = array(
			'card_type' => $card_type
		);

		$html = $this->load->view('admin/card_types/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}
	
	public function confirm_delete() {
		$_card_type_id = abs($this->input->post('_card_type_id'));
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_card_type() {	
		$_card_type_id = abs($this->input->post('_card_type_id'));
		
		$card_type = $this->cards_model->get_card_type_by_id($_card_type_id);
			
		if (!empty($card_type)) {
			
			$card_type = $this->cards_model->get_card_type_by_id($_card_type_id);
			
			$this->cards_model->delete_card_types(array('card_type_id' => $_card_type_id));
			
			$data_before = array(
				'code' => $card_type->code,
				'name' => $card_type->name,
				'description' => $card_type->description,
				'is_package' => $card_type->is_package
			);

			//logging of action
			$details_before = array('id' => $_card_type_id, 'details' => $data_before);
			$details_before = json_encode($details_before);
			
			$update_card_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'CARDS TYPES',
				'table_name' => 'rf_card_types',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $update_card_log_data);
			
			echo json_encode(array("status"=>"1"));
			return;			
		} else {
			echo json_encode(array("status"=>"0","message"=>"Card Type ID not found."));
			return;
		}	
	}
	
	
	public function add_series_to_type()
	{
		$_card_type_id = $this->input->post('_card_type_id');
		
		// get card type details 		
		$card_type_details = $this->cards_model->get_card_type_by_id($_card_type_id);
		
		$data = array(
			'card_type_details' => $card_type_details
		);
		$html = $this->load->view('/card_types/add_series', $data, TRUE);
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;				
	}
	
	
	
	
	
	public function excel_view(){
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$start_column_num = 1;
 
        $objPHPExcel->setActiveSheetIndex(0);
		
		$card_types = $this->cards_model->get_card_types();
		
		// auto resize columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		
		//show timestamp
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'List of Card Types as of '.date("Y-m-d H:i:s"));
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
		foreach ($card_types as $vt)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $vt->card_type_id);
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
		header('Content-Disposition: attachment;filename="list_of_card_types_'.$date.'.xls"');
		header('Cache-Control: max-age=0');		

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

	}

}
