<div class='content-area'>
<div class='section-header-main'>Card Details<span style='float:right;color:#990000;'>&nbsp;[<?=$card_master_details->status?>]</span><span id='card_id_display' style='float:right;'><?=$_card_id?></span></div>

	<table class='table table-striped table-bordered'>
		<thead>
			<tr>
				<td style='width:100px;'><strong>Type</strong></td>
				<td style='width:100px;'><strong>Released To</strong></td>
				<td style='width:100px;'><strong>Date Released</strong></td>
				<td style='width:100px;'><strong>Issued To</strong></td>
				<td style='width:100px;'><strong>Date Issued</strong></td>
				<td style='width:100px;'><strong>Date Activated</strong></td>
				<td style='width:100px;'><strong>Date Used</strong></td>
			</tr>
		</thead>
		<tbody>
			<tr>			
				<td><?=$card_master_details->type?></td>	
				<td><?=$card_master_details->released_to?></td>	
				<td><?=$card_master_details->released_timestamp?></td>	
				<td><?=$card_master_details->issued_to?></td>	
				<td><?=$card_master_details->issued_timestamp?></td>	
				<td><?=$card_master_details->activate_timestamp?></td>	
				<td>1231231</td>		
			</tr>
		
		</tbody>
	</table>
</div>