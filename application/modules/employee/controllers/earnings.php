<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Earnings extends Site_Controller {

	function __construct() {
		// call the base model constructor
		parent::__construct();
		$this->load->library('pager');
		$this->load->model("items_model");
		$this->load->model('members_model');
		$this->load->model('tracking_model');
		$this->load->model('payment_model');
		$this->load->model('payout_model');
		$this->load->model('settings_model');
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
	
	public function index() {		
		$member_id = $this->member->member_id;
		
		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i >= 2007; $i--)
			$_years[$i] = $i;
		
		
		
		// get current payout period
		$sql = "SELECT 
				payout_period_id, start_date, end_date
				FROM 
				 po_payout_periods			
				WHERE 				 
				 status = 'COMPLETED'
				AND 
				 payout_type = 'IGPSM'
				AND 
				 is_official = 1 
				ORDER BY 
				 payout_period_id DESC";

		$query = $this->db->query($sql);
		$datepick_period_result = $query->result();			
		$query->free_result();
		
		$datepick_start_date = $datepick_period_result[0]->start_date;
		$datepick_end_date = $datepick_period_result[0]->end_date;
		
		
		// get current payout period
		$sql = "SELECT 
				payout_period_id, start_date, end_date
				FROM 
				 po_payout_periods			
				WHERE 
				 now() BETWEEN start_date AND end_date
				AND
				 status = 'ACTIVE'
				AND 
				 is_official = 1 
				AND 
				 payout_type = 'IGPSM'
				ORDER BY 
				 payout_period_id DESC";

		$query = $this->db->query($sql);
		$payout_period_result = $query->result();			
		$query->free_result();
		
		$payout_start_date = "";
		$payout_end_date = "";
		$payout_id = "";
		
		if ((empty($payout_period_result)) ||  ($payout_period_result[0]->payout_period_id == NULL)) {
			// set values to N/A
			$gross_igpsm = "n/a";
			$gross_gc = "n/a";
			$gross_unilevel = "n/a";
			$gross_total = "n/a";			
		} else {	
			$payout_id = $payout_period_result[0]->payout_period_id;
			$payout_start_date = $payout_period_result[0]->start_date;
			$payout_end_date = $payout_period_result[0]->end_date;
			
			// get this payout's 
			// IGPSM
			$sql = "SELECT 
					   SUM(amount) as amount
					FROM 
					   tr_member_acct_credit_logs
					WHERE 
					 insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}'
					AND
					 transaction_code IN (100, 101, 102, 103, 104)
					AND 
					   member_id = {$this->member->member_id}
					";

			$query = $this->db->query($sql);
			$gross_igpsm_qresult = $query->result();			
			$query->free_result();
			
			
			if ((empty($gross_igpsm_qresult)) ||  ($gross_igpsm_qresult[0]->amount == NULL)) {
				$gross_igpsm = "0.00";
			} else {	
				//var_dump($gross_igpsm_qresult[0]->amount);
				$gross_igpsm = $gross_igpsm_qresult[0]->amount;
			}
			
			// GC
			$sql = "SELECT 
					   SUM(amount) as amount
					FROM 
					   tr_member_acct_credit_logs
					WHERE 
					   insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}' 
					AND
					 transaction_code IN (106, 107, 108, 109)
					AND 
					   member_id = {$this->member->member_id}
					AND 
                       type = 'GC'";

			$query = $this->db->query($sql);
			$gross_gc_qresult = $query->result();			
			$query->free_result();
			
			
			if ((empty($gross_gc_qresult)) ||  ($gross_gc_qresult[0]->amount == NULL)) {
				$gross_gc = "0.00";
			} else {				
				$gross_gc = $gross_gc_qresult[0]->amount;
			}
			
			// Unilevel
			$sql = "SELECT 
					   SUM(amount) as amount
					FROM 
					   tr_member_acct_credit_logs
					WHERE 
					   insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}'   
					AND
					   transaction_code = 105
					AND 
					   member_id = {$this->member->member_id}
					";

			$query = $this->db->query($sql);
			$gross_unilevel_qresult = $query->result();			
			$query->free_result();
			
			
			if ((empty($gross_unilevel_qresult)) ||  ($gross_unilevel_qresult[0]->amount == NULL)) {
				$gross_unilevel = "0.00";
			} else {				
				$gross_unilevel = $gross_unilevel_qresult[0]->amount;		
			}
			
			// TOTAL
			$gross_total = $gross_igpsm + $gross_gc + $gross_unilevel;
			$gross_igpsm = number_format($gross_igpsm, 2);
			$gross_gc = number_format($gross_gc, 2);
			$gross_unilevel = number_format($gross_unilevel, 2);
			$gross_total = number_format($gross_total, 2);
			
		}		
		
		$this->template->member_id = $member_id;
		$this->template->days = $_days;
		$this->template->months = $this->_months;
		$this->template->years = $_years;
		$this->template->member = $this->members_model->get_member_by_id($member_id);
		$this->template->current_page = 'earnings';
		$this->template->gross_igpsm = $gross_igpsm;
		$this->template->gross_gc = $gross_gc;
		$this->template->gross_unilevel = $gross_unilevel;
		$this->template->gross_total = $gross_total;
		$this->template->payout_start_date = $payout_start_date;
		$this->template->payout_end_date = $payout_end_date;
		$this->template->datepick_start_date = $datepick_start_date;
		$this->template->datepick_end_date = $datepick_end_date;
		$this->template->view('earnings');
	}
	
	public function get_member_earnings()
	{
		$member_id = $this->member->member_id;

		$earnings = $this->members_model->get_all_member_earnings($member_id);
		
		// 20130320 get past unilevel total gross
		// get last unilevel payout schedule
		$where = "payout_type = 'UNILEVEL' and status = 'COMPLETED' AND is_official = 1";
		$unilevel_periods = $this->payout_model->get_payout_periods($where,'','start_date DESC');

		if (empty($unilevel_periods)) {
			// default message when no record was found
			$previous_unilevel_value = "<i>- record not available -</i>";
			
		} else {
			
			// construct table suffix based on start and end dates			
			$suffix_start_date = date(  "Ymd", strtotime($unilevel_periods[0]->start_date));
			$suffix_end_date = date(  "Ymd", strtotime($unilevel_periods[0]->end_date));
			
			$unilevel_period_suffix = $suffix_start_date . "_" . $suffix_end_date;
			
			$sql = "SELECT 
					 SUM(amount) as amount
					FROM 
					 ph_member_commissions_{$unilevel_period_suffix}
					WHERE 
					 member_id = {$this->member->member_id}
					AND
					 transaction_code = 105
					AND 
					 account_status_id <> 2";
			
			$query = $this->db->query($sql);
			$gross_past_unilevel_qresult = $query->result();			
			$query->free_result();
		
			$previous_unilevel_value = number_format($gross_past_unilevel_qresult[0]->amount, 2);
		
		}
	
		$igpsm_html = "";
		$unilevel_html = "";
		$sms_notifications_count_html = "";

		//$igpsm_html .= "
		//	<tr>
		//		<td>Referral Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->referral_bonus,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Starter Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_sp + $earnings['all_earnings']->gift_cheque_sp,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Value Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_vp + $earnings['all_earnings']->gift_cheque_vp,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Trial Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_tp + $earnings['all_earnings']->gift_cheque_tp,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Repeat Sales Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_rs + $earnings['all_earnings']->gift_cheque_rs,2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Witholding Tax</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['witholding_tax'],2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Net Cash Commissions Generated</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['cash_earnings'],2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Net GC Commissions Generated</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['gc_commissions'],2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Total Commissions Generated</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['igpsm_earnings'],2)."</td>		
		//	</tr>";

		// get all card types to display on dashboard
		$where = "display_on_dashboard = 1";
		$card_type_details = $this->cards_model->get_card_types($where, '', 'card_type_id');
		
		$igpsm_html .= "
			<tr>
				<td>Referral Bonus</td>
				<td style='text-align:right;'>".number_format($earnings['all_earnings']->referral_bonus,2)."</td>
			</tr>";
		
		$earnings_tag = "";
		$merge_earnings = 0;
		foreach ($card_type_details as $ctd) {
		
			if (($ctd->code == 'ERHM') || ($ctd->code == 'UP1') || ($ctd->code == 'P2P')) {
				if($this->settings->switch_to_erhm == '0') 
					continue;
			} 
			
			$earnings_tag = "pairing_and_gc_" . strtolower($ctd->code);
		
			if (($ctd->code == 'SP') || ($ctd->code == 'ERHM')) {
				$merge_earnings = $merge_earnings + ($earnings[$earnings_tag]->pairing_bonus + $earnings[$earnings_tag]->gift_cheque);
			}
			
			if ($ctd->code <> 'ERHM') {
			
				$igpsm_html .= "
				<tr>
					<td>{$ctd->name} Pairing Bonus</td>
					<td style='text-align:right;'>".number_format($earnings[$earnings_tag]->pairing_bonus + $earnings[$earnings_tag]->gift_cheque,2)."</td>
				</tr>";
			}

		}
		
		$igpsm_html .= "
				<tr>
					<td>ERHM Pairing Bonus</td>
					<td style='text-align:right;'>".number_format($merge_earnings,2)."</td>
				</tr>";
		
		//$erhm_html = "";
		//if($this->settings->switch_to_erhm == '1')
		//{
		//	$erhm_html .= "
		//	<tr> 
		//		<td>ERHM Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_erhm']->pairing_bonus + $earnings['pairing_and_gc_erhm']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>UP1 Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_up1']->pairing_bonus + $earnings['pairing_and_gc_up1']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>P2P Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_p2p']->pairing_bonus + $earnings['pairing_and_gc_p2p']->gift_cheque,2)."</td>
		//	</tr>
		//	";
		//}
		
		//$igpsm_html .= "
		//	<tr>
		//		<td>Referral Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->referral_bonus,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Starter Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_sp']->pairing_bonus + $earnings['pairing_and_gc_sp']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Value Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_vp']->pairing_bonus + $earnings['pairing_and_gc_vp']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Trial Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_tp']->pairing_bonus + $earnings['pairing_and_gc_tp']->gift_cheque,2)."</td>
		//	</tr>
		//	</tr>
		//		{$erhm_html}
		//	<tr>
		//	<tr>
		//		<td>Repeat Sales Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_rs']->pairing_bonus + $earnings['pairing_and_gc_rs']->gift_cheque,2)."</td>
		//	</tr>";
			
		$igpsm_html .= "
			<tr>
				<td><strong>Witholding Tax</strong></td>
				<td style='text-align:right;'>".number_format($earnings['witholding_tax'],2)."</td>
			</tr>
			<tr>
				<td><strong>Net Cash Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['cash_earnings'],2)."</td>
			</tr>
			<tr>
				<td><strong>Net GC Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['gc_commissions'],2)."</td>
			</tr>
			<tr>
				<td><strong>Total Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['igpsm_earnings'],2)."</td>
			</tr>";

		$unilevel_html .= "
			<tr>
				<td>Repeat Sales Unilevel Commission</td>
				<td style='text-align:right;'>".number_format($earnings['all_earnings']->unilevel_commission,2)."</td>
			</tr>
			<tr><td>Witholding Tax</td>
				<td style='text-align:right;'>".number_format($earnings['unilevel_witholding_tax'],2)."</td>
			</tr>
			<tr>
				<td><strong>Net Unilevel Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['unilevel_earnings'],2)."</td>
			</tr>
			
			<tr>
				<td>Previous Unilevel Commission Payout</td>
				<td style='text-align:right;'>".$previous_unilevel_value."</td>
			</tr>";
		
		
		//// sms notifications count
		//$where = "display_on_dashboard = 1";
		//$card_type_details = $this->cards_model->get_card_types($where, '', 'card_type_id');
		
		
		// get current payout period
		$sql = "SELECT 
				payout_period_id, start_date, end_date
				FROM 
				 po_payout_periods			
				WHERE 
				 now() BETWEEN start_date AND end_date
				AND
				 status = 'ACTIVE'
				AND 
				 is_official = 1 
				AND 
				 payout_type = 'IGPSM'
				ORDER BY 
				 payout_period_id DESC";

		$query = $this->db->query($sql);
		$payout_period_result = $query->result();			
		$query->free_result();
		
		$payout_start_date = "";
		$payout_end_date = "";
		$payout_id = "";
		
		if ((empty($payout_period_result)) ||  ($payout_period_result[0]->payout_period_id == NULL)) {
			// get last po_payout periods last IGPSM entry
			$sql = "SELECT 
					payout_period_id, start_date, end_date
					FROM 
					 po_payout_periods			
					WHERE 
					 status = 'COMPLETED'
					AND 
					 is_official = 1 
					AND 
					 payout_type = 'IGPSM'
					ORDER BY 
					 payout_period_id DESC";

			$query = $this->db->query($sql);
			$last_igpsm_payout_period_result = $query->result();			
			$query->free_result();
			
			$payout_id = $last_igpsm_payout_period_result[0]->payout_period_id;
			$payout_start_date = $last_igpsm_payout_period_result[0]->start_date;
			$payout_end_date = $last_igpsm_payout_period_result[0]->end_date;
		} else {	
			$payout_id = $payout_period_result[0]->payout_period_id;
			$payout_start_date = $payout_period_result[0]->start_date;
			$payout_end_date = $payout_period_result[0]->end_date;
		}
		
		foreach ($card_type_details as $ctd) {
			
			$sms_referral_cutoff = 0;
			$sms_pairing_cutoff = 0;
			$sms_referral_total = 0;
			$sms_pairing_total = 0;
			
			//// PAIRING CUTOFF
			//if (($ctd->code == 'SP') || ($ctd->code == 'ERHM') || ($ctd->code == 'UP1') || ($ctd->code == 'P2P')) {
			//	$in_transaction_codes = "'101','106'";
			//} else if ($ctd->code == 'VP') {
			//	$in_transaction_codes = "'102','107'";
			//} else if ($ctd->code == 'RS') {
			//	$in_transaction_codes = "'104','109'";
			//} else if ($ctd->code == 'TP') {
			//	$in_transaction_codes = "'103','108'";
			//} else {	
			//	$in_transaction_codes = "0";
			//}
			//
			//// get total sms for this cut-off
			//$sql = "SELECT 
			//		 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_pairing_cutoff
			//		FROM 
			//		 tr_member_acct_credit_logs
			//		WHERE 
			//		 insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}'
			//		AND
			//		 sms_sent = 1	
			//		AND
			//		 transaction_code IN ({$in_transaction_codes})
			//		AND 
			//		 member_id = {$this->member->member_id}
			//		";
			
			if ($ctd->code == 'ERHM') {
				$sql = "SELECT 
					 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_pairing_cutoff
					FROM 
					 tr_member_acct_credit_logs
					WHERE 
					 insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}'
					AND
					 sms_sent = 1	
					AND
					 (LOWER(remarks) LIKE '%erhm pairing bonus%'
					OR
					 LOWER(remarks) LIKE '%cd-erhm pairing bonus%')
					AND 
					 member_id = {$this->member->member_id}
					";

			} else {
			
				$sql = "SELECT 
					 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_pairing_cutoff
					FROM 
					 tr_member_acct_credit_logs
					WHERE 
					 insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}'
					AND
					 sms_sent = 1	
					AND
					 LOWER(remarks) LIKE LOWER('%{$ctd->code} pairing bonus%')
					AND 
					 member_id = {$this->member->member_id}
					";
            }
			
			$query = $this->db->query($sql);
			$sms_pairing_cutoff_result = $query->result();			
			$query->free_result();
			
			$sms_pairing_cutoff = $sms_pairing_cutoff_result[0]->sms_pairing_cutoff;
			
			// REFERRAL CUTOFF			
			$code_remarks = "{$ctd->code} referral bonus";
			
			if ($ctd->code == 'ERHM') {
								
				$sql = "SELECT 
						 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_referral_cutoff
						FROM 
						 tr_member_acct_credit_logs
						WHERE 
						 insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}'
						AND
						 sms_sent = 1
						AND
						 transaction_code = 100
						AND
						 (LOWER(remarks) LIKE '%erhm referral bonus%'
						OR
						 LOWER(remarks) LIKE '%cd-erhm referral bonus%')
						AND 
						 member_id = {$this->member->member_id}
						";
			
			} else {
			
				// get total sms for this cut-off
				$sql = "SELECT 
						 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_referral_cutoff
						FROM 
						 tr_member_acct_credit_logs
						WHERE 
						 insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}'
						AND
						 sms_sent = 1
						AND
						 transaction_code = 100
						AND
						 LOWER(remarks) LIKE '%{$code_remarks}%'
						AND 
						 member_id = {$this->member->member_id}
						";
            }
			
			$query = $this->db->query($sql);
			$sms_referral_cutoff_result = $query->result();			
			$query->free_result();
			
			$sms_referral_cutoff = $sms_referral_cutoff_result[0]->sms_referral_cutoff;
			
			// TOTAL PAIRING
			//if (($ctd->code == 'ERHM') || ($ctd->code == 'CD-ERHM')) {
			//	// get card codes of ERHM and CD-ERHM
			//	
			//	$erhm_details = $this->cards_model->get_card_type_by_code("ERHM");
			//	$cd_erhm_details = $this->cards_model->get_card_type_by_code("CD-ERHM");
			//	
			//	$card_condition = "IN ({$erhm_details->card_type_id}, {$cd_erhm_details->card_type_id})";
			//} else {
			//	$card_condition = "IN ({$ctd->card_type_id})";
			//}
			//
			//$sql = "SELECT 
			//		 CASE WHEN sum(sms_pairing_total) IS NULL THEN 0 ELSE sum(sms_pairing_total) END as sms_pairing_total,
			//		 CASE WHEN sum(sms_referral_total) IS NULL THEN 0 ELSE sum(sms_referral_total) END as sms_referral_total
			//		FROM 
			//		 cm_member_earnings_per_type
			//		WHERE 
			//		 member_id = {$this->member->member_id}
			//		AND
			//		 type_id {$card_condition}
			//		";
            //
			//$query = $this->db->query($sql);
			//$sms_pairing_total_result = $query->result();			
			//$query->free_result();
		    //
			//$sms_pairing_total = $sms_pairing_total_result[0]->sms_pairing_total;
			//$sms_referral_total = $sms_pairing_total_result[0]->sms_referral_total;
		
			if ($ctd->code == 'ERHM') {
				
				$sql = "SELECT 
					 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_pairing_total
					FROM 
					 tr_member_acct_credit_logs
					WHERE 
					 insert_timestamp <= now()
					AND
					 sms_sent = 1	
					AND
					 (LOWER(remarks) LIKE '%erhm pairing bonus%'
					OR
					 LOWER(remarks) LIKE '%cd-erhm pairing bonus%')
					AND 
					 member_id = {$this->member->member_id}
					";

			} else {
		
				$sql = "SELECT 
					 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_pairing_total
					FROM 
					 tr_member_acct_credit_logs
					WHERE 
					 insert_timestamp <= now()
					AND
					 sms_sent = 1	
					AND
					 LOWER(remarks) LIKE '%{$ctd->code} pairing bonus%'
					AND 
					 member_id = {$this->member->member_id}
					";
            }
			
			$query = $this->db->query($sql);
			$sms_pairing_total_result = $query->result();			
			$query->free_result();
			
			$sms_pairing_total = $sms_pairing_total_result[0]->sms_pairing_total;
			
			// REFERRAL CUTOFF			
			$code_remarks = "{$ctd->code} referral bonus";
			
			if ($ctd->code == 'ERHM') {
								
				$sql = "SELECT 
						 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_referral_total
						FROM 
						 tr_member_acct_credit_logs
						WHERE 
						 insert_timestamp <= now()
						AND
						 sms_sent = 1
						AND
						 transaction_code = 100
						AND
						 (LOWER(remarks) LIKE '%erhm referral bonus%'
						OR
						 LOWER(remarks) LIKE '%cd-erhm referral bonus%')
						AND 
						 member_id = {$this->member->member_id}
						";

			} else {
			
				// get total sms for this cut-off
				$sql = "SELECT 
						 CASE WHEN count(*) IS NULL THEN 0 ELSE count(*) END as sms_referral_total
						FROM 
						 tr_member_acct_credit_logs
						WHERE 
						 insert_timestamp <= now()
						AND
						 sms_sent = 1
						AND
						 transaction_code = 100
						AND
						 LOWER(remarks) LIKE '%{$code_remarks}%'
						AND 
						 member_id = {$this->member->member_id}
						";
            }
			
			$query = $this->db->query($sql);
			$sms_referral_total_result = $query->result();			
			$query->free_result();
			
			$sms_referral_total = $sms_referral_total_result[0]->sms_referral_total;
		
			$sms_notifications_count_html .= "<tr>
				<td>" . $ctd->code . "</td>
				<td style='text-align:right;'>".$sms_referral_cutoff ."</td>
				<td style='text-align:right;'>".$sms_pairing_cutoff."</td>
				<td style='text-align:right;'>".$sms_referral_total."</td>
				<td style='text-align:right;'>".$sms_pairing_total."</td>
			</tr>";
		
		}
		
		$this->return_json("ok","Get Member Earnings Success!!!",
				array(
					"igpsm_earnings" => $igpsm_html,
					"unilevel_earnings" => $unilevel_html,
					"weekly_igpsm" => number_format($earnings['weekly_igpsm_amount'],2),
					"monthly_unilevel" => number_format($earnings['monthly_unilevel_amount'],2),
					"monthly_igpsm" => number_format($earnings['monthly_igpsm_amount'],2),
					"sms_notifications_count" => $sms_notifications_count_html,
				)
			);
		return;
	}
	
	
	public function get_account_earnings()
	{
		$account_id = $this->input->post("account_id");

		$account = $this->members_model->get_member_account_by_account_id($account_id);

		if(empty($account) || is_null($account))
		{
			$this->return_json("error","This account does not exist!",array("html" => ""));
			return;
		}

		$member_id = $this->member->member_id;

		$earnings = $this->members_model->get_all_member_earnings($member_id, $account_id);
		
		$igpsm_html = "";
		$unilevel_html = "";
		$erhm_html = "";

		//$igpsm_html .= "
		//	<tr>
		//		<td>Referral Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->referral_bonus,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Starter Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_sp + $earnings['all_earnings']->gift_cheque_sp,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Value Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_vp + $earnings['all_earnings']->gift_cheque_vp,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Trial Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_tp + $earnings['all_earnings']->gift_cheque_tp,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Repeat Sales Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->pairing_bonus_rs + $earnings['all_earnings']->gift_cheque_rs,2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Witholding Tax</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['witholding_tax'],2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Net Cash Commissions Generated</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['cash_earnings'],2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Net GC Commissions Generated</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['gc_commissions'],2)."</td>
		//	</tr>
		//	<tr>
		//		<td><strong>Total Commissions Generated</strong></td>
		//		<td style='text-align:right;'>".number_format($earnings['igpsm_earnings'],2)."</td>
		//	</tr>";

		// get all card types to display on dashboard
		$where = "display_on_dashboard = 1";
		$card_type_details = $this->cards_model->get_card_types($where, '', 'card_type_id');
		
		$igpsm_html .= "
			<tr>
				<td>Referral Bonus</td>
				<td style='text-align:right;'>".number_format($earnings['all_earnings']->referral_bonus,2)."</td>
			</tr>";
		
		$earnings_tag = "";
		$merge_earnings = 0;
		foreach ($card_type_details as $ctd) {
		
			if (($ctd->code == 'ERHM') || ($ctd->code == 'UP1') || ($ctd->code == 'P2P')) {
				if($this->settings->switch_to_erhm == '0') 
					continue;
			} 
			
			$earnings_tag = "pairing_and_gc_" . strtolower($ctd->code);
			
			
			if (($ctd->code == 'SP') || ($ctd->code == 'ERHM')) {
				$merge_earnings = $merge_earnings + ($earnings[$earnings_tag]->pairing_bonus + $earnings[$earnings_tag]->gift_cheque);
			}
			
			if ($ctd->code <> 'ERHM') {
					
				$card_type_name = $ctd->name;
				
				if ($ctd->code == 'P2P')
					$card_type_name = "(P-P)<sup>3</sup>";
				
				$igpsm_html .= "
				<tr>
					<td>{$card_type_name} Pairing Bonus</td>
					<td style='text-align:right;'>".number_format($earnings[$earnings_tag]->pairing_bonus + $earnings[$earnings_tag]->gift_cheque,2)."</td>
				</tr>";
			}	

		}
		
		$igpsm_html .= "
				<tr>
					<td>ERHM Pairing Bonus</td>
					<td style='text-align:right;'>".number_format($merge_earnings,2)."</td>
				</tr>";
		
		
		//if($this->settings->switch_to_erhm == '1')
		//{
		//	$erhm_html .= "
		//	<tr> 
		//		<td>ERHM Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_erhm']->pairing_bonus + $earnings['pairing_and_gc_erhm']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>UP1 Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_up1']->pairing_bonus + $earnings['pairing_and_gc_up1']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr style='display:none;'>
		//		<td>P2P Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_p2p']->pairing_bonus + $earnings['pairing_and_gc_p2p']->gift_cheque,2)."</td>
		//	</tr>
		//	";
		//}

		//$igpsm_html .= "
		//	<tr>
		//		<td>Referral Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['all_earnings']->referral_bonus,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Starter Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_sp']->pairing_bonus + $earnings['pairing_and_gc_sp']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Value Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_vp']->pairing_bonus + $earnings['pairing_and_gc_vp']->gift_cheque,2)."</td>
		//	</tr>
		//	<tr>
		//		<td>Trial Pack Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_tp']->pairing_bonus + $earnings['pairing_and_gc_tp']->gift_cheque,2)."</td>
		//	</tr>
		//	$erhm_html
		//	<tr>
		//		<td>Repeat Sales Pairing Bonus</td>
		//		<td style='text-align:right;'>".number_format($earnings['pairing_and_gc_rs']->pairing_bonus + $earnings['pairing_and_gc_rs']->gift_cheque,2)."</td>
		//	</tr>";
			
		$igpsm_html .= "
			<tr>
				<td><strong>Witholding Tax</strong></td>
				<td style='text-align:right;'>".number_format($earnings['witholding_tax'],2)."</td>
			</tr>
			<tr>
				<td><strong>Net Cash Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['cash_earnings'],2)."</td>
			</tr>
			<tr>
				<td><strong>Net GC Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['gc_commissions'],2)."</td>
			</tr>
			<tr>
				<td><strong>Total Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['igpsm_earnings'],2)."</td>
			</tr>";
		
		$unilevel_html .= "
			<tr>
				<td>Repeat Sales Unilevel Commission</td>
				<td style='text-align:right;'>".number_format($earnings['all_earnings']->unilevel_commission,2)."</td>
			</tr>
			<tr><td>Witholding Tax</td>
				<td style='text-align:right;'>".number_format($earnings['unilevel_witholding_tax'],2)."</td>
			</tr>
			<tr>
				<td><strong>Net Unilevel Commissions Generated</strong></td>
				<td style='text-align:right;'>".number_format($earnings['unilevel_earnings'],2)."</td>
			</tr>";

		$this->return_json("ok","Get Member Earnings Success!!!",
				array(
					"igpsm_earnings" => $igpsm_html,
					"unilevel_earnings" => $unilevel_html,
					"weekly_igpsm" => number_format($earnings['weekly_igpsm_amount'],2),
					"monthly_unilevel" => number_format($earnings['monthly_unilevel_amount'],2),
					"monthly_igpsm" => number_format($earnings['monthly_igpsm_amount'],2)
				)
			);
		return;
	}
	
	public function get_member_history()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
		
		$member_id = $this->member->member_id;
		
		$where = "member_id = {$this->member->member_id} AND payout_type = '{$payout_type}' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";
		
		$all_logs = $this->members_model->get_member_account_commissions($where, array('rows' => 100, 'offset' => 0),"insert_timestamp DESC");
		
		if(count($all_logs) == 0){
			$this->return_json("error","No Entries Found!",array("html" => '<tr><td colspan="9" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}
		
		$history_html = "";
		
		foreach($all_logs as $al)
		{
			$history_html .= "<tr>								
								<td style='text-align:right;'>{$al->account_id}</td>
								<td style='text-align:right;'>".number_format($al->gross,2)."</td>
								<td style='text-align:right;'>{$al->witholding_tax}</td>
								<td style='text-align:right;'>".number_format($al->net_of_tax,2)."</td>
								<td style='text-align:right;'>".number_format($al->balance,2)."</td>
								<td style='text-align:right;'>".number_format($al->total,2)."</td>								
								<td style='text-align:right;'>{$al->cash_card}</td>  
								<td>{$al->commission_status}</td>										
							</tr>";
		}
		
		$this->return_json("ok","",array("html" => $history_html));
		return;
	}
	
	
	//public function get_funds_to_paycard_history()
	//{
	//	$member_id = $this->member->member_id;	
	//	//$member_id = 9988;
	//	
	//	$where = "member_id = {$member_id} AND status = 'PROCESSED'";		
	//	$all_logs = $this->payout_model->get_funds_to_paycard($where, array('rows' => 250, 'offset' => 0),"start_date DESC");
	//
	//	if(count($all_logs) == 0){
	//		$this->return_json("error","No Entries Found!",array("html" => '<tr><td colspan="10" style="text-align: center;">No Entries Found</td></tr>'));
	//		return;
	//	}
	//	
	//	$history_html = "";
	//	
	//	foreach($all_logs as $al)
	//	{
	//		
	//		if ($al->status == 'PENDING') {
	//			$status = 'PROCESSING';
	//		} else {
	//			$status = $al->status;
	//		}
	//		
	//		$payout_period = $al->start_date ." to ". $al->end_date; 
	//		$history_html .= "<tr>																
	//							<td style='text-align:right;'>".number_format($al->gross,2)."</td>
	//							<td style='text-align:right;'>".number_format($al->gcep,2)."</td>
	//							<td style='text-align:right;'>".number_format($al->net_of_gcep,2)."</td>
	//							<td style='text-align:right;'>".number_format($al->wtax,2)."</td>
	//							<td style='text-align:right;'>".number_format($al->net_of_wtax,2)."</td>
	//							<td style='text-align:right;'>".number_format($al->deduction1,2)."</td>
	//							<td style='text-align:right;'>".number_format($al->deduction2,2)."</td>
	//							<td style='text-align:right;'>".number_format($al->final_commission,2)."</td>							
	//							<td>{$payout_period}</td>								
	//						</tr>";
	//	}
	//	
	//	$this->return_json("ok","",array("html" => $history_html));
	//	return;
	//}
	
	
	
	
	public function get_epm_history()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
		
		$member_id = $this->member->member_id;
		
		$where = "member_id = {$this->member->member_id} AND payout_type = '{$payout_type}' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";
		
		
		$all_logs = $this->members_model->get_member_payouts($where, array('rows' => 100, 'offset' => 0),"insert_timestamp DESC");
		
		if(count($all_logs) == 0){
			$this->return_json("error","No Entries Found!",array("html" => '<tr><td colspan="8" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}
		
		$history_html = "";
		
		foreach($all_logs as $al)
		{
			
			if ($al->status == 'PENDING') {
				$status = 'PROCESSING';
			} else {
				$status = $al->status;
			}
			
			$payout_period = $al->start_date ." to ". $al->end_date; 
			$history_html .= "<tr>																
								<td style='text-align:right;'>".number_format($al->gross,2)."</td>
								<td style='text-align:right;'>".number_format($al->gcep,2)."</td>
								<td style='text-align:right;'>{$al->witholding_tax}</td>
								<td style='text-align:right;'>".number_format($al->balance,2)."</td>
								<td style='text-align:right;'>".number_format($al->total_amount,2)."</td>								
								<td style='text-align:right;'>{$al->cash_card}</td> 								
								<td>{$payout_period}</td>								
							</tr>";
		}
		
		$this->return_json("ok","",array("html" => $history_html));
		return;
	}
	
	
	
	public function get_account_history()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$account_id = $this->input->post('account_id');
		$type = $this->input->post('type');
		
		$member_id = $this->member->member_id;

		$account = $this->members_model->get_member_account_by_account_id($account_id);

		if(empty($account) || is_null($account))
		{
			$this->return_json("error","This account does not exist!",array("html" => '<tr><td colspan="11" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}

		if($account->member_id != $member_id)
		{
			$this->return_json("error","You are not the owner if this account.",array("html" => '<tr><td colspan="11" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}
		
		
		//$proper_start_date = date("Y-m-d H:i:s", strtotime($start_date));
		//$proper_end_date = date("Y-m-d H:i:s", strtotime($end_date));
        
        $proper_start_date = date("Y-m-d", strtotime($start_date));
		$proper_end_date = date("Y-m-d", strtotime($end_date));
		
		$data = "account_id = '{$account_id}' AND member_id = '{$member_id}' AND DATE(insert_timestamp) >= '{$proper_start_date}' and DATE(insert_timestamp) <= '{$proper_end_date}'";
		
		//if (($type=="SP") || ($type=="ERHM")) {		
		//	$data .= " AND transaction_code IN (101, 106)";
		//} else if ($type=="VP") {		
		//	$data .= " AND transaction_code IN (102, 107)";			
		//} else if ($type=="RS") {		
		//	$data .= " AND transaction_code IN (104, 109)";			
		//} else if ($type=="GC") {		
		//	$data .= " AND transaction_code IN (106, 107, 108, 109)";
		//} else if ($type=="UNILEVEL") {		
		//	$data .= " AND transaction_code IN (105)";			
		//} else {
		//	// IGPSM
		//	$data .= " AND transaction_code <> 105";
		//	
		//}		
	
		if ($type == "GC") {	
			$data .= " AND transaction_code IN (106, 107, 108, 109)";
		} else if ($type == "UNILEVEL") {		
			$data .= " AND transaction_code IN (105)";			
		} else if ($type == "IGPSM") {	
			// IGPSM
			//$data .= " AND transaction_code <> 105";
			$data .= " AND transaction_code NOT IN (105, 0)";
		} else if ($type == "ALL") {	
			// IGPSM
			$data .= "";	
		} else {
			$data .= " AND remarks LIKE '%{$type}%'";
		}
		
		//var_dump($type . "|". $data);
				
		$credit_logs = $this->tracking_model->get_acct_credit_logs($data,array('rows' => 500, 'offset' => 0),"credit_log_id DESC, insert_timestamp DESC");
	
			
		if(!is_array($credit_logs))
		{
			$credit_logs = array();
		}
		
		foreach($credit_logs as $cl)
		{
			$cl->transaction = "Credit";
		}
		
		$all_logs = $credit_logs;
		
		if(empty($all_logs) || is_null($all_logs))
		{
			$this->return_json("ok","",array("html" => '<tr><td colspan="9" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}
		$history_html = "";

		foreach($all_logs as $al)
		{
			$amount = "<td>".number_format($al->amount,2)."</td>";
			
			$transaction_code_details = $this->tracking_model->get_transaction_code_by_code($al->transaction_code);
				
			// check if not empty	
			if (!(empty($transaction_code_details))) {
				
				// check if first 3 char of transaction description = 2
				if (strlen(trim(substr($transaction_code_details->transaction_description, 0, 3))) == 2) {				
					$transaction_code = substr($transaction_code_details->transaction_description, 0, 2);
				} else {
					$transaction_code = "";
				}
			}
			
			$remarks = str_replace("P2P", "(P-P)<sup>3</sup>", $al->remarks);
			
			$history_html .= "<tr><td>{$remarks}</td><td>{$al->from_account_id}</td>{$amount}<td>{$al->level}</td><td>{$al->type}</td><td>{$transaction_code}</td><td>".date('F d, Y h:i:s A', strtotime($al->insert_timestamp))."</td></tr>";
		}
		
		$this->return_json("ok","",array("html" => $history_html));
		return;
	}
	
	public function get_funds_history()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$member_id = $this->input->post('member_id');

		$member = $this->members_model->get_member_by_id($member_id);

		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("html" => '<tr><td colspan="9" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}

		$data = array(
			"member_id" => $member_id,
			"type" => "FUNDS",
			"insert_timestamp >=" => $start_date." 00:00:00",
			"insert_timestamp <=" => $end_date." 23:59:59"
		);

		$credit_logs = $this->tracking_model->get_credit_logs($data,array('rows' => 500, 'offset' => 0),"credit_log_id DESC, insert_timestamp DESC");

		$debit_logs = $this->tracking_model->get_debit_logs($data,array('rows' => 500, 'offset' => 0),"insert_timestamp DESC, debit_log_id DESC");

		if(!is_array($credit_logs))
		{
			$credit_logs = array();
		}

		if(!is_array($debit_logs))
		{
			$debit_logs = array();
		}

		foreach($credit_logs as $cl)
		{
			$cl->transaction = "Credit";
		}

		foreach($debit_logs as $dl)
		{
			$dl->transaction = "Debit";
		}

		$all_logs = $credit_logs;

		array_splice($all_logs,count($all_logs),0,$debit_logs);

		$dates = array();

		foreach($all_logs as $key => $all)
		{
			$dates[$key] = strtotime($all->insert_timestamp);
		}

		array_multisort($dates,SORT_DESC,$all_logs);

		if(empty($all_logs) || is_null($all_logs))
		{
			$this->return_json("ok","",array("html" => '<tr><td colspan="11" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}
		$history_html = "";

		foreach($all_logs as $al)
		{
			$amount = "<td>".number_format($al->amount,2)."</td>";
			if(strcmp(strtoupper($al->transaction),"DEBIT") == 0)
			{
				$amount = "<td style='color: red;'>(".number_format($al->amount,2).")</td>";
			}
			
			$history_html .= "<tr><td>{$al->type}</td>{$amount}<td>{$al->remarks}</td><td>".date('F d, Y h:i:s A', strtotime($al->insert_timestamp))."</td></tr>";
		}

		$this->return_json("ok","",array("html" => $history_html));
		return;
	}
	
	public function get_gc_history()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$member_id = $this->input->post('member_id');

		$member = $this->members_model->get_member_by_id($member_id);

		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("html" => '<tr><td colspan="11" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}

		$data = array(
			"member_id" => $member_id,
			"type" => "GC",
			"insert_timestamp >=" => $start_date." 00:00:00",
			"insert_timestamp <=" => $end_date." 23:59:59"
		);

		$credit_logs = $this->tracking_model->get_credit_logs($data,array('rows' => 500, 'offset' => 0),"credit_log_id DESC, insert_timestamp DESC");

		$debit_logs = $this->tracking_model->get_debit_logs($data,array('rows' => 500, 'offset' => 0),"insert_timestamp DESC, debit_log_id DESC");

		if(!is_array($credit_logs))
		{
			$credit_logs = array();
		}

		if(!is_array($debit_logs))
		{
			$debit_logs = array();
		}

		foreach($credit_logs as $cl)
		{
			$cl->transaction = "Credit";
		}

		foreach($debit_logs as $dl)
		{
			$dl->transaction = "Debit";
		}

		$all_logs = $credit_logs;

		array_splice($all_logs,count($all_logs),0,$debit_logs);

		$dates = array();

		foreach($all_logs as $key => $all)
		{
			$dates[$key] = strtotime($all->insert_timestamp);
		}

		array_multisort($dates,SORT_DESC,$all_logs);

		if(empty($all_logs) || is_null($all_logs))
		{
			$this->return_json("ok","",array("html" => '<tr><td colspan="11" style="text-align: center;">No Entries Found</td></tr>'));
			return;
		}
		$history_html = "";

		foreach($all_logs as $al)
		{
			$amount = "<td>".number_format($al->amount,2)."</td>";
			if(strcmp(strtoupper($al->transaction),"DEBIT") == 0)
			{
				$amount = "<td style='color: red;'>(".number_format($al->amount,2).")</td>";
			}
			
			$history_html .= "<tr><td>{$al->type}</td>{$amount}<td>{$al->remarks}</td><td>".date('F d, Y h:i:s A', strtotime($al->insert_timestamp))."</td></tr>";
		}

		$this->return_json("ok","",array("html" => $history_html));
		return;
	}
	
	public function excel_earnings($earnings)
	{
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$start_column_num = 1;
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('Account Earnings');
			
		// auto resize columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
					
		// set column header to bold		
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $start_column_num)->getFont()->setBold(true);
					
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Left PPV (Starter Pack)');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'Right PPV (Starter Pack)');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'Left PPV (Value Pack)'); 
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'Right PPV (Value Pack)');
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'Left PPV (Repeat Sales)');
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'Right PPV (Repeat Sales)');
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'Funds (as of this transaction)');
			
		$row = 3;
		$id = 0;
		$current_item_date = 0;
		foreach ($earnings as $e)
		{
			if(date('F-d-Y', strtotime($e->insert_timestamp)) != date('F-d-Y', strtotime($current_item_date))) //different date
			{
				if ($current_item_date != 0)
					$row++;
				$current_item_date = $e->insert_timestamp;
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, date('l, F d, Y', strtotime($e->insert_timestamp)));
				$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
				$row = $row + 1;
				$id = 0;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $e->left_ppv);
			$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $e->right_ppv);
			$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $e->left_ppv_mf);
			$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $e->right_ppv_mf);
			$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $e->right_ppv_rs);
			$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $e->right_ppv_rs);
			$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $e->funds);
		
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$row++;
		}
		
		$current_year = date('Y');
		$current_month = date('m');
		$current_day = date('d');
		$date = $current_month . $current_day . $current_year;
			
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="account_earnings_member.xls"');
		header('Cache-Control: max-age=0');		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	public function get_member_funds()
	{
		$member_id = $this->input->post('member_id');

		$member = $this->members_model->get_member_by_id($member_id);

		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("funds" => '0.00'));
			return;
		}
		
		$this->return_json("ok","",array("funds" => number_format($member->funds,2)));
		return;
	}
	
	public function get_member_gc()
	{
		$member_id = $this->input->post('member_id');

		$member = $this->members_model->get_member_by_id($member_id);

		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("gc" => '0'));
			return;
		}
		
		$this->return_json("ok","",array("gc" => $member->gift_cheques));
		return;
	}
	
	public function update_points()
	{
		$type = 'Starter Pack';//$this->input->post('type');
		$side = 'Left';//$this->input->post('side');
		//$points = $this->input->post('points');
		
		if($type == 'Starter Pack')
		{
			if($side == 'Left')
				$data = array('left_ppv' => $points);
			elseif($side == 'Right')
				$data = array('right_ppv' => $points);
			$where = array('account_id' => $account_id, 'member_id' => $member_id);
			$this->members_model->update_member_accounts($data, $where);
			
			//call check pairing
			$member = $this->members_model->get_member_accounts($where);
			$left_ppv = $member->left_ppv;
			$right_ppv = $member->right_ppv;
			
			$left_pair = (int)($left_ppv / 700);
			$right_pair = (int)($right_ppv / 700);
			$pair = min($left_pair, $right_pair);
			if ($pair > 0)
			{
				$new_pair = ($member->pair_cnt) + $pair;
				$data = array(
					'left_ppv' => 0,
					'right_ppv' => 0,
					'pair_cnt' => $new_pair
				);
				$this->members_model->update_member_accounts($data, $where);
			}
		}
		elseif($type ==  'Value Pack')
		{
			if($side == 'Left')
				$data = array('left_ppv_mf' => $points);
			elseif($side == 'Right')
				$data = array('right_ppv_mf' => $points);
			$where = array('account_id' => $account_id, 'member_id' => $member_id);
			$this->members_model->update_member_accounts($data, $where);
			
			//call check pairing
			$member = $this->members_model->get_member_accounts($where);
			$left_ppv_mf = $member->left_ppv_mf;
			$right_ppv_mf = $member->right_ppv_mf;
			
			$left_pair = (int)($left_ppv_mf / 1400);
			$right_pair = (int)($right_ppv_mf / 1400);
			$pair = min($left_pair, $right_pair);
			if ($pair > 0)
			{
				$new_pair = ($member->pair_cnt_mf) + $pair;
				$data = array(
					'left_ppv_mf' => 0,
					'right_ppv_mf' => 0,
					'pair_cnt_mf' => $new_pair
				);
				$this->members_model->update_member_accounts($data, $where);
			}
		}
		elseif($type == 'Repeat Sales')
		{
			if($side == 'Left')
				$data = array('left_ppv_rs' => $points);
			elseif($side == 'Right')
				$data = array('right_ppv_rs' => $points);
			$where = array('account_id' => $account_id, 'member_id' => $member_id);
			$this->members_model->update_member_accounts($data, $where);
			
			//call check pairing
			$member = $this->members_model->get_member_accounts($where);
			$left_ppv_rs = $member->left_ppv_rs;
			$right_ppv_rs = $member->right_ppv_rs;
			
			$left_pair = (int)($left_ppv_rs / 700);
			$right_pair = (int)($right_ppv_rs / 700);
			$pair = min($left_pair, $right_pair);
			if ($pair > 0)
			{
				$new_pair = ($member->pair_cnt_rs) + $pair;
				$data = array(
					'left_ppv_mf' => 0,
					'right_ppv_mf' => 0,
					'pair_cnt_mf' => $new_pair
				);
				$this->members_model->update_member_accounts($data, $where);
			}
		}
	}
        
	public function igpsm()
	{
		$this->template->view('dashboard');
	}
	public function uni()
	{
		$this->template->view('dashboard');
	}
		
		
	public function summary(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
		$account_id = $this->input->post('account_id');
		
		//$member_id = $this->member->member_id;		
		$member_id = $this->input->post('member_id');
		
		// get member_accounts
		$member_accounts = $this->members_model->get_member_accounts("member_id = " . $member_id);
		
		// get all payout periods
		$where ="payout_type = '{$payout_type}' AND status = 'COMPLETED' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";
		
		//$where ="status NOT IN ('CANCELLED')";
		$payout_period_details = $this->payout_model->get_payout_periods($where, "", "start_date DESC");
		
		$payout_period_count = $this->payout_model->get_payout_periods_count($where);
		
		$data = array(
			'member_accounts' => $member_accounts,
			'account_id' => $account_id,
			'payout_period_details' => $payout_period_details,
			'payout_type' => $payout_type,
			'member_id' => $member_id
		);
			
        $view = $this->load->view('members/earnings/summary', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }	
	
		
	public function encashments(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
		
		
		//$member_id = $this->member->member_id;	
		$member_id = $this->input->post('member_id');		
		
		$where = "member_id = {$member_id} 
				AND payout_type = '{$payout_type}' 
				AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') AND (end_date BETWEEN '{$start_date}' AND '{$end_date}'))
		 		";		
		
		$all_logs = $this->members_model->get_member_payouts($where, "","start_date DESC");
		
		$data = array(
			'all_logs' => $all_logs,
			'member_id' => $member_id,			
		);
		
        $view = $this->load->view('members/earnings/encashments', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }
	
	
	public function earnings_per_account(){
		// NOTE: This Method is almost similar to summary. May consider merging them.	
		
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
		$account_id = $this->input->post('account_id');
		
		//$member_id = $this->member->member_id;	
		$member_id = $this->input->post('member_id');				
		
		// get member_accounts
		$member_accounts = $this->members_model->get_member_accounts("member_id = " . $member_id);
			
		// get all payout periods
		$where ="payout_type = '{$payout_type}' AND status = 'COMPLETED' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";
		
		//$where ="status NOT IN ('CANCELLED')";
		$payout_period_details = $this->payout_model->get_payout_periods($where, "", "payout_period_id DESC");
		
		$payout_period_count = $this->payout_model->get_payout_periods_count($where);
		
		$data = array(			
			'member_accounts' => $member_accounts,
			'account_id' => $account_id,
			'payout_period_details' => $payout_period_details,	
			'payout_type' => $payout_type,
			'member_id' => $member_id
		);
			
        $view = $this->load->view('members/earnings/earnings_per_account', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }
	
	public function giftcheques(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
		$account_id = $this->input->post('account_id');
		
		//$member_id = $this->member->member_id;		
		$member_id = $this->input->post('member_id');		
		
		// get member_accounts
		$member_accounts = $this->members_model->get_member_accounts("member_id = " . $member_id);
		
		// get all payout periods
		$where ="payout_type = '{$payout_type}' AND status = 'COMPLETED' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";		
		//$where ="status NOT IN ('CANCELLED')";
		$payout_period_details = $this->payout_model->get_payout_periods($where, "", "start_date DESC");
		
		// get gc of current member
		//$where = "member_id = {$member_id} AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') AND (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";		
		//
		//if ((!($account_id == "ALL")) || (empty($account_id)) || ($account_id == NULL)) {
		//	$where .= " AND account_id = {$account_id}";
		//}
		
		$data = array(
			//'all_logs' => $all_logs,
			'member_accounts' => $member_accounts,
			'account_id' => $account_id,
			'payout_period_details' => $payout_period_details,
			'payout_type' => $payout_type,
			'member_id' => $member_id
		);
			
        $view = $this->load->view('members/earnings/giftcheques', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }
	
	public function gcep(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		
		//$member_id = $this->member->member_id;
		$member_id = $this->input->post('member_id');				
		
		// get all affected payout period based on start_date and end_date
		$sql = "SELECT * FROM po_payout_periods where date(start_date) >= '2014-01-10' and date(end_date) <= '2014-05-21' and payout_type = 'IGPSM' and is_official = 1;";

		// 20140520
		// check if has record in ph_member_gcep_commissions
		$lookup_table = 'ph_member_gcep_commissions';

		$where = "member_id = {$member_id} AND total_gcep <> 0 AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";	
		
		$all_logs = $this->payout_model->get_member_gcep_commissions($where, "","start_date DESC");

		if (empty($all_logs)) {
			$where = "member_id = {$member_id} AND gcep <> 0 AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";	
			$all_logs = $this->members_model->get_member_payouts($where, "","transaction_id DESC");
			$lookup_table = 'cm_member_payouts';
		}
		
		$data = array(
			'all_logs' => $all_logs,
			'member_id' => $member_id,
			'lookup_table' => $lookup_table
		);
		
        $view = $this->load->view('members/earnings/gcep', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }
	
	
	public function deductions(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
		$member_id = $this->input->post('member_id');		

		//$where = "member_id = {$this->member->member_id} AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";	
		//$all_logs = $this->payout_model->get_member_deductions($where, "","start_date DESC");
		
		
		/*//  20130825 union all results
		$sql = "(SELECT start_date, end_date, status, type, gross, gcep, net_of_gcep, witholding_tax, net_of_tax, deduction1, deduction2, card_fee, final_commission  AS total_amount, cash_card, remarks 
			FROM po_member_deductions 
			WHERE member_id = {$member_id} AND type = '{$payout_type}' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}')))
		UNION ALL
		(SELECT start_date, end_date, status, payout_type AS type, gross, gcep, net_of_gcep, witholding_tax, net_of_tax, balance AS deduction1, deduction2, card_fee, total_amount, cash_card, remarks 
			FROM cm_member_payouts 
			WHERE member_id = {$member_id} AND (balance + deduction2 + card_fee) > 0 AND payout_type = '{$payout_type}' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}')))
		ORDER BY start_date, end_date DESC";
		*/

		$sql = "(SELECT 
					member_id, start_date, end_date, deduction_type, amount 
				FROM 
					po_member_deductions 
				WHERE
					((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))
				AND 
					amount > 0
				AND
					member_id = {$member_id} 
				AND 
					type = '{$payout_type}'
				)
				UNION ALL
				(
				SELECT 
					member_id, start_date, end_date, 'TOTAL DEDUCTIONS', total_deductions AS amount 
				FROM 
					cm_member_payouts
				WHERE 
					((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))
				AND 
					total_deductions > 0
				AND
					member_id = {$member_id} 
				AND	
					member_id NOT IN 
					(SELECT 
						member_id 
					FROM 
						po_member_deductions 
					WHERE 
						((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}')) 
					AND 
						amount > 0))
				ORDER BY 
					start_date, end_date DESC";

		$query = $this->db->query($sql);
		$all_logs = $query->result();			
		$query->free_result();
		
		$data = array(
			'all_logs' => $all_logs,
			'member_id' => $member_id
		);
		
        $view = $this->load->view('members/earnings/deductions', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }
	
	
	public function adjustments(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$member_id = $this->input->post('member_id');		

		//$where = "member_id = {$member_id} AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";	
		//
		//$all_logs = $this->payout_model->get_member_adjustments($where, "","start_date DESC");
		
		
		//  20140128 union all results
		$sql = "(SELECT start_date, end_date, status, type, gross, gcep, net_of_gcep, witholding_tax, net_of_tax, 0 AS deduction1, 0 AS deduction2, 0 AS card_fee, final_commission  AS total_amount, cash_card, remarks 
			FROM po_member_adjustments 
			WHERE member_id = {$member_id} AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}')))
		UNION ALL
		(SELECT start_date, end_date, status, payout_type AS type, gross, gcep, net_of_gcep, witholding_tax, net_of_tax, balance AS deduction1, deduction2, card_fee, total_amount, cash_card, remarks 
			FROM cm_member_payouts 
			WHERE member_id = {$member_id} AND adjustments > 0 AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}')))
		ORDER BY start_date, end_date DESC";
		
		$query = $this->db->query($sql);
		$all_logs = $query->result();			
		$query->free_result();
		
		
		$data = array(
			'all_logs' => $all_logs,
			'member_id' => $member_id
		);
		
        $view = $this->load->view('members/earnings/adjustments', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }
	
	
	public function funds_to_paycard()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
	
		//$member_id = $this->member->member_id;	
		$member_id = $this->input->post('member_id');		
		
		$where = "type = '{$payout_type}' AND member_id = {$member_id} AND status = 'PROCESSED' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') OR (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";
	
		$all_logs = $this->payout_model->get_funds_to_paycard($where, array('rows' => 500, 'offset' => 0),"start_date DESC");
	
		$data = array(
			'all_logs' => $all_logs,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'payout_type' => $payout_type,
			'member_id' => $member_id
		);
		
        $view = $this->load->view('members/earnings/funds_to_paycard', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
	
	}
	
	
	public function transfers(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		
		//$member_id = $this->member->member_id;
		$member_id = $this->input->post('member_id');		
		
		$where = "(from_member_id = {$member_id} OR to_member_id = {$member_id}) AND DATE(insert_timestamp) BETWEEN '{$start_date}' AND '{$end_date}'";		
		
		$all_logs = $this->tracking_model->get_member_transfers($where, "","insert_timestamp DESC");
		
		$data = array(
			'all_logs' => $all_logs,
			'member_id' => $member_id
		);
		
        $view = $this->load->view('members/earnings/transfers', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }
	
	public function corpo_sharing(){
        $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$payout_type = $this->input->post('payout_type');
				
		//$member_id = $this->member->member_id;
		$member_id = $this->input->post('member_id');				
		
		//$where = "member_id = {$member_id} AND payout_type = '{$payout_type}' AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') AND (end_date BETWEEN '{$start_date}' AND '{$end_date}')) AND is_corpo_sharing = 1";		
		$where = "payout_type = '{$payout_type}' 
				AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') AND (end_date BETWEEN '{$start_date}' AND '{$end_date}')) 
				AND is_corpo_sharing = {$member_id}";		
		
		$all_logs = $this->members_model->get_member_payouts($where, "","start_date DESC");
		
		$data = array(
			'all_logs' => $all_logs,
			'member_id' => $member_id
		);
		
        $view = $this->load->view('members/earnings/corpo_sharing', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view));
        return;
    }	
}
