<?php

$history_html = "";

?>

<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered'>
		<thead>
			<?php
			if ($lookup_table == 'cm_member_payouts') {
			
				echo "
					<tr>													
						<th style='text-align:center;'>Gross Amount</th>	
						<th style='text-align:center;'>GCEP</th>
						<th style='text-align:center;'>Net of GCEP</th>				
						<th style='text-align:center;'>Cash Card</th>
						<th style='text-align:center;'>Payout Period</th>										
					</tr>
					";
				
			} else {
				echo "
					<tr>													
						<th style='text-align:center;'>Total GCEP</th>	
						<th style='text-align:center;'>GCEP Variance</th>
						<th style='text-align:center;'>Net of GCEP</th>				
						<th style='text-align:center;'>Status</th>
						<th style='text-align:center;'>Payout Period</th>										
					</tr>
					";
				
			}		
			?>
		</thead>
		<tbody id="funds_history">
		<?php

		if (empty($all_logs)) {
			$history_html = "<tr><td colspan='5' style='text-align: center;'>No Entries Found</td></tr>";
			
		} else {	
			foreach($all_logs as $al)
			{
				// get payout period
				$payout_period = $al->start_date ." to ". $al->end_date; 
								
				$proper_status = strtoupper($al->status);
				
				if ($lookup_table == 'cm_member_payouts') {
					$history_html .= "<tr>																									
										<td style='text-align:right;'>".number_format($al->gross,2)."</td>
										<td style='text-align:right;'>".number_format($al->gcep,2)."</td>
										<td style='text-align:right;'>".number_format($al->net_of_gcep,2)."</td>				
										<td style='text-align:center;'>{$al->cash_card}</td>
										<td style='text-align:center;'>{$payout_period}</td>											
									</tr>";
				} else {

					$proper_status = $al->status;	

					if ($al->is_on_hold == 1) {
						$proper_status = "HOLD";
						$statustag = "<label class='label label-important'>{$proper_status}</label>";		
					} else if (($al->status == 'COMPLETED') || ($al->status == 'PROCESSED')) {
						$statustag = "<label class='label label-success'>{$proper_status}</label>";	
					} else {
						$statustag = "<label class='label label-warning'>{$proper_status}</label>";	
					}	

					$history_html .= "<tr>																									
										<td style='text-align:right;'>".number_format($al->total_gcep, 2)."</td>
										<td style='text-align:right;'>".number_format($al->gcep_variance, 2)."</td>
										<td style='text-align:right;'>".number_format($al->net_gcep, 2)."</td>
										<td style='text-align:center;'>{$statustag}</td>				
										<td style='text-align:center;'>{$payout_period}</td>											
									</tr>";
				}			
			}
		}
		?>
		<?= $history_html ?>
		</tbody>
	</table>
</div>