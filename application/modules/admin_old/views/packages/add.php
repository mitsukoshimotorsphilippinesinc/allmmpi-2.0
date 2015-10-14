<style type="text/css">
	.item {width:200px;}
	.swappable {width:150px;}
	.quantity {width:150px;}
</style>
<?php
$product_options_html = "<option value=''>Please Select a Product</option>";
$product_options = array();
foreach($products as $product)
{
	$name= $product->item_name;
	if(is_null($name) || $name == "") $name = $product->product_name;
	$product_options_html .= "<option value='{$product->product_id}'>{$name}</option>";
	$product_options[$product->product_id] = $product->item_name;
}
?>
<div class='alert alert-info'><h3>Add New Package <a href="/admin/products" class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Products Dashboard</a></h3></div>
<form action='/admin/products/add/package/<?= $product_type_id; ?>' method='post' class='form-inline'>
	<div class="row-fluid">
		<div class='alert alert-success'>
			<h4>Package Details</h4>
		</div>
	</div>
		
	<fieldset >						
		<div class="row-fluid">
			<div class="span11">
				<div class="control-group <?= $this->form_validation->error_class('package_type_id') ?>" style="width:1250px">
					<label class="control-label" for="item_type_id"><strong>Type <em>*</em></strong></label>
					<div class="controls">
						<?php
							$pt = $package_types[$product_type_id];
						?>
						<input type="text" value="<?= $pt->name; ?>" readonly>
						<input type="hidden" name="product_type_id" id="product_type_id" value="<?= $product_type_id; ?>" readonly>
						<p class="help-block"><?= $this->form_validation->error('package_type_id'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('product_code') ?>">
					<label class="control-label" for="product_code"><strong>Product Code <em>*</em></strong></label>
					<div class="controls">
						<input type="text" class='span4' placeholder="Product Code" name="product_code" id="product_code" value="<?= set_value('product_code') ?>">
						<p class="help-block"><?= $this->form_validation->error('product_code'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('package_name') ?>">
					<label class="control-label" for="package_name"><strong>Package Name <em>*</em></strong></label>
					<div class="controls">
						<input type="text" class='span4' placeholder="Package Name" name="package_name" id="package_name" value="<?= $this->form_validation->set_value('package_name') ?>">
						<p class="help-block"><?= $this->form_validation->error('package_name'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('product_description') ?>">
					<label class="control-label" for="product_description"><strong>Product Description <em>*</em></strong></label>
					<div class="controls">
						<textarea class='span4' placeholder="Product Description" name="product_description" id="product_description" rows="5"><?= $this->form_validation->set_value('product_description') ?></textarea>
						<p class="help-block"><?= $this->form_validation->error('product_description'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
					<label class="control-label" for="is_active"><strong>Active? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_active',$product_line_options,set_value('is_active'),'id="is_active" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_vatable') ?>">
					<label class="control-label" for="is_vatable"><strong>Vatable? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_vatable',$product_line_options,set_value('is_vatable'),'id="is_vatable" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_vatable'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_display') ?>">
					<label class="control-label" for="is_display"><strong>Displayed? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_display',$product_line_options,set_value('is_display'),'id="is_display" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_display'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_gc_buyable') ?>">
					<label class="control-label" for="is_gc_buyable"><strong>GC Buyable? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_gc_buyable',$product_line_options,set_value('is_gc_buyable'),'id="is_gc_buyable" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_gc_buyable'); ?></p>
					</div>
				</div>
				<div id="gc_exclusive_sec" class="control-group <?= $this->form_validation->error_class('is_gc_exclusive') ?>" style="display:none;">
					<label class="control-label" for="is_gc_exclusive"><strong>GC Exclusive? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array("0" => "No","1" => "Yes");
						echo form_dropdown('is_gc_exclusive',$product_line_options,set_value('is_gc_exclusive'),'id="is_gc_exclusive" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_gc_exclusive'); ?></p>
					</div>
				</div>
				<?php if($cpoints_switch): ?>
				<div class="control-group <?= $this->form_validation->error_class('is_cpoints_buyable') ?>">
					<label class="control-label" for="is_cpoints_buyable"><strong>C Points Buyable? <em>*</em></strong></label>
					<div class="controls">
						<?php
						//$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						$product_line_options = array("0" => "No");
						echo form_dropdown('is_cpoints_buyable',$product_line_options,set_value('is_cpoints_buyable'),'id="is_cpoints_buyable" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_cpoints_buyable'); ?></p>
					</div>
				</div>
				<?php endif; ?>
				<div class="control-group <?= $this->form_validation->error_class('is_visible') ?>">
					<label class="control-label" for="is_visible"><strong>Visible in the Corporate Site? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_visible',$product_line_options,set_value('is_visible'),'id="is_visible" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_visible'); ?></p>
					</div>
				</div>
			</div>
		</div>
		<br>
		<br>
	</fieldset>
	<div class='alert alert-success'>
		<h4>Products List</h4>
	</div>
	<fieldset>
		<div class="row-fluid">
			<a id="add_product" class="btn btn-primary">Add Product</a>
		</div>
	</fieldset>
	
	<fieldset>
		<!-- products go here -->
		<div class="row-fluid">
			<div class="control-group error">
				<p id="product_input_error" class="help-block">
					<?= $this->form_validation->error('product_name_input'); ?>
				</p>
			</div>
			<br/>
			<h4 style="margin-bottom:5px;margin-left:10px;">List of Items</h4>
			<table class="table table-bordered table-striped">

				<thead>
					<th id="list_products" style="padding: 4px 5px;">Product</th>
					<th style="width:150px; padding: 4px 5px;">Swappable?</th>
					<th style="width:150px; padding: 4px 5px;">Quantity</th>
					<th style="width:150px; padding: 4px 5px;">Group</th>
					<th>&nbsp;</th>					
				</thead>
				
				<tbody id="products">
					<?php for($i=0;$i<count($this->input->post('product_name'));$i++):?>
						<?php if(set_value('product_name['.$i.']') != '' && set_value('is_swappable['.$i.']') != ''):?>
						<tr>
							<td class="item" style="padding: 4px 5px;"><?= $product_options[set_value('product_name['.$i.']')] ?><input type="hidden" name="product_name[]" value="<?= set_value('product_name['.$i.']') ?>" style="width:auto;" readonly="readonly" ></td>
							<td class="swappable" style="padding: 4px 5px;"><?= (set_value('is_swappable['.$i.']') == 0) ? "No" : "Yes" ?><input type="hidden" name="is_swappable[]" value="<?= set_value('is_swappable['.$i.']') ?>" style="width:auto;" readonly="readonly" ></td>
							<td class="quantity" style="padding: 4px 5px;"><?= set_value('quantity['.$i.']'); ?><input type="hidden" name="quantity[]" value="<?= set_value('quantity['.$i.']') ?>" style="width:auto;" readonly="readonly" ></td>
							<?php
							
							$group = set_value('group['.$i.']');
							$group_qty = set_value('group_qty['.$i.']');
							$group_text = "";
							if($group != 0) $group_text = "<br/>(Can select up to {$group_qty} item(s))";
							
							?>
							<td class="group" style="width:150px; padding: 4px 5px;"><?= set_value('group['.$i.']'); ?><?= $group_text; ?><input type="hidden" name="group[]" value="<?= set_value('group['.$i.']'); ?>" style="width:auto;" readonly="readonly" ><input class="group_qty" type="hidden" name="group_qty[]" value="<?= set_value('group_qty['.$i.']'); ?>" style="width:auto;" readonly="readonly" ></td>
							<td style="padding: 4px 5px;"><a class="btn btn-danger rmv_product" title="Remove"><i class="icon-remove icon-white"></i></a></td>
						</tr>
						<?php endif;?>
					<?php endfor;?>
				</tbody>
			</table>
		</div>
		<br>
		<br>
	</fieldset>
	<div class='alert alert-success'>
		<h4>Prices</h4>
	</div>
	<fieldset>
		<div class="control-group <?= $this->form_validation->error_class('standard_retail_price') ?>">
			<label class="control-label" for="standard_retail_price"><strong>Standard Retail Price <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Standard Retail Price" name="standard_retail_price" id="standard_retail_price" value="<?= set_value('standard_retail_price') ?>">
				<p class="help-block"><?= $this->form_validation->error('standard_retail_price'); ?></p>
			</div>
		</div>
		<?php if($cpoints_switch): ?>
		<div class="control-group <?= $this->form_validation->error_class('cpoints_value') ?>">
			<label class="control-label" for="cpoints_value"><strong>C Points Value <em>*</em></strong></label>
			<div class="controls">
				<!--<input type="text" class='span2' placeholder="C Points Value" name="cpoints_value" id="cpoints_value" value="<?= set_value('cpoints_value') ?>">-->
				<input type="text" class='span2' placeholder="C Points Value" name="cpoints_value" id="cpoints_value" value="0">
				<p class="help-block"><?= $this->form_validation->error('cpoints_value'); ?></p>
			</div>
		</div>
		<?php endif; ?>
		<div class="control-group <?= $this->form_validation->error_class('igpsm_points') ?>">
			<label class="control-label" for="igpsm_points"><strong>IGPSM Points <em>*</em><strong></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="IGPSM Points" name="igpsm_points" id="igpsm_points" value="<?= set_value('igpsm_points') ?>">
				<p class="help-block"><?= $this->form_validation->error('igpsm_points'); ?></p>
			</div>
		</div>
	
		<hr/>
		<div class="control-group" align="right">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Confirm New Package</button>
				<a href="/admin/products" class="btn return-btn">Cancel</a>
			</div>
		</div>
		
	</fieldset>
</form>
<script type="text/javascript">
	
	var swap_group = <?= $swap_group ?>;
	var product_options_html = "<?= $product_options_html; ?>";
	var product_options = <?= json_encode($product_options); ?>
	
	$(window).ready(function(){
		$("#list_products").css("width",$("#head_products").css("width"));
		$("#is_gc_buyable").trigger("change");
	});
	
	$("#is_gc_buyable").change(function(){
		var is_gc_buyable = $(this).val();
		
		if(is_gc_buyable == "" || is_gc_buyable == 0)
		{
			$("#gc_exclusive_sec").hide();
			$("#is_gc_exclusive").val(0);
		}
		else
		{
			$("#gc_exclusive_sec").show();
		}
	});
	
	$("#add_product").click(function(){
		
		var product_type_id = $('#product_type_id').val();
		var p2p_package_type_id = "<?=$p2p_package_type_id?>";
		
		var html = "<label style='display:inline;'><strong>Type: </strong></label><select id='entry_type' style='width: auto;'><option value='0'>Individual Product</option><option value='1'>Variant Group</option></select>";
		/*if(product_type_id == p2p_package_type_id)
		{
			html = "<label style='display:inline;'><strong>Type: </strong></label><select id='entry_type' style='width: auto;'><option value='0'>Individual Product</option></select>";
		}*/
		
		var new_product_modal = b.modal.new({
			title: "Add New Product",
			html: html,
			width: 300,
			buttons: {
				"Ok": function(){
					var type = $("#entry_type").val();
					var html = "";
					var title = "";
					var width = 300;
					if(type == 0)
					{
						html = _.template($("#indiv_template").html(),{"options": product_options_html});
						title = "Select Item";
						width = 350;
					}
					else if(type == 1)
					{
						html = _.template($("#variant_template").html(),{"group_qty": 1,"options": product_options_html,"components": ""});
						title = "New Variant Group";
						width = 450;
					}
					
					var add_entry_modal = b.modal.new({
						title: title,
						html: html,
						width: width,
						disableClose: true,
						buttons:{
							"Cancel": function(){
								add_entry_modal.hide();
							},
							"Ok": function(){
								
								var row_class ="nonswap_0";
								var qty = 0;
								var variant = 0;
								var group_text = "";
								var group_qty = 0;
								var has_error = false;
								$("#input_error").text("");
								if(type == 0)
								{
									//individual product
									qty = parseInt($('#quantity').val());
									if(_.isEmpty($('#product_name_input').val()))
									{
										$("#input_error").append("Please select an item.");
										has_error = true;
									}
									if(qty < 1)
									{
										$("#input_error").append(" Quantity must be greater than 0.");
										has_error = true;
									}
									
									if(!has_error)
									{
										$("#products").append('\
											<tr class="'+row_class+'">\n\
												<td class="item" style="padding: 4px 5px;">'+$('#product_name_input option:selected').text()+'<input type="hidden" name="product_name[]" value="'+$('#product_name_input').val()+'" style="width:auto;" readonly="readonly" ></td>\n\
												<td class="swappable" style="width:150px; padding: 4px 5px;">No<input type="hidden" name="is_swappable[]" value="0" style="width:auto;" readonly="readonly" ></td>\n\
												<td class="quantity" style="width:150px; padding: 4px 5px;">'+qty+'<input type="hidden" name="quantity[]" value="'+qty+'" style="width:auto;" ></td>\n\
												<td class="group" style="width:150px; padding: 4px 5px;">'+variant+''+group_text+'<input type="hidden" name="group[]" value="'+variant+'" style="width:auto;" readonly="readonly" ><input class="group_qty" type="hidden" name="group_qty[]" value="'+group_qty+'" style="width:auto;" readonly="readonly" ></td>\n\
												<td style="padding: 4px 5px;"><a class="btn btn-danger rmv_product" title="Remove"><i class="icon-remove icon-white"></i></a></td>\n\
											</tr>');

										add_entry_modal.hide();
										new_product_modal.hide();
									}
								}
								else if(type == 1)
								{
									//variant group
									qty = parseInt($('#quantity').val());
									
									if($(".variant_component").length < 1)
									{
										$("#input_error").append("Please add an item.");
										has_error = true;
									}
									
									if($(".variant_component").length == 1)
									{
										$("#input_error").append("The variant group must have more than one product.");
										has_error = true;
									}
									
									if(qty < 1)
									{
										$("#input_error").append(" Quantity must be greater than 0.");
										has_error = true;
									}
									
									if(!has_error)
									{
										var variant = 1;
										if(!_.isEmpty(swap_group))
										{
											variant = _.size(swap_group);
										}
										group_text = "<br/>(Can select up to "+qty+" item(s))";
										swap_group[variant] = qty;
										row_class = "swap_"+variant;
										$(".variant_component").each(function(){
											$("#products").append('\
												<tr class="'+row_class+'">\n\
													<td class="item" style="padding: 4px 5px;">'+$(this).parent().parent().find(".component_name").text()+'<input type="hidden" name="product_name[]" value="'+$(this).val()+'" style="width:auto;" readonly="readonly" ></td>\n\
													<td class="swappable" style="width:150px; padding: 4px 5px;">Yes<input type="hidden" name="is_swappable[]" value="1" style="width:auto;" readonly="readonly" ></td>\n\
													<td class="quantity" style="width:150px; padding: 4px 5px;">1<input type="hidden" name="quantity[]" value="1" style="width:auto;" ></td>\n\
													<td class="group" style="width:150px; padding: 4px 5px;">'+variant+''+group_text+'<input type="hidden" name="group[]" value="'+variant+'" style="width:auto;" readonly="readonly" ><input class="group_qty" type="hidden" name="group_qty[]" value="'+qty+'" style="width:auto;" readonly="readonly" ></td>\n\
													<td style="padding: 4px 5px;"><a class="btn btn-danger rmv_product" title="Remove"><i class="icon-remove icon-white"></i></a></td>\n\
												</tr>');
										});
										add_entry_modal.hide();
										new_product_modal.hide();
									}
								}
								
							}
						}
					});
					add_entry_modal.show();
				}
			}
		});
		
		new_product_modal.show();
	});

	
	$(document).on('click',".rmv_product",function(e){

		var tr_rmv = $(this);
		var confirm_remove = beyond.modal.new({

			title: "Confirm Item Removal",
			width: 300,
			html: "<p>Do you want to remove this item entry?</p>",
			buttons: {
				"Confirm": function(){
					//alert($(tr_rmv).attr("class"));
					tr_rmv.parent().parent().remove();
					confirm_remove.hide();
				}
			}
		});
		confirm_remove.show();
	});
	
	$(document).on("click","#add_component",function(){
		var product_id = $("#product_name_input").val();
		var exists = false;
		
		$(".variant_component").each(function(){
			if($(this).val() == product_id)
			{
				exists = true;
				return;
			}
		});
		
		if(!_.isEmpty(product_id) && !exists)
		{
			$("#components").append("<tr>\n\
									<td class='component_name'>"+$("#product_name_input>option:selected").text()+"</td>\n\
									<td><a class='btn btn-danger rmv_component' title='Remove'><i class='icon-remove icon-white'></i></a><input type='hidden' value='"+product_id+"' class='variant_component' name='variant_component[]' readonly></td>\n\
									</tr>");
		}
		
	});
	
	$(document).on("click",".rmv_component",function(){
		$(this).parent().parent().remove();
	});
	
	$(document).on("change",".modal_variant",function(){
		
		var modal_qty = 1;
		
		if(!_.isUndefined(swap_group[$(this).val()])) modal_qty = swap_group[$(this).val()];
		
		$(".modal_qty").val(modal_qty);
	});
</script>
<script id="indiv_template" type='text/template'>
	<div>
		<label>Item</label>
		<select name="product_name_input" id="product_name_input">
			<%= options %>
		</select>
		<label>Quantity</label>
		<input id="quantity" name="quantity" value="1">
	</div>
	<div class="control-group error">
		<p id="input_error" class="help-block">
		</p>
	</div>
</script>
<script id="variant_template" type='text/template'>
	<div>
		<label>Group Quantity</label>
		<input id="quantity" name="quantity" value="<%= group_qty %>" style="width: 40px;">
	</div>
	<div>
		<label>Item</label>
		<select name="product_name_input" id="product_name_input"  style="margin-bottom: 0px;">
			<%= options %>
		</select>
		<a id="add_component" class="btn btn-primary">Add Product</a>
	</div>
	<div class="control-group error">
		<p id="input_error" class="help-block">
		</p>
	</div>
	<div>
		<table class="table table-bordered table-striped">
			<thead>
				<tr><th>Product</th><th>&nbsp;</th></tr>
			</thead>
			<tbody id="components">
				<%= components %>
			</tbody>
		</table>
	</div>
</script>