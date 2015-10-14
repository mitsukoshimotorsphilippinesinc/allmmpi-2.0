<div>
	<table  class='table table-striped table-bordered'>
		<thead>
			<tr>			
				<th style='width:60px;'>REQUEST CODE</th>				
				<th style='width:40px;'>MODULE NAME</th>				
				<th style='width:40px;'>STATUS</th>
				<th style='width:40px;'>DATE CREATED</th>
				<th style='width:40px;'></th>
			</tr>
		</thead>
		<tbody>
			<?php if(empty($pending_reservations_details)):?>
				<tr><td colspan='5' style='text-align:center;'><strong>No Record Found</strong></td></tr>
			<?php else: ?>
			<?php foreach ($pending_reservations_details as $prd): ?>
			<tr>
				<td><?= $prd->transaction_number; ?></td>				
				<td><?= $prd->module_name; ?></td>
				<td><?= $prd->status; ?></td>
				<td><?= $prd->insert_timestamp; ?></td>				
				<td>
					<a class='btn btn-small btn-primary view-details' data='info' title="View Details"><i class="icon-white icon-list"></i></a>	
				</td>				
			</tr>	
			<?php endforeach; ?>
			<?php endif; ?>
		<tbody>
	</table>
	<?php	
	if ($pending_reservations_count > 20) {
	?>	
	
	<div>
		<a href="" 	style="">See more details <i class='icon-arrow-right'></i></a>
	</div>

	<?php } ?>
		 
</div>													
<div style="margin-bottom:30px;"></div>