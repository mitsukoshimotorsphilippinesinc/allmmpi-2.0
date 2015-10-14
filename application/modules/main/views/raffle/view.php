<table class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>ID</th>
			<th>Raffle Number</th>
			<th>Date</th>						
		</tr>
	</thead>
	<tbody>
	
		<?php if(empty($raffles_details)): ?>
			<tr><td colspan='10' style='text-align:center;'><strong>No Records Found</strong></td></tr>
		<?php else: ?>
		<?php foreach ($raffles_details as $raffle_detail): ?>
			<tr>
				<td><?= $raffle_detail->raffle_entry_id; ?></td>
				<td><?= $raffle_detail->raffle_number; ?></td>
				<td><?= $raffle_detail->insert_timestamp; ?></td>				
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	
	
	
	</tbody>
</table>