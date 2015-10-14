<?php

$history_html = "";


$_options = "<option value='ALL'>ALL</option>";

	foreach($member_accounts as $ma){
	    $selected = ($ma->account_id == $account_id) ? "selected='selected'":"";
	    $_options .= "<option value=\"".$ma->account_id."\" ".$selected.">".$ma->account_id."</option>";
	}

	// country list
	$_accounts_list = "
						  <div class='control-group' style='float:right;'>
						  <label class='control-label'><strong>Account ID</strong></span></label>
	                      <select name='account_id_val' id='account_id_val'>
	                      {$_options}
	                      </select>
						  </div>";
?>

<div><?= $_accounts_list; ?></div>


<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered'>
		<thead>
			<tr>									
				<th style="text-align:center;">Account ID</th>	
				<th style="text-align:center;">Gross</th>	
				<th style="text-align:center;">W. Tax</th>	
				<th style="text-align:center;">Net of Tax</th>	
				<th style="text-align:center;">Balance</th>
				<th style="text-align:center;">Total Amount</th>
				<th style="text-align:center;">Cash Card</th>
				<th style="text-align:center;">Account Status</th>		
				<th style="text-align:center;">Payout Period</th>								
			</tr>
		</thead>
		<tbody id="funds_history">
		<?php
		
		$with_result = 0;
		
		if (empty($payout_period_details)) {
			//$history_html .= "<tr><td colspan='9' style='text-align: center;'>No Entries Found</td></tr>";			
		} else {
			// get all affected files
			foreach($payout_period_details as $ppd) {
			
				$payout_period = $ppd->start_date ." to ". $ppd->end_date; 
							
				// construct table suffix based on start and end dates			
				$suffix_start_date = date(  "Ymd", strtotime($ppd->start_date));
				$suffix_end_date = date(  "Ymd", strtotime($ppd->end_date));
				
				$suffix_payout_period = $suffix_start_date . "_" . $suffix_end_date;
					
				
				// check if table exists
				$sql = "SHOW TABLES LIKE '%ph_member_account_commissions_{$suffix_payout_period}%'";
				
				$query = $this->db->query($sql);
				$check_table_result = $query->result();			
				$query->free_result();
				
				if (empty($check_table_result)) {
					// record not found
					//$igpsm_total = "<i>- record not available -</i>";	
					$history_html .= "<tr>
										<td colspan='8' style='text-align: center;'><i>- Record not available -</i></td>
										<td style='text-align:right;'>{$payout_period}</td>
										</tr>";					
				} else {	
				
					// get all records of member under the current table
					$sql = "SELECT 
						 account_id, gross, witholding_tax, net_of_tax, balance, total, cash_card, account_status, start_date, end_date
						FROM 
						 ph_member_account_commissions_{$suffix_payout_period}
						WHERE 
						 member_id = {$member_id}
						AND
						 payout_type = '{$payout_type}'";	 		 
						 
					if ($account_id != "ALL") {
						$sql .= " AND account_id = {$account_id}";
					}	 
						 
					$query = $this->db->query($sql);
					//$earnings_per_acct = $query->result();							
					$earnings_per_acct = $query->result_object();
					$query->free_result();
					
					if (count($earnings_per_acct) == 0) {
						//$history_html .= "<tr><td colspan='9' style='text-align: center;'>No Entries Found</td></tr>";
					} else {
						$with_result = 1;
					
						foreach ($earnings_per_acct as $eca) {
						
							if ($eca->account_status == 'INACTIVE') {
								$label_status = "<span class='label label-important'>FORFEITED</span>";
							} else {							
								$label_status = "<span class='label label-success'>{$eca->account_status}</span>";
							}
						
							$payout_period = $eca->start_date ." to ". $eca->end_date; 
							$history_html .= "<tr>				
												<td style='text-align:right;'>{$eca->account_id}</td>
												<td style='text-align:right;'>".number_format($eca->gross,2)."</td>
												<td style='text-align:right;'>{$eca->witholding_tax}</td>
												<td style='text-align:right;'>".number_format($eca->net_of_tax,2)."</td>
												<td style='text-align:right;'>".number_format($eca->balance,2)."</td>
												<td style='text-align:right;'>".number_format($eca->total,2)."</td>								
												<td style='text-align:right;'>{$eca->cash_card}</td>  
												<td style='text-align:center;'>{$label_status}</td>  
												<td style='text-align:right;'>{$payout_period}</td>  
											</tr>";
						}						
					}										
				}				
			}
		
		}
		
		if ($with_result == 0) {
			$history_html .= "<tr><td colspan='9' style='text-align: center;'>No Entries Found</td></tr>";	
		}
		
		?>
		<?= $history_html ?>
		</tbody>
	</table>
</div>