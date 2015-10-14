<?php if (empty($voucher_product)): ?>
	<h3>Product not found.</h3>
<?php else: ?>
	<label>You are about to delete a Voucher Product having the following details:</label>
	<br/>
	
	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:120px;'><label><strong>Product Name</strong></label></td>
				<td><label class=''><?= $voucher_product->voucher_product_name ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Voucher Type</strong></label></td>
				<td><label class=''><?= $voucher_type_text ?></label></td>		
			</tr>		
		</tbody>
	</table>
<?php endif; ?>
