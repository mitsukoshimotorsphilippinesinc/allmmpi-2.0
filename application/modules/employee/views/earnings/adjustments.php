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
				<th style="text-align:center;">Witholding Tax</th>
				<th style="text-align:center;">Net of Tax</th>
				<th style="text-align:center;">Final Commission</th>	
				<th style="text-align:center;">Cash Card</th>								
				<th style="text-align:center;">Payout Period</th>									
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
				$history_html .= "<tr>																
									<td style='text-align:right;'>".number_format($al->gross,2)."</td>
									<td style='text-align:right;width:50px;'>".number_format($al->gcep,2)."</td>
									<td style='text-align:right;'>".number_format($al->net_of_gcep,2)."</td>
									<td style='text-align:right;width:50px;'>".number_format($al->witholding_tax,2)."</td>	
									<td style='text-align:right;'>".number_format($al->net_of_tax,2)."</td>	
									<td style='text-align:right;width:50px;'>".number_format($al->total_amount,2)."</td>							
									<td style='text-align:right;width:100px;'>{$al->cash_card}</td>									
									<td>{$payout_period}</td>								
								</tr>";
				
				if (!(trim($al->remarks) == "")) {
				$history_html .= "	
								<tr style='font-size:12px;'>
									<td style='text-align:center;'><strong>Remarks</strong></td>
									<td colspan ='7'><i>{$al->remarks}</i></td>";								
				}
				
				$log_counter++;
				
				if ($log_counter != count($all_logs)) {
				
					$history_html .= "<tr><tr>
									<td colspan='8' style='height:10px;'></td>
									</tr>";
				}				
			}
		}
		?>
		<?= $history_html ?>
		</tbody>
	</table>
</div>