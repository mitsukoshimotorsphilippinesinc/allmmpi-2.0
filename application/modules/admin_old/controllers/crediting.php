<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crediting extends Admin_Controller {

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
        $this->load->model('tracking_model');
        $this->load->model('users_model');
	}

	public function get_template($payout_type, $start_date, $end_date)
	{
		$payout_type = strtolower($payout_type);
		$start_date = str_replace("-", "", $start_date);
		$end_date = str_replace("-", "", $end_date);
		$generated_filename = "commission_report_payout_{$payout_type}_{$start_date}_{$end_date}.xlsx";
		$template_path = FCPATH . "/assets/media/uploads/commission_report_payout_template.xlsx";
		$template_contents = file_get_contents($template_path);

		$mime = get_mime_by_extension($template_path);
		header('Content-Type: ' . $mime);
		header('Content-Disposition: inline; filename="' . $generated_filename . '";');
		echo $template_contents;
	}

	public function get_crediting_logs()
	{
		$auto_crediting = $this->members_model->get_auto_crediting();
		foreach($auto_crediting as $crediting)
		{
			$user_data = $this->users_model->get_users(array('user_id' => $crediting->user_id));
			if(sizeof($user_data) == 0) continue;
			$crediting->user_name = $user_data[0]->username;
		}
		$this->return_json(1, 'success', array('auto_crediting' => $auto_crediting)); 
	}

	public function download_crediting_log()
	{
		$crediting_id = $this->input->post('crediting_id');

		$crediting_log_data = $this->members_model->get_auto_crediting(array('crediting_id' => $crediting_id));
		if(sizeof($crediting_log_data) == 0)
		{
			$this->return_json(0, 'Crediting Log not found');
			return;
		}
		$crediting_log_data = $crediting_log_data[0];

		$payout_type = strtolower($crediting_log_data->payout_type);
		$start_date = explode(" ", $crediting_log_data->start_date);
		$start_date = str_replace("-", "", $start_date[0]);
		$end_date = explode(" ", $crediting_log_data->end_date);
		$end_date = str_replace("-", "", $end_date[0]);
		$filename = "commission_report_payout_{$payout_type}_{$start_date}_{$end_date}_{$crediting_log_data->hash}.xlsx";
		$filepath = site_url('/assets/media/uploads/crediting') . "/" . $filename;

		$this->return_json(1, 'Success', array('file' => $filepath));
		return;
	}

	public function get_crediting_history()
	{
		$crediting_id = $this->input->post('crediting_id');

		$crediting_log_data = $this->members_model->get_auto_crediting(array('crediting_id' => $crediting_id));
		if(sizeof($crediting_log_data) == 0)
		{
			$this->return_json(0, 'Crediting Log not found');
			return;
		}
		$crediting_log_data = $crediting_log_data[0];

		$crediting_logs = $this->tracking_model->get_auto_crediting_logs(array(
			'crediting_id' => $crediting_id
		));

		foreach($crediting_logs as $crediting_log)
		{
			$user_data = $this->users_model->get_users(array('user_id' => $crediting_log->user_id));
			if(sizeof($user_data) == 0) continue;
			$crediting_log->user_name = $user_data[0]->username;
		}

		$this->return_json(1, 'Success', array('crediting_logs' => $crediting_logs));
		return;
	}

	public function run_crediting()
	{
		$crediting_id = $this->input->post('crediting_id');

		$crediting_log_data = $this->members_model->get_auto_crediting(array('crediting_id' => $crediting_id));
		if(sizeof($crediting_log_data) == 0)
		{
			$this->return_json(0, 'Crediting Log not found');
			return;
		}
		$crediting_log_data = $crediting_log_data[0];

		$payout_type = strtolower($crediting_log_data->payout_type);
		$start_date = explode(" ", $crediting_log_data->start_date);
		$start_date = str_replace("-", "", $start_date[0]);
		$end_date = explode(" ", $crediting_log_data->end_date);
		$end_date = str_replace("-", "", $end_date[0]);
		$filename = "commission_report_payout_{$payout_type}_{$start_date}_{$end_date}_{$crediting_log_data->hash}.xlsx";
		$location = "/assets/media/uploads/crediting";
		$fullpath = FCPATH . $location . "/". $filename;

		// setttings
		$filenameParts = explode('_', $filename);
		// standard filename format: commission_report_payout_(igpsm/unilevel)_(start_date)_(end_date).xlsx
		$this->_payout_type = $filenameParts[3];
		if(!in_array(strtolower($this->_payout_type), array('igpsm','unilevel'))) return false;

		$startDateRaw = $filenameParts[4];
		$this->_start_date = substr($startDateRaw, 0, 4) . '-' . substr($startDateRaw, 4, 2) . '-' . substr($startDateRaw, 6, 2);
		$endDateRaw = $filenameParts[5];
		$this->_end_date = substr($endDateRaw, 0, 4) . '-' . substr($endDateRaw, 4, 2) . '-' . substr($endDateRaw, 6, 2);

		// run process
		$this->_process_upload($fullpath, $crediting_id);

		// store before data
		$this->_store_before_data();
        
		// actual crediting
		$this->_actual_crediting();

		// store after data
		$this->_store_after_data();

		// update crediting log
		$this->members_model->update_auto_crediting(array(
			'status' => 'COMPLETED'
		), array(
			'crediting_id' => $crediting_id
		));

		// add to tr_auto_crediting_logs
		$this->tracking_model->insert_auto_crediting_logs(array(
			'crediting_id' => $crediting_id,
			'user_id' => $this->user->user_id
		));

		$this->return_json(1, 'success');
		return;
	}

	public function delete_crediting_log()
	{
		$crediting_id = $this->input->post('crediting_id');

		$crediting_log_data = $this->members_model->get_auto_crediting(array('crediting_id' => $crediting_id));
		if(sizeof($crediting_log_data) == 0)
		{
			$this->return_json(0, 'Crediting Log not found');
			return;
		}
		$crediting_log_data = $crediting_log_data[0];

		$this->members_model->delete_auto_crediting(array('crediting_id' => $crediting_id));

		$this->return_json(1, 'success');
		return;
	}

	public function index()
	{
		// get all uploaded crediting
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

			$_hash = sha1(date('Y-m-d h:i:s'));

			$filename = $this->input->get('filename');
			if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
            
            // additional checking involving payout periods 
            $filenameParts = explode('_', $filename);
            //// standard filename format: commission_report_payout_(igpsm/unilevel)_(start_date)_(end_date).xlsx           
            $startDateRaw = $filenameParts[4];
            $_startdate = substr($startDateRaw, 0, 4) . '-' . substr($startDateRaw, 4, 2) . '-' . substr($startDateRaw, 6, 2);
            $endDateRaw = $filenameParts[5];
            $_enddate = substr($endDateRaw, 0, 4) . '-' . substr($endDateRaw, 4, 2) . '-' . substr($endDateRaw, 6, 2);

            // check if existing payout period
            $where = "DATE(start_date) = '{$_startdate}' AND DATE(end_date) = '{$_enddate}' AND is_official = 1 AND status <> 'ACTIVE'";
            $payout_period_details = $this->payout_model->get_payout_periods($where);
           
            if (empty($payout_period_details)) {            
            	echo('{"success":false, "details": "[ERR1] Invalid payout period. Please check the Start Date and End Date on your attachment filename."}');
            	
                if (file_exists($target_path.$filename.'.xlsx')) 
                    unlink($target_path.$filename.'.xlsx');
                 
            } 
            
			$ret = $this->_upload($filename, $fileName, $target_path.$fileName, $location, $_hash);

			if($ret)
			{
				echo('{"success":true, "details": "Success."}');
			}
			else
			{
				//echo('{"success":false, "details": "Processing Error Test."}');
				echo('{"success":false, "details": "[ERR2] Invalid Payout Period. Please check the Start Date and End Date on your attachment filename."}');
                 
			}

			} else { 
				echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); 
			}
		} else {
			echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
		}
	}

	private function _upload($filename, $uploading_filename, $tmp_filename, $location, $hash)
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
            AND 
                status <> 'ACTIVE'    
		";
        
		$po_payout_period = $this->db->query($sql);
		$po_payout_period = $po_payout_period->result();

		if(count($po_payout_period) == 0 || count($po_payout_period) > 1) return false;

		$po_payout_period = $po_payout_period[0];
		if($po_payout_period->payout_type != strtoupper($this->_payout_type)) return false;

		//// backup payouts
		//$this->_backup_member_payouts($this->_start_date, $this->_end_date);

		$filename = $filename . "_" . $hash . "." . $format;
		
		$target_filename = $filename;
		
		$location = substr($location,1,strlen($location));
		
		$target_fullpath = FCPATH . $location;
		$fullpath = FCPATH . $location . "/". $filename;
		
		$_ret = true;
		$_ret = copy($temp_file, $fullpath);		
		
		//chmod($fullpath, 777);

		// insert to cm_auto_crediting
		$this->members_model->insert_auto_crediting(array(
			'payout_type' => strtoupper($this->_payout_type),
			'start_date' => $this->_start_date,
			'end_date' => $this->_end_date,
			'status' => 'PENDING',
			'hash' => $hash,
			'user_id' => $this->user->user_id
		));
		
		if (file_exists($temp_file)) unlink($temp_file);
		// $_ret = ($_ret)?$this->_process_upload($fullpath):false;
        
		// // store before data
		// $this->_store_before_data();
        
		// //// actual crediting
		// $this->_actual_crediting();

		// //// store after data
		// $this->_store_after_data();
        //
		//// run variance deductions
		////$this->_variance_deductions();

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
			AND 
				status = 'PENDING'
			";
			$this->db->query($sql);
		}

		// update status to COMPLETED		
		$sql = "
		UPDATE
			cm_member_payouts
		SET
			status = 'COMPLETED'
		WHERE
			start_date = '" . $this->_start_date . "'
		AND
			end_date = '" . $this->_end_date . "'
		AND 
			status = 'PENDING'
		";
		$this->db->query($sql);
		
		// gc
		$sql = "
		UPDATE
			ph_member_gc_commissions a
		SET
			a.gc_after = (
				SELECT
					b.gift_cheques
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			),
			a.gc_variance_after = (
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
		AND
			start_date = '" . $this->_start_date . "'
		AND
			end_date = '" . $this->_end_date . "'
		AND 
			status = 'PENDING'
		";
		
		$this->db->query($sql);
		
		// update status to COMPLETED		
		$sql = "
		UPDATE
			ph_member_gc_commissions
		SET
			status = 'COMPLETED'
		WHERE
			start_date = '" . $this->_start_date . "'
		AND
			end_date = '" . $this->_end_date . "'
		AND 
			status = 'PENDING'
		";
		$this->db->query($sql);
		
		// gcep
		$sql = "
		UPDATE
			ph_member_gcep_commissions a
		SET
			a.gcep_after = (
				SELECT
					b.gcep
				FROM
					cm_members b 
				WHERE
					b.member_id = a.member_id
			),
			a.gcep_variance_after = (
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
		AND
			start_date = '" . $this->_start_date . "'
		AND
			end_date = '" . $this->_end_date . "'
		AND 
			status = 'PENDING'
		";
		
		$this->db->query($sql);
		
		// update status to COMPLETED		
		$sql = "
		UPDATE
			ph_member_gcep_commissions
		SET
			status = 'COMPLETED'
		WHERE
			start_date = '" . $this->_start_date . "'
		AND
			end_date = '" . $this->_end_date . "'
		AND 
			status = 'PENDING'
		";
		$this->db->query($sql);
		

	}

	private function _actual_crediting()
	{
		
		// ===========
		// FUNDS START
		// ===========
		
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
				cash_card LIKE ('%FUNDS%')
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
				cash_card LIKE ('%FUNDS%')
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
		
		// =========
		// FUNDS END
		// =========
		// =======================================================
		// ========
		// GC START
		// ========
		
		$sql = "
		UPDATE
			cm_members a
		SET
			a.gift_cheques = a.gift_cheques + (
				SELECT
					b.net_gc
				FROM
					ph_member_gc_commissions b
				WHERE
					b.member_id = a.member_id
				AND 
					b.start_date = '" . $this->_start_date . "'
				AND 
					b.end_date = '" . $this->_end_date . "'	
				AND 
					b.status = 'PENDING'
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					ph_member_gc_commissions
				WHERE
					is_on_hold = 0
				AND 
					start_date = '" . $this->_start_date . "'
				AND 
					end_date = '" . $this->_end_date . "'	
				AND 
					status = 'PENDING'	
			)
		";
		
		$this->db->query($sql);

		// ======
		// GC END
		// ======
		// =======================================================
		// ==========
		// GCEP START
		// ==========
		
		$sql = "
		UPDATE
			cm_members a
		SET
			a.gcep = a.gcep + (
				SELECT
					b.net_gcep
				FROM
					ph_member_gcep_commissions b
				WHERE
					b.member_id = a.member_id
				AND 
					b.start_date = '" . $this->_start_date . "'
				AND 
					b.end_date = '" . $this->_end_date . "'	
				AND 
					b.status = 'PENDING'	
			)
		WHERE
			a.member_id IN (
				SELECT
					member_id
				FROM
					ph_member_gcep_commissions
				WHERE
					is_on_hold = 0
				AND 
					start_date = '" . $this->_start_date . "'
				AND 
					end_date = '" . $this->_end_date . "'	
				AND 
					status = 'PENDING'	
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
			AND
				status = 'PENDING'	
			";
			$this->db->query($sql);
		}

		// gc
		$sql = "
		UPDATE
			ph_member_gc_commissions a
		SET
			a.gc_before = (
				SELECT
					b.gift_cheques
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			),
			a.gc_variance_before = (
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
		AND
			a.start_date = '" . $this->_start_date . "'
		AND
			a.end_date = '" . $this->_end_date . "'
		AND
			a.status = 'PENDING'		
		";
		
		$this->db->query($sql);

		// gcep
		$sql = "
		UPDATE
			ph_member_gcep_commissions a
		SET
			a.gcep_before = (
				SELECT
					b.gcep
				FROM
					cm_members b
				WHERE
					b.member_id = a.member_id
			),
			a.gcep_variance_before = (
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
		AND
			a.start_date = '" . $this->_start_date . "'
		AND 
			a.end_date = '" . $this->_end_date . "'
		AND
			a.status = 'PENDING'
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
				`total_deductions`,
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
				`crediting_id`,
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
					`total_deductions`,
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
					`crediting_id`,
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

	private function _process_upload($filepath, $crediting_id)
	{
		$_ret = true;

        $reader = new PHPExcel_Reader_Excel2007();
        $excel = $reader->load($filepath);

        foreach ($excel->getAllSheets() as $sheet)
        {
        	$method = "_sheet_process_" . str_replace(" ", "_", strtolower($sheet->getTitle()));
        	if(method_exists($this, $method)) {
        		//echo "_process_upload: " . $crediting_id;
        		$tmp = $this->$method($sheet, $crediting_id);
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

			foreach($_map as $k => $v) $rowData->$v = $sheet->getCell($k.$row)->getCalculatedValue();
			//foreach($_map as $k => $v) $rowData->$v = $sheet->getCell($k.$row)->getValue();
			//foreach($_map as $k => $v) if(substr($rowData->$v, 0, 1) == "=") $rowData->$v = $sheet->getCell($k.$row)->getCalculatedValue();

			$sheetData[] = $rowData;
			$row++;
		}

		return $sheetData;
	}

	/*
	private function _sheet_column_read_funds($sheet)
	{
		
		$sheetData = array();
		$row = 4; // get column names
		$deduction_data = array();
		$_funds_map = array();


		$lastColumn = $sheet->getHighestColumn();
		$lastColumn++;
		for ($column = 'A'; $column != $lastColumn; $column++) {
		    $cell = $sheet->getCell($column.$row);

		    // check in rf_payout_crediting_fields
		    $where = array(
		    	'always_present' => 1,
		    	'name' => $cell
		    	);

		    $is_field_present = $this->payout_model->get_payout_crediting_fields_count($where);

		    if ($is_field_present == 1) {
		    	// update is_present and column position
		    	$data = array(
		    			'column_position' => $column
		    		);

		    	$where = array(
		    			'name' => $cell
		    		);			    	

		    	$this->payout_model->update_payout_crediting_fields($data, $where);
		    } else {
		    	// check if deduction
		    	if (substr($cell, 0, 4) == 'DED:')	{
		    		$deduction_details = $this->payout_model->get_payout_crediting_field_by_name('DEDUCTIONS');		    		
					$deduction_data[$column] = trim(substr($cell, 5, strlen($cell) - 4));		    			    			
		    	}
		    }
		}

		if (sizeof($deduction_data) > 0) {
			// insert to rf_payout_crediting_fields table - DEDUCTIONS
			$data = array('column_position' => json_encode($deduction_data));
			$where = array('name' => 'DEDUCTIONS');
			$this->payout_model->update_payout_crediting_fields($data, $where); 
		}
	
		// check if all "always_present" columns are present first
		$where = "trim(column_position) = '' AND always_present = 1";
		$has_incomplete_columns = $this->payout_model->get_payout_crediting_fields_count($where);

		
		//if ($has_incomplete_columns > 0) {
		//	// ERROR
		//	// TODO
		//
		//	return;
		//}
		

		// get all crediting fields with always present = 1
		$where = array('always_present' => 1);
		$payout_crediting_field_details = $this->payout_model->get_payout_crediting_fields($where);

		foreach($payout_crediting_field_details as $pcfd) {
			$_funds_map[$pcfd->column_position] = $pcfd->member_payout_column;
		}

		// for deduction mapping
		foreach($deduction_data as $k => $v) {
			$_funds_map[$k] = strtolower(str_replace(' ', '_', $v));
		}	
	
		return $_funds_map;

	}*/

	private function _sheet_column_read($sheet)
	{

		// clear all column_position
		$sql = "UPDATE rf_payout_crediting_fields SET column_position = ''";
		$this->db->query($sql);

		$sheetData = array();
		$row = 4; // get column names
		$deduction_data = array();
		$_funds_map = array();


		$lastColumn = $sheet->getHighestColumn();
		$lastColumn++;
		for ($column = 'A'; $column != $lastColumn; $column++) {
		    $cell = $sheet->getCell($column.$row);

		    $_funds_map[$column] = strtolower(str_replace(' ', '_', $cell));

		    // update rf_payout_crediting_fields
		    $where = array(
		    	'always_present' => 1,
		    	'name' => $cell
		    	);

		    $is_field_present = $this->payout_model->get_payout_crediting_fields_count($where);

		    if ($is_field_present == 1) {
		    	// update is_present and column position
		    	$data = array(
		    			'column_position' => $column
		    		);

		    	$where = array(
		    			'name' => $cell
		    		);			    	

		    	$this->payout_model->update_payout_crediting_fields($data, $where);
		    } else {
		    	// check if deduction
		    	if (substr($cell, 0, 4) == 'DED:')	{
		    		$deduction_details = $this->payout_model->get_payout_crediting_field_by_name('DEDUCTIONS');		    		
					$deduction_data[$column] = trim(substr($cell, 5, strlen($cell) - 4));		    			    			
		    	}
		    }	
		}

		if (sizeof($deduction_data) > 0) {
			// insert to rf_payout_crediting_fields table - DEDUCTIONS
			$data = array('column_position' => json_encode($deduction_data));
			$where = array('name' => 'DEDUCTIONS');
			$this->payout_model->update_payout_crediting_fields($data, $where); 
		}
	
		// check if all "always_present" columns are present first
		$where = "trim(column_position) = '' AND always_present = 1";
		$has_incomplete_columns = $this->payout_model->get_payout_crediting_fields($where);

		if (empty($has_incomplete_columns)) {
			$this->has_missing_column = 0;				
			return $_funds_map;
		
		} else {

			$this->has_missing_column = 1;
			return $has_incomplete_columns;
		}
		
	}

	private function _sheet_process_funds($sheet, $crediting_id)
	{
		
		// backup
		$this->_backup_member_payouts($this->_start_date, $this->_end_date);
		
		// remove old member payout data
		$sql = "
			DELETE FROM
				cm_member_payouts
			WHERE
				start_date = '" . $this->_start_date . "'
			AND
				end_date = '" . $this->_end_date . "'
		";
		$this->db->query($sql);
	
		/*// prepare data from excel to be imported to cm_member_payouts
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
			'J'	=> 'deduction1A',
			'K'	=> 'deduction1B',
			'L'	=> 'funds_variance',
			'M'	=> 'card_fee',
			'N'	=> 'net',
			'O'	=> 'cash_card',
			'P'	=> 'account_number',
			'Q' => 'depot',
			'R' => 'group_name',
			'S' => 'is_corpo_sharing',
			'T' => 'check_release',
			'U' => 'is_on_hold',
			'V' => 'start_date',
			'W' => 'end_date',
			'X' => 'remarks'
		);
		*/

		/*$_hash_value_override = array(
			'net_gross' => function($rowData){
				return floatval($rowData->gross) - floatval($rowData->gcep);
			},
			'net_of_tax' => function($rowData){
				return floatval($rowData->net_gross) - floatval($rowData->tax);
			},
			//'total_amount' => function($rowData){
			//	return floatval($rowData->net_of_tax) - floatval($rowData->deduction1) - abs(floatval($rowData->funds_variance)) - floatval($rowData->card_fee);
			'net_amount' => function($rowData){
				return floatval($rowData->net_of_tax);
			}
		);*/
		
		// get the column dynamically and create proper map
		$_map = $this->_sheet_column_read($sheet);

		if ($this->has_missing_column == 1) {

			// TODO: return missing columns
			return false;

		} else {

			// process the sheet
			$sheetData = $this->_sheet_processor($sheet, $_map);
			
			foreach($sheetData as $rowData)
			{
				$total_deductions = 0;	
				$_adjustments = 0;
				$additional_remarks = '';

				foreach($_map as $k => $v)  {
					//if($rowData->$v === "#VALUE!") $rowData->$v = $_hash_value_override[$v]($rowData);

					if (substr($v, 0, 4) == 'ded:') {

						// accumulate deductions
						$total_deductions = $total_deductions + $rowData->$v;					

						if ($rowData->$v > 0) {
							// insert to po_member_deductions
							$data_deductions = array(
								'member_id' =>  $rowData->member_id,	
								'type' =>  strtoupper($this->_payout_type),	
								'deduction_type' =>  ucfirst(trim(substr(str_replace('_', ' ', $v), 5, strlen($v) - 4))),	
								'amount' =>  floatval($rowData->$v),	
								'start_date' =>  $this->_start_date,
								'end_date' =>  $this->_end_date,
								'status' =>  'PROCESSED',
								'crediting_id' =>  $crediting_id
								);

							//echo json_encode($data_deductions);
							$this->payout_model->insert_member_deductions($data_deductions);
						}
					}

					// if adjustment's value
					if ($v == 'net_amount_(adj)') {		
						$_adjustments = $rowData->net_amount;
					} 

					// REMARKS					
					if ($v == 'is_corpo_sharing') {

						if ($rowData->$v > 0) {
							// get member_details
							$member_details = $this->members_model->get_member_by_id($rowData->$v);

							if (!empty($member_details)) {
								$proper_member_name = $member_details->first_name . ' ' . $member_details->middle_name . ' ' . $member_details->last_name;
								// add remarks
								$additional_remarks = "Corpo sharing from " . strtoupper($proper_member_name) . ' ';

							}
						}
					}

				}

				$insert_data = array(
					'payout_type' => $this->_payout_type,
					'member_id' => $rowData->member_id,
					'gross' => floatval($rowData->gross),
					'gcep' => floatval($rowData->less_10_percent),
					'net_of_gcep' => floatval($rowData->net_gross),
					'witholding_tax' => floatval($rowData->tax),
					'net_of_tax' => floatval($rowData->net_of_tax),
					'adjustments' => floatval($_adjustments),
					'total_deductions' => floatval($total_deductions),
					'total_amount' => floatval($rowData->net_amount),
					'service_depot' => $rowData->depot,
					'group_name' => $rowData->group_name,
					'cash_card' => trim($rowData->cash_card),
					'is_corpo_sharing' => intval($rowData->is_corpo_sharing),
					'check_release' => intval($rowData->check_release),
					'is_on_hold' => intval($rowData->is_on_hold),
					'status' => 'PENDING',
					'start_date' => $this->_start_date,
					'end_date' => $this->_end_date,
					'user_id' => $this->user->user_id,
					'crediting_id' => $crediting_id,
					'remarks' => $additional_remarks . $rowData->remarks 
				);
				$this->members_model->insert_member_payouts($insert_data);
			}

			return true;
		}	
	}

	private function _sheet_process_gc($sheet, $crediting_id)
	{
	
		// backup gc
		$sql = "
			INSERT INTO
				ph_member_gc_commissions_backups
			(				
				`member_id`,
				`ibsp_gc`,
				`irs_gc`,
				`ivp_gc`,
				`total_gc`,
				`gc_variance`,
				`net_gc`,
				`is_on_hold`,
				`remarks`,
				`status`,
				`start_date`,
				`end_date`,
				`gc_before`,
				`gc_after`,
				`gc_variance_before`,
				`gc_variance_after`,
				`user_id`,
				`crediting_id`,				
				`insert_timestamp`
			)
			(
				SELECT
					`member_id`,
					`ibsp_gc`,
					`irs_gc`,
					`ivp_gc`,
					`total_gc`,
					`gc_variance`,
					`net_gc`,
					`is_on_hold`,
					`remarks`,
					`status`,
					`start_date`,
					`end_date`,
					`gc_before`,
					`gc_after`,
					`gc_variance_before`,
					`gc_variance_after`,
					`user_id`,
					`crediting_id`,				
					`insert_timestamp`
				FROM
					ph_member_gc_commissions
				WHERE
					start_date = '" . $this->_start_date . "'
				AND
					end_date = '" . $this->_end_date . "'
			)
		";
		$this->db->query($sql);

		// remove OLD data
		$sql = "
			DELETE FROM
				ph_member_gc_commissions
			WHERE
				start_date = '" . $this->_start_date . "'
			AND
				end_date = '" . $this->_end_date . "'
		";
		$this->db->query($sql);
		
		
		
	
		$_map = array(
			'A' => 'member_id',
			'B' => 'full_name',
			'C' => 'sp',
			'D'	=> 'rs',
			'E' => 'vp',
			'F' => 'total_gc',
			'G'	=> 'negative_gc_fund',
			'H'	=> 'balance_for_upload',
			'I' => 'is_on_hold',
			'J' => 'remarks'
		);

		$_hash_value_override = array(
			'total_gc' => function($rowData){
				return floatval($rowData->sp) + floatval($rowData->rs) + floatval($rowData->vp);
			},
			'balance_for_upload' => function($rowData){
				return floatval($rowData->total_gc) - floatval($rowData->negative_gc_fund);
			}
		);

		$sheetData = $this->_sheet_processor($sheet, $_map);
		
		foreach($sheetData as $item) {

			foreach($_map as $k => $v) if($item->$v === "#VALUE!") $item->$v = $_hash_value_override[$v]($item);

			$sql = "
			INSERT INTO
				ph_member_gc_commissions
			(
				`member_id`,
				`ibsp_gc`,
				`irs_gc`,
				`ivp_gc`,
				`total_gc`,
				`gc_variance`,
				`net_gc`,
				`is_on_hold`,
				`remarks`,
				`status`,
				`start_date`,
				`end_date`,
				`user_id`,
				`crediting_id`
			)
			VALUES
			(
				'{$item->member_id}',
				" . floatval($item->sp) . ",
				" . floatval($item->rs) . ",
				" . floatval($item->vp) . ",
				" . floatval($item->total_gc) . ",
				" . abs(floatval($item->negative_gc_fund)) . ",
				" . floatval($item->balance_for_upload) . ",
				" . intval($item->is_on_hold) . ",
				'" . trim($item->remarks) . "',
				'PENDING',
				'" . $this->_start_date . "',
				'" . $this->_end_date . "',
				'" . $this->user->user_id . "',
				'{$crediting_id}'
			)
			";
			$this->db->query($sql);
		}
			
		return true;
	}

	private function _sheet_process_gcep($sheet, $crediting_id)
	{
		
		// backup ph_member_gcep_commissions		
		$sql = "
			INSERT INTO
				ph_member_gcep_commissions_backups
			(				
				`member_id`,
				`total_gcep`,
				`gcep_variance`,
				`net_gcep`,
				`cash_card`,
				`is_on_hold`,
				`remarks`,
				`status`,
				`start_date`,
				`end_date`,
				`gcep_before`,
				`gcep_after`,
				`gcep_variance_before`,
				`gcep_variance_after`,
				`user_id`,
				`crediting_id`,				
				`insert_timestamp`
			)
			(
				SELECT
					`member_id`,
					`total_gcep`,
					`gcep_variance`,
					`net_gcep`,
					`cash_card`,
					`is_on_hold`,
					`remarks`,
					`status`,
					`start_date`,
					`end_date`,
					`gcep_before`,
					`gcep_after`,
					`gcep_variance_before`,
					`gcep_variance_after`,
					`user_id`,
					`crediting_id`,				
					`insert_timestamp`
				FROM
					ph_member_gcep_commissions
				WHERE
					start_date = '" . $this->_start_date . "'
				AND
					end_date = '" . $this->_end_date . "'
			)
		";
		$this->db->query($sql);

		// remove old data
		$sql = "
			DELETE FROM
				ph_member_gcep_commissions
			WHERE
				start_date = '" . $this->_start_date . "'
			AND
				end_date = '" . $this->_end_date . "'
		";
		$this->db->query($sql);
	
		// for excel to table import
		$_map = array(
			'A' => 'member_id',
			'B' => 'full_name',
			'C' => 'total_gcep',
			'D'	=> 'negative_gcep_fund',
			'E'	=> 'balance_for_upload',
			'F' => 'is_on_hold',
			'G' => 'remarks'
		);

		$_hash_value_override = array(
			'balance_for_upload' => function($rowData){
				return floatval($rowData->total_gcep) - floatval($rowData->negative_gcep_fund);
			}
		);

		$sheetData = $this->_sheet_processor($sheet, $_map);

		// insert to ph_member_gcep_commissions
		foreach($sheetData as $item) {

			foreach($_map as $k => $v) if($item->$v === "#VALUE!") $item->$v = $_hash_value_override[$v]($item);

			$sql = "
			INSERT INTO
				ph_member_gcep_commissions
			(
				`member_id`,
				`total_gcep`,
				`gcep_variance`,
				`net_gcep`,
				`is_on_hold`,
				`remarks`,
				`status`,
				`start_date`,
				`end_date`,				
				`user_id`,
				`crediting_id`				
			)
			VALUES
			(
				'{$item->member_id}',
				" . floatval($item->total_gcep) . ",
				" . abs(floatval($item->negative_gcep_fund)) . ",
				" . floatval($item->balance_for_upload). ",
				" . intval($item->is_on_hold) . ",
				'" . trim($item->remarks) . "',
				'PENDING',
				'" . $this->_start_date . "',
				'" . $this->_end_date . "',
				'" . $this->user->user_id . "',
				'{$crediting_id}'
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
	
	/*
	private function _sheet_process_adjustments($sheet)
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
			'J'	=> 'deduction1A',
			'K'	=> 'deduction1B',
			'L'	=> 'funds_variance',
			'M'	=> 'card_fee',
			'N'	=> 'net',
			'O'	=> 'cash_card',
			'P'	=> 'account_number',
			'Q' => 'depot',
			'R' => 'group_name',
			'S' => 'is_corpo_sharing',
			'T' => 'check_release',
			'U' => 'is_on_hold',
			'V' => 'is_gcep_exempted',
			'W' => 'start_date',
			'X' => 'end_date',
			'Y' => 'remarks'
		);
		
		$_hash_value_override = array(
			'net_gross' => function($rowData){
				return floatval($rowData->gross) - floatval($rowData->gcep);
			},
			'net_of_tax' => function($rowData){
				return floatval($rowData->net_gross) - floatval($rowData->tax);
			},
			'net' => function($rowData){
				return floatval($rowData->net_of_tax) - floatval($rowData->deduction1) - abs(floatval($rowData->funds_variance)) - floatval($rowData->card_fee);
			}
		);
		
		$sheetData = $this->_sheet_processor($sheet, $_map);
		
		foreach($sheetData as $rowData)
		{
			foreach($_map as $k => $v) if($rowData->$v === "#VALUE!") $rowData->$v = $_hash_value_override[$v]($rowData);

			$insert_data = array(
				'payout_type' => $this->_payout_type,
				'member_id' => $rowData->member_id,
				'gross' => floatval($rowData->gross),
				'gcep' => floatval($rowData->gcep),
				'net_of_gcep' => floatval($rowData->net_gross),
				'witholding_tax' => floatval($rowData->tax),
				'net_of_tax' => floatval($rowData->net_of_tax),
				'balance' => floatval($rowData->deduction1A) + floatval($rowData->deduction1B),
				'deduction2' => abs(floatval($rowData->funds_variance)),
				'card_fee' => floatval($rowData->card_fee),
				'total_amount' => floatval($rowData->net),
				'adjustments' => floatval($rowData->net),
				'service_depot' => $rowData->depot,
				'group_name' => $rowData->group_name,
				'cash_card' => trim($rowData->cash_card),
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
	*/
    
    public function check_payout_period()
	{
		$payout_type = $this->input->post('payout_type');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
		
        $where = "payout_type = UPPER('{$payout_type}') AND DATE(start_date) = '{$start_date}' AND DATE(end_date) = '{$end_date}' AND is_official = 1";
        $payout_period_details = $this->payout_model->get_payout_periods($where);
        
        if (empty($payout_period_details)) {
            // invalid payout period
            $this->return_json(0, 'Invalid Payout Period. Please check the Payout Periods page.');
			return;           
        }  else {
            $this->return_json(1, 'Valid Payout Period');
			return;      
        } 
       
		$this->return_json(1, 'success');
		return;
	}

	public function check_report_generation()
	{
		$crediting_id = $this->input->post('crediting_id');

		$crediting_log_data = $this->members_model->get_auto_crediting(array('crediting_id' => $crediting_id));
		if(sizeof($crediting_log_data) == 0)
		{
			$this->return_json(0, 'Crediting Log not found');
			return;
		}
		$crediting_log_data = $crediting_log_data[0];

		$payout_type = strtolower($crediting_log_data->payout_type);
		$start_date = explode(" ", $crediting_log_data->start_date);
		$start_date = str_replace("-", "", $start_date[0]);
		$end_date = explode(" ", $crediting_log_data->end_date);
		$end_date = str_replace("-", "", $end_date[0]);

		//$filename = "commission_report_payout_{$payout_type}_{$start_date}_{$end_date}_{$crediting_log_data->hash}.xlsx";
		//$filepath = site_url('/assets/media/uploads/crediting') . "/" . $filename;

		$html = "<p>You are to dowload the after {$payout_type} crediting report from {$start_date} to {$end_date} with Crediting ID: {$crediting_id}. 
				<br/>Proceed with download?</p>";

		$this->return_json(1, 'Success', array('html' => $html));
		return;
	}

    function export_xls($crediting_id)
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
		
        // get payout crediting details
        $auto_crediting_details = $this->members_model->get_auto_crediting_by_id($crediting_id);

        $start_date = date("Y-m-d",strtotime($auto_crediting_details->start_date));
        $end_date = date("Y-m-d",strtotime($auto_crediting_details->end_date));
        //$end_date = $auto_crediting_details->end_date;

		try {
			
			// adjusting memory limit to accommodate PHPExcel processing 
			set_time_limit(0); // eliminating the timeout
			ini_set('memory_limit', '512M');

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle("payout requests")->setDescription("none");
			$start_column_num = 3;

			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle("FUNDS");

			$where = "DATE(start_date) = DATE('{$start_date}') AND DATE(end_date) = DATE('{$end_date}') AND crediting_id = {$crediting_id}";

			$member_payout_count = $this->members_model->get_member_payouts_count($where);

			$funds_in_backup = 0;

			if ($member_payout_count <= 0) {
				$member_payout_count = $this->members_model->get_member_payouts_backups_count($where);
				$funds_in_backup = 1;
			}

			$filename = "crediting_report_" . "_" . $crediting_id . "_" . str_replace("-", "", $start_date) . "_to_" . str_replace("-", "", $end_date) . ".xlsx";

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
			$worksheet->getStyle('O' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('P' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('Q' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('R' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('S' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('T' . $start_column_num)->getFont()->setBold(true);
			
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
			$worksheet->getStyle('S' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('T' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//set column names
			$worksheet->setCellValue('A1', "Credited Funds from {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'MEMBER ID');
			$worksheet->setCellValue('B' . $start_column_num, 'LAST NAME');
			$worksheet->setCellValue('C' . $start_column_num, 'FIRST NAME');
			$worksheet->setCellValue('D' . $start_column_num, 'MIDDLE NAME');
			$worksheet->setCellValue('E' . $start_column_num, 'GROSS');
			$worksheet->setCellValue('F' . $start_column_num, 'LESS 10 PERCENT');
			$worksheet->setCellValue('G' . $start_column_num, 'NET GROSS');
			$worksheet->setCellValue('H' . $start_column_num, 'TAX');
			$worksheet->setCellValue('I' . $start_column_num, 'NET OF TAX');
			$worksheet->setCellValue('J' . $start_column_num, 'TOTAL DEDUCTIONS');
			$worksheet->setCellValue('K' . $start_column_num, 'NET AMOUNT');
			$worksheet->setCellValue('L' . $start_column_num, 'STATUS');
			$worksheet->setCellValue('M' . $start_column_num, 'FUNDS BEFORE');
			$worksheet->setCellValue('M' . $start_column_num, 'FUNDS AFTER');			
			$worksheet->setCellValue('O' . $start_column_num, 'START DATE');
			$worksheet->setCellValue('P' . $start_column_num, 'END DATE');			
			$worksheet->setCellValue('Q' . $start_column_num, 'IS CORPO SHARING');
			$worksheet->setCellValue('R' . $start_column_num, 'CHECK RELEASE');
			$worksheet->setCellValue('S' . $start_column_num, 'IS ON HOLD');
			$worksheet->setCellValue('T' . $start_column_num, 'REMARKS');

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($member_payout_count/$allowed_rows)+1;$prow++)
			{
				$offset_val = $prow * $allowed_rows;
				
				if ($funds_in_backup == 0) {
					//$member_payouts = $this->members_model->get_member_payouts($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'member_id');
					$sql = "SELECT
								a.`member_id`,
								b.`last_name`,
								b.`first_name`,
								b.`middle_name`,
								a.`gross`,
								a.`gcep`,
								a.`net_of_gcep`,
								a.`witholding_tax`,
								a.`net_of_tax`,
								a.`total_deductions`,
								a.`total_amount`,
								a.`status`,
								a.`funds_before`,
								a.`funds_after`,
								a.`start_date`,
								a.`end_date`,
								a.`is_corpo_sharing`,
								a.`check_release`,
								a.`is_on_hold`,
								a.`remarks`
							FROM 
								`cm_member_payouts` a
							LEFT JOIN 
								`cm_members` b
							ON 
								a.`member_id` = b.`member_id`
							WHERE 
								{$where}
							ORDER BY 
								a.`member_id`
							LIMIT
								{$allowed_rows}
							OFFSET 
								{$offset_val}";

				} else {
					//$member_payouts = $this->members_model->get_member_payouts_backups($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'member_id');
					$sql = "SELECT
							a.`member_id`,
							b.`last_name`,
							b.`first_name`,
							b.`middle_name`,
							a.`gross`,
							a.`gcep`,
							a.`net_of_gcep`,
							a.`witholding_tax`,
							a.`net_of_tax`,
							a.`total_deductions`,
							a.`total_amount`,
							a.`status`,
							a.`funds_before`,
							a.`funds_after`,
							a.`start_date`,
							a.`end_date`,
							a.`is_corpo_sharing`,
							a.`check_release`,
							a.`is_on_hold`,
							a.`remarks`
						FROM 
							`cm_member_payouts_backups` a
						LEFT JOIN 
							`cm_members` b
						ON 
							a.`member_id` = b.`member_id`
						WHERE 
							{$where}
						ORDER BY 
							a.`member_id`
						LIMIT
							{$allowed_rows}
						OFFSET 
							{$offset_val}";
				}

				$query = $this->db->query($sql);
				$member_payouts = $query->result();			
				$query->free_result();

				foreach ($member_payouts as $mp)
				{

					// get member details					
					$pretty_last_name = strtoupper($mp->last_name);
					$pretty_first_name = strtoupper($mp->first_name);
					$pretty_middle_name = strtoupper($mp->middle_name);

					$pretty_net_amount = number_format($mp->total_amount, 2, '.', ',');	
					$pretty_gross = number_format($mp->gross, 2, '.', ',');
					$pretty_net_gross = number_format($mp->net_of_gcep, 2, '.', ',');
					$pretty_less_10_percent = number_format($mp->gcep, 2, '.', ',');
					$pretty_tax = number_format($mp->witholding_tax, 2, '.', ',');
					$pretty_net_of_tax = number_format($mp->net_of_tax, 2, '.', ',');
					$pretty_total_deductions = number_format($mp->total_deductions, 2, '.', ',');
					$pretty_funds_before = number_format($mp->funds_before, 2, '.', ',');
					$pretty_funds_after = number_format($mp->funds_after, 2, '.', ',');
					
					// align right					
					$worksheet->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('J' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('K' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('L' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('M' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('N' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$worksheet->setCellValue('A'. $row, $mp->member_id);
					$worksheet->setCellValue('B'. $row, $pretty_last_name);
					$worksheet->setCellValue('C'. $row, $pretty_first_name);
					$worksheet->setCellValue('D'. $row, $pretty_middle_name);														
					$worksheet->setCellValue('E'. $row, $pretty_gross);
					$worksheet->setCellValue('F'. $row, $pretty_less_10_percent);
					$worksheet->setCellValue('G'. $row, $pretty_net_gross);
					$worksheet->setCellValue('H'. $row, $pretty_tax);
					$worksheet->setCellValue('I'. $row, $pretty_net_of_tax);
					$worksheet->setCellValue('J'. $row, $pretty_total_deductions);										
					$worksheet->setCellValue('K'. $row, $pretty_net_amount);
					$worksheet->setCellValue('L'. $row, $mp->status);
					$worksheet->setCellValue('M'. $row, $pretty_funds_before);
					$worksheet->setCellValue('N'. $row, $pretty_funds_after);					
					$worksheet->setCellValue('O'. $row, $mp->start_date);
					$worksheet->setCellValue('P'. $row, $mp->end_date);
					$worksheet->setCellValue('Q'. $row, $mp->is_corpo_sharing);
					$worksheet->setCellValue('R'. $row, $mp->check_release);
					$worksheet->setCellValue('S'. $row, $mp->is_on_hold);
					$worksheet->setCellValue('T'. $row, $mp->remarks);
					
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
					$worksheet->getColumnDimension('O')->setAutoSize(true);
					$worksheet->getColumnDimension('P')->setAutoSize(true);
					$worksheet->getColumnDimension('Q')->setAutoSize(true);
					$worksheet->getColumnDimension('R')->setAutoSize(true);
					$worksheet->getColumnDimension('S')->setAutoSize(true);
					$worksheet->getColumnDimension('T')->setAutoSize(true);
					
					$row++;
				}

				if ($auto_crediting_details->payout_type == 'IGPSM') {

					// FOR GCEP	
						
					$objWorksheet = new PHPExcel_Worksheet($objPHPExcel);	
					$objPHPExcel->addSheet($objWorksheet);	
					$worksheetGCEP = $objPHPExcel->setActiveSheetIndex(1);
					$objPHPExcel->getActiveSheet()->setTitle("GCEP");

					// set column header to bold
					$worksheetGCEP->getStyle('A1')->getFont()->setBold(true);
					$worksheetGCEP->getStyle('A' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('B' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('C' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('D' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('E' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('F' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('G' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('H' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('I' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('J' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('K' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('L' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('M' . $start_column_num)->getFont()->setBold(true);
					$worksheetGCEP->getStyle('N' . $start_column_num)->getFont()->setBold(true);	


					//center column names
					$worksheetGCEP->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('K' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('L' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('M' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGCEP->getStyle('N' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					//set column names
					$worksheetGCEP->setCellValue('A1', "Credited GCEP from {$start_date} to {$end_date}");
					$worksheetGCEP->setCellValue('A' . $start_column_num, 'MEMBER ID');
					$worksheetGCEP->setCellValue('B' . $start_column_num, 'LAST NAME');
					$worksheetGCEP->setCellValue('C' . $start_column_num, 'FIRST NAME');
					$worksheetGCEP->setCellValue('D' . $start_column_num, 'MIDDLE NAME');
					$worksheetGCEP->setCellValue('E' . $start_column_num, 'TOTAL GCEP');
					$worksheetGCEP->setCellValue('F' . $start_column_num, 'GCEP VARIANCE');
					$worksheetGCEP->setCellValue('G' . $start_column_num, 'BALANCE FOR UPLOAD');
					$worksheetGCEP->setCellValue('H' . $start_column_num, 'GCEP BEFORE');
					$worksheetGCEP->setCellValue('I' . $start_column_num, 'GCEP AFTER');
					$worksheetGCEP->setCellValue('J' . $start_column_num, 'STATUS');
					$worksheetGCEP->setCellValue('K' . $start_column_num, 'START DATE');
					$worksheetGCEP->setCellValue('L' . $start_column_num, 'END DATE');			
					$worksheetGCEP->setCellValue('M' . $start_column_num, 'ON HOLD');
					$worksheetGCEP->setCellValue('N' . $start_column_num, 'REMARKS');


					$where = "DATE(start_date) = DATE('{$start_date}') AND DATE(end_date) = DATE('{$end_date}') AND crediting_id = {$crediting_id}";			
					$member_gcep_count = $this->payout_model->get_member_gcep_commissions_count($where);

					$gcep_in_backup = 0;

					if ($member_gcep_count <= 0) {
						$member_gcep_count = $this->payout_model->get_member_gcep_commissions_backups_count($where);
						$gcep_in_backup = 1;
					}

					$row = 4;

					$allowed_rows = 5000;

					for($prow = 0;$prow < ceil($member_gcep_count/$allowed_rows)+1;$prow++)
					{
						$offset_val = $prow * $allowed_rows;

						if ($gcep_in_backup == 0) {
							//$member_gcep = $this->payout_model->get_member_gcep_commissions($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'member_id');
							$sql = "SELECT
										a.`member_id`,
										b.`last_name`,
										b.`first_name`,
										b.`middle_name`,
										a.`total_gcep`,
										a.`gcep_variance`,
										a.`net_gcep`,
										a.`gcep_before`,
										a.`gcep_after`,
										a.`status`,
										a.`start_date`,
										a.`end_date`,	
										a.`is_on_hold`,
										a.`remarks`
									FROM 
										`ph_member_gcep_commissions` a
									LEFT JOIN 
										`cm_members` b
									ON 
										a.`member_id` = b.`member_id`
									WHERE 
										{$where}
									ORDER BY 
										a.`member_id`
									LIMIT
										{$allowed_rows}
									OFFSET 
										{$offset_val}";
						} else {
							//$member_gcep = $this->payout_model->get_member_gcep_commissions_backups($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'member_id');
							$sql = "SELECT
										a.`member_id`,
										b.`last_name`,
										b.`first_name`,
										b.`middle_name`,
										a.`total_gcep`,
										a.`gcep_variance`,
										a.`net_gcep`,
										a.`gcep_before`,
										a.`gcep_after`,
										a.`status`,
										a.`start_date`,
										a.`end_date`,	
										a.`is_on_hold`,
										a.`remarks`
									FROM 
										`ph_member_gcep_commissions_backups` a
									LEFT JOIN 
										`cm_members` b
									ON 
										a.`member_id` = b.`member_id`
									WHERE 
										{$where}
									ORDER BY 
										a.`member_id`
									LIMIT
										{$allowed_rows}
									OFFSET 
										{$offset_val}";		
						}					

						$query = $this->db->query($sql);
						$member_gcep = $query->result();			
						$query->free_result();

						foreach ($member_gcep as $mgcep)
						{

							// get member details	
							$pretty_last_name = strtoupper($mgcep->last_name);
							$pretty_first_name = strtoupper($mgcep->first_name);
							$pretty_middle_name = strtoupper($mgcep->middle_name);
				
							$pretty_total_gcep = number_format($mgcep->total_gcep, 2, '.', ',');	
							$pretty_gcep_variance = number_format($mgcep->gcep_variance, 2, '.', ',');
							$pretty_balance_for_upload = number_format($mgcep->net_gcep, 2, '.', ',');
							$pretty_gcep_before = number_format($mgcep->gcep_before, 2, '.', ',');
							$pretty_gcep_after = number_format($mgcep->gcep_after, 2, '.', ',');
							
							// align right					
							$worksheetGCEP->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGCEP->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGCEP->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGCEP->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGCEP->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							
							$worksheetGCEP->setCellValue('A'. $row, $mgcep->member_id);
							$worksheetGCEP->setCellValue('B'. $row, $pretty_last_name);
							$worksheetGCEP->setCellValue('C'. $row, $pretty_first_name);
							$worksheetGCEP->setCellValue('D'. $row, $pretty_middle_name);												
							$worksheetGCEP->setCellValue('E'. $row, $pretty_total_gcep);
							$worksheetGCEP->setCellValue('F'. $row, $pretty_gcep_variance);
							$worksheetGCEP->setCellValue('G'. $row, $pretty_balance_for_upload);
							$worksheetGCEP->setCellValue('H'. $row, $pretty_gcep_before);
							$worksheetGCEP->setCellValue('I'. $row, $pretty_gcep_after);					
							$worksheetGCEP->setCellValue('J'. $row, $mgcep->status);
							$worksheetGCEP->setCellValue('K'. $row, $mgcep->start_date);
							$worksheetGCEP->setCellValue('L'. $row, $mgcep->end_date);					
							$worksheetGCEP->setCellValue('M'. $row, $mgcep->is_on_hold);
							$worksheetGCEP->setCellValue('N'. $row, $mgcep->remarks);
							
							// auto resize columns
							$worksheetGCEP->getColumnDimension('A')->setAutoSize(false);
							$worksheetGCEP->getColumnDimension('B')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('C')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('D')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('E')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('F')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('G')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('H')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('I')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('J')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('K')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('L')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('M')->setAutoSize(true);
							$worksheetGCEP->getColumnDimension('N')->setAutoSize(true);
							
							$row++;
						}
					}	


					// FOR GC	
						
					$objWorksheet = new PHPExcel_Worksheet($objPHPExcel);	
					$objPHPExcel->addSheet($objWorksheet);	
					$worksheetGC = $objPHPExcel->setActiveSheetIndex(2);
					$objPHPExcel->getActiveSheet()->setTitle("GC");

					// set column header to bold
					$worksheetGC->getStyle('A1')->getFont()->setBold(true);
					$worksheetGC->getStyle('A' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('B' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('C' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('D' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('E' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('F' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('G' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('H' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('I' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('J' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('K' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('L' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('M' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('N' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('O' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('P' . $start_column_num)->getFont()->setBold(true);
					$worksheetGC->getStyle('Q' . $start_column_num)->getFont()->setBold(true);

					//center column names
					$worksheetGC->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('K' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('L' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('M' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('N' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('O' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('P' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$worksheetGC->getStyle('Q' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);					
					
					//set column names
					$worksheetGC->setCellValue('A1', "Credited GC from {$start_date} to {$end_date}");
					$worksheetGC->setCellValue('A' . $start_column_num, 'MEMBER ID');
					$worksheetGC->setCellValue('B' . $start_column_num, 'LAST NAME');
					$worksheetGC->setCellValue('C' . $start_column_num, 'FIRST NAME');
					$worksheetGC->setCellValue('D' . $start_column_num, 'MIDDLE NAME');
					$worksheetGC->setCellValue('E' . $start_column_num, 'IBSP');
					$worksheetGC->setCellValue('F' . $start_column_num, 'IRS GC');
					$worksheetGC->setCellValue('G' . $start_column_num, 'IVP GC');
					$worksheetGC->setCellValue('H' . $start_column_num, 'TOTAL GC');
					$worksheetGC->setCellValue('I' . $start_column_num, 'NEGATIVE GC');
					$worksheetGC->setCellValue('J' . $start_column_num, 'BALANCE FOR UPLOAD');
					$worksheetGC->setCellValue('K' . $start_column_num, 'GC BEFORE');
					$worksheetGC->setCellValue('L' . $start_column_num, 'GC AFTER');
					$worksheetGC->setCellValue('M' . $start_column_num, 'STATUS');
					$worksheetGC->setCellValue('N' . $start_column_num, 'START DATE');
					$worksheetGC->setCellValue('O' . $start_column_num, 'END DATE');			
					$worksheetGC->setCellValue('P' . $start_column_num, 'ON HOLD');
					$worksheetGC->setCellValue('Q' . $start_column_num, 'REMARKS');

					$where = "DATE(start_date) = DATE('{$start_date}') AND DATE(end_date) = DATE('{$end_date}') AND crediting_id = {$crediting_id}";			
					$member_gc_count = $this->payout_model->get_member_gc_commissions_count($where);

					$gc_in_backup = 0;

					if ($member_gc_count <= 0) {
						$member_gc_count = $this->payout_model->get_member_gc_commissions_backups_count($where);
						$gc_in_backup = 1;
					}

					$row = 4;

					$allowed_rows = 5000;

					for($prow = 0;$prow < ceil($member_gc_count/$allowed_rows)+1;$prow++)
					{					
						$offset_val = $prow * $allowed_rows;	

						if ($gcep_in_backup == 0) {
							//$member_gc = $this->payout_model->get_member_gc_commissions($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'member_id');
							$sql = "SELECT
										a.`member_id`,
										b.`last_name`,
										b.`first_name`,
										b.`middle_name`,
										a.`ibsp_gc`,
										a.`irs_gc`,
										a.`ivp_gc`,
									  a.`total_gc`,
									  a.`gc_variance`,
									  a.`net_gc`,
										a.`gc_before`,
										a.`gc_after`,
										a.`status`,
										a.`start_date`,
										a.`end_date`,	
										a.`is_on_hold`,
										a.`remarks`
									FROM 
										`ph_member_gc_commissions` a
									LEFT JOIN 
										`cm_members` b
									ON 
										a.`member_id` = b.`member_id`
									WHERE 
										{$where}
									ORDER BY 
										a.`member_id`
									LIMIT
										{$allowed_rows}
									OFFSET 
										{$offset_val}";
						} else {
							//$member_gc = $this->payout_model->get_member_gc_commissions_backups($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'member_id');
							$sql = "SELECT
										a.`member_id`,
										b.`last_name`,
										b.`first_name`,
										b.`middle_name`,
										a.`ibsp_gc`,
										a.`irs_gc`,
										a.`ivp_gc`,
									  a.`total_gc`,
									  a.`gc_variance`,
									  a.`net_gc`,
										a.`gc_before`,
										a.`gc_after`,
										a.`status`,
										a.`start_date`,
										a.`end_date`,	
										a.`is_on_hold`,
										a.`remarks`
									FROM 
										`ph_member_gc_commissions_backups` a
									LEFT JOIN 
										`cm_members` b
									ON 
										a.`member_id` = b.`member_id`
									WHERE 
										{$where}
									ORDER BY 
										a.`member_id`
									LIMIT
										{$allowed_rows}
									OFFSET 
										{$offset_val}";
						}

						$query = $this->db->query($sql);
						$member_gc = $query->result();			
						$query->free_result();	

						foreach ($member_gc as $mgc)
						{

							// get member details	
							$pretty_last_name = strtoupper($mgc->last_name);
							$pretty_first_name = strtoupper($mgc->first_name);
							$pretty_middle_name = strtoupper($mgc->middle_name);

							$pretty_ibsp = number_format($mgc->ibsp_gc, 2, '.', ',');	
							$pretty_irs_gc = number_format($mgc->irs_gc, 2, '.', ',');
							$pretty_ivp_gc = number_format($mgc->ivp_gc, 2, '.', ',');
							$pretty_total_gc = number_format($mgc->total_gc, 2, '.', ',');
							$pretty_negative_gc = number_format($mgc->gc_variance, 2, '.', ',');
							$pretty_balance_for_upload = number_format($mgc->net_gc, 2, '.', ',');
							$pretty_gc_before = number_format($mgc->gc_before, 2, '.', ',');
							$pretty_gc_after = number_format($mgc->gc_after, 2, '.', ',');
							
							// align right					
							$worksheetGC->setCellValue('A'. $row, $mgc->member_id);										
							$worksheetGC->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGC->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGC->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGC->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGC->getStyle('I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGC->getStyle('J' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGC->getStyle('K' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$worksheetGC->getStyle('L' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							//$worksheetGC->getStyle('M' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							//$worksheetGC->getStyle('N' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							//$worksheetGC->getStyle('O' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							//$worksheetGC->getStyle('P' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							//$worksheetGC->getStyle('Q' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

							$worksheetGC->setCellValue('A'. $row, $mgc->member_id);
							$worksheetGC->setCellValue('B'. $row, $pretty_last_name);
							$worksheetGC->setCellValue('C'. $row, $pretty_first_name);
							$worksheetGC->setCellValue('D'. $row, $pretty_middle_name);												
							$worksheetGC->setCellValue('E'. $row, $pretty_ibsp);
							$worksheetGC->setCellValue('F'. $row, $pretty_irs_gc);
							$worksheetGC->setCellValue('G'. $row, $pretty_ivp_gc);
							$worksheetGC->setCellValue('H'. $row, $pretty_total_gc);
							$worksheetGC->setCellValue('I'. $row, $pretty_negative_gc);					
							$worksheetGC->setCellValue('J'. $row, $pretty_balance_for_upload);
							$worksheetGC->setCellValue('K'. $row, $pretty_gc_before);
							$worksheetGC->setCellValue('L'. $row, $pretty_gc_after);					
							$worksheetGC->setCellValue('M'. $row, $mgc->status);					
							$worksheetGC->setCellValue('N'. $row, $mgc->start_date);					
							$worksheetGC->setCellValue('O'. $row, $mgc->end_date);					
							$worksheetGC->setCellValue('P'. $row, $mgc->is_on_hold);					
							$worksheetGC->setCellValue('Q'. $row, $mgc->remarks);
							
							// auto resize columns
							$worksheetGC->getColumnDimension('A')->setAutoSize(false);
							$worksheetGC->getColumnDimension('B')->setAutoSize(true);
							$worksheetGC->getColumnDimension('C')->setAutoSize(true);
							$worksheetGC->getColumnDimension('D')->setAutoSize(true);
							$worksheetGC->getColumnDimension('E')->setAutoSize(true);
							$worksheetGC->getColumnDimension('F')->setAutoSize(true);
							$worksheetGC->getColumnDimension('G')->setAutoSize(true);
							$worksheetGC->getColumnDimension('H')->setAutoSize(true);
							$worksheetGC->getColumnDimension('I')->setAutoSize(true);
							$worksheetGC->getColumnDimension('J')->setAutoSize(true);
							$worksheetGC->getColumnDimension('K')->setAutoSize(true);
							$worksheetGC->getColumnDimension('L')->setAutoSize(true);
							$worksheetGC->getColumnDimension('M')->setAutoSize(true);
							$worksheetGC->getColumnDimension('N')->setAutoSize(true);
							$worksheetGC->getColumnDimension('O')->setAutoSize(true);
							$worksheetGC->getColumnDimension('P')->setAutoSize(true);
							$worksheetGC->getColumnDimension('Q')->setAutoSize(true);
							
							$row++;
						}
					}	
				}

			}

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


}
