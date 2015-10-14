<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crediting_f2p extends Systems_Controller {

	private $_row_start = 5;
	private $_payout_type = '';
	private $_start_date = '';
	private $_end_date = '';
	private $_gc_before_after_table = '';
	private $_gcep_before_after_table = '';

	function __construct() 
	{
  		parent::__construct();
  		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $this->load->model('payout_model');
        $this->load->model('members_model');
	}

	public function index()
	{
		$this->template->view('crediting/dashboard');
	}

	public function upload()
	{
		$target_path = FCPATH . "assets/media/uploads/";
		$allowedExts = array();
		$maxFileSize = 0;

		$headers = get_fu_headers();

		$location = "/assets/media/uploads/crediting";
		if(!file_exists(FCPATH . 'assets/media/uploads/crediting')) mkdir(FCPATH . 'assets/media/uploads/crediting', 0755);

		$fileName = $headers['X-File-Name'];
		$fileSize = $headers['X-File-Size'];
		$ext = substr($fileName, strrpos($fileName, '.') + 1);
		if (in_array($ext,$allowedExts) or empty($allowedExts)) {
			if ($fileSize<$maxFileSize or empty($maxFileSize)) {
			$content = file_get_contents("php://input");
			file_put_contents($target_path.$fileName,$content);

			$filename = $this->input->get('filename');
			if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
			$ret = $this->_upload($filename, $fileName, $target_path.$fileName, $location);

			if($ret)
			{
				echo('{"success":true, "details": "Success."}');
			}
			else
			{
				echo('{"success":false, "details": "Processing Error."}');
			}

		} else { echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); };
		} else {
			echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
		}
	}

	private function _upload($filename, $uploading_filename, $tmp_filename, $location)
	{
		$format = 'xlsx';
		
		$_uploading_filename = $uploading_filename;
		
		$temp_file = $tmp_filename;

		$filenameParts = explode('_', $filename);
		// standard filename format: commission_report_payout_(igpsm/unilevel)_(start_date)_(end_date).xlsx
		$this->_payout_type = $filenameParts[3];
		if(!in_array(strtolower($this->_payout_type), array('igpsm','unilevel'))) return false;

		$startDateRaw = $filenameParts[4];
		$this->_start_date = substr($startDateRaw, 0, 4) . '-' . substr($startDateRaw, 4, 2) . '-' . substr($startDateRaw, 6, 2);
		$endDateRaw = $filenameParts[5];
		$this->_end_date = substr($endDateRaw, 0, 4) . '-' . substr($endDateRaw, 4, 2) . '-' . substr($endDateRaw, 6, 2);

		//$po_payout_period = $this->payout_model->get_payout_periods(array(
		//	'start_date' => $this->_start_date . " 00:00:00",
		//	'end_date' => $this->_end_date . " 23:59:59",
		//	'is_official' => 1
		//));
		
		$sql = "
			SELECT
				*
			FROM
				po_payout_periods
			WHERE
				DATE(start_date) = '" . $this->_start_date . "'
			AND
				DATE(end_date) = '" . $this->_end_date . "'
			AND
				is_official = 1
			";

		$po_payout_period = $this->db->query($sql);
		
		if(count($po_payout_period) == 0 || count($po_payout_period) > 1) return false;
		$po_payout_period = $po_payout_period[0];
		if($po_payout_period->payout_type != strtoupper($this->_payout_type)) return false;

		// backup payouts
		$this->_backup_member_payouts($this->_start_date, $this->_end_date);

		return;
		
		$filename = $filename . "_" . time(). rand(0,99) . "." . $format;
		
		$target_filename = $filename;
		
		$location = substr($location,1,strlen($location));
		
		$target_fullpath = FCPATH . $location;
		$fullpath = FCPATH . $location . "/". $filename;
		
		$_ret = true;
		$_ret = copy($temp_file, $fullpath);		
		
		chmod($fullpath, 777);
		
		if ($_ret) unlink($temp_file);
		$_ret = ($_ret)?$this->_process_upload($fullpath):false;

		// store before data
		$this->_store_before_data();

		// actual crediting
		$this->_actual_crediting();

		// store after data
		$this->_store_after_data();

		// run variance deductions
		//$this->_variance_deductions();

		return $_ret;
	}

	private function _variance_deductions()
	{
		// funds
		$sql = "
			UPDATE
				cm_members a 
			SET 
				a.funds_variance = a.funds_variance + (
			SELECT 
				b.deduction2 
			FROM 
				cm_member_payouts b
			WHERE 
				b.member_id = a.member_id 
			AND 
				b.deduction2 > 0
			AND
				b.start_date = '" . $this->_start_date . "'
			AND
				b.end_date = '" . $this->_end_date . "'
			)
			WHERE 
				a.member_id IN (
			SELECT 
				member_id 
			FROM
				cm_member_payouts 
			WHERE 
				deduction2 > 0
			AND
				start_date = '" . $this->_start_date . "'
			AND
				end_date = '" . $this->_end_date . "'
			)
		";
		$this->db->query($sql);

		// gc

		// gcep
	}

	private function _store_after_data()
	{
		// funds
		$_members_data = $this->_get_funds_gc_gcep();
		foreach($_members_data as $_member_data)
		{
			$sql = "
			UPDATE
				cm_member_payouts
			SET
				funds_after = '" . $_member_data->funds . "',
				funds_variance_after = '" . $_member_data->funds_variance . "'
			WHERE
				member_id = '" . $_member_data->member_id . "'
			AND
				start_date = '" . $this->_start_date . "'
			AND
				end_date = '" . $this->_end_date . "'
			";
			$this->db->query($sql);
		}

		// gc
		$sql = "
		UPDATE
			{$this->_gc_before_after_table} a
		SET
			a.gc_after_credit = (
				SELECT
					b.gift_cheques
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			),
			a.gc_variance_after_credit = (
				SELECT
					b.gift_cheques_variance
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					cm_members
			)
		";
		$this->db->query($sql);

		// gcep
		$sql = "
		UPDATE
			{$this->_gcep_before_after_table} a
		SET
			a.gcep_after_credit = (
				SELECT
					b.gcep
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			),
			a.gcep_variance_after_credit = (
				SELECT
					b.gcep_variance
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					cm_members
			)
		";
		$this->db->query($sql);
	}

	private function _actual_crediting()
	{
		// funds
		$sql = "
			UPDATE
				cm_members a 
			SET 
				funds = funds + (
			SELECT 
				SUM(total_amount)
			FROM 
				cm_member_payouts b 
			WHERE 
				a.member_id = b.member_id
			AND 
				status = 'PENDING'
			AND 
				cash_card IN ('TO FUNDS', 'TO FUNDS - Blank Paycard')
			AND 
				check_release = 0
			AND 
				is_on_hold = 0
			AND 
				start_date = '" . $this->_start_date . "'
			AND 
				end_date = '" . $this->_end_date . "'
			GROUP BY
				member_id
			)
			WHERE 
				member_id IN (
			SELECT 
				DISTINCT member_id 
			FROM 
				cm_member_payouts
			WHERE 
				status = 'PENDING'
			AND 
				cash_card IN ('TO FUNDS', 'TO FUNDS - Blank Paycard')
			AND 
				check_release = 0
			AND 
				is_on_hold = 0
			AND 
				is_corpo_sharing = 0
			AND 
				start_date = '" . $this->_start_date . "'
			AND 
				end_date = '" . $this->_end_date . "'
			)
		";
		$this->db->query($sql);

		// gc
		$sql = "
		UPDATE
			cm_members a
		SET
			a.gift_cheques = a.gift_cheques + (
				SELECT
					b.net_gc
				FROM
					{$this->_gc_before_after_table} b
				WHERE
					b.member_id = a.member_id
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					{$this->_gc_before_after_table}
				WHERE
					is_on_hold = 0
			)
		";
		$this->db->query($sql);

		// gcep
		$sql = "
		UPDATE
			cm_members a
		SET
			a.gcep = a.gcep + (
				SELECT
					b.net_gcep
				FROM
					{$this->_gcep_before_after_table}
				WHERE
					b.member_id = a.member_id
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					{$this->_gcep_before_after_table}
				WHERE
					is_on_hold = 0
			)
		";
		$this->db->query($sql);
	}

	private function _store_before_data()
	{
		// funds
		$_members_data = $this->_get_funds_gc_gcep();
		foreach($_members_data as $_member_data)
		{
			$sql = "
			UPDATE
				cm_member_payouts
			SET
				funds_before = '" . $_member_data->funds . "',
				funds_variance_before = '" . $_member_data->funds_variance . "'
			WHERE
				member_id = '" . $_member_data->member_id . "'
			AND
				start_date = '" . $this->_start_date . "'
			AND
				end_date = '" . $this->_end_date . "'
			";
			$this->db->query($sql);
		}

		// gc
		$sql = "
		UPDATE
			{$this->_gc_before_after_table} a
		SET
			a.gc_before_credit = (
				SELECT
					b.gift_cheques
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			),
			a.gc_variance_before_credit = (
				SELECT
					b.gift_cheques_variance
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					cm_members
			)
		";
		$this->db->query($sql);

		// gcep
		$sql = "
		UPDATE
			{$this->_gcep_before_after_table} a
		SET
			a.gcep_before_credit = (
				SELECT
					b.gcep
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			),
			a.gcep_variance_before_credit = (
				SELECT
					b.gcep_variance
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					cm_members
			)
		";
		$this->db->query($sql);
	}

	private function _get_funds_gc_gcep()
	{
		$sql = "
		SELECT
			member_id,
			funds,
			funds_variance,
			gift_cheques,
			gift_cheques_variance,
			gcep,
			gcep_variance
		FROM
			cm_members
		WHERE
			member_id IN (
		SELECT
			DISTINCT member_id
		FROM
			cm_member_payouts
		WHERE
			start_date = '" . $this->_start_date . "'
		AND
			end_date = '" . $this->_end_date . "'
		)
		";

		$query = $this->db->query($sql);
		return $query->result();
	}

	private function _backup_member_payouts($start_date, $end_date)
	{
		// export current date
		$sql = "
			INSERT INTO
				cm_member_payouts_backups
			(
				`payout_type`,
				`member_id`,
				`gross`,
				`gcep`,
				`net_of_gcep`,
				`witholding_tax`,
				`net_of_tax`,
				`balance`,
				`deduction2`,
				`card_fee`,
				`adjustments`,
				`total_amount`,
				`funds_before`,
				`funds_after`,
				`funds_variance_before`,
				`funds_variance_after`,
				`service_depot`,
				`group_name`,
				`cash_card`,
				`is_corpo_sharing`,
				`check_release`,
				`is_on_hold`,
				`status`,
				`start_date`,
				`end_date`,
				`user_id`,
				`remarks`,
				`insert_timestamp`
			)
			(
				SELECT
					`payout_type`,
					`member_id`,
					`gross`,
					`gcep`,
					`net_of_gcep`,
					`witholding_tax`,
					`net_of_tax`,
					`balance`,
					`deduction2`,
					`card_fee`,
					`adjustments`,
					`total_amount`,
					`funds_before`,
					`funds_after`,
					`funds_variance_before`,
					`funds_variance_after`,
					`service_depot`,
					`group_name`,
					`cash_card`,
					CASE
						WHEN `is_corpo_sharing` IS NULL THEN '0'
						ELSE `is_corpo_sharing`
					END AS `is_corpo_sharing`,
					CASE
						WHEN `check_release` IS NULL THEN '0'
						ELSE `check_release`
					END AS `check_release`,
					CASE
						WHEN `is_on_hold` IS NULL THEN '0'
						ELSE `is_on_hold`
					END AS `is_on_hold`,
					`status`,
					`start_date`,
					`end_date`,
					`user_id`,
					`remarks`,
					`insert_timestamp`
				FROM
					cm_member_payouts
				WHERE
					start_date = '" . $start_date . "'
				AND
					end_date = '" . $end_date . "'
			)
		";
		$this->db->query($sql);

		// remove old member payout data
		$sql = "
			DELETE FROM
				cm_member_payouts
			WHERE
				start_date = '" . $start_date . "'
			AND
				end_date = '" . $end_date . "'
		";
		$this->db->query($sql);
	}

	private function _process_upload($filepath)
	{
		$_ret = true;

        $reader = new PHPExcel_Reader_Excel2007();
        $excel = $reader->load($filepath);

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

	private function _sheet_processor($sheet, $_map)
	{
		$sheetData = array();
		$row = $this->_row_start;
		while($sheet->getCell('A'.$row)->getValue() != '')
		{
			$rowData = new stdClass();
			foreach($_map as $k => $v) $rowData->$v = $sheet->getCell($k.$row)->getValue();
			$sheetData[] = $rowData;
			$row++;
		}

		return $sheetData;
	}

	private function _sheet_process_funds($sheet)
	{
		$_map = array(
			'A' => 'member_id',
			'B' => 'last_name',
			'C' => 'first_name',
			'D'	=> 'middle_name',
			'E' => 'gross',
			'F' => 'gcep',
			'G'	=> 'net_gross',
			'H'	=> 'tax',
			'I'	=> 'net_of_tax',
			'J'	=> 'funds_variance',
			'K'	=> 'deduction1',
			'L'	=> 'card_fee',
			'M'	=> 'net',
			'N'	=> 'cash_card',
			'O'	=> 'account_number',
			'P' => 'group',
			'Q' => 'group_name',
			'R' => 'remarks',
			'S' => 'is_corpo_sharing',
			'T' => 'check_release',
			'U' => 'is_on_hold'
		);

		$sheetData = $this->_sheet_processor($sheet, $_map);
		
		foreach($sheetData as $rowData)
		{
			$insert_data = array(
				'payout_type' => $this->_payout_type,
				'member_id' => $rowData->member_id,
				'gross' => floatval($rowData->gross),
				'gcep' => floatval($rowData->gcep),
				'net_of_gcep' => floatval($rowData->net_gross),
				'witholding_tax' => floatval($rowData->tax),
				'net_of_tax' => floatval($rowData->net_of_tax),
				'balance' => floatval($rowData->deduction1),
				'deduction2' => abs(floatval($rowData->funds_variance)),
				'total_amount' => floatval($rowData->net),
				'cash_card' => $rowData->cash_card,
				'is_corpo_sharing' => intval($rowData->is_corpo_sharing),
				'check_release' => intval($rowData->check_release),
				'is_on_hold' => intval($rowData->is_on_hold),
				'status' => 'PENDING',
				'start_date' => $this->_start_date,
				'end_date' => $this->_end_date,
				'user_id' => $this->user->user_id,
				'remarks' => $rowData->remarks
			);
			$this->members_model->insert_member_payouts($insert_data);
		}

		return true;
	}

	private function _sheet_process_gc($sheet)
	{
		$_map = array(
			'A' => 'member_id',
			'B' => 'full_name',
			'C' => 'sp',
			'D'	=> 'rs',
			'E' => 'vp',
			'F' => 'total_gc',
			'G'	=> 'negative_gc_fund',
			'H'	=> 'balance_for_upload',
			'I' => 'is_on_hold'
		);

		$sheetData = $this->_sheet_processor($sheet, $_map);

		// create before after table
		$formatted_start_date = str_replace("/", "", $this->_start_date);
		$formatted_end_date = str_replace("/", "", $this->_end_date);
		$this->_gc_before_after_table = "gc_crediting_" . $formatted_start_date . "_" . $formatted_end_date;
		$sql = "DROP TABLE IF EXISTS `{$this->_gc_before_after_table}`;";
		$this->db->query($sql);
		$sql = "
		CREATE TABLE `{$this->_gc_before_after_table}` (
       		`transaction_id` int(11) NOT NULL AUTO_INCREMENT,
       		`member_id` int(10) unsigned NOT NULL DEFAULT '0',
       		`sp_gc` decimal(10,2) DEFAULT '0.00',
       		`rs_gc` decimal(10,2) DEFAULT '0.00',
       		`vp_gc` decimal(10,2) DEFAULT '0.00',
       		`total_gc` decimal(10,2) DEFAULT '0.00',
       		`gc_variance` decimal(10,2) DEFAULT '0.00',
       		`net_gc` decimal(10,2) DEFAULT '0.00',
       		`gc_before_credit` decimal(10,2) DEFAULT '0.00',
       		`gc_variance_before_credit` decimal(10,2) DEFAULT '0.00',
       		`gc_after_credit` decimal(10,2) DEFAULT '0.00',
       		`gc_variance_after_credit` decimal(10,2) DEFAULT '0.00',
       		`is_on_hold` tinyint(1) DEFAULT 0,
       		`remarks` text COLLATE utf8_unicode_ci,
       		`insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
       		PRIMARY KEY (`transaction_id`),
       		KEY `member_id` (`member_id`)
     	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		";
		$this->db->query($sql);

		// insert data to before after table
		foreach($sheetData as $item) {
			$sql = "
			INSERT INTO
				{$this->_gc_before_after_table}
			(
				`member_id`,
				`sp_gc`,
				`rs_gc`,
				`vp_gc`,
				`total_gc`,
				`gc_variance`,
				`net_gc`
				`is_on_hold`
			)
			(
				'{$item->member_id}',
				" . floatval($item->sp) . ",
				" . floatval($item->rs) . ",
				" . floatval($item->vp) . ",
				" . floatval($item->total_gc) . ",
				" . abs(floatval($item->negative_gc_fund)) . ",
				" . floatval($item->balance_for_upload) . ",
				" . intval($item->is_on_hold) . "
			)
			";
			$this->db->query($sql);
		}

		return true;
	}

	private function _sheet_process_gcep($sheet)
	{
		$_map = array(
			'A' => 'member_id',
			'B' => 'full_name',
			'C' => 'total_gcep',
			'D'	=> 'negative_gcep_fund',
			'E'	=> 'balance_for_upload',
			'F' => 'is_on_hold'
		);

		$sheetData = $this->_sheet_processor($sheet, $_map);

		// create before after table
		$formatted_start_date = str_replace("/", "", $this->_start_date);
		$formatted_end_date = str_replace("/", "", $this->_end_date);
		$this->_gcep_before_after_table = "gcep_crediting_" . $formatted_start_date . "_" . $formatted_end_date;
		$sql = "DROP TABLE IF EXISTS `{$this->_gcep_before_after_table}`;";
		$this->db->query($sql);
		$sql = "
		CREATE TABLE `{$this->_gcep_before_after_table}` (
       		`transaction_id` int(11) NOT NULL AUTO_INCREMENT,
       		`member_id` int(10) unsigned NOT NULL DEFAULT '0',
       		`net_gcep` decimal(10,2) DEFAULT '0.00',
       		`gcep_variance` decimal(10,2) DEFAULT '0.00',
       		`gcep_before_credit` decimal(10,2) DEFAULT '0.00',
       		`gcep_variance_before_credit` decimal(10,2) DEFAULT '0.00',
       		`gcep_after_credit` decimal(10,2) DEFAULT '0.00',
       		`gcep_variance_after_credit` decimal(10,2) DEFAULT '0.00',
       		`is_on_hold` tinyint(1) DEFAULT 0,
       		`remarks` text COLLATE utf8_unicode_ci,
       		`insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
       		PRIMARY KEY (`transaction_id`),
       		KEY `member_id` (`member_id`)
     	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		";
		$this->db->query($sql);

		// insert data to before after table
		foreach($sheetData as $item) {
			$sql = "
			INSERT INTO
				{$this->_gcep_before_after_table}
			(
				`member_id`,
				`net_gcep`,
				`gcep_variance`,
				`is_on_hold`
			)
			(
				'{$item->member_id}',
				" . floatval($item->balance_for_upload) . ",
				" . abs(floatval($item->negative_gc_fund)) . ",
				" . intval($item->is_on_hold) . "
			)
			";
			$this->db->query($sql);
		}

		return true;
	}

	private function _sheet_process_epa($sheet)
	{
		//print_r("processing epa");
		return true;
	}
}
