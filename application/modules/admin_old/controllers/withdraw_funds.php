<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Withdraw_funds extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('payment_model');
		$this->load->model('members_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('withdrawals');
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
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = "";			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}

		// initialize pagination class
		// set pagination data
		$config = array(
				'pagination_url' => "/admin/withdraw_funds2/index/",
				'total_items' => $this->members_model->get_member_withdraw_funds_transactions_count($where),
				'per_page' => 5,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		$withdraw_details = $this->members_model->get_member_withdraw_funds_transactions($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "transaction_id DESC");

		foreach ($withdraw_details as $k => $p)
		{
			$member = $this->members_model->get_member_by_id($p->member_id);

			$p->first_name = $member->first_name;
			$p->last_name = $member->last_name;

			$withdraw_details[$k] = $p;
		}

		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i >= 2000; $i--)
			$_years[$i] = $i;

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;

		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->withdraw_details = $withdraw_details;
		$this->template->view('withdraw_funds/list');
	}

	public function view_transaction()
	{
		$_transaction_id = $this->input->post("_transaction_id");

		$withdraw_details = $this->members_model->get_member_withdraw_funds_transactions_by_transaction_id($_transaction_id);
		
		if (empty($withdraw_details)) {
			// TODO :: Transaction does not exist
		}
		
		// get member details		
		$member = $this->members_model->get_member_by_id($withdraw_details->member_id);

		$withdraw_details->first_name = $member->first_name;
		$withdraw_details->last_name = $member->last_name;
		$withdraw_details->email = $member->email;
		$withdraw_details->mobile_number = $member->mobile_number;

		$this->return_json("ok", "", $withdraw_details);
		return;
	}

	public function execute()
	{
		$_transaction_id = $this->input->post("_transaction_id");

		// update cm_member_payouts, set status to COMPLETED
		$data = array(
				'status' => 'COMPLETED',
				'update_timestamp' => date("Y-m-d H:i:s")
		);
		$where = array("transaction_id" => $_transaction_id);

		$this->payment_model->update_member_payout($data, $where);

		//add to logs
		/* TODO: CREATE LOGGING FOR PROCESSING */

		$this->return_json("ok", "Update (COMPLETE WITHDRAW REQUEST) Successful. Inserted to Tracking Table");
		return;
	}

	public function download_check()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));

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
		$where = "status IN ('PENDING','PROCESSING') AND CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date'";

		//$pending_count = $this->payment_model->get_member_payouts($where);
		$pending_count = $this->members_model->get_member_withdraw_funds_transactions($where);

		if (empty($pending_count))
		{
			$return_html = "<span>No pending requests from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.</span>";
			$this->return_json("ok",$return_html);
			return;
		} else
		{
			$return_html = "<span>You are to generate pending payouts from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.<br/>Do you want to proceed?</span>";
			$this->return_json("ok",$return_html);
			return;
		}
	}

	public function download_proceed()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));
		
		$current_timestamp = date('Y-m-d H:i:s');

		// update all affected records
		$data = array('status'=>'PREPROCESS', 'user_id' => $this->user->user_id,'update_timestamp'=>$current_timestamp);
		$where = "status = 'PENDING' AND CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date'";

		$this->members_model->update_member_withdraw_funds_transactions($data, $where);

		$return_html = "<span>Request Completed.<br/><br/>You may now download the generated spreadsheet file.<br/><br/>If you choose <strong>'Download'</strong>, you agree that status of generated records will be set to <strong>PROCESSING</strong>.</span>";

		$this->return_json("ok",$return_html);
		return;

	}

	public function download_update_user_action() {
    	$start_date = trim($this->input->post("start_date"));
    	$end_date = trim($this->input->post("end_date"));
    	$user_action = trim($this->input->post("user_action"));

    	if (empty($user_action)) {

			$this->return_json("error","Empty User Action");
			return;
    	}


		$current_timestamp = date('Y-m-d H:i:s');

		if ($user_action == "cancel") {
			// update all affected records
			$data = array('status'=>'PENDING', 'user_id' => 0, 'update_timestamp'=>'0000-00-00 00:00:00');
			$where = "status = 'PREPROCESS' AND insert_timestamp BETWEEN '$start_date' AND '$end_date'";

			$return_html = "<span>You have cancelled the process. Status set to <strong>PENDING</strong><br/></span>";
			$this->return_json("ok",$return_html);

		} else if ($user_action == "download") {
			// update all affected records
			$data = array('status'=>'PROCESSING', 'user_id' => $this->user->user_id ,'update_timestamp'=>$current_timestamp);
			$where = "status = 'PREPROCESS' AND CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date'";

			$return_html = "<span>Done. Status set to <strong>PROCESSING</strong><br/></span>";
    		$this->return_json("ok",$return_html);

		} else {
			$this->return_json("ok","Invalid User Action");
    		return;
		}

		$this->members_model->update_member_withdraw_funds_transactions($data, $where);

		return;
    }

	function export_xls($start_date,$end_date)
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

			$where = "status = 'PROCESSING' AND CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date'";
			$member_payout_count = $this->payment_model->get_member_payout_count($where);

			$filename = "withdraw_request_" . str_replace("-", "", $start_date) . "_to_" . str_replace("-", "", $end_date) . ".xls";

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
			
			//set column names
			$worksheet->setCellValue('A1', "Payout Request for the period of {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'Status');
			$worksheet->setCellValue('B' . $start_column_num, 'Transaction ID');
			$worksheet->setCellValue('C' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('D' . $start_column_num, 'First Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('F' . $start_column_num, 'Email');
			$worksheet->setCellValue('G' . $start_column_num, 'Mobile Number');
			$worksheet->setCellValue('H' . $start_column_num, 'Gross Amount');
			$worksheet->setCellValue('I' . $start_column_num, 'Tax');
			$worksheet->setCellValue('J' . $start_column_num, 'Net Amount');
			$worksheet->setCellValue('K' . $start_column_num, 'Remarks');

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($member_payout_count/$allowed_rows)+1;$prow++)
			{
				$member_payouts = $this->members_model->get_member_withdraw_funds_transactions($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'transaction_id ASC',"status, transaction_id, member_id, amount, tax, amount_after_tax, remarks");

				foreach ($member_payouts as $mp)
				{

					// get member details
					$member = $this->members_model->get_member_by_id($mp->member_id);

					//set column formatting
					/*$worksheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$worksheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
					$worksheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
					$worksheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
					$worksheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
					$worksheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);*/


					$worksheet->setCellValue('A'. $row, $mp->status);
					$worksheet->setCellValue('B'. $row, $mp->transaction_id);
					$worksheet->setCellValue('C'. $row, $mp->member_id);
					$worksheet->setCellValue('D'. $row, $member->first_name);
					$worksheet->setCellValue('E'. $row, $member->last_name);
					$worksheet->setCellValue('F'. $row, $member->email);
					$worksheet->setCellValue('G'. $row, $member->mobile_number);
					$worksheet->setCellValue('H'. $row, $mp->amount);
					$worksheet->setCellValue('I'. $row, $mp->tax);
					$worksheet->setCellValue('J'. $row, $mp->amount_after_tax);
					$worksheet->setCellValue('K'. $row, $mp->remarks);
					
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
					
					$row++;
				}
			}

			//$member_payouts = $this->payment_model->get_member_payouts($where, null, 'insert_timestamp DESC');


			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit(0);
			
		} catch (Exception $e) {
			exit($e->getMessage());
		}
	}

	function upload()
	{
		$target_path = FCPATH . "assets/uploads/";
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
		$this->load->library('spreadsheet_excel_reader');
		
		// adjusting memory limit to accomodate PHPExcel processing 
		set_time_limit(0); // eliminating the timeout
		ini_set('memory_limit', '512M');
		
		$this->spreadsheet_excel_reader->setOutputEncoding('UTF-8');
		$this->spreadsheet_excel_reader->read($filename);
		$rows = $this->spreadsheet_excel_reader->sheets[0]['cells'];
		
		
		foreach ($rows as $row)
		{
			$status = strtoupper(trim($row[1]));
			if ($status == 'COMPLETED')
			{

				$transaction_id = $row[2];
				$member_id = $row[3];
				$additional_tax = $row[9];
				// $total_amount is the amount before tax
				$total_amount = $row[8];
				//$type = $row[13];
				$data = array(
					'status' => 'COMPLETED',
					'additional_tax' => $additional_tax,
					'total_amount' => $total_amount
				);

				$withdraw = $this->members_model->get_member_withdraw_funds_transactions_by_transaction_id($transaction_id);

				if(strcmp(strtoupper($withdraw->type),"FUNDS") == 0 && strcmp(strtoupper($withdraw->status),"PROCESSING") == 0 )
				{
					//$this->members_model->credit_funds($member_id,$total_amount,"FUNDS","CREDIT FROM WITHDRAW TRANSACTIONS");
					$this->members_model->debit_funds($member_id,$total_amount,"FUNDS","CREDIT FROM WITHDRAW TRANSACTIONS");
				}
				$this->members_model->update_member_withdraw_transactions($data, "transaction_id = {$transaction_id} AND status='PROCESSING'");
			}
		}
	}

}