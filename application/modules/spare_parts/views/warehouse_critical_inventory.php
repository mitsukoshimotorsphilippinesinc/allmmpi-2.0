<div>
	<table  class='table table-striped table-bordered'>
		<thead>
			<tr>			
				<th style='width:60px;'>SKU</th>
				<th style=''>BRAND-MODEL/DESCRIPTION</th>
				<th style='width:40px;'>STOCK LIMIT</th>				
				<th style='width:40px;'>GOOD QTY</th>
				<th style='width:40px;'></th>
			</tr>
		</thead>
		<tbody>
			<?php if(empty($critical_parts_details)):?>
				<tr><td colspan='5' style='text-align:center;'><strong>No Record Found</strong></td></tr>
			<?php else: ?>
			<?php foreach ($critical_parts_details as $cpd): ?>
			<tr>
				<td><?= $cpd->sku; ?></td>
				<?php
					$complete_description = $cpd->brand_name . ' - ' . $cpd->model_name ;
				?>				
				<td><?= $complete_description; ?><br/><?=  $cpd->description; ?></td>
				<td style='text-align:right;'><?= $cpd->stock_limit; ?></td>				
				<td style='text-align:right;'><?= $cpd->good_quantity; ?></td>
				<td>
					<a class='btn btn-small btn-primary view-details' data='info' title="View Details"><i class="icon-white icon-list"></i></a>	
				</td>				
			</tr>	
			<?php endforeach; ?>
			<?php endif; ?>
		<tbody>
	</table>
	<?php
	if ($critical_parts_count > 20) {
	?>	
	
	<div>
		<a href="" 	style="">See more details <i class='icon-arrow-right'></i></a>
	</div>

	<?php } ?>
		 
</div>													
<div style="margin-bottom:30px;"></div>