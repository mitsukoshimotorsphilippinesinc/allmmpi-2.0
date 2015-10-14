<?php if (empty($product_type)): ?>
<h3>Product Type not found.</h3>
<?php else: ?>
	<label>You are about to delete an Product Type having the following details:</label>
	<br/>

	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:100px;'><label><strong>Name</strong></label></td>
				<td><label class=''><?= $product_type->name ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>Visible?</strong></label></td>
				<td><label class=''><?= ($product_type->is_visible == 1) ? "YES" : "NO" ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>GC Buyable?</strong></label></td>
				<td><label class=''><?= ($product_type->is_gc_buyable == 1) ? "YES" : "NO"  ?></label></td>		
			</tr>				
		</tbody>		
	</table>
<?php endif; ?>

<script type="text/javascript">

	$("#delete_product_type").click(function(){
		var action = "delete";
		var type = "Product Type";
		validateAction(action, type);
	});
</script>
