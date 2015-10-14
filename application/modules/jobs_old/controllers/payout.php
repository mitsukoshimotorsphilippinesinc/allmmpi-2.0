<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Payout extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $this->load->model('payout_model');
        $this->load->model('members_model');
        $this->load->model('facilities_model');
        $this->load->model('tracking_model');
	}
	
	public function index() 
	{
		echo "Process Payout...";
	}

	public function process ($params=array()) 
	{
		$sheet_id = trim($params['sheet_id']);
		$type = trim($params['type']);
		$start_date = trim($params['start_date']);
		$end_date = trim($params['end_date']);
		$payout_id = trim($params['payout_id']);

		$sheet = $this->payout_model->get_payout_download_sheets(array(
			'sheet_id' => $sheet_id
		));

		if(count($sheet) > 0)
		{
			$sheet = $sheet[0];

			$method = $sheet->sheet_method;
			$this->$method($type, $start_date, $end_date, $payout_id);

			echo "SUCCESS";
		}
		else
		{
			echo "FAILED";
		}
        
        return;	
	}

	private function segmented_gc_transaction_logs($type, $start_date, $end_date, $payout_id)
	{
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
						$worksheet->setCellValue('F'. $row, $r->remarks);
						$worksheet->setCellValue('G'. $row, $r->type);
						$worksheet->setCellValue('H'. $row, $r->level);
						$worksheet->setCellValue('I'. $row, $r->amount);
						$worksheet->setCellValue('J'. $row, $r->insert_timestamp);

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

						$row++;
					}
				}
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function segmented_transaction_logs($type, $start_date, $end_date, $payout_id)
	{
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
						$worksheet->setCellValue('F'. $row, $r->remarks);
						$worksheet->setCellValue('G'. $row, $r->type);
						$worksheet->setCellValue('H'. $row, $r->level);
						$worksheet->setCellValue('I'. $row, $r->amount);
						$worksheet->setCellValue('J'. $row, $r->insert_timestamp);

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

						$row++;
					}
				}
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);
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

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);
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

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);
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
					$worksheet->setCellValue('G'. $row, $sortedRows[$v][$i]->account_status);

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

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);
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
			$worksheet->setCellValue('E' . $start_column_num, 'Gross');
			$worksheet->setCellValue('F' . $start_column_num, 'Tax');
			$worksheet->setCellValue('G' . $start_column_num, 'Net');
			$worksheet->setCellValue('H' . $start_column_num, 'Balance');
			$worksheet->setCellValue('I' . $start_column_num, 'Total');
			$worksheet->setCellValue('J' . $start_column_num, 'Cash Card');
			$worksheet->setCellValue('K' . $start_column_num, 'Commission Date');

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

				$account = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));
				$account = $account[0];
				$c->account_insert_timestamp = $account->insert_timestamp;

				$date_registered_time = date(strtotime($c->account_insert_timestamp));
				$curr_time = date(time());

				$difference = $curr_time - $date_registered_time;
				$months = floor($difference / 86400 / 30 );

				if($months > 3) continue;

				$member_obj = new stdClass;
				$member_obj->member_id = $c->member_id;
				$member_obj->last_name = $c->last_name;
				$member_obj->first_name = $c->first_name;
				$member_obj->middle_name = $c->middle_name;
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

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);
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
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('C' . $start_column_num, 'First Name');
			$worksheet->setCellValue('D' . $start_column_num, 'Middle Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Account ID');
			$worksheet->setCellValue('F' . $start_column_num, 'Gross');
			$worksheet->setCellValue('G' . $start_column_num, 'Tax');
			$worksheet->setCellValue('H' . $start_column_num, 'Net');
			$worksheet->setCellValue('I' . $start_column_num, 'Balance');
			$worksheet->setCellValue('J' . $start_column_num, 'Total');
			$worksheet->setCellValue('K' . $start_column_num, 'Cash Card');
			$worksheet->setCellValue('L' . $start_column_num, 'Account Status');
			$worksheet->setCellValue('M' . $start_column_num, 'Date Registered');
			$worksheet->setCellValue('N' . $start_column_num, 'Commission Date');

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

				$account = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));
				$account = $account[0];
				$c->account_insert_timestamp = $account->insert_timestamp;

				if($filter=="new_accounts"){
					$date_registered_time = date(strtotime($c->account_insert_timestamp));
					$curr_time = date(time());

					$difference = $curr_time - $date_registered_time;
					$months = floor($difference / 86400 / 30 );

					if($months > 3) continue;
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

				$worksheet->setCellValue('A'. $row, $c->member_id);
				$worksheet->setCellValue('B'. $row, $c->last_name);
				$worksheet->setCellValue('C'. $row, $c->first_name);
				$worksheet->setCellValue('D'. $row, $c->middle_name);
				$worksheet->setCellValue('E'. $row, $c->account_id);
				$worksheet->setCellValue('F'. $row, $c->amount);
				$worksheet->setCellValue('G'. $row, $tax);
				$worksheet->setCellValue('H'. $row, "=F{$row} - G{$row}");
				$worksheet->setCellValue('I'. $row, $balance);
				$worksheet->setCellValue('J'. $row, "=H{$row} - I{$row}");
				$worksheet->setCellValueExplicit('K'. $row, "{$c->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValue('L'. $row, $c->account_status);
				$worksheet->setCellValue('M'. $row, $c->insert_timestamp);
				$worksheet->setCellValue('N'. $row, $commission_date);

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

				$row++;			
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);

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
			$worksheet->setCellValue('E' . $start_column_num, 'Gross');
			$worksheet->setCellValue('F' . $start_column_num, 'Less 10%');
			$worksheet->setCellValue('G' . $start_column_num, 'Net Gross');
			$worksheet->setCellValue('H' . $start_column_num, 'Tax');
			$worksheet->setCellValue('I' . $start_column_num, 'Net of Tax');
			$worksheet->setCellValue('J' . $start_column_num, 'Balance');
			$worksheet->setCellValue('K' . $start_column_num, 'Total');
			$worksheet->setCellValue('L' . $start_column_num, 'Cash Card');
			$worksheet->setCellValue('M' . $start_column_num, 'Account Number');
			$worksheet->setCellValue('N' . $start_column_num, 'Service Depot');
			$worksheet->setCellValue('O' . $start_column_num, 'Group Name');
			
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
				$worksheet->setCellValue('K'. $row, "=I{$row} - J{$row}");
				$worksheet->setCellValueExplicit('L'. $row, "{$r->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValueExplicit('M'. $row, "{$rfid_account_number}",PHPExcel_Cell_DataType::TYPE_STRING);
				$worksheet->setCellValue('N'. $row, $service_depot);
				$worksheet->setCellValue('O'. $row, $member->group_name);

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

				// format total amount if negative
				$worksheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
				
				$row++;
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
			//exit(0);

		} catch (Exception $e) {
			//exit($e->getMessage());
		}
	}

	private function _get_gc_transaction_logs_query_count($type,$start_date,$end_date)
	{
		$where_transaction_codes = "transaction_code IN (106,107,108,109)";
		$sql = "
			SELECT
			    COUNT(*) as cnt
			FROM
				tr_member_acct_credit_logs a
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
		$where_transaction_codes = "transaction_code IN (106,107,108,109)";
		$sql = "
			SELECT
			    a.*,
				b.first_name,
				b.last_name,
				b.middle_name
			FROM
				tr_member_acct_credit_logs a
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
		if($type=="IGPSM"){
			$where_transaction_codes = "transaction_code IN (100,101,102,103,104)";
		}else{
			$where_transaction_codes = "transaction_code = 105";
		}
		$sql = "
			SELECT
			    COUNT(*) as cnt
			FROM
				tr_member_acct_credit_logs a
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
				tr_member_acct_credit_logs a
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

	private function _get_gc_per_account_query($start_date,$end_date)
	{
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
				cm_member_accounts c ON c.account_id = a.account_id
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
		$query = $this->db->query($sql);
		return $query;
	}
}