<?php
$summary_html = "";

$_options = "<option value='ALL'>ALL</option>";

	foreach($member_accounts as $ma){
	    $selected = ($ma->account_id == $account_id) ? "selected='selected'":"";
	    $_options .= "<option value=\"".$ma->account_id."\" ".$selected.">".$ma->account_id."</option>";
	}

	// country list
	$_accounts_list = "
						  <div class='control-group' style='float:right;'>
						  <label class='control-label'><strong>Account ID</strong></span></label>
	                      <select name='account_id_val2' id='account_id_val2'>
	                      {$_options}
	                      </select>
						  </div>";
?>

<div><?= $_accounts_list; ?></div>

<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered'>
		<thead>
			<tr>									
				<th style="text-align:center;">Payout Period</th>	
				<th style="text-align:center;">IBSP</th>	
				<th style="text-align:center;">IVP</th>	
				<th style="text-align:center;">IRS</th>				
			</tr>
		</thead>
		<tbody id="gc_summary">
		<?php

		
		
		if (empty($payout_period_details)) {
			$summary_html = "<tr><td colspan='8' style='text-align: center;'>No Entries Found</td></tr>";
			
		} else {	
			foreach($payout_period_details as $ppd)
			{
			
				//$payout_period = $ppd->start_date ." to ". $ppd->end_date; 
				$payout_period = date('Y-m-d', strtotime($ppd->start_date)) ." to ". date('Y-m-d', strtotime($ppd->end_date)); 
				
				// construct table suffix based on start and end dates			
				$suffix_start_date = date(  "Ymd", strtotime($ppd->start_date));
				$suffix_end_date = date(  "Ymd", strtotime($ppd->end_date));
				
				$suffix_payout_period = $suffix_start_date . "_" . $suffix_end_date;
				
				
				// check if table exists
				$sql = "SHOW TABLES LIKE '%ph_member_account_commissions_{$suffix_payout_period}%'";
				
				$query = $this->db->query($sql);
				$check_table_result = $query->result();			
				$query->free_result();
			
				$ibsp_total = "";
			
				if (empty($check_table_result)) {
					// record not found
					if ($payout_type == "IGPSM") {
						$ibsp_total = "<i>- record not available -</i>";					
					} else {
						$ibsp_total = "<i>- not applicable -</i>";
					}
				} else {					
					
					// get IBSP total for the payout period
					$sql = "SELECT 
					 SUM(amount) as amount
					FROM 
					 ph_member_commissions_{$suffix_payout_period}
					WHERE 					 
					 transaction_code = 106
					AND 
					 member_id = {$member_id}
					";	
					 
					if ($account_id != 'ALL') {
						$sql .= " AND account_id = '{$account_id}'";
					}

					$query = $this->db->query($sql);
					$ibsp_total_result = $query->result();			
					$query->free_result();
					
					//var_dump($ibsp_total_result[0]->amount);
					
					if ((empty($ibsp_total_result)) ||  ($ibsp_total_result[0]->amount == NULL)) {
						$ibsp_total = "0.00";
					} else {						
						$ibsp_total = $ibsp_total_result[0]->amount;
					}
					
				}
				
				
				$ivp_total = "";
			
				if (empty($check_table_result)) {
					// record not found
					if ($payout_type == "IGPSM") {
						$ivp_total = "<i>- record not available -</i>";					
					} else {
						$ivp_total = "<i>- not applicable -</i>";
					}
				} else {					
					
					// get IBSP total for the payout period
					$sql = "SELECT 
					 SUM(amount) as amount
					FROM 
					 ph_member_commissions_{$suffix_payout_period}
					WHERE 					 
					 transaction_code = 107
					AND 
					 member_id = {$member_id}
					";	
					 
					if ($account_id != 'ALL') {
						$sql .= " AND account_id = '{$account_id}'";
					} 

					$query = $this->db->query($sql);
					$ivp_total_result = $query->result();			
					$query->free_result();
					
					//var_dump($ibsp_total_result[0]->amount);
					
					if ((empty($ivp_total_result)) ||  ($ivp_total_result[0]->amount == NULL)) {
						$ivp_total = "0.00";
					} else {						
						$ivp_total = $ivp_total_result[0]->amount;
					}
					
				}
				
				$irs_total = "";
			
				if (empty($check_table_result)) {
					// record not found
					if ($payout_type == "IGPSM") {
						$irs_total = "<i>- record not available -</i>";					
					} else {
						$irs_total = "<i>- not applicable -</i>";
					}
				} else {					
					
					// get IBSP total for the payout period
					$sql = "SELECT 
					 SUM(amount) as amount
					FROM 
					 ph_member_commissions_{$suffix_payout_period}
					WHERE 					 
					 transaction_code = 109
					AND 
					 member_id = {$member_id}
					";	
					 
					if ($account_id != 'ALL') {
						$sql .= " AND account_id = '{$account_id}'";
					} 

					$query = $this->db->query($sql);
					$irs_total_result = $query->result();			
					$query->free_result();
					
					//var_dump($ibsp_total_result[0]->amount);
					
					if ((empty($irs_total_result)) ||  ($irs_total_result[0]->amount == NULL)) {
						$irs_total = "0.00";
					} else {						
						$irs_total = $irs_total_result[0]->amount;
					}
					
				}
				
				$summary_html .= "<tr>																
									<td style='text-align:center;'>{$payout_period}</td>
									<td style='text-align:right;'>{$ibsp_total}</td>
									<td style='text-align:right;'>{$ivp_total}</td>
									<td style='text-align:right;'>{$irs_total}</td>
									
								</tr>";
			}
		}
		?>
		<?= $summary_html ?>
		</tbody>
	</table>
</div>
