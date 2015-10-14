<?php if (empty($card_series)): ?>
	<h3>Product not found.</h3>
<?php else: ?>
	<label>You are about to delete a Card Series having the following details:</label>
	<br/>
	
	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:120px;'><label><strong>Number Series</strong></label></td>
				<td><label class=''><?= $card_series->number_series ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Voucher Type</strong></label></td>
				<td><label class=''><?= $voucher_type_text ?></label></td>		
			</tr>					
		</tbody>
	</table>
<?php endif; ?>
