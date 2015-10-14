<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tmp_Commissions extends Base_Controller
{
	
	public $start_date;
	public $end_date;
	public $type;
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("members_model");
		$this->load->model("tracking_model");
		$this->load->model("payout_model");
	}
	
	public function index()
	{
		$this->display_tmp_commissions();
	}
	
	public function display_tmp_commissions()
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
			$this->template->view('tmp_commissions_view');
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

		//set codes, hardcoded for now
		$sp_gc_code = 106;
		$vp_gc_code = 107;
		$tp_gc_code = 108;
		$rs_gc_code = 109;
		
		//get sp first
		$offset = 0;
		$rows_per_page = 1000;
		$tmp_where = $where. " AND transaction_code = '{$sp_gc_code}' GROUP BY account_id";
		$tmp_select = "member_id, account_id, transaction_code, sum(amount) as amount";
		$records  = $this->payout_model->get_tmp_commissions($tmp_where);
		$total_records = count($records);
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$objPHPExcel->getActiveSheet()->setTitle('SP Gift Cheques');
		
			//$limit = array('rows' => 1000, 'offset' => $page * $rows_per_page);
			$sp_gc_tmp = $this->payout_model->get_tmp_commissions($tmp_where, null, null, $tmp_select);
			
			if(!empty($sp_gc_tmp))
			{
				$start_column_num = 4;
				$total_quantity = 0;
				$total_amount = 0;
					
				// auto resize columns
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				
				// set column header to bold		
				$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num. ':G' . $start_column_num)->getFont()->setBold(true);
					
				//center column names
				$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$header = "VITAL C HEALTH PRODUCTS, INC.";
				$header2 = "SP GC Commissions Per Account";
				$header3 = "For Payout Period" . $from_date . ' - ' . $to_date;
				$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
				$objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
					
				$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
				$objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
				
				$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
				$objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
				
				//set column names
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Last Name');
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'First Name');
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Middle Name');
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'Account ID');
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Amount'); 
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'Quantity'); 
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'Status');
					
				$objPHPExcel->getActiveSheet()->freezePane('A5');
				$row = $start_column_num + 1;
				
				foreach($sp_gc_tmp as $c)
				{
					//get member 
					$member_details = $this->members_model->get_member_by_id($c->member_id);
					$member_name = $member_details->last_name. ' ' . $member_details->first_name . ' ' . $member_details->middle_name;
					
					$account_details = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));	
					$account_details = $account_details[0];
					$account_status = $this->members_model->get_member_account_status_by_id($account_details->account_status_id);
					
					$sp_gc_list = $this->payout_model->get_tmp_commissions($where. " AND transaction_code = '{$sp_gc_code}' AND account_id = '{$c->account_id}'");
					
					$qty = count($sp_gc_list);
						
					$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $member_details->last_name);
					$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $member_details->first_name);
					$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $member_details->middle_name);
					$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $account_details->account_id);
					$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, number_format($c->amount, 2));
					$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $qty);
					$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $account_status->account_status);
					
					$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$total_amount += $c->amount;
					$total_quantity += $qty;
					
					$row++;
				}
				$total_amount = number_format($total_amount, 2);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, "TOTALS");
				$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $total_amount);
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $total_quantity);
				
				$totals_border = array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				);

				$style_totals = array(
					'borders' => array(
						'bottom' => $totals_border,
						'left' => $totals_border,
						'top' => $totals_border,
						'right' => $totals_border,
					),
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb'=>'FBEC5D'),
					),
					'font' => array(
						'bold' => true,
					)
				);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($style_totals);
				
				$row++;
			}
		
		
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1);		
		
		//get vp
		$offset = 0;
		$rows_per_page = 1000;
		$tmp_where = $where. " AND transaction_code = '{$vp_gc_code}' GROUP BY account_id";
		$tmp_select = "member_id, account_id, transaction_code, sum(amount) as amount";
		$records  = $this->payout_model->get_tmp_commissions($tmp_where);
		$total_records = count($records);
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$objPHPExcel->getActiveSheet()->setTitle('VP Gift Cheques');

		$vp_gc_tmp = $this->payout_model->get_tmp_commissions($tmp_where, null, null, $tmp_select);
			
		if(!empty($vp_gc_tmp))
		{
			$start_column_num = 4;
			$total_quantity = 0;
			$total_amount = 0;
				
			// auto resize columns
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			
			// set column header to bold		
			$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num. ':G' . $start_column_num)->getFont()->setBold(true);
				
			//center column names
			$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$header = "VITAL C HEALTH PRODUCTS, INC.";
			$header2 = "VP GC Commissions Per Account";
			$header3 = "For Payout Period" . $from_date . ' - ' . $to_date;
			$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
				
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
			
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
			
			//set column names
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Last Name');
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'First Name');
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Middle Name');
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'Account ID');
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Amount'); 
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'Quantity'); 
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'Status'); 
				
			$objPHPExcel->getActiveSheet()->freezePane('A5');
			$row = $start_column_num + 1;
			
			foreach($vp_gc_tmp as $c)
			{
				//get member 
				$member_details = $this->members_model->get_member_by_id($c->member_id);
				$member_name = $member_details->last_name. ' ' . $member_details->first_name . ' ' . $member_details->middle_name;
				
				$account_details = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));	
				$account_details = $account_details[0];
				$account_status = $this->members_model->get_member_account_status_by_id($account_details->account_status_id);
				
				$vp_gc_list = $this->payout_model->get_tmp_commissions($where. " AND transaction_code = '{$vp_gc_code}' AND account_id = '{$c->account_id}'");
				
				$qty = count($vp_gc_list);
					
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $member_details->last_name);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $member_details->first_name);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $member_details->middle_name);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $account_details->account_id);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, number_format($c->amount, 2));
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $qty);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $account_status->account_status);
				
				$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$total_amount += $c->amount;
				$total_quantity += $qty;
				
				$row++;
			}
			$total_amount = number_format($total_amount, 2);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, "TOTALS");
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $total_amount);
			$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $total_quantity);
			
			$totals_border = array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb'=>'000000')
			);

			$style_totals = array(
				'borders' => array(
					'bottom' => $totals_border,
					'left' => $totals_border,
					'top' => $totals_border,
					'right' => $totals_border,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb'=>'FBEC5D'),
				),
				'font' => array(
					'bold' => true,
				)
			);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($style_totals);
			
			$row++;
		}
		
		
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(2);		
		
		//get tp
		$offset = 0;
		$rows_per_page = 1000;
		$tmp_where = $where. " AND transaction_code = '{$tp_gc_code}' GROUP BY account_id";
		$tmp_select = "member_id, account_id, transaction_code, sum(amount) as amount";
		$records  = $this->payout_model->get_tmp_commissions($tmp_where);
		$total_records = count($records);
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$objPHPExcel->getActiveSheet()->setTitle('TP Gift Cheques');
		
		$tp_gc_tmp = $this->payout_model->get_tmp_commissions($tmp_where, null, null, $tmp_select);
			
		if(!empty($tp_gc_tmp))
		{
			$start_column_num = 4;
			$total_quantity = 0;
			$total_amount = 0;
					
			// auto resize columns
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			
			// set column header to bold		
			$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num. ':G' . $start_column_num)->getFont()->setBold(true);
				
			//center column names
			$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$header = "VITAL C HEALTH PRODUCTS, INC.";
			$header2 = "TP GC Commissions Per Account";
			$header3 = "For Payout Period" . $from_date . ' - ' . $to_date;
			$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
				
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
			
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
			
			//set column names
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Last Name');
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'First Name');
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Middle Name');
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'Account ID');
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Amount'); 
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'Quantity'); 
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'Status'); 
				
			$objPHPExcel->getActiveSheet()->freezePane('A5');
			$row = $start_column_num + 1;
			
			foreach($tp_gc_tmp as $c)
			{
				//get member 
				$member_details = $this->members_model->get_member_by_id($c->member_id);
				$member_name = $member_details->last_name. ' ' . $member_details->first_name . ' ' . $member_details->middle_name;
				
				$account_details = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));	
				$account_details = $account_details[0];
				$account_status = $this->members_model->get_member_account_status_by_id($account_details->account_status_id);
				
				$tp_gc_list = $this->payout_model->get_tmp_commissions($where. " AND transaction_code = '{$tp_gc_code}' AND account_id = '{$c->account_id}'");
				
				$qty = count($tp_gc_list);
					
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $member_details->last_name);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $member_details->first_name);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $member_details->middle_name);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $account_details->account_id);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, number_format($c->amount, 2));
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $qty);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $account_status->account_status);
				
				$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$total_amount += $c->amount;
				$total_quantity += $qty;
				
				$row++;
			}
			$total_amount = number_format($total_amount, 2);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, "TOTALS");
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $total_amount);
			$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $total_quantity);
			
			$totals_border = array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb'=>'000000')
			);

			$style_totals = array(
				'borders' => array(
					'bottom' => $totals_border,
					'left' => $totals_border,
					'top' => $totals_border,
					'right' => $totals_border,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb'=>'FBEC5D'),
				),
				'font' => array(
					'bold' => true,
				)
			);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($style_totals);
			
			$row++;
		}
	
		
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(3);		
		
		//get rs
		$offset = 0;
		$rows_per_page = 1000;
		$tmp_where = $where. " AND transaction_code = '{$rs_gc_code}' GROUP BY account_id";
		$tmp_select = "member_id, account_id, transaction_code, sum(amount) as amount";
		$records  = $this->payout_model->get_tmp_commissions($tmp_where);
		$total_records = count($records);
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$objPHPExcel->getActiveSheet()->setTitle('RS Gift Cheques');
		
		$rs_gc_tmp = $this->payout_model->get_tmp_commissions($tmp_where, null, null, $tmp_select);
			
		if(!empty($rs_gc_tmp))
		{
			$start_column_num = 4;
			$total_quantity = 0;
			$total_amount = 0;
					
			// auto resize columns
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				
			// set column header to bold		
			$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num. ':G' . $start_column_num)->getFont()->setBold(true);
				
			//center column names
			$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$header = "VITAL C HEALTH PRODUCTS, INC.";
			$header2 = "TP GC Commissions Per Account";
			$header3 = "For Payout Period" . $from_date . ' - ' . $to_date;
			$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
				
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
			
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
			
			//set column names
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Last Name');
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'First Name');
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Middle Name');
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'Account ID');
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Amount'); 
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'Quantity'); 
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'Status'); 
				
			$objPHPExcel->getActiveSheet()->freezePane('A5');
			$row = $start_column_num + 1;
			
			foreach($rs_gc_tmp as $c)
			{
				//get member 
				$member_details = $this->members_model->get_member_by_id($c->member_id);
				$member_name = $member_details->last_name. ' ' . $member_details->first_name . ' ' . $member_details->middle_name;
				
				$account_details = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));	
				$account_details = $account_details[0];
				$account_status = $this->members_model->get_member_account_status_by_id($account_details->account_status_id);
				
				$rs_gc_list = $this->payout_model->get_tmp_commissions($where. " AND transaction_code = '{$rs_gc_code}' AND account_id = '{$c->account_id}'");
				
				$qty = count($rs_gc_list);
					
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $member_details->last_name);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $member_details->first_name);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $member_details->middle_name);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $account_details->account_id);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, number_format($c->amount, 2));
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $qty);
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $account_status->account_status);
				
				$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$total_amount += $c->amount;
				$total_quantity += $qty;
				
				$row++;
			}
			$total_amount = number_format($total_amount, 2);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, "TOTALS");
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $total_amount);
			$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $total_quantity);
			
			$totals_border = array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb'=>'000000')
			);

			$style_totals = array(
				'borders' => array(
					'bottom' => $totals_border,
					'left' => $totals_border,
					'top' => $totals_border,
					'right' => $totals_border,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb'=>'FBEC5D'),
				),
				'font' => array(
					'bold' => true,
				)
			);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($style_totals);
			
			$row++;
		}
		
		
		$objPHPExcel->setActiveSheetIndex(0);		
		$filename_date = $from_date . '-' . $to_date;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="tmp_member_commissions'.$filename_date.'.xls"');
		header('Cache-Control: max-age=0');		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');		
	}


}