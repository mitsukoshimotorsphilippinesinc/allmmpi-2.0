<?php if (empty($product_line)): ?>
<h3>Product Line not found.</h3>
<?php else: ?>
	<label>You are about to delete an Product Line having the following details:</label>
	<br/>

	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:100px;'><label><strong>Product Line</strong></label></td>
				<td><label class=''><?= $product_line->product_line ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>Visible?</strong></label></td>
				<td><label class=''><?= ($product_line->is_visible == 1) ? "YES" : "NO" ?></label></td>		
			</tr>
		</tbody>		
	</table>
<?php endif; ?>

<script type="text/javascript">

	$("#delete_product_line").click(function(){
		var action = "delete";
		var type = "Product Line";
		validateAction(action, type);
	});
</script>
