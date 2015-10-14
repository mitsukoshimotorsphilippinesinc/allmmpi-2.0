<?php
$summary_html = "";
$history_html = "";


$_options = "<option value='ALL'>ALL</option>";

	foreach($member_accounts as $ma){
	    $selected = ($ma->account_id == $account_id) ? "selected='selected'":"";
	    $_options .= "<option value=\"".$ma->account_id."\" ".$selected.">".$ma->account_id."</option>";
	}

	// country list
	$_selected_account = "
						  <div class='control-group' style='float:right;'>
						  <label class='control-label'><strong>Account ID</strong></span></label>
	                      <select name='account_id_val' class='account_id_val'>
	                      {$_options}
	                      </select>
						  </div>";


?>

<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered'>
		<thead>
			<tr>									
				<th style="text-align:center;">Payout Period</th>	
				<th style="text-align:center;">Gross IGPSM</th>	
				<th style="text-align:center;">Gross GC</th>	
				<th style="text-align:center;">Gross Unilevel</th>				
				<th style="text-align:center;">Gross Total</th>
			</tr>
		</thead>
		<tbody id="gc_summary">
		<?php

		
		
		if (empty($payout_period_details)) {
			$summary_html = "<tr><td colspan='8' style='text-align: center;'>No Entries Found</td></tr>";
			
		} else {	
			foreach($payout_period_details as $ppd)
			{
				
				$payout_period = date('Y-m-d', strtotime($ppd->start_date)) ." to ". date('Y-m-d', strtotime($ppd->end_date)); 
				//$payout_period = date('M d, Y h:i a', strtotime($ppd->start_date)) ." - ". date('M d, Y h:i a', strtotime($ppd->end_date)); 
				
				// construct table suffix based on start and end dates			
				$suffix_start_date = date(  "Ymd", strtotime($ppd->start_date));
				$suffix_end_date = date(  "Ymd", strtotime($ppd->end_date));
				
				$suffix_payout_period = $suffix_start_date . "_" . $suffix_end_date;
				
				// check if table exists
				$sql = "SHOW TABLES LIKE '%ph_member_account_commissions_{$suffix_payout_period}%'";
				
				$query = $this->db->query($sql);
				$check_table_result = $query->result();			
				$query->free_result();
				
				$gross_total = 0;
				
				if (empty($check_table_result)) {
					// record not found
					if ($payout_type == "IGPSM") {
						$igpsm_total = "<i>- record not available -</i>";					
					} else {
						$igpsm_total = "<i>- not applicable -</i>";
					}
				} else {					
					if ($payout_type == "IGPSM") {
						// get IGPSM total for the payout period
						$sql = "SELECT 
						 SUM(gross) as amount
						FROM 
						 ph_member_account_commissions_{$suffix_payout_period}
						WHERE 
						 member_id = {$member_id}
						AND 
						 account_status IN ('ACTIVE', 'COMPANY')";
						 
						$query = $this->db->query($sql);
						$ibsp_total_result = $query->result();			
						$query->free_result();
						
						if ((empty($ibsp_total_result)) ||  ($ibsp_total_result[0]->amount == NULL)) {
							$igpsm_total = "0.00";
						} else {
							$gross_total = $gross_total + $ibsp_total_result[0]->amount;
							$igpsm_total = number_format($ibsp_total_result[0]->amount, 2);
						}
					} else {
						$igpsm_total = "0.00";
					}					
				}
				
				// check if table exists
				$sql = "SHOW TABLES LIKE '%ph_member_commissions_{$suffix_payout_period}%'";
				
				$query = $this->db->query($sql);
				$check_table_result = $query->result();			
				$query->free_result();
				
				
				if (empty($check_table_result)) {
					// record not found
					if ($payout_type == "IGPSM") {
						$gc_total = "<i>- record not available -</i>";					
					} else {
						$gc_total = "<i>- not applicable -</i>";
					}		
				} else {	
					if ($payout_type == "IGPSM") {
						// get GC
						$sql = "SELECT 
						 SUM(amount) as amount
						FROM 
						 ph_member_commissions_{$suffix_payout_period}
						WHERE 
						 member_id = {$member_id}
						AND 
						 transaction_code IN (106, 107, 108, 109) ";	 
						 
						$query = $this->db->query($sql);
						$gc_total_result = $query->result();			
						$query->free_result();
						
						if ((empty($gc_total_result)) ||  ($gc_total_result[0]->amount == NULL)) {
							$gc_total = "0.00";
						} else {
							$gross_total = $gross_total + $gc_total_result[0]->amount;	
							$gc_total = number_format($gc_total_result[0]->amount, 2);
						}				
					} else {
						$gc_total = "0.00";
					}
				}
					
				// get UNILEVEL
				// get data directly from tr_member_acct_credit_logs since we need to display it per cut off period
				if ($payout_type == "IGPSM") {
				
					$sql = "SELECT 
					 SUM(total_amount) as amount
					FROM 
					 trh_member_acct_credit_logs_summary
					WHERE 
					 transaction_code = 105
					AND 
					 member_id = {$member_id}					
					AND
					 start_date = DATE('{$ppd->start_date}') AND end_date = DATE('{$ppd->end_date}')";	
					
				} else {
					
					$sql = "SELECT 
						 SUM(amount) as amount
						FROM 
						 ph_member_commissions_{$suffix_payout_period}
						WHERE 
						 transaction_code = 105
						AND
						 member_id = {$member_id}
						AND 
						 account_status_id IN (1, 3)";
				}
								
				$query = $this->db->query($sql);
				$irs_total_result = $query->result();			
				$query->free_result();
				
				if ((empty($irs_total_result)) ||  ($irs_total_result[0]->amount == NULL)) {
					$unilevel_total = "0.00";
				} else {
					$gross_total = $gross_total + $irs_total_result[0]->amount;	
					$unilevel_total = number_format($irs_total_result[0]->amount, 2);
				}
				
				// get total of all grosses
				$gross_total = number_format($gross_total, 2);
				
				$summary_html .= "<tr>																
									<td style='text-align:center;'>{$payout_period}</td>
									<td style='text-align:right;'>{$igpsm_total}</td>
									<td style='text-align:right;'>{$gc_total}</td>
									<td style='text-align:right;'>{$unilevel_total}</td>
									<td style='text-align:right;'>{$gross_total}</td>
									
								</tr>";

			}
		}
		?>
		<?= $summary_html ?>
		</tbody>
	</table>
</div>

