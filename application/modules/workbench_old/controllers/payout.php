<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payout extends  Base_Controller
{
	
	public $start_date;
	public $end_date;
	public $type;
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("members_model");
	}
	
	public function index()
	{
		
	}

	public function process()
	{
		$this->start_date = $this->input->get_post("start_date");
		$this->end_date = $this->input->get_post("end_date");
		$type = $this->input->get_post("type");
		
		/*
		$this->start_date = "2012-10-20";		
		$this->end_date = "2012-10-27";		
		$this->type = "IGPSM";
		*/
		
		$this->_process_tables();

	}
	
	public function download()
	{
		$this->start_date = $this->input->get_post("start_date");
		$this->end_date = $this->input->get_post("end_date");
		$type = $this->input->get_post("type");

		$this->_process_earnings();		
	}
	
	private function _process_tables()
	{
		// drop temp table for processing
		$sql = "DROP TABLE IF EXISTS tmp_cm_member_commissions";

		$this->db->query($sql);

		// create temp table for processing
		$sql = "CREATE TABLE tmp_cm_member_commissions (
			member_id INT,
			account_id VARCHAR(32),
			transaction_code INT,
			amount DECIMAL(10,2),
			start_date DATE,
			end_date DATE,
			insert_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			KEY(member_id),
			KEY(account_id),
			KEY(start_date,end_date)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$this->db->query($sql);
	
		// INSERT INTO temp table 
		$sql = "INSERT INTO tmp_cm_member_commissions (member_id,account_id,transaction_code,amount,start_date,end_date) (SELECT member_id,account_id,transaction_code,SUM(amount) as amount,'{$this->start_date}' as start_date, '{$this->end_date}' as end_date FROM tr_member_acct_credit_logs WHERE DATE(insert_timestamp) BETWEEN '{$this->start_date}' AND '{$this->end_date}' GROUP BY account_id,transaction_code)";		
		
		$this->db->query($sql);

		// drop temp table for processing
		$sql = "DROP TABLE IF EXISTS tmp_cm_member_funds";

		$this->db->query($sql);

		// create temp table for processing
		$sql = "CREATE TABLE tmp_cm_member_funds (
			member_id INT,
			amount DECIMAL(10,2),
			start_date DATE,
			end_date DATE,
			insert_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,			
			KEY(member_id),
			KEY(start_date,end_date)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$this->db->query($sql);
		
		$members = $this->members_model->get_members("funds > 0",NULL,NULL,"member_id,funds");
		
		foreach ($members as $m)
		{
			$sql = "INSERT INTO tmp_cm_member_funds (member_id,amount,start_date,end_date) VALUES ({$m->member_id},$m->funds,'{$this->start_date}','{$this->end_date}')";
			$this->db->query($sql);
			
			$this->members_model->debit_funds($m->member_id,$m->funds,"FUNDS","Processed for payout");
		}		
	}
	
	private function _process_earnings()
	{
		
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

		$pretty_start_date = str_replace("-","",$this->start_date);
		$pretty_end_date = str_replace("-","",$this->end_date);

		$filename = "commission_payout_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {

			$title = "Commission Payout for {$this->start_date} to {$this->end_date}";

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

			$title = "{$this->type} Per Account";
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_commissions_per_account($worksheet);

			$objPHPExcel->createSheet();
			
			$title = "Gift Cheques Per Account";
			$worksheet = $objPHPExcel->setActiveSheetIndex(1);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_gc_per_account($worksheet);						

			$objPHPExcel->createSheet();
			
			$title = "Member Payout";
			$worksheet = $objPHPExcel->setActiveSheetIndex(2);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_member_payout($worksheet);						
			
			$objPHPExcel->setActiveSheetIndex(0);

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

	private function _get_commissions_per_account($worksheet)
	{	
		$title = "{$this->type} Commissions per Account for {$this->start_date} to {$this->end_date}";

		$start_column_num = 3;

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
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Account ID');
		$worksheet->setCellValue('E' . $start_column_num, 'Gross');
		$worksheet->setCellValue('F' . $start_column_num, 'Tax');
		$worksheet->setCellValue('G' . $start_column_num, 'Net');
		$worksheet->setCellValue('H' . $start_column_num, 'Balance');
		$worksheet->setCellValue('I' . $start_column_num, 'Total');
		$worksheet->setCellValue('J' . $start_column_num, 'Cash Card');
		$worksheet->setCellValue('K' . $start_column_num, 'Account Status');
		
		if ($this->type=='IGPSM')
			$table = "tmp_cm_member_commissions_igpsm_view";
		else 
			$table = "tmp_cm_member_commissions_unilevel_view";

		$sql = "
			SELECT
			    member_id,
				account_id,
				last_name,
				first_name,
				middle_name,
				amount,
				cd_amount,
				cash_card,
				account_status
			FROM
				{$table}
			WHERE
				start_date = '{$this->start_date}'
			AND
				end_date = '{$this->end_date}'	
			ORDER BY
				last_name,first_name,middle_name";
				
		$query = $this->db->query($sql);

		$row = 4;

		$witholding_tax = $this->settings->witholding_tax;

		foreach ($query->result() as $r)
		{
			if ($this->type=="IGPSM")
				$balance = $r->cd_amount;				
			else
				$balance = 0;
			
			// computed values
			if ($r->cash_card == 'TO FUNDS')
				$tax = 0;
			else
				$tax = $r->amount * $witholding_tax;
			
			$net = $r->amount - $tax;
			$total = $net - $balance;
			
			$worksheet->setCellValue('A'. $row, $r->last_name);
			$worksheet->setCellValue('B'. $row, $r->first_name);
			$worksheet->setCellValue('C'. $row, $r->middle_name);
			$worksheet->setCellValue('D'. $row, $r->account_id);
			$worksheet->setCellValue('E'. $row, $r->amount);
			$worksheet->setCellValue('F'. $row, $tax);
			$worksheet->setCellValue('G'. $row, "=E{$row} - F{$row}");
			$worksheet->setCellValue('H'. $row, $balance);
			$worksheet->setCellValue('I'. $row, "=G{$row} - H{$row}");
			$worksheet->setCellValue('J'. $row, $r->cash_card);
			$worksheet->setCellValue('K'. $row, $r->account_status);

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
		
		return $worksheet;
	}

	private function _get_gc_per_account($worksheet)
	{
		$title = "Gift Cheques per Account for {$this->start_date} to {$this->end_date}";

		$start_column_num = 3;

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

		//center column names
		$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
			$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Account ID');
		$worksheet->setCellValue('E' . $start_column_num, 'Gift Cheques');
		$worksheet->setCellValue('F' . $start_column_num, 'Account Status');
		
		$witholding_tax = $this->settings->witholding_tax;

		$sql = "
			SELECT
		    	a.member_id,				
				a.account_id,
				b.last_name,
				b.first_name,
				b.middle_name,
				SUM(a.amount) AS amount,
				CASE 
					WHEN c.account_status_id = 1 THEN 'ACTIVE' 
					WHEN c.account_status_id = 2 THEN 'INACTIVE' 
					WHEN c.account_status_id = 3 THEN 'COMPANY' 
				END AS account_status
			FROM
				tmp_cm_member_commissions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			LEFT JOIN
				cm_member_accounts c ON a.account_id = c.account_id
			WHERE
				a.transaction_code >= 106 AND a.transaction_code <= 109
			AND
				a.start_date = '{$this->start_date}'
			AND
				a.end_date = '{$this->end_date}'	
			GROUP BY
				a.account_id
			ORDER BY
				b.last_name,b.first_name,b.middle_name";
				
		$query = $this->db->query($sql);

		$row = 4;

		foreach ($query->result() as $r)
		{

			$worksheet->setCellValue('A'. $row, $r->last_name);
			$worksheet->setCellValue('B'. $row, $r->first_name);
			$worksheet->setCellValue('C'. $row, $r->middle_name);
			$worksheet->setCellValue('D'. $row, $r->account_id);
			$worksheet->setCellValue('E'. $row, $r->amount);
			$worksheet->setCellValue('F'. $row, $r->account_status);

			// auto resize columns
			$worksheet->getColumnDimension('A')->setAutoSize(true);
			$worksheet->getColumnDimension('B')->setAutoSize(true);
			$worksheet->getColumnDimension('C')->setAutoSize(true);
			$worksheet->getColumnDimension('D')->setAutoSize(true);
			$worksheet->getColumnDimension('E')->setAutoSize(true);
			$worksheet->getColumnDimension('F')->setAutoSize(true);

			$row++;
		}
	}

	private function _get_member_payout($worksheet)
	{				
		$title = "Member Payout for {$this->start_date} to {$this->end_date}";

		$start_column_num = 3;

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
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Gross');
		$worksheet->setCellValue('E' . $start_column_num, 'Less 10%');
		$worksheet->setCellValue('F' . $start_column_num, 'Net Gross');
		$worksheet->setCellValue('G' . $start_column_num, 'Tax');
		$worksheet->setCellValue('H' . $start_column_num, 'Net of Tax');
		$worksheet->setCellValue('I' . $start_column_num, 'Balance');
		$worksheet->setCellValue('J' . $start_column_num, 'Total');
		$worksheet->setCellValue('K' . $start_column_num, 'Total');
		
		
		if ($this->type=='IGPSM')
			$table = "tmp_cm_member_commissions_igpsm_view";
		else 
			$table = "tmp_cm_member_commissions_unilevel_view";

		$sql = "
			SELECT
			    a.member_id,
				a.last_name,
				a.first_name,
				a.middle_name,
				SUM(a.amount) as amount,
				CASE 
					WHEN b.is_auto_payout = 1 AND b.metrobank_paycard_number IS NOT NULL THEN b.metrobank_paycard_number 
					ELSE 'TO FUNDS' 
				END AS cash_card
			FROM
				{$table} a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				a.account_status = 'ACTIVE'
			AND
				a.start_date = '{$this->start_date}'
			AND
				a.end_date = '{$this->end_date}'
			GROUP BY
				a.member_id
			ORDER BY
				a.last_name,a.first_name,a.middle_name";
				
		$query = $this->db->query($sql);

		$row = 4;
		
		$witholding_tax = $this->settings->witholding_tax;

		foreach ($query->result() as $r)
		{
			$tax = $r->amount * $witholding_tax;
			
			$worksheet->setCellValue('A'. $row, $r->last_name);
			$worksheet->setCellValue('B'. $row, $r->first_name);
			$worksheet->setCellValue('C'. $row, $r->middle_name);
			$worksheet->setCellValue('D'. $row, $r->amount);
			$worksheet->setCellValue('E'. $row, 0);			
			$worksheet->setCellValue('F'. $row, "=D{$row} - E{$row}");			
			$worksheet->setCellValue('G'. $row, $tax);
			$worksheet->setCellValue('H'. $row, "=F{$row} - G{$row}");
			$worksheet->setCellValue('I'. $row, 0);
			$worksheet->setCellValue('J'. $row, "=H{$row} - I{$row}");
			$worksheet->setCellValue('K'. $row, $r->cash_card);

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
			$worksheet->getColumnDimension('Ks')->setAutoSize(true);
			
			$row++;
		}
		
		return $worksheet;
	}

	
}