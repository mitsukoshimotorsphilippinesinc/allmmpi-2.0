<?php

$history_html = "";

?>

<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered'>
		<thead>
			<tr>									
				<th style="text-align:center;">Gross</th>
				<th style="text-align:center;">GCEP</th>	
				<th style="text-align:center;">Net of GCEP</th>	
				<th style="text-align:center;">W. Tax</th>	
				<th style="text-align:center;">Net of Tax</th>
				<th style="text-align:center;">Deductions<br/><span style='font-size:12px;'>(Boracay Incentive, etc.)</span></th>
				<th style="text-align:center;">Card Fee</th>
				<th style="text-align:center;">Final Commission</th>								
				<th style="text-align:center;">Payout Period</th>									
			</tr>
		</thead>
		<tbody id="fundstopaycard_history">
		<?php
	
		if (empty($all_logs)) {
			$history_html = "<tr><td colspan='10' style='text-align: center;'>No Entries Found</td></tr>";
			
		} else {			
			
			foreach($all_logs as $al)
			{
				
				if ($al->status == 'PENDING') {
					$status = "PROCESSING";
				} else {
					$status = $al->status;
				}
				
				$payout_period = $al->start_date ." to ". $al->end_date; 
				
				$remaining_balance = $al->gross - $al->deducted_amount;
				
				$history_html .= "<tr>																
								<td style='text-align:right;'>".number_format($al->gross,2)."</td>
								<td style='text-align:right;'>".number_format($al->gcep,2)."</td>
								<td style='text-align:right;'>".number_format($al->net_of_gcep,2)."</td>
								<td style='text-align:right;'>".number_format($al->wtax,2)."</td>
								<td style='text-align:right;'>".number_format($al->net_of_wtax,2)."</td>
								<td style='text-align:right;'>".number_format($al->deduction1,2)."</td>
								<td style='text-align:right;'>".number_format($al->deduction2,2)."</td>
								<td style='text-align:right;'>".number_format($al->final_commission,2)."</td>						
								
								<td>{$payout_period}</td>								
							</tr>";
		
			}
		}
		?>
		<?= $history_html ?>
		</tbody>
	</table>
</div>