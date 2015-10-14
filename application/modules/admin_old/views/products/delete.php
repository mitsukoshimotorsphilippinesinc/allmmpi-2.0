<?php if (empty($product)): ?>
	<h3>Product not found.</h3>
<?php else: ?>
	<label>You are about to delete a Product having the following details:</label>
	<br/>
	
	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:140px;'><label><strong>Item Name</strong></label></td>
				<td><label class=''><?= $product->item_name ?></label></td>		
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>Product Line</strong></label></td>
				<td><label class=''><?= $product->product_line ?></label></td>		
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>Standard Retail Price</strong></label></td>
				<td><label class=''>PHP <?= $product->standard_retail_price ?></label></td>		
			</tr>	
			<tr>
				<td style='width:140px;'><label><strong>Member's Price</strong></label></td>
				<td><label class=''>PHP <?= $product->member_price ?></label></td>		
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>Employee's Price</strong></label></td>
				<td><label class=''>PHP <?= $product->employee_price ?></label></td>		
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>GC Standard Retail Price</strong></label></td>
				<td><label class=''>PHP <?= $product->giftcheque_standard_retail_price ?></label></td>
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>GC Member Price</strong></label></td>
				<td><label class=''>PHP <?= $product->giftcheque_member_price ?></label></td>
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>GC Employee Price</strong></label></td>
				<td><label class=''>PHP <?= $product->giftcheque_employee_price ?></label></td>
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>IGPSM Points</strong></label></td>
				<td><label class=''><?= $product->igpsm_points ?></label></td>		
			</tr>
		</tbody>
	</table>
<?php endif; ?>
