<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_Logs extends Base_Controller
{
	
	public $start_date;
	public $end_date;
	public $type;
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("members_model");
		$this->load->model("tracking_model");
	}
	
	public function index()
	{
		$this->display_credit_logs();
	}
	
	public function display_credit_logs()
	{
		$from_date = trim($this->input->get_post('from_date'));
		$to_date = trim($this->input->get_post('to_date'));
		
		$export = $this->input->get_post('export');
		$where = "";
		if (empty($from_date)) $from_date = date('Y-m-d 12:00 \a\m');
		if (empty($to_date)) $to_date = date('Y-m-d h:i a');
		
		//date set
		$from_dt = $from_date;
		$to_dt = $to_date;
		$from_single = "";
		$from_t = strtotime($from_date);
		$to_t = strtotime($to_date);
		if ($from_t !== false) $from_dt = date('Y-m-d H:i:s', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d H:i:s', $to_t); 
				
		if ($from_t !== false && $to_t !== false)
			$where .= "(date_format(insert_timestamp, '%Y-%c-%d %H:%i:00') BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		else if ($from_t !== false && $to_t === false)
			$where .= "insert_timestamp >= '{$from_dt}'";
		else if ($from_t === false && $to_t !== false)
			$where .= "insert_timestamp <= '{$to_dt}'";
			
		$data = new ArrayClass(array(
			'from_date' => $from_dt,
			'to_date' => $to_dt,
			'where' => $where,
		));
		
		// check if to export
		if ($export == 'excel')
		{
			$this->export($data);
		}
		else
		{	
			$this->template->from_date = $from_date;
			$this->template->to_date = $to_date;
			$this->template->view('acct_credit_logs_view');
		}
	}

	public function export($data)
	{
		$from_date = slugify($data->from_date);
		$to_date = slugify($data->to_date);
		$where = $data->where;
		$current_date = date('Y-m-d');
		
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();

		$offset = 0;
		$rows_per_page = 1000;
		$total_records = $this->tracking_model->get_acct_credit_logs_count($where);
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		
		for ($page = 0; $page < $cnt; $page++)
		{
			$limit = array('rows' => 1000, 'offset' => $page * $rows_per_page);
			$credit_logs = $this->tracking_model->get_acct_credit_logs($where, $limit);
			
			if(!empty($credit_logs))
			{
				$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
				$start_column_num = 4;
					
				// auto resize columns
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
				
				// set column header to bold		
				$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num. ':I' . $start_column_num)->getFont()->setBold(true);
					
				//center column names
				$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$header = "Credit Logs";
				//$header2 = "as of " . $date;
				$header2 = "for " . $from_date . '-' . $to_date;
				$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
				$objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
					
				$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('A2:I2');
				$objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
				
				//set column names
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Member Name');
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'Account ID');
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Card ID'); 
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'From Account ID'); 
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Transaction'); 
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'Type'); 
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'Amount'); 
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $start_column_num, 'Remarks'); 
				$objPHPExcel->getActiveSheet()->setCellValue('I' . $start_column_num, 'Insert Timestamp'); 
					
				$objPHPExcel->getActiveSheet()->freezePane('A5');
				$row = $start_column_num + 1;
				
				foreach($credit_logs as $c)
				{
					//get member 
					$member_details = $this->members_model->get_member_by_id($c->member_id);
					$member_name = $member_details->last_name. ' ' . $member_details->first_name . ' ' . $member_details->middle_name;
					
					$transaction_type = $this->members_model->get_transaction_codes(array('transaction_code' => $c->transaction_code));
					$transaction_type = $transaction_type[0];
					if(!empty($transaction_type))
						$transaction_desc = $transaction_type->transaction_description;
					else
						$transaction_desc = "Undefined";
						
					$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $member_name);
					$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $c->account_id);
					$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $c->card_id);
					$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $c->from_account_id);
					$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $transaction_desc);
					$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $c->type);
					$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $c->amount);
					$objPHPExcel->getActiveSheet()->setCellValue('H'. $row, $c->remarks);
					$objPHPExcel->getActiveSheet()->setCellValue('I'. $row, $c->insert_timestamp);
					
					$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$row++;
				}
			}
		}
		
		$objPHPExcel->setActiveSheetIndex(0);		
		$filename_date = $from_date . '-' . $to_date;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="credit_logs'.$filename_date.'.xls"');
		header('Cache-Control: max-age=0');		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');		
	}


}