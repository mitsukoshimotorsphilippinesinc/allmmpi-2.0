<?php
	$products_dropdown = "<label>Please Select a Product:</label><select id='group_products_dropdown' name='group_products_dropdown'>";
	foreach($products as $p)
		$products_dropdown .= "<option value='{$p->product_id}'>{$p->product_name}</option>";
	$products_dropdown .= "</select>";
?>
<div class='alert alert-info'><h3>Add New Product <a href="/admin/products" class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Products Dashboard</a></h3></div>

<form action='/admin/products/add/product/1' method='post' class='form-inline'>
	<div class='alert alert-success'>
		<h4>Product Cards</h4>
	</div>
	<div class='controls span11'>
		<h4><?=$product_name?></h4>
		<hr/>
	</div>
	<fieldset>
	<div class='span6'>
		<?php foreach($product_cards as $p): ?>
			<div class="control-group">
				<label class="control-label" ><strong>Quantity Needed to Issue Card:</strong></label>
				<div>
					<input type='text' class='small-input' readonly='' value='<?=$p->qty_needed?>'>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" ><strong>Number of Cards Issued:</strong></label>
				<div>
					<input type='text' class='small-input' readonly='' value='<?=$p->qty_counted?>'>
				</div>
			</div>
			<div class="control-group">
				<label class='control-label'><strong>Grouped With:</strong></label>
				<div class='controls'>
					<?php
						if($p->group_product_ids == "" || $p->group_product_ids == 0)
							$group_products = "None";
						else
						{
							$group_product_array = explode(",", $p->group_product_ids);
							$group_product_names = array();
							foreach($group_product_array as $k => $g)
							{
								$group_product = $this->items_model->get_product_by_id($g);
								$group_product_names[$k] = $group_product->product_name;
							}
							$group_products .= implode("<br />", $group_product_names);
						}
						echo $group_products;
					?>
				</div>
			</div>
			<div style='float:right;margin-right:87px;margin-bottom:20px;'>
				<a class='btn btn-danger' id='remove_product_card' data-product='<?=$p->product_id?>' data-group='<?=$p->group_product_ids?>'>Remove</a>
			</div>
		<?php endforeach; ?>
	</div>
	<div class='span5'>
		<h4>Add New Card</h4>
		<br />
		<div class='control-group'>
			<label class="control-label" for="type"><strong>Select Type</strong></label>
			<div class='controls'>
				<select id='group_type' name='group_type'>
					<option value='individual'>INDIVIDUAL</option>
					<option value='group'>GROUP</option>
				</select>
			</div>
		</div>
		<label class='control-label' for='quantity_needed'><strong>Enter Quantity Needed to Issue Cards:</strong></label>
		<div class='controls'>
			<input type='text' id='quantity_needed' value='' placeholder='Enter Quantity Needed' />
		</div>
		<label style='margin-top:5px;display:none;' id='quantity_error' class='label label-important'>Please Enter a Quantity</label>
		<label class='control-label' for='quantity_needed'><strong>Enter Number of Cards Issued:</strong></label>
		<div class='controls'>
			<input type='text' id='quantity_issued' value='' placeholder='Enter Quantity Issued' />
		</div>
		<label style='margin-top:5px;display:none;' id='quantity_issued_error' class='label label-important'>Please Enter a Quantity</label>
		<br />
		<div style='display:none;' id='group_form'>
			<a class='btn btn-primary' id='add_product_group'>Add Product</a>
			<br /><br /><label><strong>Current Group:</strong></label>
			<div><table class='table table-condensed' id='add_group_products_list'></table></div>
		</div>
		<div style='float:right;margin-right:87px'>
			<a class='btn btn-primary' id='save_changes'>Save</a>
		</div>
	</div>
	</fieldset>
</form>
<script type="text/javascript">

	$("#group_type").change(function() {
		var type = $(this).val();
		if(type == 'individual') {
			$("#individual_form").show();
			$("#group_form").hide();
		}else if(type == 'group') {
			$("#individual_form").hide();
			$("#group_form").show();
		}
	});

	$('body').on('click', '#add_product_group', function() {
		var products_dropdown = "<?=$products_dropdown?>";
		var add_product_modal = b.modal.create({
			title: 'Select Product',
			html: products_dropdown,
			width: 400,
			disableClose: true,
			buttons: {
				'Cancel': function(){
					add_product_modal.hide();
				},
				'Add': function(){
					var selected_product = $("#group_products_dropdown").val();
					var selected_product_name = $("#group_products_dropdown option[value="+selected_product+"]").text();
					add_product_modal.hide();
					add_product_to_group_list(selected_product, selected_product_name);
				}
			}
		});
		add_product_modal.show();
	});
	
	$("body").on('click', '.rmv_product', function(){
		var row = $(this).parent().parent().remove();
	});
	
	var add_product_to_group_list = function(selected_product, product_name)
	{
		var add_to_list = "<tr class='group_products' data='"+selected_product+"'><td>"+product_name+"</td>"
		+"<td><a class='btn btn-danger rmv_product' data='"+selected_product+"' title='Remove'>"
		+"<i class='icon-remove icon-white'></i>"
		+"</td></tr>";
		$("#add_group_products_list").append(add_to_list);
		
	}

	$("body").on('click', '#save_changes', function() {
		var product_id = "<?=$product_id?>";
		var quantity_needed = $("#quantity_needed").val();
		var quantity_issued = $("#quantity_issued").val();
		var type = $("#group_type").val();
		var error_count = 0;
		var group_products_array = [];
		if(quantity_needed == "") {
			$("#quantity_error").show();
			error_count++;
		}else {
			$("#quantity_error").hide();
		}
		
		if(quantity_issued == "")
		{
			$("#quantity_issued_error").show();
			error_count++;
		}
		else{
			$("#quantity_issued_error").hide();
		}
		
		if(error_count > 0) {
			return;
		}
		
		group_products_array.push(product_id);
		
		if(type == 'group')
		{
			$("tr.group_products").each(function() {
				$this = $(this);
				var grp_product_id = $this.attr('data');
				group_products_array.push(grp_product_id);
			});
		}
		
		b.request({
			url: '/admin/products/edit_product_cards',
			data: {
				'product_id': product_id,
				'quantity_needed': quantity_needed,
				'quantity_issued': quantity_issued,
				'type': type,
				'group_products_array': group_products_array
			},
			on_success: function(data){
				if(data.status == 'ok') {
					var success_modal = b.modal.create({
						title: 'Success',
						html: data.msg,
						width: 350,
						disableClose: true,
						buttons: {
							'Close': function() {
								success_modal.hide();
								redirect('/admin/products/view_cards/'+product_id);
							}
						}
					});
					success_modal.show();
				}
				else if(data.status == 'error'){
					var error_modal = b.modal.create({
						title: 'Error',
						html: data.msg,
						width: 350
					});
					error_modal.show();
				}
			}
		});
	});
	
	$('#remove_product_card').on('click', function(){
		var product_id = $(this).data('product');
		var group_products_array = $(this).data('group');
		var confirm_modal = b.modal.create({
			title : 'Confirm Remove Product Cards',
			html : "Are you sure you want to remove this product card combination?",
			width: 350,
			disableClose: true,
			buttons: {
				'Close': function(){
					confirm_modal.hide();
				},
				'Confirm': function(){
					confirm_modal.hide();
					b.request({
						url: '/admin/products/remove_product_cards',
						data: {
							'product_id': product_id,
							'group_products_array': group_products_array
						},
						on_success: function(data){
							if(data.status == 'ok') {
								var success_modal = b.modal.create({
									title: 'Success',
									html: data.msg,
									width: 350,
									disableClose: true,
									buttons: {
										'Close': function() {
											success_modal.hide();
											redirect('/admin/products/view_cards/'+product_id);
										}
									}
								});
								success_modal.show();
							}
							else if(data.status == 'error'){
								var error_modal = b.modal.create({
									title: 'Error',
									html: data.msg,
									width: 350
								});
								error_modal.show();
							}
						}
					});
				}
			}
		});
		confirm_modal.show();
	});
</script>