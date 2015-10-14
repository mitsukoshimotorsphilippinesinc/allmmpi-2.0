<?php

$history_html = "";

?>

<div class='span span12'>
	<table class='table table-condensed table-striped table-bordered'>
		<thead>
			<tr>									
				<th style="text-align:center;">Transaction ID</th>
				<th style="text-align:center;">From Member Name</th>	
				<th style="text-align:center;">To Member Name</th>	
				<th style="text-align:center;">Type</th>	
				<th style="text-align:center;">Amount</th>
				<th style="text-align:center;">Status</th>
				<th style="text-align:center;">Date Initiated</th>
				<th style="text-align:center;">Date Completed</th>									
			</tr>
		</thead>
		<tbody id="funds_history">
		<?php

		if (empty($all_logs)) {
			$history_html = "<tr><td colspan='8' style='text-align: center;'>No Entries Found</td></tr>";
			
		} else {	
			foreach($all_logs as $al)
			{
				$from_member_details = $this->members_model->get_member_by_id($al->from_member_id);
				$proper_from_member_name = $from_member_details->last_name . ", " . $from_member_details->first_name . " " . $from_member_details->middle_name;
				$proper_from_member_name = strtoupper($proper_from_member_name);
				
				$to_member_details = $this->members_model->get_member_by_id($al->to_member_id);
				$proper_to_member_name = $to_member_details->last_name . ", " . $to_member_details->first_name . " " . $to_member_details->middle_name;
				$proper_to_member_name = strtoupper($proper_to_member_name);
				
				$proper_type = strtoupper($al->type);
				$proper_status = strtoupper($al->status);
				
				if ($proper_status == 'COMPLETED') {
					$label_status = "<span class='label label-success'>{$proper_status}</span>";
				} else {
					$label_status = "<span class='label label-important'>{$proper_status}</span>";
				}
				
				//$payout_period = $al->start_date ." to ". $al->end_date; 
				$history_html .= "<tr>																
									<td style='text-align:right;'>{$al->member_transfer_id}</td>
									<td style='text-align:right;'>{$proper_from_member_name}</td>
									<td style='text-align:right;'>{$proper_to_member_name}</td>
									<td style='text-align:right;'>{$proper_type}</td>
									<td style='text-align:right;'>".number_format($al->amount,2)."</td>								
									<td style='text-align:right;'>{$label_status}</td>  
									<td>{$al->update_timestamp}</td>		
									<td>{$al->insert_timestamp}</td>								
								</tr>";
			}
		}
		?>
		<?= $history_html ?>
		</tbody>
	</table>
</div>