<?php

$history_html = "";

?>

<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered'>
		<thead>
			<tr>									
				<th style="text-align:center;">Payout Period</th>				
				<th style="text-align:center;">Deduction Type</th>	
				<th style="text-align:center;">Amount</th>				
			</tr>
		</thead>
		<tbody id="funds_history">
		<?php

		$log_counter = 0;
		
		if (empty($all_logs)) {
			$history_html = "<tr><td colspan='8' style='text-align: center;'>No Entries Found</td></tr>";
			
		} else {	
			foreach($all_logs as $al)
			{
				
				$payout_period = $al->start_date ." to ". $al->end_date; 
				$proper_deduction_type = ucwords($al->deduction_type); 
				
				$history_html .= "<tr>																
									<td style='text-align:center;;width:220px;'>{$payout_period}</td>								
									<td style='text-align:left;'>{$proper_deduction_type}</td>
									<td style='text-align:right;width:120px;'>".number_format($al->amount,2)."</td>
								</tr>";
				
				$log_counter++;
							
			}
		}
		?>
		<?= $history_html ?>
		</tbody>
	</table>
</div>