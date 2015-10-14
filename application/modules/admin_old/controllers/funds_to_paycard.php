<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Funds_to_paycard extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('payment_model');
		$this->load->model('payout_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
		// load pager library
		$this->load->library('pager');
	}

	private $_months = array(
			'0' => '-',
			'1' => 'January',
			'2' => 'February',
			'3' => 'March',
			'4' => 'April',
			'5' => 'May',
			'6' => 'June',
			'7' => 'July',
			'8' => 'August',
			'9' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December'
	);

	public function index()
	{
		$this->view();
	}

	public function view()
	{

		$search_by = trim($this->input->get("search_option"));
		$filter_by = trim($this->input->get("filter_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";
		$count_is = 0;
		$transfers = "";
		
			
		if (($filter_by == "") || empty($filter_by)) {
			$filter_by = "ALL";
		}
		
		if (($search_text == "") || empty($search_text)) {
			$where = '';
			
			if ($filter_by != "ALL") {
					$where .= "status = '{$filter_by}'";
				}
			
		} else {
		
			if ($search_by == "funds_to_paycard_id") {
				$search_text = ltrim($search_text, "0");	
				$where = "{$search_by} = '{$search_text}'";
				
				if ($filter_by != "ALL") {
					$where .= " AND status = '{$filter_by}'";
				}
				
			} else {				
				$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
				
				if ($filter_by != "ALL") {
					$where .= " AND status = '{$filter_by}'";
				}
			}			
		}
			
		$count_is = $this->payout_model->get_funds_to_paycard_count($where);
		
		$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text. "&filter_option=" . $filter_by;
		
		// initialize pagination class
		// set pagination data
		$config = array(
				'pagination_url' => "/admin/funds_to_paycard/index/",
				'total_items' => $count_is,
				'per_page' => 10,
				'uri_segment' => 4,
		);
        
		$this->pager->set_config($config);

		$transfers = $this->payout_model->get_funds_to_paycard($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "funds_to_paycard_id DESC");

		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i >= 2000; $i--)
			$_years[$i] = $i;

		// search vars
		$this->template->search_by = $search_by;
		$this->template->filter_by = $filter_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;

		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->transfers = $transfers;
		
		$this->template->view('funds_to_paycard/dashboard');
	}

	public function download_check()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));
		$status_select = trim($this->input->post("status_select"));

		//hash check
		/*TODO: CREATE HASH CHECK*/

		// check if start_date and end_date is null
		if (((empty($start_date)) && (empty($start_date))) || (($start_date == NULL) && ($start_date == NULL)))
		{
			$this->return_json("error","Enter both Start Date and End Date.");
			return;
		}

		if ((empty($start_date)) || ($start_date == NULL))
		{
			$this->return_json("error","Enter Start Date.");
			return;
		}

		if ((empty($end_date)) || ($end_date == NULL))
		{
			$this->return_json("error","Enter End Date.");
			return;
		}

		// check if start_date is greater than end_date
		if ($start_date > $end_date)
		{
			$this->return_json("error","Start Date must not exceed End Date.");
			return;
		}

		$current_date = date("Y-n-j");


		// start date must not exceed the current date
		if ($start_date > $current_date)
		{
			$this->return_json("error","Start Date must not exceed Current Date.");
			return;
		}

		$proper_start_date = date('F d, Y', strtotime($start_date));
		$proper_end_date = date('F d, Y', strtotime($end_date));

		// check if query will return records to execute
		$where = "CAST(insert_timestamp AS DATE) BETWEEN '{$start_date}' AND '{$end_date}'";
		
		if ($status_select != "ALL") {
			$where .= " AND status = '{$status_select}'";
		}

		$pending_count = $this->payout_model->get_funds_to_paycard($where);

		if (empty($pending_count))
		{
			$return_html = "<span>No result from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.</span>";
			$this->return_json("ok",$return_html);
			return;
		} else
		{
			$return_html = "<span>You are to generate Funds To Paycard Transactions from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.<br/>Do you want to proceed?</span>";
			$this->return_json("ok",$return_html);
			return;
		}
	}

	public function download_proceed()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));
		$status_select = trim($this->input->post("status_select"));
		
		$current_timestamp = date('Y-m-d H:i:s');

		$return_html = "<span>Request Completed.<br/><br/>You may now download the generated spreadsheet file.</span>";

		$this->return_json("ok",$return_html);
		return;

	}

	function export_xls($start_date,$end_date, $status_select)
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
		
		try {
			
			// adjusting memory limit to accomodate PHPExcel processing 
			set_time_limit(0); // eliminating the timeout
			ini_set('memory_limit', '512M');

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle("payout requests")->setDescription("none");
			$start_column_num = 3;

			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$worksheet->setTitle("F2P");

			$where = "CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date'";
			
			if ($status_select != "ALL") {
				$where .= " AND status = '{$status_select}'";
			}
			
			$member_payout_count = $this->payout_model->get_funds_to_paycard_count($where);

			$filename = "funds_to_paycard_" . str_replace("-", "", $start_date) . "_to_" . str_replace("-", "", $end_date) . ".xlsx";

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('J' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('K' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('L' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('M' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('N' . $start_column_num)->getFont()->setBold(true);
			
			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('K' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('L' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('M' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('N' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			
			//set column names
			$worksheet->setCellValue('A1', "Funds To Paycard from {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'Status');
			$worksheet->setCellValue('B' . $start_column_num, 'Transaction ID');
			$worksheet->setCellValue('C' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('D' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('E' . $start_column_num, 'First Name');
			$worksheet->setCellValue('F' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('G' . $start_column_num, 'Gross');			
			$worksheet->setCellValue('H' . $start_column_num, 'Tax');
			$worksheet->setCellValue('I' . $start_column_num, 'Net Of Tax');			
			$worksheet->setCellValue('J' . $start_column_num, 'Net Total');
			$worksheet->setCellValue('K' . $start_column_num, 'Cash Card');
			$worksheet->setCellValue('L' . $start_column_num, 'Date Requested');
			$worksheet->setCellValue('M' . $start_column_num, 'Payout Period');
			$worksheet->setCellValue('N' . $start_column_num, 'Date Updated');
		

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($member_payout_count/$allowed_rows)+1;$prow++)
			{
				$member_payouts = $this->payout_model->get_funds_to_paycard($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'insert_timestamp ASC');

				foreach ($member_payouts as $mp)
				{

					// get member details					
					$pretty_final_commission = number_format($mp->final_commission, 2, '.', ',');	
					$pretty_gross = number_format($mp->gross, 2, '.', ',');					
					$pretty_wtax = number_format($mp->wtax, 2, '.', ',');
					$pretty_net_of_wtax = number_format($mp->net_of_wtax, 2, '.', ',');					
					$pretty_cash_card = $mp->cash_card;					
					$pretty_id = str_pad($mp->funds_to_paycard_id, 8, "0", STR_PAD_LEFT);					
					$proper_payout_period = $mp->start_date . " to " . $mp->end_date;
					
					// align right					
					$worksheet->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('J' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$worksheet->setCellValue('A'. $row, $mp->status);
					$worksheet->setCellValueExplicit('B'. $row, $pretty_id, PHPExcel_Cell_DataType::TYPE_STRING);
					$worksheet->setCellValue('C'. $row, $mp->member_id);
					$worksheet->setCellValue('D'. $row, $mp->last_name);
					$worksheet->setCellValue('E'. $row, $mp->first_name);
					$worksheet->setCellValue('F'. $row, $mp->middle_name);					
					$worksheet->setCellValueExplicit('G'. $row, $pretty_gross, PHPExcel_Cell_DataType::TYPE_STRING);					
					$worksheet->setCellValue('H'. $row, $pretty_wtax);
					$worksheet->setCellValue('I'. $row, $pretty_net_of_wtax);					
					$worksheet->setCellValueExplicit('J'. $row, $pretty_final_commission, PHPExcel_Cell_DataType::TYPE_STRING);
					$worksheet->setCellValueExplicit('K'. $row, $pretty_cash_card);
					$worksheet->setCellValue('L'. $row, $mp->insert_timestamp);
					$worksheet->setCellValue('M'. $row, $proper_payout_period);
					$worksheet->setCellValue('N'. $row, $mp->update_timestamp);
					
					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(false);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);
					$worksheet->getColumnDimension('G')->setAutoSize(true);
					$worksheet->getColumnDimension('H')->setAutoSize(true);
					$worksheet->getColumnDimension('I')->setAutoSize(true);
					$worksheet->getColumnDimension('J')->setAutoSize(true);
					$worksheet->getColumnDimension('K')->setAutoSize(true);
					$worksheet->getColumnDimension('L')->setAutoSize(true);
					$worksheet->getColumnDimension('M')->setAutoSize(true);
					$worksheet->getColumnDimension('N')->setAutoSize(true);
					
					$row++;
				}
			}

			// 20140523 - update status to PROCESSING
			// START
			$data = array(
				'status' => 'PROCESSING'
				);

			$where = "CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date' AND status IN ('PENDING')";
			$this->payout_model->update_funds_to_paycard($data, $where);
			// END

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit(0);
			
		} catch (Exception $e) {
			exit($e->getMessage());
		}

	}

	function upload()
	{
		$target_path = FCPATH . "assets/media/uploads/";
		$allowedExts = array();
		$maxFileSize = 0;

		$headers = get_fu_headers();
		
		// adjusting memory limit to accomodate PHPExcel processing 
		set_time_limit(0); // eliminating the timeout
		ini_set('upload_max_filesize', '512M');
		
		if ($headers['X-Requested-With'] == 'XMLHttpRequest')
		{
			$fileName = $headers['X-File-Name'];
			$fileSize = $headers['X-File-Size'];
			$ext = substr($fileName, strrpos($fileName, '.') + 1);
			if (in_array($ext, $allowedExts) or empty($allowedExts))
			{
				if ($fileSize < $maxFileSize or empty($maxFileSize))
				{
					$content = file_get_contents("php://input");
					file_put_contents($target_path . $fileName, $content);

					//echo $this->_saveImage($this->input->get('username'), $fileName, $target_path.$fileName, false);

					$this->_process_uploaded_xls($target_path . $fileName);


					echo json_encode(array('success' => true, 'file' => $fileName));
					return;
				} else
				{
					echo('{"success":false, "details": "Maximum file size: ' . get_byte_size($maxFileSize) . '."}');
				};
			} else
			{
				echo('{"success":false, "details": "File type ' . $ext . ' not allowed."}');
			}
		} else
		{
			if ($_FILES['file']['name'] != '')
			{
				$fileName = $_FILES['file']['name'];
				$fileSize = $_FILES['file']['size'];
				$ext = substr($fileName, strrpos($fileName, '.') + 1);
				if (in_array($ext, $allowedExts) or empty($allowedExts))
				{
					if ($fileSize < $maxFileSize or empty($maxFileSize))
					{


						//echo $this->_saveImage($this->input->get('username'), $_FILES['file']['name'], $_FILES['file']['tmp_name']);
						$this->_process_uploaded_xls($target_path . $fileName);
						$_ret = ($_ret)?$this->_process_uploaded_xls($fullpath):false;
						echo json_encode(array('success' => true, 'file' => $fileName));
						return;
					} else
					{
						echo('{"success":false, "details": "Maximum file size: ' . get_byte_size($maxFileSize) . '."}');
					};
				} else
					echo('{"success":false, "details": "File type ' . $ext . ' not allowed."}');
			} else
				echo '{"success":false, "details": "No file received."}';
		}
	}

	function _process_uploaded_xls($filename)
	{
		$_ret = true;	
		
		$reader = new PHPExcel_Reader_Excel2007();
        $excel = $reader->load($filename);

        foreach ($excel->getAllSheets() as $sheet)
        {
        	$method = "_sheet_process_" . strtolower($sheet->getTitle());
        	if(method_exists($this, $method)) {
        		$tmp = $this->$method($sheet);
        		if(!$tmp) $_ret = false;
        	}
	    }

        return $_ret;
	}

	private function _sheet_processor($sheet, $_map) {
		$sheetData = array();
		$row = 4;
		while($sheet->getCell('A'.$row)->getValue() != '')
		{
			$rowData = new stdClass();
			foreach($_map as $k => $v) $rowData->$v = $sheet->getCell($k.$row)->getValue();
			$sheetData[] = $rowData;
			$row++;
		}

		return $sheetData;
	}

	private function _sheet_process_f2p($sheet) {
		$_map = array(
			'A' => 'status',
			'B' => 'transaction_id',
			'C' => 'member_id',
			'D'	=> 'last_name',
			'E' => 'first_name',
			'F' => 'middle_name'		
		);

		$sheetData = $this->_sheet_processor($sheet, $_map);
		
		foreach($sheetData as $rowData)
		{
			
			$funds_to_paycard_id = ltrim($rowData->transaction_id, '0');

			$data = array(
					'status' => $rowData->status
				);

			$where = "funds_to_paycard_id = '{$funds_to_paycard_id}'";
			$this->payout_model->update_funds_to_paycard($data, $where);			


		}

		return true;
	}

}