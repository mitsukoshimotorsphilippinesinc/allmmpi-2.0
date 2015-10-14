<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achievements extends Systems_Controller 
{
	
	
	function __construct() 
	{
  		parent::__construct();
		//set navigation
		$this->set_navigation('Achievements');
		// load pager library
		$this->load->library('pager');
		//load models used by this controller
		$this->load->model('contents_model');
		
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
		    'pagination_url' => '/admin/achievements/index/',
		    'total_items' => $this->contents_model->get_member_achievements_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		
		$this->template->achievements = $this->contents_model->get_member_achievements($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('achievements/list');

	}
	
	public function add()	{
	
		$data = "";
		$html = $this->load->view('admin/achievements/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_add() {
		$_achievement_name = trim($this->input->post('_achievement_name'));
		$_max_pairs = abs($this->input->post('_max_pairs'));
		$_earnings_to_upgrade = $this->input->post('_earnings_to_upgrade');
		$_earnings_maintenance = $this->input->post('_earnings_maintenance');
		$_remarks = trim($this->input->post('_remarks'));
			
		$_remarks = strtoupper($_remarks);		
		$_achievement_name = strtoupper($_achievement_name);
				
		// check if member_achievement_details exists in table
		$member_achievement_details = $this->contents_model->get_member_achievement_by_name($_achievement_name);
				
		if (count($member_achievement_details) > 0)  {
			
			$html = "<p>
						<label>Sorry, the Code already exists. See the details below:</label>
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Achievement Name</strong></label></td>
									<td><label style='color:#990000;'>{$member_achievement_details->achievement_name}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Max Pairs</strong></label></td>
									<td><label style='color:#990000;'>{$member_achievement_details->max_pairs}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Earnings To Upgrade</strong></label></td>
									<td><label style='color:#990000;'>{$member_achievement_details->earnings_to_upgrade}</label></td>		
								</tr>	
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Earnings Maintenance</strong></label></td>
									<td><label style='color:#990000;'>{$member_achievement_details->earnings_maintenance}</label></td>		
								</tr>								
							</tbody>
						</table>						
					</p>";
					
			echo json_encode(array("status"=>"0","html"=>$html));
			
		} else { 	
							
			$html = "<p><label>You are about to add a new Card Type with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:100px;'><label><strong>Achievement Name</strong></label></td>
								<td><label class=''>{$_achievement_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Max Pairs</strong></label></td>
								<td><label class=''>{$_max_pairs}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Earnings To Upgrade</strong></label></td>
								<td><label class=''>{$_earnings_to_upgrade}</label></td>		
							</tr>	
							<tr>
								<td style='width:100px;'><label><strong>Earnings Maintenance</strong></label></td>
								<td><label>{$_earnings_maintenance}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Remarks</strong></label></td>
								<td><label>{$_remarks}</label></td>		
							</tr>							
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			echo json_encode(array("status"=>"1","html"=>$html));
		}
		
		return;
		
	}
	
	public function add_member_achievement() {
		$_achievement_name = trim($this->input->post('_achievement_name'));
		$_max_pairs = abs($this->input->post('_max_pairs'));
		$_earnings_to_upgrade = $this->input->post('_earnings_to_upgrade');
		$_earnings_maintenance = $this->input->post('_earnings_maintenance');
		$_remarks = trim($this->input->post('_remarks'));
			
		$_remarks = strtoupper($_remarks);		
		$_achievement_name = strtoupper($_achievement_name);
	
	
		// insert to card_type table
		$data = array(
			'achievement_name' => $_achievement_name,
			'max_pairs' => $_max_pairs,
			'earnings_to_upgrade' => $_earnings_to_upgrade,
			'earnings_maintenance' => $_earnings_maintenance
		);
		$this->contents_model->insert_member_achievements($data);
		
		$type_id = $this->contents_model->insert_id();
		
		//logging of action
		$details_after = array('id' => $type_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_member_achievement_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'MEMBER ACHIEVEMENTS',
			'table_name' => 'rf_member_achievements',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => $_remarks,
		);
		
		$this->tracking_model->insert_logs('admin', $add_member_achievement_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}

	public function edit()
	{
		$achievement_id = abs($this->input->post('achievement_id'));
				
		$member_achievement_details = $this->contents_model->get_member_achievement_by_id($achievement_id);
		
		$data = array(
			'member_achievement_details' => $member_achievement_details
		);

		$html = $this->load->view('admin/achievements/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;				
	}
	
	public function confirm_edit() {
		$_achievement_name = $this->input->post('_achievement_name');
		$_max_pairs = abs($this->input->post('_max_pairs'));
		$_earnings_to_upgrade = $this->input->post('_earnings_to_upgrade');
		$_earnings_maintenance = ($this->input->post('_earnings_maintenance'));
		$_remarks = trim($this->input->post('_remarks'));

		$_achievement_name = strtoupper($_achievement_name);
		$_remarks = strtoupper($_remarks);
			
		$html = "<p><label>You are about to edit a Member Achievement with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Achievement Name</strong></label></td>
							<td><label class=''>{$_achievement_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Max Pairs</strong></label></td>
							<td><label class=''>{$_max_pairs}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Earnings To Upgrade</strong></label></td>
							<td><label class=''>{$_earnings_to_upgrade}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Earnings Maintenance</strong></label></td>
							<td><label>{$_earnings_maintenance}</label></td>		
						</tr>					
						<tr>
							<td style='width:100px;'><label><strong>Remarks</strong></label></td>
							<td><label>{$_remarks}</label></td>		
						</tr>					
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_achievement() {
		$_member_achievement_id = abs($this->input->post('_member_achievement_id'));
		$_achievement_name = $this->input->post('_achievement_name');
		$_max_pairs = abs($this->input->post('_max_pairs'));
		$_earnings_to_upgrade = $this->input->post('_earnings_to_upgrade');
		$_earnings_maintenance = ($this->input->post('_earnings_maintenance'));
		$_remarks = trim($this->input->post('_remarks'));

		$_achievement_name = strtoupper($_achievement_name);
		$_remarks = strtoupper($_remarks);

		// insert the new data
		$data = array(
			'achievement_name' => $_achievement_name,
			'max_pairs' => $_max_pairs,
			'earnings_to_upgrade' => $_earnings_to_upgrade,		
			'earnings_maintenance' => $_earnings_maintenance
		);
		
		$member_achievement_details = $this->contents_model->get_member_achievement_by_id($_member_achievement_id);
		
		$this->contents_model->update_member_achievements($data, array('member_achievement_id' => $_member_achievement_id));
		
		$data_before = array(
			'achievement_name' => $member_achievement_details->achievement_name,
			'max_pairs' => $member_achievement_details->max_pairs,
			'earnings_to_upgrade' => $member_achievement_details->earnings_to_upgrade,
			'earnings_maintenance' => $member_achievement_details->earnings_maintenance
		);
		
		//logging of action
		$details_before = array('id' => $_member_achievement_id, 'details' => $data_before);
		$details_before = json_encode($details_before);
		
		$details_after = array('id' => $_member_achievement_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_member_achievements_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'MEMBER ACHIEVEMENTS',
			'table_name' => 'rf_member_achievements',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => $_remarks,
		);
		
		$this->tracking_model->insert_logs('admin', $update_member_achievements_log_data);
			
		echo json_encode(array("status"=>"1"));
		return;		
	}
		
	public function excel_view(){
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$start_column_num = 1;
 
        $objPHPExcel->setActiveSheetIndex(0);
		
		$achievements = $this->contents_model->get_member_achievements();
		
		// auto resize columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		
		//show timestamp
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'List of Member Achievements as of '.date("Y-m-d H:i:s"));
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
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'ID');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'Achievement Name');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Max Pairs');
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'Earnings To Upgrade');
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Earnings Maintenance');
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'Date Created');

		$row = $start_column_num + 1;
		foreach ($achievements as $vt)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $vt->member_achievement_id);
			$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $vt->achievement_name);
			$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $vt->max_pairs);
			$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $vt->earnings_to_upgrade);
			$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $vt->earnings_maintenance);
			$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $vt->insert_timestamp);
			
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$row++;
		}
			
		$current_year = date('Y');
		$current_month = date('m');
		$current_day = date('d');
		$date = $current_month . $current_day . $current_year;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="list_of_achievements_'.$date.'.xls"');
		header('Cache-Control: max-age=0');		

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

	}

}
