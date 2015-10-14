<?php

$history_html = "";

?>

<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered' style='font-size:12px'>
		<thead>
			<tr>									
				<th style="text-align:center;">Gross</th>
				<th style="text-align:center;">GCEP</th>	
				<th style="text-align:center;">Net of GCEP</th>	
				<th style="text-align:center;">Withholding Tax</th>	
				<th style="text-align:center;">Net of Tax</th>	
				<th style="text-align:center;">Total Deductions<br/></th>					
				<th style="text-align:center;">Net Amount</th>
				<th style="text-align:center;">Cash Card</th>				
				<th style="text-align:center;">Payout Period</th>									
			</tr>
		</thead>
		<tbody id="funds_history" style="font-size:12px">
		<?php

		if (empty($all_logs)) {
			$history_html = "<tr><td colspan='11' style='text-align: center;'>No Entries Found</td></tr>";
			
		} else {	
			$log_counter = 0;
		
			foreach($all_logs as $al)
			{
				
				if ($al->status == 'PENDING') {
					$status = "PROCESSING";
				} else {
					$status = $al->status;
				}
				
				$cr_tag = "";
				// tag if check release
				if ($al->check_release == 1) {	
					$cr_tag = "<label class='label label-warning'>CHECK RELEASE</label>";
				}	
				
				$payout_period = $al->start_date ." to ". $al->end_date; 
				//$payout_period = date('M d, Y h:i a', strtotime($al->start_date)) ." - ". date('M d, Y h:i a', strtotime($al->end_date)); 
				$history_html .= "<tr>																
									<td style='text-align:right;'>".number_format($al->gross,2)."</td>
									<td style='text-align:right;'>".number_format($al->gcep,2)."</td>
									<td style='text-align:right;'>".number_format($al->net_of_gcep,2)."</td>
									<td style='text-align:right;'>".number_format($al->witholding_tax,2)."</td>
									<td style='text-align:right;'>".number_format($al->net_of_tax,2)."</td>
									<td style='text-align:right;'>".number_format($al->total_deductions,2)."</td>
									<td style='text-align:right;'>".number_format($al->total_amount,2)."</td>								
									<td style='text-align:left;'>{$al->cash_card}<br/>{$cr_tag}</td>  									
									<td style='text-align:center;'>{$payout_period}</td>								
								</tr>";
				if (!(trim($al->remarks) == "")) {				 				
					$history_html .="<tr style='font-size:5px;'>
									<td style='text-align:center;'><strong>Remarks</strong></td>
									<td colspan ='11'><i>{$al->remarks}</i></td>
								</tr>";
				}
			}
			
			$log_counter++;
				
			if ($log_counter != count($all_logs)) {				
				$history_html .= "<tr><tr>
								<td colspan='11' style='height:10px;'></td>
								</tr>";
			}	
		}
		?>
		<?= $history_html ?>
		</tbody>
	</table>
</div>