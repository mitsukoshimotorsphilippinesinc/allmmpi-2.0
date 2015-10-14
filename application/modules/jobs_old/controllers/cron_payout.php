<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Cron_payout extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $this->load->model('cards_model');
        $this->load->model('payout_model');
        $this->load->model('members_model');
        $this->load->model("settings_model");
        $this->load->model("tracking_model");
        $this->load->model("facilities_model");
        ini_set('memory_limit', '2000M');
	}

	private $trh_table_name;
	private $tr_log_count;
	private $full_start_date;
	private $full_end_date;
	
	public function index() 
	{
		echo "Cron Process Payout...";
	}

	public function test_appending_rows()
	{
		/*
		$sample_row_data = array(
			'14075',
			'CASTILLON',
			'MIRALINDA',
			'REGALADO',
			'9900081599',
			'Credit repeat sales unilevel commission from 7500604192',
			'FUNDS',
			'1',
			'10',
			'12/1/12 0:00'
		);

		// CREATE INITIAL FILE
		$objPHPExcel = new PHPExcel();

        $title = "Transaction Logs";

        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle($title);

		$title = "Transaction Logs sample to sample";

		$start_column_num = 3;

		//set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);
		$worksheet->mergeCells('A1:E1');

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
		$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('C' . $start_column_num, 'First Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('E' . $start_column_num, 'Account ID');
		$worksheet->setCellValue('F' . $start_column_num, 'Details');
		$worksheet->setCellValue('G' . $start_column_num, 'Type');
		$worksheet->setCellValue('H' . $start_column_num, 'Level');
		$worksheet->setCellValue('I' . $start_column_num, 'Amount');
		$worksheet->setCellValue('J' . $start_column_num, 'Date Time');

		$filename = "sample.xlsx";

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
		if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
			unlink(FCPATH . "assets/media/tmp/" . $filename);
		}
		$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

		$worksheet->garbageCollect();
		$worksheet = null;
		$objPHPExcel = null;
		$objWriter = null;
		unset($objPHPExcel);
		unset($objWriter);

		$row = 4;
		$filepath = FCPATH . "assets/media/tmp/" . $filename;

		for($j = 0; $j < 20; $j++)
		{
			echo ($j * 1000) . "\n";
			// append 1000 sample row data
			$reader = new PHPExcel_Reader_Excel2007();
			$objPHPExcel = $reader->load($filepath);
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			for($i = 0; $i < 1000; $i++)
			{
				$worksheet->setCellValue('A'. $row, $sample_row_data[0]);
				$worksheet->setCellValue('B'. $row, $sample_row_data[1]);
				$worksheet->setCellValue('C'. $row, $sample_row_data[2]);
				$worksheet->setCellValue('D'. $row, $sample_row_data[3]);
				$worksheet->setCellValue('E'. $row, $sample_row_data[4]);
				$worksheet->setCellValue('F'. $row, $sample_row_data[5]);
				$worksheet->setCellValue('G'. $row, $sample_row_data[6]);
				$worksheet->setCellValue('H'. $row, $sample_row_data[7]);
				$worksheet->setCellValue('I'. $row, $sample_row_data[8]);
				$worksheet->setCellValue('J'. $row, $sample_row_data[9]);
				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($filepath);

			unset($reader);
		    unset($objWriter);
		    unset($objPHPExcel);
		    unset($worksheet);
			$objPHPExcel = null;
		    $objWriter = null;
		    $reader = null;
		    $worksheet = null;

		    echo "sleep\n";
		    usleep(500000);
		}
		*/
	}

	public function test_appending_rows_2()
	{
		/*
		// CREATE INITIAL FILE
		$objPHPExcel = new PHPExcel();

        $title = "Transaction Logs";

        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle($title);

		$title = "Transaction Logs sample to sample";

		$start_column_num = 3;

		//set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);
		$worksheet->mergeCells('A1:E1');

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
		$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('C' . $start_column_num, 'First Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('E' . $start_column_num, 'Account ID');
		$worksheet->setCellValue('F' . $start_column_num, 'Details');
		$worksheet->setCellValue('G' . $start_column_num, 'Type');
		$worksheet->setCellValue('H' . $start_column_num, 'Level');
		$worksheet->setCellValue('I' . $start_column_num, 'Amount');
		$worksheet->setCellValue('J' . $start_column_num, 'Date Time');

		$filename = "sample.xlsx";

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
		if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
			unlink(FCPATH . "assets/media/tmp/" . $filename);
		}
		$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

		$worksheet->garbageCollect();
		unset($objPHPExcel);
		unset($objWriter);
		unset($worksheet);
		$worksheet = null;
		$objPHPExcel = null;
		$objWriter = null;

		$row = 4;
		$filepath = FCPATH . "assets/media/tmp/" . $filename;

		for($j = 0; $j < 20; $j++)
		{
			echo "prepare json\n";
			$addcount = 1000;
			echo ($j * $addcount) . "\n";
			$jsonData = json_encode(array(
				'row' => $row,
				'addcount' => $addcount
			));
			$row+=$addcount;

			echo "create lookup file\n";
			$myFile = FCPATH . "assets/media/tmp/appendingTestFile.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $jsonData);
			fclose($fh);

			echo "exec shell\n";
			// exec shell command
			$root_path = FCPATH;
			exec("/usr/bin/php {$root_path}jobs.php jobs cron_payout test_appending_sub_shell >> /dev/null 2>&1");

			while(file_exists($myFile))
			{
				// wait for sub shell to finish
			}
		}
		*/
	}

	public function test_appending_sub_shell()
	{
		/*
		$myFile = FCPATH . "assets/media/tmp/appendingTestFile.txt";
		$fh = fopen($myFile, 'r');
		$theData = fgets($fh);
		fclose($fh);
		$jsonData = json_decode($theData);
		$row = $jsonData->row;
		$addcount = $jsonData->addcount;

		$filepath = FCPATH . "assets/media/tmp/sample.xlsx";

		$sample_row_data = array(
			'14075',
			'CASTILLON',
			'MIRALINDA',
			'REGALADO',
			'9900081599',
			'Credit repeat sales unilevel commission from 7500604192',
			'FUNDS',
			'1',
			'10',
			'12/1/12 0:00'
		);

		echo "create 1000\n";
		// append 1000 sample row data
		$reader = new PHPExcel_Reader_Excel2007();
		$objPHPExcel = $reader->load($filepath);
		$worksheet = $objPHPExcel->setActiveSheetIndex(0);
		for($i = 0; $i < $addcount; $i++)
		{
			$worksheet->setCellValue('A'. $row, $sample_row_data[0]);
			$worksheet->setCellValue('B'. $row, $sample_row_data[1]);
			$worksheet->setCellValue('C'. $row, $sample_row_data[2]);
			$worksheet->setCellValue('D'. $row, $sample_row_data[3]);
			$worksheet->setCellValue('E'. $row, $sample_row_data[4]);
			$worksheet->setCellValue('F'. $row, $sample_row_data[5]);
			$worksheet->setCellValue('G'. $row, $sample_row_data[6]);
			$worksheet->setCellValue('H'. $row, $sample_row_data[7]);
			$worksheet->setCellValue('I'. $row, $sample_row_data[8]);
			$worksheet->setCellValue('J'. $row, $sample_row_data[9]);
			$row++;
		}

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($filepath);

		unset($reader);
	    unset($objWriter);
	    unset($objPHPExcel);
	    unset($worksheet);
		$objPHPExcel = null;
	    $objWriter = null;
	    $reader = null;
	    $worksheet = null;

	    echo "sleep\n";
	    usleep(500000);
	    unlink(FCPATH . "assets/media/tmp/appendingTestFile.txt");
	    */
	}

	public function test_xml_to_xlsx()
	{
		//error_reporting(E_ERROR | E_PARSE);
		//$orig = FCPATH . "assets/media/tmp/test.xml";
		//$converted = FCPATH . "assets/media/tmp/coverted_test.xlsx";

		//echo "LOAD STARTED..\n";
		//$objPHPExcel = IOFactory::load($orig);
		//echo "DONE LOADING\n";
		//$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save($converted);
	}

	public function check_payouts($payout_period_id = NULL) 
	{

		echo $payout_period_id;
		
		if ($payout_period_id == NULL) {
			// automated
			$today = date('Y-m-d H:i:s');
			$where = "end_date < '" . $today . "' AND status = 'ACTIVE' ";

		} else {
			// manual
			$where = "payout_period_id = " . $payout_period_id;
		}

		//$today = date('Y-m-d H:i:s');
		//$where = "end_date < '" . $today . "' AND status = 'ACTIVE' ";
		
		echo "where condition: " . $where . "\n";
		$active_payout = $this->payout_model->get_payout_periods($where);
		
		echo "count: " . count($active_payout) . "\n";
		
		if(count($active_payout) > 0)
		{
			echo "add cron log\n";
			$data = array(
				"module_name"=>"Jobs Cron Payout",
				"table_name"=>"po_payout_periods",
				"action"=>"Payout Processing",
				"details"=>"Backend Payout Process"
			);
			$this->tracking_model->insert_cron_logs($data);

			echo "process one entry\n";
			$active_payout = $active_payout[0];

			echo "truncate previews payout\n";
			$this->truncate_payout();

			echo "generate trh table\n";
			$this->trh_table_name = $this->generate_trh_table($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);
			// FOR MANUAL TESTING
			//$this->trh_table_name = "trh_member_acct_credit_logs_20140517_20140523";

			$this->full_start_date = $active_payout->start_date;
			$tmp = explode(" ", $active_payout->start_date); // remove time
			$active_payout->start_date = $tmp[0];

			$this->full_end_date = $active_payout->end_date;
			$tmp = explode(" ", $active_payout->end_date);
			$active_payout->end_date = $tmp[0];

			// 20140507
			//START
			// check if po_member_accounts table is available for this payout	
			echo "assign table where to get account_status_id\n";
			$this->account_lookup_table = $this->check_account_table($this->full_start_date, $this->full_end_date);
			// END

			echo "process commissions\n";
			$this->process_commissions($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);

			echo "process commission report\n";
			$this->get_member_payout($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);

			echo "generate sms deductions table\n";
            $this->populate_member_sms_deductions_table($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);

			if($active_payout->payout_type == "UNILEVEL"){
				echo "pre generate transaction log csv\n";
				$this->tr_log_count = $this->get_transaction_log_csv($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);
			}

			echo "generate excel files\n";
			$this->generate_excel_files($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);

			echo "merge excel files\n";
			$merged_filename = $this->shell_excel_merge($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);

			// echo "populate tr logs table\n";
			// $this->populate_tr_logs_sheet($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);

			echo "email excel file\n";
			$base_url = 'http://cp.vital-c.net';
			$extra_msg = "";
			if($active_payout->payout_type == "UNILEVEL")
			{
				$start = str_replace("-", "", $active_payout->start_date);
				$end = str_replace("-", "", $active_payout->end_date);
				$csv_filename = "tr_log_" . $start . "_" . $end . ".csv";
				$extra_msg = "
					<p>Due to the size of UNILEVEL transaction log it would take a very long time before it could be appended to the main spreadsheet and was instead separated.</p>
					<p>Click <a href='" . $base_url . "/assets/media/tmp/" . $csv_filename . "'>here</a> to down the the raw transaction logs or follow the link below.</p>
					<p>" . $base_url . "/assets/media/tmp/" . $csv_filename . "</p>
				";
			}
			$params = array(
				"excel_path" => $base_url . '/assets/media/tmp/' . $merged_filename,
				"extra_msg" => $extra_msg
			);

			$email_group = $this->settings->payout_email_group;
			$emails = explode(',',$email_group);

			foreach($emails as $email)
			{
				$data = array(
					"email"=>$email,
					"type"=>"payout_email",
					"params"=>$params
				);

				Modules::run('jobs/notifications/send_email',$data);
			}

			echo "update payout status\n";
			$this->payout_model->update_payout_period(array(
				'status' => 'COMPLETED',
				'rerun_status' => 'ACTIVE'
			),array(
				'payout_period_id' => $active_payout->payout_period_id
			));

			echo "insert to cm_member_payouts as PENDING\n";
			$this->pending_member_payout($active_payout->start_date, $active_payout->end_date, $active_payout->payout_type);

			echo "create new payout period\n";
			$this->insert_new_payout_period($active_payout);

			echo "credit completed cd to erhm\n";
			$this->_credit_completed_cd_as_erhm();

			echo "DONE\n";
		}

		exit(0);
	}

	public function schedule_checker()
	{
		$this->load->helper('date');

		$date = date('y-m-d');
		$date_arr = explode("-",$date);

		//UNILEVEL check
		$month = $date_arr[1];
		$year  = $date_arr[0];
		$uni_check = false;
		while(!$uni_check)
		{
			if($month>12)
			{
				$month = "01";
				$year++;
			}
	
			$start_str = $year."-".$month."-01 00:00:00";
			$end_str   = $year."-".$month."-".days_in_month($month,$year)." 23:59:59";

			$this->insert_payout_if_not_existing($start_str,$end_str,"UNILEVEL","1");
			
			if($month != $date_arr[1])
				$uni_check = true;

			$month++;
		}

		//IGPMS check
		$month = $date_arr[1];
		$year  = $date_arr[0];
		$igp_check = false;

		while(!$igp_check)
		{
			if($month>12)
			{
				$month = "01"; 
				$year++;
			}

			for($i = 1 ; $i <= days_in_month($month,$year) ; $i++)
			{
				if(date("w",strtotime($year."-".$month."-".$i)) == 6)
				{
					$start_str = $year."-".$month."-".$i." 00:00:00";
					$end_str   = date("y-m-d",strtotime($start_str)+"604799")." 23:59:59";

					$this->insert_payout_if_not_existing($start_str,$end_str,"IGPSM","1");
				}
			}

			if($month != $date_arr[1])
				$igp_check = true;

			$month++;
		}
		exit(0);
	}

	private function insert_new_payout_period($active_payout = array())
	{
		$this->load->helper('date');
		
		$start_date  = $active_payout->start_date;
		$end_date    = $active_payout->end_date;
		$payout_type = $active_payout->payout_type;
		$is_official = $active_payout->is_official;
		
		$start_date_arr = explode(" ",$start_date);
		$end_date_arr  = explode(" ",$end_date);

		$start_arr = explode("-",$start_date_arr[0]);
		$end_arr   = explode("-",$end_date_arr[0]);

    	if($payout_type == "IGPSM")
    	{    		
    		if(date('w',strtotime($start_date_arr[0])) == 6 && date('w',strtotime($end_date_arr[0])) == 5 &&  strtotime($end_date_arr[0]) - strtotime($start_date_arr[0]) == 518400)
    		{
    			$this->insert_payout_if_not_existing(date('y-m-d',strtotime($start_date_arr[0])+ "604800")." 00:00:00",date('y-m-d',strtotime($end_date_arr[0])+"604800")." 23:59:59",$payout_type,"1");
			}
    	}

    	if($payout_type == "UNILEVEL")
    	{
			if($start_arr[2] == 1 && $end_arr[2] == days_in_month($end_arr[1], $end_arr[0]) && $start_arr[0] == $end_arr[0] && $start_arr[1] == $end_arr[1])
			{
				if($start_arr[1] == 12)
				{
					$year = $start_arr[0]++;
					$month = $start_arr[1] = "01";
				}
				else
				{
					$year  = $start_arr[0];
					$month = ++$start_arr[1];
				}

				if($month < 10) 
					$month = "0".$month;

				$this->insert_payout_if_not_existing($year."-".$month."-01 00:00:00",$year."-".$month."-".days_in_month($month,$year)." 23:59:59",$payout_type,"1");
			}	
    	} 
	}

	private function insert_payout_if_not_existing($start_date = "",$end_date = "",$payout_type = "",$is_official = "1")
	{
		$where = array("start_date"=>$start_date,"end_date"=>$end_date) ;

		if(!$this->payout_model->get_payout_periods($where))
		{
			$data = array(			
				'payout_type' => $payout_type,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'status' => "ACTIVE",
				'is_official' => "1"
			);
			echo "add new payout_period (".$start_date."-".$end_date.") - ".$payout_type."\n";
			$this->payout_model->insert_payout_period($data); 
		}
		else
		{
			echo "payout_period is already existing (".$start_date."-".$end_date.") - ".$payout_type."\n";
		}
	}

	private function populate_tr_logs_sheet($start_date, $end_date, $type)
	{
        $chunkSize = 10000;
        $row = 4;

        for($startRow = 1; $startRow <= $this->tr_log_count; $startRow += $chunkSize)
        {
        	$jsonData = json_encode(array(
				'chunkSize' => $chunkSize,
				'startRow' => $startRow,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'type' => $type,
				'row' => $row
			));

			echo "tr logs " . $startRow . "\n";

			$myFile = FCPATH . "assets/media/tmp/tr_populate_file.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $jsonData);
			fclose($fh);

			// exec shell command
			$root_path = FCPATH;
			exec("/usr/bin/php {$root_path}jobs.php jobs cron_payout populate_tr_logs_sheet_shell >> /dev/null 2>&1");

			$row+=$chunkSize;

			// wait for the process to be completed
			while(file_exists(FCPATH . "assets/media/tmp/tr_populate_file.txt"))
			{
				// do nothing
			}
		}
	}

	public function populate_tr_logs_sheet_shell()
	{
		$myFile = FCPATH . "assets/media/tmp/tr_populate_file.txt";
		$fh = fopen($myFile, 'r');
		$theData = fgets($fh);
		fclose($fh);
		$jsonData = json_decode($theData);

		print_r($jsonData);

		$chunkSize = $jsonData->chunkSize;
		$startRow = $jsonData->startRow;
		$start_date = $jsonData->start_date;
		$end_date = $jsonData->end_date;
		$type = $jsonData->type;
		$row = $jsonData->row;

		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);

		$chunkFilter = new chunkReadFilter();
		$chunkFilter->setRows($startRow,$chunkSize);

        $excel_filename = $type . "_commission_payout_" . $start . "_to_" . $end . ".xlsx";
        $excel_filepath = FCPATH . "assets/media/tmp/" . $excel_filename;

        //$out_chunkFilter = new chunkReadFilter();
		//$out_chunkFilter->setRows($row,$chunkSize);

		$out_objReader = new PHPExcel_Reader_Excel2007();
		//$sheetname = 'Transaction Logs';
		//$out_objReader->setLoadSheetsOnly($sheetname);
		//$out_objReader->setReadFilter($out_chunkFilter);
		$out_objPHPExcel = $out_objReader->load($excel_filepath);
		$out_worksheet = $out_objPHPExcel->getActiveSheet(7);
		//$bigObjReader->setReadFilter($out_chunkFilter);
		//$out_objPHPExcel = $bigObjReader->load($excel_filepath);
        //$out_worksheet = $out_objPHPExcel->setActiveSheetIndex(7);

		$csv_filename = "tr_log_" . $start . "_" . $end . ".csv";
		$csv_filepath = FCPATH . "assets/media/tmp/" . $csv_filename;

		$in_objReader = new PHPExcel_Reader_CSV();
        echo "startRow=" . $startRow . "\n";
        echo "chunkSize=" . $chunkSize . "\n";
        $in_chunkFilter = new chunkReadFilter();
		$in_chunkFilter->setRows($startRow,$chunkSize);
        $in_objReader->setReadFilter($in_chunkFilter);
	    $in_objPHPExcel = $in_objReader->load($csv_filepath);
	    $worksheet = $in_objPHPExcel->getActiveSheet();

	    foreach($worksheet->getRowIterator() as $datarow)
	    {
	    	$rowIndex = $datarow->getRowIndex();

	    	if($rowIndex >= $startRow)
	    	{
	    		//echo "$rowIndex= " . $rowIndex . "\n";
	    		$row = $rowIndex + 4;

		    	$cell = $worksheet->getCell('A' . $rowIndex);
		    	$out_worksheet->setCellValue('A'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('B' . $rowIndex);
		    	$out_worksheet->setCellValue('B'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('C' . $rowIndex);
		    	$out_worksheet->setCellValue('C'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('D' . $rowIndex);
		    	$out_worksheet->setCellValue('D'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('E' . $rowIndex);
		    	$out_worksheet->setCellValue('E'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('F' . $rowIndex);
		    	$out_worksheet->setCellValue('F'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('G' . $rowIndex);
		    	$out_worksheet->setCellValue('G'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('H' . $rowIndex);
		    	$out_worksheet->setCellValue('H'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('I' . $rowIndex);
		    	$out_worksheet->setCellValue('I'. $row, $cell->getCalculatedValue());
		    	$cell = $worksheet->getCell('J' . $rowIndex);
		    	$out_worksheet->setCellValue('J'. $row, $cell->getCalculatedValue());
		    	
	    		//$row++;
	    	}
	    }

	    $out_objWriter = IOFactory::createWriter($out_objPHPExcel, 'Excel2007');
		if(file_exists($excel_filepath)){
			unlink($excel_filepath);
		}
		$out_objWriter->save($excel_filepath);

		$out_worksheet->garbageCollect();
		foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
		{
			unset($v);
		}

		unlink(FCPATH . "assets/media/tmp/tr_populate_file.txt");
	}

	private function get_transaction_log_csv($start_date, $end_date, $type)
	{
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		$csv_filename = "tr_log_" . $start . "_" . $end . ".csv";

		$where = ($type == "IGPSM")?" a.transaction_code IN (100,101,102,103,104) ":" a.transaction_code = 105 ";
		$where .= "AND a.amount > 0 ";

		$sql = "SELECT a.member_id, b.last_name, b.first_name, b.middle_name, a.account_id, a.type, CASE WHEN a.level IS NULL THEN '' ELSE a.level END AS level, a.amount, a.insert_timestamp FROM " . $this->trh_table_name . " a LEFT JOIN cm_members b ON a.member_id = b.member_id WHERE " . $where . "";

		$filepath = FCPATH . "assets/media/tmp/" . $csv_filename;
		$mysql_call = "mysql -h" . $this->db->hostname . " -u" . $this->db->username . " -p" . $this->db->password . " " . $this->db->database . " -e \"" . $sql . "\" | LC_CTYPE=C tr '\\t' '|' > " . $filepath . "";

		echo $mysql_call . "\n";
		shell_exec($mysql_call);

		/*
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		$csv_filename = "tr_log_" . $start . "_" . $end . ".csv";

		$where = ($type == "IGPSM")?" a.transaction_code IN (100,101,102,103,104) ":" a.transaction_code = 105 ";

		$sql = "
		SELECT
			'Member ID',
			'Last Name',
			'First Name',
			'Middle Name',
			'Account ID',
			'Details',
			'Type',
			'Level',
			'Amount',
			'Date Time'
		UNION
		SELECT
			a.member_id,
			b.last_name,
			b.first_name,
			b.middle_name,
			a.account_id,
			a.remarks,
			a.type,
			a.level,
			a.amount,
			a.insert_timestamp
		INTO OUTFILE '" . FCPATH . "assets/media/tmp/" . $csv_filename . "'
		FIELDS TERMINATED BY ','
		ENCLOSED BY '\"'
		ESCAPED BY '\\\\'
		LINES TERMINATED BY '\n'
		FROM
			" . $this->trh_table_name . " a
		LEFT JOIN
			cm_members b ON a.member_id = b.member_id
		WHERE
			" . $where . "
		";
		$this->db->query($sql);
		*/

		// return the count
		$sql = "
		SELECT
			COUNT(*) AS cnt
		FROM
			" . $this->trh_table_name . " a
		WHERE
			" . $where . "
		";
		$query = $this->db->query($sql);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	private function pending_member_payout($start_date, $end_date, $type)
	{
		$sql = "
		INSERT INTO cm_member_payouts 
		(
			payout_type,
			member_id,
			gross,
			gcep,
			net_of_gcep,
			witholding_tax,
			net_of_tax,
			balance,
			total_amount,
			cash_card,
			status,
			start_date,
			end_date,
			user_id
		)
		(
		SELECT 
			'" . $type . "',
			member_id,
			gross,
			psf,
			net_gross,
			tax,
			net_tax,
			balance,
			total_amount,
			cash_card,
			'PENDING',
			'" . $start_date . "',
			'" . $end_date . "',
			1 
		FROM 
			po_member_commissions_report
		)";
		$this->db->query($sql);
	}

	private function generate_trh_table($start_date, $end_date, $type)
	{
		$start = str_replace("-", "", $start_date);
		$tmp = explode(" ", $start);
		$start = $tmp[0];
		$end = str_replace("-", "", $end_date);
		$tmp = explode(" ", $end);
		$end = $tmp[0];
		$trh_table_name = "trh_member_acct_credit_logs_" . $start . "_" . $end;

		$sql = "DROP TABLE IF EXISTS " . $trh_table_name;
		$this->db->query($sql);
		
		/*$sql = "
			CREATE TABLE " . $trh_table_name . "
			SELECT
				*
			FROM
				tr_member_acct_credit_logs
			WHERE
				insert_timestamp BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND
				LEFT(account_id, 2) <> '11'
		";*/

		$sql = "
			CREATE TABLE " . $trh_table_name . "
			SELECT
				*
			FROM
				tr_member_acct_credit_logs
			WHERE
				insert_timestamp BETWEEN '" . $start_date . "' AND '" . $end_date . "'
			AND
				LEFT(card_id, 2) <> '11'
		";

		$this->db->query($sql);
		$sql = "ALTER TABLE " . $trh_table_name . " ADD PRIMARY KEY (  `credit_log_id` ), ADD KEY `insert_timestamp` ( `insert_timestamp` )";
		$this->db->query($sql);

		// trh summary entry
		$sql = "
			INSERT INTO trh_member_acct_credit_logs_summary
			(
			member_id,
			account_id,
			transaction_code,
			total_amount,
			start_date,
			end_date
			)
			(
			SELECT
			     member_id,
			     account_id,
			     transaction_code,
			     SUM(amount) AS total_amount,
			     '" . $start_date . "' AS start_date,
			     '" . $end_date . "' AS end_date
			FROM
			     " . $trh_table_name . "
			WHERE
			     remarks NOT LIKE '%SUMMARY%'
			AND
			     amount > 0
			GROUP BY
			     member_id, account_id, transaction_code
			);
		";
		$this->db->query($sql);

		return $trh_table_name;
	}

	// 20140507
	// START
	private function check_account_table($full_start_date, $full_end_date)
	{
		
		$enddatetime_suffix = date("Ymd_Hi", strtotime($full_end_date));

		$member_account_tablename = "po_member_accounts_" . $enddatetime_suffix;

		// check if specific po_member_accounts table exists
		$sql = "SHOW TABLES LIKE 'po_member_accounts_{$enddatetime_suffix}'";
				
		$query = $this->db->query($sql);
		$table_exists = $query->first_row();
		
		if (empty($table_exists)) {
			$member_account_tablename = "cm_member_accounts";
		} 

		return $member_account_tablename;
	}
	// END


	private function shell_excel_merge($start_date, $end_date, $type)
	{
		// create checker file
		$jsonData = json_encode(array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'type' => $type
		));

		$myFile = FCPATH . "assets/media/tmp/payoutFile.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $jsonData);
		fclose($fh);

		// exec shell command
		$root_path = FCPATH;
		exec("/usr/bin/php {$root_path}jobs.php jobs cron_payout merge_excel_files >> /dev/null 2>&1");

		// wait for the process to be completed
		while(file_exists(FCPATH . "assets/media/tmp/payoutFile.txt"))
		{
			// do nothing
		}

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);
		$merged_filename = $type . '_commission_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '.xlsx';

		return $merged_filename;
	}

	public function merge_excel_files()
	{	
		$myFile = FCPATH . "assets/media/tmp/payoutFile.txt";
		$fh = fopen($myFile, 'r');
		$theData = fgets($fh);
		fclose($fh);
		$jsonData = json_decode($theData);
		$start_date = $jsonData->start_date;
		$end_date = $jsonData->end_date;
		$type = $jsonData->type;

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filenames = array();
		$sheets = $this->payout_model->get_payout_download_sheets();
		$tr_log_file = FCPATH . "assets/media/tmp/tr_log_" . $pretty_start_date . "_" . $pretty_end_date . ".csv";
		foreach($sheets as $sheet)
		{
			if($type == "UNILEVEL" && $sheet->file_name == '_transaction_logs_') continue;

			$filenames[] = FCPATH . "assets/media/tmp/" . $type . $sheet->file_name . $pretty_start_date . "_to_" . $pretty_end_date . ".xlsx";
		}

		if(!is_dir(FCPATH . "assets/media/payout"))
		{
			mkdir(FCPATH . "assets/media/payout/", 0775);
		}

		$bigExcel = new PHPExcel();
		$bigExcel->removeSheetByIndex(0);

		foreach ($filenames as $filename) {
			echo "merging: " . $filename . "\n";
			$reader = new PHPExcel_Reader_Excel2007();
			$excel = $reader->load($filename);
	    
		    foreach ($excel->getAllSheets() as $sheet) {
		        $bigExcel->addExternalSheet($sheet);

		        unset($sheet);
		        $sheet = null;
		        break;
		    }

		    unset($reader);
		    unset($excel);
		    $reader = null;
		    $excel = null;

		    unlink($filename);
		}

		// generate result excel
		$objWriter = IOFactory::createWriter($bigExcel, 'Excel2007');
		$merged_filename = $type . '_commission_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '.xlsx';
		if(file_exists(FCPATH . "assets/media/payout/" . $merged_filename)){
			unlink(FCPATH . "assets/media/payout/" . $merged_filename);
		}
		$bigExcelPath = FCPATH . "assets/media/tmp/" . $merged_filename;
		$objWriter->save($bigExcelPath);
		
		unset($bigExcel);
		unset($objWriter);
		$bigExcel = null;
		$objWriter = null;

		foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
		{
			if($v != 'merged_filename')
			{
				unset($v);
			}
		}

		unlink(FCPATH . "assets/media/tmp/payoutFile.txt");

		return $merged_filename;
	}

	private function generate_excel_files($start_date, $end_date, $type)
	{
		// get all payout sheets
		$po_sheets = $this->payout_model->get_payout_download_sheets();
		$module_path = "jobs/payout/index";

		foreach($po_sheets as $po_sheet)
		{
			echo $po_sheet->sheet_name . "\n";
			echo $type . "\n";
			
			if($po_sheet->file_name == "_transaction_logs_" && $type == "UNILEVEL")
			{
				continue;
			}
			
			// create checker file
			$jsonData = json_encode(array(
				'sheet_id' => $po_sheet->sheet_id,
				'type' => $type,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'payout_id' => 1,
				'member_account_table_ref' => $this->account_lookup_table,
			));

			$myFile = FCPATH . "assets/media/tmp/payoutFile.txt";
			$logFile = FCPATH . "assets/media/tmp/payout.log";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $jsonData);
			fclose($fh);

			// exec shell command
			echo "PROCESSING.. " . $po_sheet->sheet_name . "\n";
			$root_path = FCPATH;
			//exec("/usr/bin/php {$root_path}jobs.php jobs cron_payout excel_process >> {$logFile} 2>&1");
			exec("/usr/bin/php {$root_path}jobs.php jobs cron_payout excel_process >> {$logFile} 2>&1");

			// wait for the process to be completed
			while(file_exists(FCPATH . "assets/media/tmp/payoutFile.txt"))
			{
				// do nothing
			}

			echo "PROCESS DONE " . $po_sheet->sheet_name . "\n";
		}
	}

	public function excel_process ($params=array()) 
	{
		echo "start: excel process";

		$myFile = FCPATH . "assets/media/tmp/payoutFile.txt";
		$fh = fopen($myFile, 'r');
		$theData = fgets($fh);
		fclose($fh);
		$jsonData = json_decode($theData);

		$sheet_id = $jsonData->sheet_id;		
		$type = $jsonData->type;		
		$start_date = $jsonData->start_date;		
		$end_date = $jsonData->end_date;		
		$payout_id = $jsonData->payout_id;		
		$this->account_lookup_table = $jsonData->member_account_table_ref;

		$sheet = $this->payout_model->get_payout_download_sheets(array(
			'sheet_id' => $sheet_id
		));

		//echo $sheet;

		if(count($sheet) > 0)
		{
			$sheet = $sheet[0];
			//if($sheet->sheet_method <> 'segmented_transaction_logs') continue;
			$method = $sheet->sheet_method;
			$this->$method($type, $start_date, $end_date, $payout_id);

			echo "SUCCESS " . $sheet->sheet_name . "\n";
		}
		else
		{
			echo "FAILED";
		}
        unlink(FCPATH . "assets/media/tmp/payoutFile.txt");

        return;
	}

	private function segmented_commission_deduct_completions($type, $start_date, $end_date)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_commission_deduct_completions_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Commission Deduct Completions {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Commission Deduct Completions";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Commission Deduct Completions {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'First Name');
			$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Account ID');
			$worksheet->setCellValue('F' . $start_column_num, 'Date Time');

			$row = 4;

			$cd_completions = $this->_get_commission_deduct_completions($start_date,$end_date);

			foreach($cd_completions as $r)
			{
				$worksheet->setCellValue('A'. $row, $r->member_id);
				$worksheet->setCellValue('B'. $row, $r->first_name);
				$worksheet->setCellValue('C'. $row, $r->middle_name);
				$worksheet->setCellValue('D'. $row, $r->last_name);
				$worksheet->setCellValue('E'. $row, $r->account_id);
				$worksheet->setCellValue('F'. $row, $r->insert_timestamp);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);

				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_vouchers_released($type, $start_date, $end_date)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_vouchers_released_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Vouchers Released {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Vouchers Released";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Vouchers Released {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Voucher Code');
			$worksheet->setCellValue('B' . $start_column_num, 'Type');
			$worksheet->setCellValue('C' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('D' . $start_column_num, 'First Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('F' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('G' . $start_column_num, 'Date Time');

			$row = 4;

			$vouchers = $this->_get_vouchers_released($start_date,$end_date);

			foreach($vouchers as $r)
			{
				$worksheet->setCellValue('A'. $row, $r->voucher_code);
				$worksheet->setCellValue('B'. $row, $r->voucher_type);
				$worksheet->setCellValue('C'. $row, $r->member_id);
				$worksheet->setCellValue('D'. $row, $r->first_name);
				$worksheet->setCellValue('E'. $row, $r->middle_name);
				$worksheet->setCellValue('F'. $row, $r->last_name);
				$worksheet->setCellValue('G'. $row, $r->insert_timestamp);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);
				$worksheet->getColumnDimension('G')->setAutoSize(true);

				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_gc_transaction_logs($type, $start_date, $end_date, $payout_id)
	{
		//$local_vars = new stdClass;
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_gc_transaction_logs_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} GC Transaction Logs {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "GC Transaction Logs";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "GC Transaction Logs {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

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
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Account ID');
			//$worksheet->setCellValue('F' . $start_column_num, 'Details');
			$worksheet->setCellValue('F' . $start_column_num, 'Type');
			$worksheet->setCellValue('G' . $start_column_num, 'Level');
			$worksheet->setCellValue('H' . $start_column_num, 'Amount');
			$worksheet->setCellValue('I' . $start_column_num, 'Date Time');
			$worksheet->setCellValue('J' . $start_column_num, 'Sms Sent');
			$worksheet->setCellValue('K' . $start_column_num, 'Sms Cost');

			$row = 4;

			$count = $this->_get_gc_transaction_logs_query_count($type,$start_date,$end_date);
			$count = $count[0]->cnt;
			$count_limit = 0;
			$query_limit = 5000;

			while($count_limit < $count)
			{
				$count_limit += $query_limit;
			}
			$count_limit += $query_limit;
			$loops = $count_limit/$query_limit;

			for($i = 0; $i <= $loops; $i++)
			{
				$offset = $i * $query_limit;
				$query = $this->_get_gc_transaction_logs_query($type,$start_date,$end_date,$query_limit,$offset);
				foreach ($query->result() as $r)
				{
					if($r->amount != 0)
					{
						$worksheet->setCellValue('A'. $row, $r->member_id);
						$worksheet->setCellValue('B'. $row, $r->last_name);
						$worksheet->setCellValue('C'. $row, $r->first_name);
						$worksheet->setCellValue('D'. $row, $r->middle_name);
						$worksheet->setCellValue('E'. $row, $r->account_id);
						//$worksheet->setCellValue('F'. $row, $r->remarks);
						$worksheet->setCellValue('F'. $row, $r->type);
						$worksheet->setCellValue('G'. $row, $r->level);
						$worksheet->setCellValue('H'. $row, $r->amount);
						$worksheet->setCellValue('I'. $row, $r->insert_timestamp);
						$worksheet->setCellValue('J'. $row, $r->sms_sent);
						$worksheet->setCellValue('K'. $row, $r->tariff);

						// auto resize columns
						$worksheet->getColumnDimension('A')->setAutoSize(true);
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
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_transaction_logs($type, $start_date, $end_date, $payout_id)
	{
		// insert only empty logs sheet

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_transaction_logs_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Transaction Logs {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Transaction Logs";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Transaction Logs {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

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

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Account ID');
			$worksheet->setCellValue('F' . $start_column_num, 'From Account ID');
			$worksheet->setCellValue('G' . $start_column_num, 'Transaction Code');
			$worksheet->setCellValue('H' . $start_column_num, 'Type');
			$worksheet->setCellValue('I' . $start_column_num, 'Level');
			$worksheet->setCellValue('J' . $start_column_num, 'Amount');			
			$worksheet->setCellValue('K' . $start_column_num, 'Date Time');
			$worksheet->setCellValue('L' . $start_column_num, 'Sms Sent');
			$worksheet->setCellValue('M' . $start_column_num, 'Sms Cost');

			$row = 4;

			$count = $this->_get_transaction_logs_query_count($type,$start_date,$end_date);
			$count = $count[0]->cnt;
			$count_limit = 0;
			$query_limit = 5000;

			while($count_limit < $count)
			{
				$count_limit += $query_limit;
			}
			$count_limit += $query_limit;
			$loops = $count_limit/$query_limit;

			for($i = 0; $i <= $loops; $i++)
			{
				$offset = $i * $query_limit;
				$query = $this->_get_transaction_logs_query($type,$start_date,$end_date,$query_limit,$offset);
				foreach ($query->result() as $r)
				{
					if($r->amount != 0)
					{
						$worksheet->setCellValue('A'. $row, $r->member_id);
						$worksheet->setCellValue('B'. $row, $r->last_name);
						$worksheet->setCellValue('C'. $row, $r->first_name);
						$worksheet->setCellValue('D'. $row, $r->middle_name);
						$worksheet->setCellValue('E'. $row, $r->account_id);
						$worksheet->setCellValue('F'. $row, $r->from_account_id);
						$worksheet->setCellValue('G'. $row, $r->transaction_code);
						$worksheet->setCellValue('H'. $row, $r->type);
						$worksheet->setCellValue('I'. $row, $r->level);
						$worksheet->setCellValue('J'. $row, $r->amount);
						$worksheet->setCellValue('K'. $row, $r->insert_timestamp);
						$worksheet->setCellValue('L'. $row, $r->sms_sent);
						$worksheet->setCellValue('M'. $row, $r->tariff);

						// auto resize columns
						$worksheet->getColumnDimension('A')->setAutoSize(true);
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

						$row++;
					}

					$r = null;
					unset($r);
				}
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_download_transferred_gift_cheques($type, $start_date, $end_date, $payout_id)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_transferred_gift_cheques_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Transferred Gift Cheques {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Transferred Gift Cheques";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Transferred Gift Cheques {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			//set column names
			$worksheet->setCellValue('A1', $title);

			$worksheet->mergeCells('A' . $start_column_num . ':D' .  $start_column_num);
			$worksheet->mergeCells('E' . $start_column_num . ':H' .  $start_column_num);
			$worksheet->setCellValue('A' . $start_column_num, "FROM");
			$worksheet->setCellValue('E' . $start_column_num, "TO");
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);

			$row = $start_column_num + 1;
			// entries
			$worksheet->getStyle('A' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('H' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('I' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('J' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('K' . $row)->getFont()->setBold(true);

			$worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('J' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('K' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$worksheet->setCellValue('A' . $row, 'Member ID');
			$worksheet->setCellValue('B' . $row, 'Last Name');
			$worksheet->setCellValue('C' . $row, 'First Name');
			$worksheet->setCellValue('D' . $row, 'Middle Name');
			$worksheet->setCellValue('E' . $row, 'Member ID');
			$worksheet->setCellValue('F' . $row, 'Last Name');
			$worksheet->setCellValue('G' . $row, 'First Name');
			$worksheet->setCellValue('H' . $row, 'Middle Name');
			$worksheet->setCellValue('I' . $row, 'Amount');
			$worksheet->setCellValue('J' . $row, 'Status');
			$worksheet->setCellValue('K' . $row, 'Date Time');

			$sql = "
			SELECT *
			FROM
				tr_member_transfers
			WHERE
				`type` = 'GIFT CHECQUES'
			AND
				`insert_timestamp` BETWEEN '{$start_date}' AND '{$end_date}'
			ORDER BY
				status, from_member_id, to_member_id";
		    $query = $this->db->query($sql);
		    $transfers = $query->result();

		    $row = 5;
		    foreach($transfers as $trans)
		    {
				$from_member = $this->members_model->get_member_by_id($trans->from_member_id);
				$to_member = $this->members_model->get_member_by_id($trans->to_member_id);

				$worksheet->setCellValue('A'. $row, $from_member->member_id);
				$worksheet->setCellValue('B'. $row, $from_member->last_name);
				$worksheet->setCellValue('C'. $row, $from_member->first_name);
				$worksheet->setCellValue('D'. $row, $from_member->middle_name);
				$worksheet->setCellValue('E'. $row, $to_member->member_id);
				$worksheet->setCellValue('F'. $row, $to_member->last_name);
				$worksheet->setCellValue('G'. $row, $to_member->first_name);
				$worksheet->setCellValue('H'. $row, $to_member->middle_name);
				$worksheet->setCellValue('I'. $row, $trans->amount);
				$worksheet->setCellValue('J'. $row, $trans->status);
				$worksheet->setCellValue('K'. $row, $trans->insert_timestamp);

				// format total amount if negative
				$worksheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
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

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_download_transferred_funds($type, $start_date, $end_date, $payout_id)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_transferred_funds_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Transferred Funds {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Transferred Funds";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Transferred Funds {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			//set column names
			$worksheet->setCellValue('A1', $title);

			$worksheet->mergeCells('A' . $start_column_num . ':D' .  $start_column_num);
			$worksheet->mergeCells('E' . $start_column_num . ':H' .  $start_column_num);
			$worksheet->setCellValue('A' . $start_column_num, "FROM");
			$worksheet->setCellValue('E' . $start_column_num, "TO");
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);

			$row = $start_column_num + 1;
			// entries
			$worksheet->getStyle('A' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('H' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('I' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('J' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('K' . $row)->getFont()->setBold(true);

			$worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('J' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('K' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$worksheet->setCellValue('A' . $row, 'Member ID');
			$worksheet->setCellValue('B' . $row, 'Last Name');
			$worksheet->setCellValue('C' . $row, 'First Name');
			$worksheet->setCellValue('D' . $row, 'Middle Name');
			$worksheet->setCellValue('E' . $row, 'Member ID');
			$worksheet->setCellValue('F' . $row, 'Last Name');
			$worksheet->setCellValue('G' . $row, 'First Name');
			$worksheet->setCellValue('H' . $row, 'Middle Name');
			$worksheet->setCellValue('I' . $row, 'Amount');
			$worksheet->setCellValue('J' . $row, 'Status');
			$worksheet->setCellValue('K' . $row, 'Date Time');

			$sql = "
			SELECT *
			FROM
				tr_member_transfers
			WHERE
				`type` = 'FUNDS'
			AND
				`insert_timestamp` BETWEEN '{$start_date}' AND '{$end_date}'
			ORDER BY
				status, from_member_id, to_member_id";
		    $query = $this->db->query($sql);
		    $transfers = $query->result();

		    $row = 5;
		    foreach($transfers as $trans)
		    {
				$from_member = $this->members_model->get_member_by_id($trans->from_member_id);
				$to_member = $this->members_model->get_member_by_id($trans->to_member_id);

				$worksheet->setCellValue('A'. $row, $from_member->member_id);
				$worksheet->setCellValue('B'. $row, $from_member->last_name);
				$worksheet->setCellValue('C'. $row, $from_member->first_name);
				$worksheet->setCellValue('D'. $row, $from_member->middle_name);
				$worksheet->setCellValue('E'. $row, $to_member->member_id);
				$worksheet->setCellValue('F'. $row, $to_member->last_name);
				$worksheet->setCellValue('G'. $row, $to_member->first_name);
				$worksheet->setCellValue('H'. $row, $to_member->middle_name);
				$worksheet->setCellValue('I'. $row, $trans->amount);
				$worksheet->setCellValue('J'. $row, $trans->status);
				$worksheet->setCellValue('K'. $row, $trans->insert_timestamp);

				// format total amount if negative
				$worksheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
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

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_download_gift_cheques_per_member($type, $start_date, $end_date, $payout_id)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_gift_cheques_per_member_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Gift Cheques per Member {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Gift Cheques per Member";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Gift Cheques per Member {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			//set column names
			$worksheet->setCellValue('A1', $title);

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Gross');
			$worksheet->setCellValue('F' . $start_column_num, 'GC Variance');

			$query = $this->_get_gc_per_member_query($start_date,$end_date);
			$gc_members = $query->result();

			$row = 4;
		    foreach($gc_members as $gc_member)
		    {
				$worksheet->setCellValue('A'. $row, $gc_member->member_id);
				$worksheet->setCellValue('B'. $row, $gc_member->last_name);
				$worksheet->setCellValue('C'. $row, $gc_member->first_name);
				$worksheet->setCellValue('D'. $row, $gc_member->middle_name);
				$worksheet->setCellValue('E'. $row, $gc_member->gross);
				$worksheet->setCellValue('F'. $row, ($gc_member->gc_variance < 0)?$gc_member->gc_variance:'');
				if($gc_member->gc_variance < 0) {
					$worksheet->getStyle('A' . $row . ':F' . $row)->applyFromArray(
					    array(
					        'fill' => array(
					            'type' => PHPExcel_Style_Fill::FILL_SOLID,
					            'color' => array('rgb' => 'D08182')
					        )
					    )
					);
				}

				// format total amount if negative
				$worksheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);

				$row++;
		    }

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_download_gift_cheques_per_account($type, $start_date, $end_date, $payout_id)
	{
		
		$pretty_start_date = str_replace("-","",$start_date);		
		$pretty_end_date = str_replace("-","",$end_date);
		
		$filename = "{$type}_gift_cheques_per_account_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Gift Cheques per Account {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Gift Cheques per Account";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Gift Cheques per Account {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			//set column names
			$worksheet->setCellValue('A1', $title);

			$query = $this->_get_gc_per_account_query($start_date,$end_date);

			$sortedRows = array();
			foreach ($query->result() as $r)
			{
				$gcType = explode(' ', $r->type);
				$sortedRows[$gcType[0]][] = $r;
			}

			$row = 3;
			$sortedRowsKeys = array_keys($sortedRows);
			foreach($sortedRowsKeys as $k => $v)
			{
				// group title
				$groupTitle = strtoupper($v) . " GC";
				$worksheet->getStyle('A' . $row)->getFont()->setBold(true);
				$worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->setCellValue('A' . $row, $groupTitle);
				$row++;

				// group fields
				$worksheet->getStyle('A' . $row)->getFont()->setBold(true);
				$worksheet->getStyle('B' . $row)->getFont()->setBold(true);
				$worksheet->getStyle('C' . $row)->getFont()->setBold(true);
				$worksheet->getStyle('D' . $row)->getFont()->setBold(true);
				$worksheet->getStyle('E' . $row)->getFont()->setBold(true);
				$worksheet->getStyle('F' . $row)->getFont()->setBold(true);
				$worksheet->getStyle('G' . $row)->getFont()->setBold(true);

				$worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$worksheet->setCellValue('A' . $row, 'Member ID');
				$worksheet->setCellValue('B' . $row, 'Last Name');
				$worksheet->setCellValue('C' . $row, 'First Name');
				$worksheet->setCellValue('D' . $row, 'Middle Name');
				$worksheet->setCellValue('E' . $row, 'Account ID');
				$worksheet->setCellValue('F' . $row, 'Gift Cheques');
				$worksheet->setCellValue('G' . $row, 'Account Status');

				$row++;

				// display group values
				for($i = 0; $i < sizeof($sortedRows[$v]); $i++) {
					$worksheet->setCellValue('A'. $row, $sortedRows[$v][$i]->member_id);
					$worksheet->setCellValue('B'. $row, $sortedRows[$v][$i]->last_name);
					$worksheet->setCellValue('C'. $row, $sortedRows[$v][$i]->first_name);
					$worksheet->setCellValue('D'. $row, $sortedRows[$v][$i]->middle_name);
					$worksheet->setCellValue('E'. $row, $sortedRows[$v][$i]->account_id);
					$worksheet->setCellValue('F'. $row, $sortedRows[$v][$i]->amount);
					//$worksheet->setCellValue('G'. $row, $sortedRows[$v][$i]->account_status);
					$worksheet->setCellValue('G'. $row, 'ACTIVE'); //bypass ACTIVE by request of sir edwin

					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(true);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);
					$worksheet->getColumnDimension('G')->setAutoSize(true);

					$row++;
				}
				
				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_download_3_month_old_per_member($type, $start_date, $end_date, $payout_id)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_3_month_old_per_member_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} 3 Month Old per Member {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "3 Month Old per Member";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "3 Month Old per Member for {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

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

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Gross');
			$worksheet->setCellValue('F' . $start_column_num, 'Tax');
			$worksheet->setCellValue('G' . $start_column_num, 'Net');
			$worksheet->setCellValue('H' . $start_column_num, 'Balance');
			$worksheet->setCellValue('I' . $start_column_num, 'Total');
			$worksheet->setCellValue('J' . $start_column_num, 'Cash Card');
			$worksheet->setCellValue('K' . $start_column_num, 'Commission Date');
			$worksheet->setCellValue('L' . $start_column_num, 'Primary Account ID');
			$worksheet->setCellValue('M' . $start_column_num, 'Registration Date');

			$commissions = $this->payout_model->get_member_account_commissions(array(
				'start_date' => $start_date,
				'end_date' => $end_date,
				'payout_type' => $type
			));
			
			$witholding_tax = $this->settings->witholding_tax;
			$members_commissions = array();
			$row = 4;

			foreach($commissions as $c)
			{
				$c->cd_amount = $c->balance;
				$c->amount = $c->gross;

				$member = $this->members_model->get_member_by_id($c->member_id);
				$c->member_id = $member->member_id;
				$c->last_name = $member->last_name;
				$c->first_name = $member->first_name;
				$c->middle_name = $member->middle_name;

				// get primary account id
				$member_user_account = $this->members_model->get_member_user_account_by_member_id($c->member_id);
				$c->primary_account_id = $member_user_account->primary_account_id;
				//$c->account_insert_timestamp = $member_user_account->primary_account_id;

				$account = $this->members_model->get_member_accounts(array('account_id' => $c->primary_account_id));
				$account = $account[0];
				$c->account_insert_timestamp = $account->insert_timestamp;

				if(is_null($c->account_insert_timestamp)) continue;

				$date_registered_time = date(strtotime($c->account_insert_timestamp));
				$c->primary_date_registered_time = date('Y-m-d h:i:s', $date_registered_time);
				$curr_time = date(time());

				$difference = $curr_time - $date_registered_time;
				$months = floor($difference / 86400 / 30 );

				if($months < 3) continue;

				$member_obj = new stdClass;
				$member_obj->member_id = $c->member_id;
				$member_obj->last_name = $c->last_name;
				$member_obj->first_name = $c->first_name;
				$member_obj->middle_name = $c->middle_name;
				$member_obj->primary_account_id = $c->primary_account_id;
				$member_obj->primary_date_registered_time = $c->primary_date_registered_time;
				$member_obj->amount = $c->amount;
				if ($c->cash_card == 'TO FUNDS' || $c->cash_card == 'TO FUNDS - Blank Paycard')
					$member_obj->tax = 0;
				else
					$member_obj->tax = $c->amount * $witholding_tax;
				if ($type=="IGPSM")
					$member_obj->balance = $c->cd_amount;				
				else
					$member_obj->balance = 0;
				$member_obj->cash_card = $c->cash_card;
				$member_obj->commission_dates = $c->start_date . ' - ' . $c->end_date;

				if(isset($members_commissions[$c->member_id]))
				{
					$members_commissions[$c->member_id]->amount += $member_obj->amount;
					$members_commissions[$c->member_id]->tax += $member_obj->tax;
					$members_commissions[$c->member_id]->balance += $member_obj->balance;
				}
				else
				{
					$members_commissions[$c->member_id] = $member_obj;
				}	
			}

			foreach($members_commissions as $c)
			{
				$worksheet->setCellValue('A'. $row, $c->member_id);
				$worksheet->setCellValue('B'. $row, $c->last_name);
				$worksheet->setCellValue('C'. $row, $c->first_name);
				$worksheet->setCellValue('D'. $row, $c->middle_name);
				$worksheet->setCellValue('E'. $row, $c->amount);
				$worksheet->setCellValue('F'. $row, $c->tax);
				$worksheet->setCellValue('G'. $row, "=E{$row} - F{$row}");
				$worksheet->setCellValue('H'. $row, $c->balance);
				$worksheet->setCellValue('I'. $row, "=G{$row} - H{$row}");
				$worksheet->setCellValueExplicit('J'. $row, "{$c->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValue('K'. $row, $c->commission_dates);
				$worksheet->setCellValue('L'. $row, $c->primary_account_id);
				$worksheet->setCellValue('M'. $row, $c->primary_date_registered_time);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
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

				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_download_3_month_old_per_account($type, $start_date, $end_date, $payout_id)
	{
		$this->segmented_download_commission_per_account($type, $start_date, $end_date, $payout_id, "new_accounts");
	}

	private function segmented_download_commission_per_account($type, $start_date, $end_date, $payout_id, $filter = "all")
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		if($filter == 'all')
		{
			$filename = "{$type}_commission_per_account_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";
		}
		else
		{
			$filename = "{$type}_3_month_old_per_account_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";
		}

		try {
			//excel file
			if($filter == 'all')
			{
				$title = "{$type} Commission per Account {$start_date} to {$end_date}";
			}
			else
			{
				$title = "{$type} 3 Month Old per Account {$start_date} to {$end_date}";
			}

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        if($filter == 'all')
	        {
	        	$title = "Commission per Account";
	        }
	        else
	        {
	        	$title = "3 Month Old per Account";
	        }
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);	

			// excel content
			if($filter == 'all')
			{
				$title = "Commission per Account for {$start_date} to {$end_date}";
			}
			else
			{
				$title = "3 Month Old per Account for {$start_date} to {$end_date}";
			}

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

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
			$worksheet->getStyle('O' . $start_column_num)->getFont()->setBold(true);

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
			$worksheet->getStyle('O' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Account ID');
			$worksheet->setCellValue('F' . $start_column_num, 'Type');
			$worksheet->setCellValue('G' . $start_column_num, 'Gross');
			$worksheet->setCellValue('H' . $start_column_num, 'Tax');
			$worksheet->setCellValue('I' . $start_column_num, 'Net');
			$worksheet->setCellValue('J' . $start_column_num, 'Balance');
			$worksheet->setCellValue('K' . $start_column_num, 'Sms Deductions');
			$worksheet->setCellValue('L' . $start_column_num, 'Total');
			$worksheet->setCellValue('M' . $start_column_num, 'Cash Card');
			$worksheet->setCellValue('N' . $start_column_num, 'Account Status');
			$worksheet->setCellValue('O' . $start_column_num, 'Date Registered');
			$worksheet->setCellValue('P' . $start_column_num, 'Commission Date');

			$commissions = $this->payout_model->get_member_account_commissions(array(
				'start_date' => $start_date,
				'end_date' => $end_date,
				'payout_type' => $type
			));
			
			$witholding_tax = $this->settings->witholding_tax;
			$row = 4;

			foreach($commissions as $c)
			{
				$c->cd_amount = $c->balance;
				$c->amount = $c->gross;

				$member = $this->members_model->get_member_by_id($c->member_id);
				$c->member_id = $member->member_id;
				$c->last_name = $member->last_name;
				$c->first_name = $member->first_name;
				$c->middle_name = $member->middle_name;

				// get primary account id
				$member_user_account = $this->members_model->get_member_user_account_by_member_id($c->member_id);
				$c->primary_account_id = $member_user_account->primary_account_id;
				//$c->account_insert_timestamp = $member_user_account->primary_account_id;

				$account = $this->members_model->get_member_accounts(array('account_id' => $c->primary_account_id));
				$account = $account[0];

				// get card type
				$account_card_type = $this->cards_model->get_sp_card(array('card_id' => $c->account_id), null, null, array('type'));
				$c->account_card_type = $account_card_type[0]->type;

				$c->account_insert_timestamp = $account->insert_timestamp;

				if($filter=="new_accounts"){
					if(is_null($c->account_insert_timestamp)) continue;

					$date_registered_time = date(strtotime($c->account_insert_timestamp));
					$curr_time = date(time());

					$difference = $curr_time - $date_registered_time;
					$months = floor($difference / 86400 / 30 );

					if($months < 3) continue;
				}

				if ($type=="IGPSM")
					$balance = $c->cd_amount;
				else
					$balance = 0;
				
				// computed values
				if ($c->cash_card == 'TO FUNDS' || $c->cash_card == 'TO FUNDS - Blank Paycard')
					$tax = 0;
				else
					$tax = $c->amount * $witholding_tax;
				
				$net = $c->amount - $tax;
				$total = $net - $balance;
				$commission_date = $c->start_date . ' - ' . $c->end_date;

				 $sms_deductions = 0;

                // get sms deductions from po_member_sms_deductions
                $sql = "
            			SELECT
            				CASE WHEN SUM(total_cost) IS NULL THEN 0 ELSE SUM(total_cost) END AS sms_deductions
            			FROM
            				po_member_sms_deductions
            			WHERE
                            member_id = {$c->member_id} 
                        AND 
                            account_id = {$c->account_id} 
                        ";
            		
                $query = $this->db->query($sql);
				$query_sms_deductions = $query->first_row();
            	
                //if (empty($query_sms_deductions)) {
                //    $sms_deductions = 0;
                //} else {
                    $sms_deductions = $query_sms_deductions->sms_deductions;
                //}

				$worksheet->setCellValue('A'. $row, $c->member_id);
				$worksheet->setCellValue('B'. $row, $c->last_name);
				$worksheet->setCellValue('C'. $row, $c->first_name);
				$worksheet->setCellValue('D'. $row, $c->middle_name);
				$worksheet->setCellValue('E'. $row, $c->account_id);
				$worksheet->setCellValue('F'. $row, $c->account_card_type);
				$worksheet->setCellValue('G'. $row, $c->amount);
				$worksheet->setCellValue('H'. $row, $tax);
				$worksheet->setCellValue('I'. $row, "=G{$row} - H{$row}");
				$worksheet->setCellValue('J'. $row, $balance);
				$worksheet->setCellValue('K'. $row, $sms_deductions);
				$worksheet->setCellValue('L'. $row, "=I{$row} - J{$row} - K{$row}");
				$worksheet->setCellValueExplicit('M'. $row, "{$c->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValue('N'. $row, $c->account_status);
				$worksheet->setCellValue('O'. $row, $c->account_insert_timestamp);
				$worksheet->setCellValue('P'. $row, $commission_date);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
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
				$worksheet->getColumnDimension('O')->setAutoSize(true);
				$worksheet->getColumnDimension('P')->setAutoSize(true);

				$row++;			
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			unset($objPHPExcel);
			unset($objWriter);
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_download_member_payout($type, $start_date, $end_date, $payout_id)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_member_payout_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Member Payout {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Member Payout";
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);			

			$title = "Member Payout for {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

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
			$worksheet->getStyle('O' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('P' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('Q' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('R' . $start_column_num)->getFont()->setBold(true);

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
			$worksheet->getStyle('O' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('P' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('Q' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('R' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'MEMBER ID');
			$worksheet->setCellValue('B' . $start_column_num, 'LAST NAME');
			$worksheet->setCellValue('C' . $start_column_num, 'FIRST NAME');
			$worksheet->setCellValue('D' . $start_column_num, 'MIDDLE NAME');
			$worksheet->setCellValue('E' . $start_column_num, 'GROSS');
			$worksheet->setCellValue('F' . $start_column_num, 'Less 10 PERCENT');
			$worksheet->setCellValue('G' . $start_column_num, 'NET GROSS');
			$worksheet->setCellValue('H' . $start_column_num, 'TAX');
			$worksheet->setCellValue('I' . $start_column_num, 'NET OF TAX');
			$worksheet->setCellValue('J' . $start_column_num, 'BALANCE');
			$worksheet->setCellValue('K' . $start_column_num, 'SMS DEDUCTIONS');
			$worksheet->setCellValue('L' . $start_column_num, 'NET AMOUNT');
			$worksheet->setCellValue('M' . $start_column_num, 'FUNDS VARIANCE');
			$worksheet->setCellValue('N' . $start_column_num, 'CASH CARD');
			$worksheet->setCellValue('O' . $start_column_num, 'ACCOUNT NUMBER');
			$worksheet->setCellValue('P' . $start_column_num, 'DEPOT');
			$worksheet->setCellValue('Q' . $start_column_num, 'GROUP NAME');
			$worksheet->setCellValue('R' . $start_column_num, 'MEMBERSHIP DATE');
			
			$members_payout = $this->payout_model->get_member_commissions_report(array(
				'payout_id' => $payout_id,
				'start_date' => $start_date,
				'end_date' => $end_date
			));
			
			$row = 4;
			
			foreach ($members_payout as $r)
			{
				$member = $this->members_model->get_member_by_id($r->member_id);

				$rfid_paycard_number = '';
				$rfid_account_number = '';
				$rfid = $this->members_model->get_member_rfid_cards(array('paycard_number'=>$r->cash_card));
				if(sizeof($rfid) > 0) {
					$rfid_paycard_number = $rfid[0]->paycard_number;
					$rfid_account_number = $rfid[0]->account_number;
				}

				$service_depot = $this->facilities_model->get_service_depots(array(
					'service_depot_id' => $member->service_depot
				));

				$service_depot = (count($service_depot) > 0)?$service_depot[0]->depot_name:'';

				$sms_deductions = 0;

				//echo $r->member_id . "\n";
                
                // get sms deductions from po_member_sms_deductions
                $sql = "
            			SELECT
            				CASE WHEN SUM(total_cost) IS NULL THEN 0 ELSE SUM(total_cost) END AS sms_deductions
            			FROM
            				po_member_sms_deductions
            			WHERE
                            member_id = {$r->member_id}            			
            		";
            		
                //$query_sms_deductions = $this->db->query($sql);
                $query = $this->db->query($sql);
				$query_sms_deductions = $query->first_row();
            	
                //if (empty($query_sms_deductions)) {
                //   $sms_deductions = 0;
                //} else {
                    $sms_deductions = $query_sms_deductions->sms_deductions;
                //}

               //echo $r->member_id . "|" . $sms_deductions . "\n";

				$worksheet->setCellValue('A'. $row, $member->member_id);
				$worksheet->setCellValue('B'. $row, $member->last_name);
				$worksheet->setCellValue('C'. $row, $member->first_name);
				$worksheet->setCellValue('D'. $row, $member->middle_name);
				$worksheet->setCellValue('E'. $row, $r->gross);
				$worksheet->setCellValue('F'. $row, $r->psf);			
				$worksheet->setCellValue('G'. $row, "=E{$row} - F{$row}");			
				$worksheet->setCellValue('H'. $row, $r->tax);
				$worksheet->setCellValue('I'. $row, "=G{$row} - H{$row}");
				$worksheet->setCellValue('J'. $row, $r->balance);
				$worksheet->setCellValue('K'. $row, $sms_deductions);
				$worksheet->setCellValue('L'. $row, "=I{$row} - J{$row} - K{$row}");
				$worksheet->setCellValue('M'. $row, ($member->funds_variance < 0)?$member->funds_variance:'');
				if($member->funds_variance < 0) {
					$worksheet->getStyle('A' . $row . ':R' . $row)->applyFromArray(
					    array(
					        'fill' => array(
					            'type' => PHPExcel_Style_Fill::FILL_SOLID,
					            'color' => array('rgb' => 'D08182')
					        )
					    )
					);
				}
				$worksheet->setCellValueExplicit('N'. $row, "{$r->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueExplicit('O'. $row, "{$rfid_account_number}",PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValue('P'. $row, $service_depot);
				$worksheet->setCellValue('Q'. $row, $member->group_name);
				$worksheet->setCellValue('R'. $row, $member->insert_timestamp);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
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
				$worksheet->getColumnDimension('O')->setAutoSize(true);
				$worksheet->getColumnDimension('P')->setAutoSize(true);
				$worksheet->getColumnDimension('Q')->setAutoSize(true);
                $worksheet->getColumnDimension('R')->setAutoSize(true);    
                
				// format total amount if negative
				$worksheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
				$worksheet->getStyle('M' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
				
				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_member_adjustments($type, $start_date, $end_date, $payout_id)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_member_adjustments_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Member Adjustments {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Member Adjustments";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Member Adjustments {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Adjusted Amount');
			$worksheet->setCellValue('F' . $start_column_num, 'Details');
			$worksheet->setCellValue('G' . $start_column_num, 'Date Time');

			$adjustment_rows = $this->_get_adjustment_query($start_date,$end_date);

			$row = 4;
			foreach($adjustment_rows->result() as $r)
			{
				$worksheet->setCellValue('A'. $row, $r->member_id);
				$worksheet->setCellValue('B'. $row, $r->last_name);
				$worksheet->setCellValue('C'. $row, $r->first_name);
				$worksheet->setCellValue('D'. $row, $r->middle_name);
				$worksheet->setCellValue('E'. $row, $r->adjusted_amount);
				$worksheet->setCellValue('F'. $row, $r->remarks);
				$worksheet->setCellValue('G'. $row, $r->insert_timestamp);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);
				$worksheet->getColumnDimension('G')->setAutoSize(true);

				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_member_deductions($type, $start_date, $end_date, $payout_id)
	{
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_member_deductions_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Member Deductions {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Member Deductions";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Member Deductions {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Deducted Amount');
			$worksheet->setCellValue('F' . $start_column_num, 'Details');
			$worksheet->setCellValue('G' . $start_column_num, 'Date Time');

			$deduction_rows = $this->_get_deduction_query($start_date,$end_date);

			$row = 4;
			foreach($deduction_rows->result() as $r)
			{
				$worksheet->setCellValue('A'. $row, $r->member_id);
				$worksheet->setCellValue('B'. $row, $r->last_name);
				$worksheet->setCellValue('C'. $row, $r->first_name);
				$worksheet->setCellValue('D'. $row, $r->middle_name);
				$worksheet->setCellValue('E'. $row, $r->deducted_amount);
				$worksheet->setCellValue('F'. $row, $r->remarks);
				$worksheet->setCellValue('G'. $row, $r->insert_timestamp);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);
				$worksheet->getColumnDimension('G')->setAutoSize(true);

				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_sms_count_deductions($type, $start_date, $end_date, $payout_id)
	{
		// insert only empty logs sheet

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_sms_count_deductions_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "{$type} Sms Count Deductions {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Sms Count Deductions";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "Sms Count Deductions {$start_date} to {$end_date}";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

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
            
			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            
			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
            $worksheet->setCellValue('E' . $start_column_num, 'Referral');
            $worksheet->setCellValue('F' . $start_column_num, 'Pairing');
			$worksheet->setCellValue('G' . $start_column_num, 'Total Sms Count');
			$worksheet->setCellValue('H' . $start_column_num, 'Total Sms Cost');
            
			$row = 4;

			$query = $this->_get_sms_deductions_query($start_date,$end_date);
			foreach ($query->result() as $r)
			{
				$worksheet->setCellValue('A'. $row, $r->member_id);
				$worksheet->setCellValue('B'. $row, $r->last_name);
				$worksheet->setCellValue('C'. $row, $r->first_name);
				$worksheet->setCellValue('D'. $row, $r->middle_name);
				$worksheet->setCellValue('E'. $row, $r->referral);
				$worksheet->setCellValue('F'. $row, $r->pairing);
                $worksheet->setCellValue('G'. $row, $r->sms_count);
                $worksheet->setCellValue('H'. $row, $r->sms_cost);

                				
				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);
                $worksheet->getColumnDimension('G')->setAutoSize(true);
                $worksheet->getColumnDimension('H')->setAutoSize(true);

                $worksheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
				
				$row++;
			
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			$worksheet->garbageCollect();
			unset($objPHPExcel);
			unset($objWriter);
			unset($worksheet);
			$worksheet = null;
			$objPHPExcel = null;
			$objWriter = null;
			foreach(array_diff(array_keys(get_defined_vars()), array_keys($GLOBALS)) as $v)
			{
				unset($v);
			}
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function _get_gc_transaction_logs_query_count($type,$start_date,$end_date)
	{
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		$trh_table_name = "trh_member_acct_credit_logs_" . $start . "_" . $end;
		$where_transaction_codes = "transaction_code IN (106,107,108,109)";
		$sql = "
			SELECT
			    COUNT(*) as cnt
			FROM
				{$trh_table_name} a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				{$where_transaction_codes}
			AND
				DATE(a.insert_timestamp) BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59'
			ORDER BY
				a.insert_timestamp DESC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	private function _get_gc_transaction_logs_query($type,$start_date,$end_date,$limit,$offset)
	{
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		$trh_table_name = "trh_member_acct_credit_logs_" . $start . "_" . $end;
		$where_transaction_codes = "transaction_code IN (106,107,108,109)";
		$sql = "
			SELECT
			    a.*,
				b.first_name,
				b.last_name,
				b.middle_name
			FROM
				{$trh_table_name} a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				{$where_transaction_codes}
			AND
				DATE(a.insert_timestamp) BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59'
			ORDER BY
				a.insert_timestamp DESC
			LIMIT {$limit} OFFSET {$offset}
			";
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_transaction_logs_query_count($type,$start_date,$end_date)
	{
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		$trh_table_name = "trh_member_acct_credit_logs_" . $start . "_" . $end;

		if($type=="IGPSM"){
			$where_transaction_codes = "transaction_code IN (100,101,102,103,104)";
		}else{
			$where_transaction_codes = "transaction_code = 105";
		}
		$sql = "
			SELECT
			    COUNT(*) as cnt
			FROM
				{$trh_table_name} a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				{$where_transaction_codes}
			AND
				DATE(a.insert_timestamp) BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59'
			ORDER BY
				a.insert_timestamp DESC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	private function _get_transaction_logs_query($type,$start_date,$end_date,$limit,$offset)
	{
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		$trh_table_name = "trh_member_acct_credit_logs_" . $start . "_" . $end;

		if($type=="IGPSM"){
			$where_transaction_codes = "transaction_code IN (100,101,102,103,104)";
		}else{
			$where_transaction_codes = "transaction_code = 105";
		}
		$sql = "
			SELECT
			    a.*,
				b.first_name,
				b.last_name,
				b.middle_name
			FROM
				{$trh_table_name} a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				{$where_transaction_codes}
			AND
				DATE(a.insert_timestamp) BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59'
			ORDER BY
				a.insert_timestamp DESC
			LIMIT {$limit} OFFSET {$offset}
			";
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_gc_per_member_query($start_date,$end_date)
	{
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		$sql = "
			SELECT
			     a.member_id,
			     b.last_name,
			     b.first_name,
			     b.middle_name,
			     SUM(a.amount) AS gross,
			     CASE
			          WHEN b.gift_cheques_variance < 0 THEN b.gift_cheques_variance
			          ELSE ''
			     END AS gc_variance
			FROM
			     ph_member_commissions_{$start}_{$end} a
			LEFT JOIN
			     cm_members b ON a.member_id = b.member_id
			WHERE
			     a.transaction_code IN (106, 107, 108, 109)
			AND
			     a.account_status_id IN (1,3)
			GROUP BY
			     a.member_id
			ORDER BY
			     b.last_name, b.first_name, b.middle_name";
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_gc_per_account_query($start_date,$end_date)
	{

		echo "start: _get_gc_per_account_query";

		//echo 'xx' . $this->account_lookup_table . 'xx';

		$sql = "
			SELECT
		    	a.member_id,				
				a.account_id,
				b.last_name,
				b.first_name,
				b.middle_name,
				d.transaction_description as `type`,
				SUM(a.amount) AS amount,
				CASE 
					WHEN c.account_status_id = 1 THEN 'ACTIVE' 
					WHEN c.account_status_id = 2 THEN 'INACTIVE' 
					WHEN c.account_status_id = 3 THEN 'COMPANY' 
				END AS account_status
			FROM
				po_member_commissions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			LEFT JOIN
				`{$this->account_lookup_table}` c ON c.account_id = a.account_id
			LEFT JOIN
				`rf_transaction_codes` d ON a.transaction_code = d.transaction_code
			WHERE
				a.transaction_code >= 106 AND a.transaction_code <= 109
			AND
				a.start_date = '{$start_date}'
			AND
				a.end_date = '{$end_date}'	
			GROUP BY
				a.transaction_code, a.account_id
			ORDER BY
				b.last_name,b.first_name,b.middle_name, a.account_id, a.transaction_code";

		echo $sql;		
				
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_member_payout_query($type,$start_date,$end_date)
	{
		$sql = "
			SELECT
				a.member_id,
				b.last_name,
				b.first_name,
				b.middle_name,
				b.metrobank_paycard_number,
				SUM(a.gross) AS amount,
				a.cash_card AS cash_card,
				c.depot_name,
				b.group_name,
				SUM(a.balance) AS balance
			FROM
				po_member_account_commissions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			LEFT JOIN 
				rf_service_depots c ON c.service_depot_id = b.service_depot
			WHERE 
				(a.account_status = 'ACTIVE' OR a.account_status='COMPANY')
			AND
				a.start_date = '{$start_date}'
			AND
				a.end_date = '{$end_date}'
			AND
				a.payout_type = '{$type}'
			GROUP BY
				a.member_id
			ORDER BY
				b.last_name,b.first_name,b.middle_name
		";

		$query = $this->db->query($sql);
		return $query;
	}

	private function get_member_payout($start_date, $end_date, $type)
	{
		$payout_id = 1;

		$query = $this->_get_member_payout_query($type,$start_date,$end_date);

		// set gcep as 0 percent. this will be set during crediting
		$psf_limit = 5000;
		$psf_type = 'percent';
		$psf_value = 0;

		$witholding_tax = $this->settings->witholding_tax;
		
		$html = "";
		foreach ($query->result() as $r)
		{
			$rfid_paycard_number = '';
			$rfid_account_number = '';

			// metrobank paycard account number
			if($r->cash_card != "TO FUNDS" && $r->cash_card != "TO FUNDS - Blank Paycard"){
				$metrobank_paycard = $this->members_model->get_member_rfid_cards(array(
					'paycard_number' => $r->cash_card
				));
				if(count($metrobank_paycard) > 0){
					$rfid_paycard_number = $metrobank_paycard[0]->paycard_number;
					$rfid_account_number = $metrobank_paycard[0]->account_number;
				}
			}
			
			$psf = 0;
			if($r->amount >= $psf_limit){
				$psf = $r->amount * ($psf_value / 100);
				if($psf_type!="percent"){
					$psf = $psf_value;
				}
			}

			$net_gross = $r->amount - $psf;
			$tax = ($r->cash_card == "TO FUNDS" || $r->cash_card == "TO FUNDS - Blank Paycard")?0:($net_gross * $witholding_tax);

			$net_tax = $net_gross - $tax;
			$payout_amount = $net_tax;

			$member_commission_report = $this->payout_model->get_member_commissions_report(array(
				'payout_id' => $payout_id,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'member_id' => $r->member_id
			));
			if(count($member_commission_report) == 0){
				$balance = $r->balance;

				// insert to po_member_commissions_report
				$data = array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id,
					'gross' => $r->amount,
					'psf' => $psf,
					'net_gross' => $net_gross,
					'tax' => $tax,
					'net_tax' => $net_tax,
					'balance' => $balance,
					'total_amount' => $payout_amount,
					'cash_card' => $r->cash_card
				);
				$this->payout_model->insert_member_commissions_report($data);

				// po_member_commissions_report log
				$log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAYOUT - MEMBER COMMISSION REPORT',
					'table_name' => 'po_member_commissions_report',
					'action' => 'ADD',
					'details_after' => json_encode($data)
				);
				$this->tracking_model->insert_logs('admin',$log_data);

			} else {

				// recompute data
				$psf = $member_commission_report[0]->psf;
				$gross = $r->amount;
				$net_gross = $gross - $psf;
				$tax = ($member_commission_report[0]->cash_card == "TO FUNDS" || $member_commission_report[0]->cash_card == "TO FUNDS - Blank Paycard")?0:$member_commission_report[0]->tax;
				$net_tax = $net_gross - $tax;
				$balance = $member_commission_report[0]->balance;
				$payout_amount = $net_tax - $balance;
				$cash_card = $member_commission_report[0]->cash_card;

				// update po_member_commissions_report
				$data_before = $this->payout_model->get_member_commissions_report(array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id
				));
				$this->payout_model->update_member_commissions_report(array(
					'gross' => $gross,
					'psf' => $psf,
					'net_gross' => $net_gross,
					'tax' => $tax,
					'net_tax' => $net_tax,
					'balance' => $balance,
					'total_amount' => $payout_amount,
					'cash_card' => $cash_card
				),array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id
				));
				$data_after = $this->payout_model->get_member_commissions_report(array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id
				));

				// po_member_commissions_report log
				$log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAYOUT - MEMBER COMMISSION REPORT',
					'table_name' => 'po_member_commissions_report',
					'action' => 'UPDATE',
					'details_before' => json_encode($data_before),
					'details_after' => json_encode($data_after)
				);
				$this->tracking_model->insert_logs('admin',$log_data);

			}

		}
	}

	private function truncate_payout()
	{
		$sql = "TRUNCATE `po_member_account_commissions`";
		$this->db->query($sql);
		$sql = "TRUNCATE `po_member_commissions`";
		$this->db->query($sql);
		$sql = "TRUNCATE `po_member_commissions_report`";
		$this->db->query($sql);
		$sql = "TRUNCATE `po_member_sms_deductions`";
		$this->db->query($sql);
	}

	private function process_commissions($start_date, $end_date, $type)
	{
		$start = strtotime($start_date);
		$end = strtotime($end_date);

		if($start >= $end){
			exit(0);
		}
		
		$member_payouts = $this->payout_model->get_member_payouts(array(
			'start_date' => $start_date, 
			'end_date' => $end_date
		));

		$psf_type = $this->input->post('psf_type');
		$psf_value = $this->input->post('psf_value');
		$psf_limit = $this->input->post('psf_limit');

		if(count($member_payouts) == 0){

			$this->payout_model->insert_member_payout(array(
				'type' => $type,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'psf_type' => $psf_type,
				'psf_value' => $psf_value,
				'psf_limit' => $psf_limit
			));
			$payout_id = $this->payout_model->insert_id();

		}else{
			$this->payout_model->update_member_payouts(array(
					'psf_type' => $psf_type,
					'psf_value' => $psf_value,
					'psf_limit' => $psf_limit
				), array('payout_id' => $member_payouts[0]->payout_id));
			$payout_id = $member_payouts[0]->payout_id;
		}
		
		// additional condition
		if ($type=='IGPSM')
		{
			//$condition = " AND a.transaction_code <> 105 ";
			$condition = " AND a.transaction_code NOT IN (105, 0) ";
		}
		else
		{
			$condition = " AND a.transaction_code = 105 ";
		}
			
		// make sure that there will be no duplication of commissions
		$this->db->query("DELETE FROM po_member_commissions WHERE start_date = '{$start_date}' AND end_date = '{$end_date}'");

		$sql = "INSERT INTO po_member_commissions (member_id,account_id,account_status_id,transaction_code,amount,payout_id,start_date,end_date)
			(
			SELECT
				a.member_id,
				a.account_id,
				b.account_status_id,
				a.transaction_code,
				SUM(a.amount) as amount,
				{$payout_id} as payout_id,
				'{$start_date}' as start_date,
				'{$end_date}' as end_date
			FROM {$this->trh_table_name} a
			LEFT JOIN {$this->account_lookup_table} b ON a.account_id = b.account_id
			WHERE DATE(a.insert_timestamp) BETWEEN '{$start_date}' AND '{$end_date}' AND a.amount > 0 {$condition}
			GROUP BY a.member_id,a.account_id,a.transaction_code,a.account_status_id
			)";
		$this->db->query($sql);

		// create po_member_commissions history table
		$ph_po_member_commissions = "ph_member_commissions_" . $start_date . "_" . $end_date;
		$ph_po_member_commissions = str_replace('-', '', $ph_po_member_commissions);
		$sql = "DROP TABLE IF EXISTS " . $ph_po_member_commissions;
		$this->db->query($sql);
		$sql = "
			CREATE TABLE " . $ph_po_member_commissions . "
			SELECT
				*
			FROM
				po_member_commissions
		";
		$this->db->query($sql);
		$sql = "ALTER TABLE " . $ph_po_member_commissions . " ADD PRIMARY KEY (  `commission_id` ), ADD KEY `member_id` ( `member_id` ), ADD KEY `account_id` ( `account_id` ), ADD KEY `start_date` (`start_date`,`end_date`)";
		$this->db->query($sql);

		// make sure that there will be no duplication of commissions
		$this->db->query("DELETE FROM po_member_account_commissions WHERE start_date = '{$start_date}' AND end_date = '{$end_date}'");

		if ($type=='IGPSM')
		{
			// IGPSM insert
			$sql = "INSERT INTO `po_member_account_commissions`
					(
					`payout_id`,
					`payout_type`,
					`member_id`,
					`account_id`,
					`gross`,
					`witholding_tax`,
					`net_of_tax`,
					`balance`,
					`total`,
					`cash_card`,
					`account_status`,
					`commission_status`,
					`start_date`,
					`end_date`
					)

					(
						SELECT 
						a.payout_id,
						'IGPSM' AS payout_type,
						a.member_id,
						a.account_id,
						SUM(a.amount),
						b.value AS witholding_tax,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS net_of_tax,
						CASE
							WHEN SUM(a.amount) >= e.cd_amount THEN e.cd_amount
							ELSE SUM(a.amount)
						END AS balance,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS total,
						CASE
							WHEN c.is_auto_payout = 0 THEN 'TO FUNDS'
							ELSE (
								CASE
									WHEN c.metrobank_paycard_number IS NOT NULL THEN (
										CASE
											WHEN c.metrobank_paycard_number <> '' THEN REPLACE(REPLACE(c.metrobank_paycard_number, '-', ''), ' ', '')
											ELSE 'TO FUNDS - Blank Paycard' 
										END
									)										
									ELSE 'TO FUNDS - Blank Paycard'
								END
							)
						END AS cash_card,
						CASE 
							WHEN d.account_status_id = 1 THEN 'ACTIVE' 
							WHEN d.account_status_id = 2 THEN 'INACTIVE' 
							WHEN d.account_status_id = 3 THEN 'COMPANY' 
						END AS account_status,
						f.status AS commission_status,
						a.start_date,
						a.end_date
						FROM 
							po_member_commissions a
						LEFT JOIN
							rf_settings b ON b.slug = 'witholding_tax'
						LEFT JOIN
							cm_members c ON a.member_id = c.member_id
						LEFT JOIN
							{$this->account_lookup_table} d ON d.account_id = a.account_id
						LEFT JOIN
							cm_member_accounts e ON e.account_id = a.account_id
						LEFT JOIN
							po_member_payouts f ON a.payout_id = f.payout_id AND a.start_date = f.start_date AND a.end_date = f.end_date
						WHERE
							a.transaction_code IN (100,101,102,103,104)
						GROUP BY
							a.member_id, a.account_id, a.account_status_id
					)
			";
			$this->db->query($sql);
		}
		else
		{
			// UNILEVEL insert
			$sql = "INSERT INTO `po_member_account_commissions`
					(
					`payout_id`,
					`payout_type`,
					`member_id`,
					`account_id`,
					`gross`,
					`witholding_tax`,
					`net_of_tax`,
					`total`,
					`cash_card`,
					`account_status`,
					`commission_status`,
					`start_date`,
					`end_date`
					)

					(
						SELECT 
						a.payout_id,
						'UNILEVEL' AS payout_type,
						a.member_id,
						a.account_id,
						SUM(a.amount),
						b.value AS witholding_tax,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS net_of_tax,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS total,
						CASE
							WHEN c.is_auto_payout = 0 THEN 'TO FUNDS'
							ELSE (
								CASE
									WHEN c.metrobank_paycard_number IS NOT NULL THEN (
										CASE
											WHEN c.metrobank_paycard_number <> '' THEN REPLACE(REPLACE(c.metrobank_paycard_number, '-', ''), ' ', '')
											ELSE 'TO FUNDS - Blank Paycard' 
										END
									)										
									ELSE 'TO FUNDS - Blank Paycard'
								END
							)
						END AS cash_card,
						CASE 
							WHEN d.account_status_id = 1 THEN 'ACTIVE' 
							WHEN d.account_status_id = 2 THEN 'INACTIVE' 
							WHEN d.account_status_id = 3 THEN 'COMPANY' 
						END AS account_status,
						f.status AS commission_status,
						a.start_date,
						a.end_date
						FROM 
							po_member_commissions a
						LEFT JOIN
							rf_settings b ON b.slug = 'witholding_tax'
						LEFT JOIN
							cm_members c ON a.member_id = c.member_id
						LEFT JOIN
							{$this->account_lookup_table} d ON d.account_id = a.account_id
						LEFT JOIN
							po_member_payouts f ON a.payout_id = f.payout_id AND a.start_date = f.start_date AND a.end_date = f.end_date
						WHERE
							a.transaction_code = 105
						GROUP BY
							a.member_id, a.account_id, a.account_status_id
					)
			";
			$this->db->query($sql);
		}

		// create po_member_account_commissions history table
		$ph_po_member_account_commissions = "ph_member_account_commissions_" . $start_date . "_" . $end_date;
		$ph_po_member_account_commissions = str_replace('-', '', $ph_po_member_account_commissions);
		$sql = "DROP TABLE IF EXISTS " . $ph_po_member_account_commissions;
		$this->db->query($sql);
		$sql = "
			CREATE TABLE " . $ph_po_member_account_commissions . "
			SELECT
				a.*,
				b.account_status_id AS current_account_status_id
			FROM
				po_member_account_commissions a
			LEFT JOIN
				{$this->account_lookup_table} b ON a.account_id = b.account_id
		";
		$this->db->query($sql);
		$sql = "ALTER TABLE " . $ph_po_member_account_commissions . " ADD PRIMARY KEY (  `commission_id` ), ADD KEY `member_id` (  `member_id` ), ADD KEY `account_id` ( `account_id` ), ADD KEY `start_date` ( `start_date` ), ADD KEY `end_date` ( `end_date` ), ADD KEY `start_end_date` (`start_date`,`end_date`)";
		$this->db->query($sql);
	}

	private function _get_deduction_query($start_date,$end_date)
	{
		$sql = "
			SELECT
				a.member_id,
				b.last_name,
				b.first_name,
				b.middle_name,
				a.deducted_amount,
				c.remarks,
				a.insert_timestamp
			FROM
				po_member_deductions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			LEFT JOIN
				cm_member_deductions c ON a.deduction_id = c.deduction_id
			ORDER BY
				a.member_id ASC
		";
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_adjustment_query($start_date,$end_date)
	{
		$sql = "
			SELECT
				a.member_id,
				b.last_name,
				b.first_name,
				b.middle_name,
				a.adjusted_amount,
				c.remarks,
				a.insert_timestamp
			FROM
				po_member_adjustments a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			LEFT JOIN
				cm_member_adjustments c ON a.adjustment_id = c.adjustments_id
			ORDER BY
				a.member_id ASC
		";
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_sms_deductions_query($start_date,$end_date)
	{
		$start = str_replace("-", "", $start_date);
		$end = str_replace("-", "", $end_date);
		
		/*$sql = "
			SELECT
			   a.member_id, 
               b.last_name, 
               b.first_name, 
               b.middle_name,
               sum(a.referral) as referral, 
               sum(a.pairing) as pairing,
               sum(a.total_sms) as sms_count, 
               sum(a.total_cost) as sms_cost
			FROM
				po_member_sms_deductions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			GROUP BY
				a.member_id
			";
		*/

		$sql = "
			SELECT
			   a.member_id, 
               b.last_name, 
               b.first_name, 
               b.middle_name,
               sum(a.referral) as referral, 
               sum(a.pairing) as pairing,
               sum(a.total_sms) as sms_count, 
               sum(a.total_sms) as sms_cost
			FROM
				po_member_sms_deductions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			GROUP BY
				a.member_id
			";
			
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_vouchers_released($start_date,$end_date)
	{
		$sql = "
			SELECT
				a.voucher_code,
				CASE
					WHEN a.voucher_type_id = 1 THEN 'FPV'
					WHEN a.voucher_type_id = 2 THEN 'MPV'
				END AS voucher_type,
				a.member_id,
				b.first_name,
				b.middle_name,
				b.last_name,
				a.insert_timestamp
			FROM
				cm_member_account_vouchers a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				a.voucher_type_id IN (1,2)
			AND
				a.insert_timestamp BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59'
			ORDER BY
				a.insert_timestamp
		";
		$query = $this->db->query($sql);
		return $query->result();
	}

	private function _get_commission_deduct_completions($start_date,$end_date)
	{
		$sql = "
			SELECT
				a.member_id,
				b.first_name,
				b.middle_name,
				b.last_name,
				a.account_id,
				a.insert_timestamp
			FROM
				trg_commission_deduct_completion a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				a.insert_timestamp BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59'
			ORDER BY
				a.insert_timestamp
		";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function _credit_completed_cd_as_erhm()
	{
		$sql = "
			SELECT * FROM trg_commission_deduct_completion WHERE credit_flag = 0
			";

		$query = $this->db->query($sql);
		$completed_cd = $query->result();

		$cd_ = $this->cards_model->get_card_type_by_code('CD-ERHM');
		$erhm = $this->cards_model->get_card_type_by_code('ERHM');

		if(sizeof($completed_cd)>0)
		{
			foreach($completed_cd as $cd)
			{
				$account = $this->members_model->get_member_account_by_account_id($cd->account_id);
				$position = substr($account->node_address,-1) == 1 ? "right" : "left";

				if($account->account_type_id == $cd_->card_type_id)
				{
					$params = array();
					$params["account_id"] = $account->upline_id;
					$params["card_id"] = $cd->account_id;
					$params["type"] = 'ERHM';
					$params["position"] = $position;
					$params["points"] = $erhm->points;

					Modules::run('jobs/commissions/credit_points',$params); //upline_id
					Modules::run('jobs/commissions/credit_referral_bonus',array('from_account_id' => $cd_->account_id,'card_id' => $cd_->account_id,'account_id' => $cd_->sponsor_id,'type'=>'ERHM')); 
					
					$sql = "UPDATE trg_commission_deduct_completion SET credit_flag = '1' WHERE cd_completion_id = '".$cd->cd_completion_id."'";
					$this->db->query($sql);
				}
			}
		}
	}


	private function populate_member_sms_deductions_table($start_date, $end_date, $payout_type)
	{

		$is_unilevel_tb = $this->settings->enable_unilevel_text_blast;

		if ($payout_type == "IGPSM") {
			$sql = "
	                INSERT INTO po_member_sms_deductions (
	                    `member_id`,
	                    `account_id`,
	                    `referral`,
	                    `pairing`,
	                    `total_sms`,
	                    `total_cost`
	                )
	                (SELECT
					    member_id, 
	                    account_id, 
	                    0 as referral, 
	                    0 as pairing, 
	                    count(sms_sent) as total_sms,
	                    sum(tariff) as total_cost
				     FROM
					    ". $this->trh_table_name ."
	                WHERE 
	                    sms_sent = 1
	                 AND
	                    transaction_code NOT IN (105, 0)     
				     AND
					    DATE(insert_timestamp) BETWEEN '". $start_date ."' AND '". $end_date ."'
				     GROUP BY
					    member_id, account_id
	                )
			";
			$query = $this->db->query($sql);
	        
	        // update referral column
	        $sql = "
	                UPDATE po_member_sms_deductions a
	                SET a.referral = 
	                    (SELECT 
	                        COUNT(b.sms_sent)
	                    FROM 
	                        ". $this->trh_table_name ." b
	                    WHERE 
	                        b.transaction_code NOT IN (105, 0)
	                    AND
	                        b.sms_sent = 1
	                    AND 
	                        a.member_id = b.member_id
	                    AND 
	                        a.account_id = b.account_id    
	                    AND 
	                        DATE(b.insert_timestamp) BETWEEN '". $start_date ."' AND '". $end_date ."'
	                    AND 
	                        LOWER(b.remarks) LIKE '%credit%referral bonus%'
	                    )
	        ";
	         
	        $query = $this->db->query($sql);
	                        
	        // update pairing column
	         $sql = "
	                UPDATE po_member_sms_deductions a
	                SET a.pairing = 
	                    (SELECT 
	                        COUNT(b.sms_sent)
	                    FROM 
	                        ". $this->trh_table_name ." b
	                    WHERE 
	                        b.transaction_code NOT IN (105, 0)
	                    AND
	                        b.sms_sent = 1
	                    AND 
	                        a.member_id = b.member_id
	                    AND 
	                        a.account_id = b.account_id
	                    AND 
	                        DATE(b.insert_timestamp) BETWEEN '". $start_date ."' AND '". $end_date ."'
	                    AND 
	                        LOWER(b.remarks) LIKE '%credit%pair%'
	                    )
	        ";
	         
	        $query = $this->db->query($sql);
	    
	    } else {

	    	if ($is_unilevel_tb == 1) {

	    		$sql = "
	                INSERT INTO po_member_sms_deductions (
	                    `member_id`,
	                    `account_id`,
	                    `referral`,
	                    `pairing`,
	                    `total_sms`,
	                    `total_cost`
	                )
	                (SELECT
					    member_id, 
	                    account_id, 
	                    0 as referral, 
	                    0 as pairing, 
	                    count(sms_sent) as total_sms,
	                    sum(tariff) as total_cost
				     FROM
					    ". $this->trh_table_name ."
	                WHERE 
	                    sms_sent = 1
	                 AND
	                    transaction_code = 105     
				     AND
					    DATE(insert_timestamp) BETWEEN '". $start_date ."' AND '". $end_date ."'
				     GROUP BY
					    member_id, account_id
	                )
				";
				$query = $this->db->query($sql);

	    	}
	    }
	}  
}