<h2>Add New Package  <a href='/admin/packages' class='btn btn-large' >Back</a></h2>
<hr/>
<form action='' class='form-horizontal'>
	<fieldset >
		<div style="overflow:auto; height:100px; width:300px;">

<p>

Pay attention to my coding style - you may learn something

about formatting code so that it's easier to read!

</p>

<p>

In your actual page you should have much more text to see the

scrolling action do its thing - I kept it short for the article ...

</p>

</div>
		<h3>Items</h3>
		<hr>
		<div class="row-fluid">
			<div class="span11">
				<table class="table">
					<thead>
						<th style="width:370px;">Products List</th>
						<th style="width:36px;">&nbsp;</th>
						<th style="width:370px;">Package Products</th>
						<th>Product Details</th>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php

								$options = array();
								foreach($products as $product)
								{
									$options[$product->product_id] = $product->product_name;
								}
								echo form_multiselect('product_list[]',$options,'','id="product_list" size="15" style="width:356px;"');
								?>
							</td>
							<td>
								<br>
								<br>
								<br>
								<br>
								<a id="add_product" class='btn btn-small btn-primary'><i class="icon-chevron-right icon-white" title="Add" ></i></a>
								<br>
								<br>
								<br>
								<br>
								<br>
								<a id="rmv_product" class='btn btn-small btn-primary'><i class="icon-chevron-left icon-white" title="Remove" ></i></a>
							</td>
							<td>
								<?php

								echo form_multiselect('package_products[]',array(),'','id="package_products" size="15" style="width:356px;"');
								?>
							</td>
							<td>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	$(document).on('click','#add_product', function(){
		var _selected = $('#product_list').val();

		$('#product_list option').each(function(){
			if($(this).attr("selected") == "selected")
			{
				$('#package_products').append($(this));
			}
		});
	});

	$(document).on('click','#rmv_product', function(){
		var _selected = $('#package_products').val();

		$('#package_products option').each(function(){
			if($(this).attr("selected") == "selected")
			{
				$('#product_list').append($(this));
			}
		});
	});
</script>