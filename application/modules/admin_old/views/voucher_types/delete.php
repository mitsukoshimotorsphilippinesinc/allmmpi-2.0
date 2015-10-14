<?php if (empty($voucher_type)): ?>
	<h3>Voucher Type not found.</h3>
<?php else: ?>
	<label>You are about to delete a Voucher Type having the following details:</label>
	<br/>

	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:100px;'><label><strong>Code</strong></label></td>
				<td><label class=''><?= $voucher_type->code ?></label></td>		
			</tr>		
			<tr>
				<td style='width:100px;'><label><strong>Name</strong></label></td>
				<td><label class=''><?= $voucher_type->name ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>Description</strong></label></td>
				<td><label class=''><?= $voucher_type->description ?></label></td>		
			</tr>			
		</tbody>		
	</table>	
<?php endif; ?>
