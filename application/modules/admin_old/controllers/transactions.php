<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transactions extends  Systems_Controller
{
	
	public $start_date;
	public $end_date;
	public $type;
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("members_model");
		$this->load->library('pager');
		$this->load->library('pager2');
	}
	
	public function index()
	{
		$this->set_navigation('transactions');
		$this->template->view('transaction_logs/list');
	}

	public function process()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$type = $this->input->get_post("type");
		
		$this->start_date = date("Y-m-d",strtotime($start_date));
		$this->end_date = date("Y-m-d",strtotime($end_date));
		$this->type = $type;
		
		$this->_process_tables();
		
		$data = array(
			"start_date"=>$this->start_date,
			"end_date"=>$this->end_date,
			"type"=>$type,
		);
		
		echo json_encode(array("status"=>"1","data"=>$data));
		return;
	}

	public function transaction_logs()
	{
		$type = $this->input->get_post("type");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$member_id = abs($this->input->get_post("member_id"));
		$page = $this->input->get_post('page');

		/*if($member_id == 0){ // get first member_id from tmp_cm_member_commissions_igpsm_view
			$sql = "SELECT DISTINCT(member_id) as member_id FROM tmp_cm_member_commissions_igpsm_view";
			$query = $this->db->query($sql);
			$result = $query->first_row();

			$member_id = $result->member_id;

			$query->free_result();

		}*/
		$all = false;
		if($member_id == 0){ // get all
			$sql = "SELECT DISTINCT(member_id) as member_id FROM tmp_cm_member_commissions_igpsm_view";
			$query = $this->db->query($sql);
			$result = $query->result();

			//$member_id = $result->member_id;

			$query->free_result();
			$all = true;
		}

		// get all members with commisions
		$sql = "SELECT DISTINCT(member_id) as member_id, first_name, last_name, middle_name FROM tmp_cm_member_commissions_igpsm_view";
		$query = $this->db->query($sql);
		$result = $query->result();
		$ctr = 1;
		$counter = 0;
		$html_member_option = "";
		$html_member_option .= "<option value='0' selected='selected'>ALL</option>";
		foreach($result as $r){
			//if($ctr==1 && $member_id==0) $member_id = $r->member_id;
			$selected = "";
			if($member_id == $r->member_id) $selected = "selected";
			$html_member_option .= "<option value='{$r->member_id}' {$selected}>{$r->last_name}, {$r->first_name} {$r->middle_name}</option>";
			$ctr++;
			$counter++;
		}
		$records_per_page = 20;
		$config = array(
			'pagination_url' => '/admin/transactions/transaction_logs/',
			'total_items' => $counter,
			'per_page' => $records_per_page,
			'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		$limit = $this->pager->per_page;

        $offset = ($page - 1) * $records_per_page;
        $offset = ($offset < 0 ? 0 : $offset);

		if($type=="IGPSM"){
			$where_transaction_codes = "transaction_code IN (100,101,102,103,104)";
		}else{
			$where_transaction_codes = "transaction_code = 105";
		}

		if($all)
		{
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
					(a.insert_timestamp BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59')
				ORDER BY
					a.insert_timestamp DESC
				LIMIT
					{$offset}, {$records_per_page}";
			
			$query = $this->db->query($sql);		
		}else
		{
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
					a.member_id = {$member_id}
				AND
					(a.insert_timestamp BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59')
				ORDER BY
					a.insert_timestamp DESC";
			$query = $this->db->query($sql);
			$result = $query->result();
			$counter = count($result);
			
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
					a.member_id = {$member_id}
				AND
					(a.insert_timestamp BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59')
				ORDER BY
					a.insert_timestamp DESC
				LIMIT
					{$offset}, {$records_per_page}";
		
			$query = $this->db->query($sql);
		}
		$results = $query->result();
		$html = "";
		foreach ($results as $r)
		{
			$payout_amount = number_format($r->amount, 2);
			$html .= "
			<tr>
				<td>{$r->last_name}, {$r->first_name} {$r->middle_name}</td>
				<td>{$r->account_id}</td>
				<td>{$r->remarks}</td>
				<td>{$r->type}</td>
				<td align='right'>{$payout_amount}</td>
				<td>{$r->insert_timestamp}</td>
			</tr>";
		}
		$this->pager2->set_config(array(
            'total_items' => $counter,
            'per_page' => $records_per_page,
            'offset' => $offset,
            'adjacents' => 1,
            'type' => 'ajax'
        ));

        $pagination = $this->pager2->create_links();

		$this->return_json(1,"Success!!!", array("html"=>$html,"html_member_option"=>$html_member_option, 'pagination'=>$pagination));
		return;

	}
	
	public function download()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$type = $this->input->get_post("type");
		
		$this->start_date = date("Y-m-d",strtotime($start_date));
		$this->end_date = date("Y-m-d",strtotime($end_date));
		$this->type = $type;

		$this->_process_earnings();
	}
	
	private function _process_tables()
	{
		// drop temp table for processing
		$sql = "DROP TABLE IF EXISTS tmp_cm_member_commissions";

		$this->db->query($sql);

		// create temp table for processing
		$sql = "CREATE TABLE IF NOT EXISTS tmp_cm_member_commissions (
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
	}
	
	private function _process_earnings()
	{
		
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

		$pretty_start_date = str_replace("-","",$this->start_date);
		$pretty_end_date = str_replace("-","",$this->end_date);

		$filename = "{$this->type}_transaction_logs_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {

			$title = "{$this->type} Transaction Logs for {$this->start_date} to {$this->end_date}";

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

			$title = "Transaction Logs";
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_transaction_logs($worksheet);		

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

	private function _get_transaction_logs($worksheet)
	{
		$title = "Member Transaction Logs for {$this->start_date} to {$this->end_date}";

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
		$worksheet->mergeCells('A1:H1');
		$worksheet->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Account ID');
		$worksheet->setCellValue('E' . $start_column_num, 'Details');
		$worksheet->setCellValue('F' . $start_column_num, 'Type');
		$worksheet->setCellValue('G' . $start_column_num, 'Amount');
		$worksheet->setCellValue('H' . $start_column_num, 'Date Time');
		
		if($this->type=="IGPSM"){
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
				(a.insert_timestamp BETWEEN '{$this->start_date} 00:00:00' AND '{$this->end_date} 23:59:59')
			ORDER BY
				a.member_id, a.account_id, a.insert_timestamp DESC";
				
		$query = $this->db->query($sql);

		$row = 4;

		foreach ($query->result() as $r)
		{

			$worksheet->setCellValue('A'. $row, $r->last_name);
			$worksheet->setCellValue('B'. $row, $r->first_name);
			$worksheet->setCellValue('C'. $row, $r->middle_name);
			$worksheet->setCellValue('D'. $row, $r->account_id);
			$worksheet->setCellValue('E'. $row, $r->remarks);
			$worksheet->setCellValue('F'. $row, $r->type);
			$worksheet->setCellValue('G'. $row, $r->amount);
			$worksheet->setCellValue('H'. $row, $r->insert_timestamp);

			// auto resize columns
			$worksheet->getColumnDimension('A')->setAutoSize(true);
			$worksheet->getColumnDimension('B')->setAutoSize(true);
			$worksheet->getColumnDimension('C')->setAutoSize(true);
			$worksheet->getColumnDimension('D')->setAutoSize(true);
			$worksheet->getColumnDimension('E')->setAutoSize(true);
			$worksheet->getColumnDimension('F')->setAutoSize(true);
			$worksheet->getColumnDimension('G')->setAutoSize(true);
			$worksheet->getColumnDimension('H')->setAutoSize(true);

			$row++;
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


		
		// update 
		$this->db->query("UPDATE rf_settings SET value = CURRENT_TIMESTAMP WHERE slug = 'last_encashment_timestamp'");		


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
				/*
				$transaction_id = $row[2];
				$member_id = $row[3];
				$additional_tax = $row[11];
				$total_amount = $row[12];
				$type = $row[13];
				$data = array(
					'status' => 'COMPLETED',
					'additional_tax' => $additional_tax,
					'total_amount' => $total_amount
				);

				$payout=$this->payment_model->get_member_payout_by_id($transaction_id);

				if(strcmp(strtoupper($payout->type),"FUNDS") == 0 && strcmp(strtoupper($payout->status),"PROCESSING") == 0 )
				{
					$this->members_model->credit_funds($member_id,$total_amount,"FUNDS","CREDIT FROM PAYOUT");
				}
				$this->payment_model->update_member_payout($data, "transaction_id = {$transaction_id} AND status='PROCESSING'");
				*/
			}
		}
	}

}