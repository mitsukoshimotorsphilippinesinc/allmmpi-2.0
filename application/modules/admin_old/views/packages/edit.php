<style type="text/css">
	.item {width:200px;}
	.swappable {width:150px;}
	.quantity {width:150px;}
</style>

<div class='alert alert-info'><h3>Edit Package <a href="/admin/products" class='btn' style='float:right;margin-right:-30px;' >Back to Products Dashboard</a></h3></div>

<?php if (empty($package)): ?>
	<h3>Package not found.</h3>
<?php else: ?>
<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/packages";
	$_upload_url = urlencode($upload_url);
?>

<?php
$product_options_html = "<option value=''>Please Select a Product</option>";
$product_options = array();
foreach($products as $product)
{
	$product_options_html .= "<option value='{$product->product_id}'>{$product->item_name}</option>";
	$product_options[$product->product_id] = $product->item_name;
}
?>
<form action='/admin/products/edit/<?= $package->product_id ?>' method='post' class='form-inline'>
	<div class="row-fluid">
		<div class='alert alert-success'>
			<h4>Package Details</h4>
		</div>
	</div>	
	<fieldset >
		<input type="hidden" name="orig_package_name" id="orig_package_name" value="<?= $package->product_name ?>" readonly>
		<input type="hidden" name="orig_product_code" id="orig_product_code" value="<?= $package->product_code ?>" readonly>
		<div class="row-fluid">
			<div class="span11">
				<div class="control-group <?= $this->form_validation->error_class('product_type_id') ?>">
					<label class="control-label" for="product_type_id"><strong>Type <em>*</em></strong></label>
					<div class="controls">
						<?php
							foreach($package_types as $p)
							{
								$options[$p->product_type_id] = $p->name;
							}
							echo form_dropdown('product_type_id', $options, set_value('product_type_id', $package->product_type_id),'class="span4"');
						?>
						<p class="help-block"><?= $this->form_validation->error('product_type_id'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('product_code') ?>">
					<label class="control-label" for="product_code"><strong>Product Code <em>*</em></strong></label>
					<div class="controls">
						<input type="text" class='span4' placeholder="Product Code" name="product_code" id="product_code" value="<?= set_value('product_code', $package->product_code) ?>">
						<p class="help-block"><?= $this->form_validation->error('product_code'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('package_name') ?>">
					<label class="control-label" for="package_name"><strong>Package Name <em>*</em></strong></label>
					<div class="controls">
						<input type="text" class='span4' placeholder="Package Name" name="package_name" id="package_name" value="<?= $this->form_validation->set_value('package_name', $package->product_name) ?>">
						<p class="help-block"><?= $this->form_validation->error('package_name'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('product_description') ?>">
					<label class="control-label" for="product_description"><strong>Product Description <em>*</em></strong></label>
					<div class="controls">
						<textarea class='span4' placeholder="Product Description" name="product_description" id="product_description" rows="5"><?= $this->form_validation->set_value('product_description', $package->product_description) ?></textarea>
						<p class="help-block"><?= $this->form_validation->error('product_description'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
					<label class="control-label" for="is_active"><strong>Active? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_active',$product_line_options,set_value('is_active',$package->is_active),'id="is_active" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_vatable') ?>">
					<label class="control-label" for="is_vatable"><strong>Vatable? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_vatable',$product_line_options,set_value('is_vatable',$package->is_vatable),'id="is_vatable" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_vatable'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_display') ?>">
					<label class="control-label" for="is_display"><strong>Displayed? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_display',$product_line_options,set_value('is_display',$package->is_display),'id="is_display" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_display'); ?></p>
					</div>
				</div>
				<div class="control-group <?= $this->form_validation->error_class('is_gc_buyable') ?>">
					<label class="control-label" for="is_gc_buyable"><strong>GC Buyable? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_gc_buyable',$product_line_options,set_value('is_gc_buyable',$package->is_gc_buyable),'id="is_gc_buyable" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_gc_buyable'); ?></p>
					</div>
				</div>
				<div id="gc_exclusive_sec" class="control-group <?= $this->form_validation->error_class('is_gc_exclusive') ?>" style="display:none;">
					<label class="control-label" for="is_gc_exclusive"><strong>GC Exclusive? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array("0" => "No","1" => "Yes");
						echo form_dropdown('is_gc_exclusive',$product_line_options,set_value('is_gc_exclusive',$package->is_gc_exclusive),'id="is_gc_exclusive" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_gc_exclusive'); ?></p>
					</div>
				</div>
				<?php if($cpoints_switch): ?>
				<div class="control-group <?= $this->form_validation->error_class('is_cpoints_buyable')?>">
					<label class="control-label" for="is_cpoints_buyable"><strong>C Points Buyable? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select', "1" => "Yes", "0" => "No");
						echo form_dropdown('is_cpoints_buyable', $product_line_options, set_value('is_cpoints_buyable', $package->is_cpoints_buyable), 'id="is_cpoints_buyable" style="width:auto;"');
						?>
						<p class='help-block'><?= $this->form_validation->error('is_cpoints_buyable'); ?></p>
					</div>
				</div>
				<?php endif; ?>
				<div class="control-group <?= $this->form_validation->error_class('is_visible') ?>">
					<label class="control-label" for="is_visible"><strong>Visible in the Corporate Site? <em>*</em></strong></label>
					<div class="controls">
						<?php
						$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
						echo form_dropdown('is_visible',$product_line_options,set_value('is_visible',$package->is_visible),'id="is_visible" style="width: auto;"');
						?>
						<p class="help-block"><?= $this->form_validation->error('is_visible'); ?></p>
					</div>
				</div>
			</div>
		</div>
		<br>
		</fieldset>
		<div class="row-fluid">
			<div class='alert alert-success'>
				<h4>Products List</h4>
			</div>
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
					<?php if($package_products == ''): ?>
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
					<?php else: ?>
					<?php foreach($package_products as $package_product):?>
						<tr>
							<td class="item" style="padding: 4px 5px;"><?= $product_options[$package_product->child_product_id] ?><input type="hidden" name="product_name[]" value="<?= $package_product->child_product_id ?>" style="width:auto;" ></td>
							<td class="swappable" style="padding: 4px 5px;"><?= ($package_product->is_swappable == 0) ? "No" : "Yes" ?><input type="hidden" name="is_swappable[]" value="<?= $package_product->is_swappable ?>" style="width:auto;" ></td>
							<td class="quantity" style="padding: 4px 5px;"><?= $package_product->quantity ?><input type="hidden" name="quantity[]" value="<?= $package_product->quantity ?>" style="width:auto;" ></td>
							<?php
							
							$group = $package_product->group;
							$group_qty = $package_product->group_quantity;
							$group_text = "";
							if($group != 0) $group_text = "<br/>(Can select up to {$group_qty} item(s))";
							
							?>
							<td class="group" style="width:150px; padding: 4px 5px;"><?= $package_product->group ?><?= $group_text; ?><input type="hidden" name="group[]" value="<?= $package_product->group ?>" style="width:auto;" readonly="readonly" ><input class="group_qty" type="hidden" name="group_qty[]" value="<?= $package_product->group_quantity ?>" style="width:auto;" readonly="readonly" ></td>
							<td style="padding: 4px 5px;"><a class="btn btn-danger rmv_product" title="Remove"><i class="icon-remove icon-white"></i></a></td>
						</tr>
					<?php endforeach;?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<br>
		
		</fieldset>
		<div class="row-fluid">
			<div class='alert alert-success'>
				<h4>Prices</h4>
			</div>
		</div>	
		<fieldset>
			<div class="control-group <?= $this->form_validation->error_class('standard_retail_price') ?>">
				<label class="control-label" for="standard_retail_price"><strong>Standard Retail Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="Standard Retail Price" name="standard_retail_price" id="standard_retail_price" value="<?= set_value('standard_retail_price',$package->standard_retail_price) ?>">
					<p class="help-block"><?= $this->form_validation->error('standard_retail_price'); ?></p>
				</div>
			</div>
			<?php if($cpoints_switch): ?>
			<div class="control-group <?= $this->form_validation->error_class('cpoints_value') ?>">
				<label class="control-label" for="cpoints_value"><strong>C Points Value <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="C Points Value" name="cpoints_value" id="cpoints_value" value="<?= set_value('cpoints_value', $package->cpoints_value) ?>">
					<p class="help-block"><?= $this->form_validation->error('cpoints_value'); ?></p>
				</div>
			</div>
			<?php endif; ?>
			<div class="control-group <?= $this->form_validation->error_class('igpsm_points') ?>">
				<label class="control-label" for="igpsm_points"><strong>IGPSM Points <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="IGPSM Points" name="igpsm_points" id="igpsm_points" value="<?= set_value('igpsm_points',$package->igpsm_points) ?>">
					<p class="help-block"><?= $this->form_validation->error('igpsm_points'); ?></p>
				</div>
			</div>	
		</fieldset>
	<hr/>
	<div class="controls" align="right">			
		<button type='submit' class="btn btn-primary">Update Package</button>
		<a  href="/admin/products" class="btn">Cancel</a>
	</div>
	
</form>
<?php endif; ?>
<script type="text/javascript">

	var swap_group = <?= $swap_group ?>;
	var product_options_html = "<?= $product_options_html; ?>";
	var product_options = <?= json_encode($product_options); ?>;
	
	$(document).ready(function(){
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
		
		var new_product_modal = b.modal.new({
			title: "Add New Product",
			html: "<label style='display:inline;'><strong>Type: </strong></label><select id='entry_type' style='width: auto;'><option value='0'>Individual Product</option><option value='1'>Variant Group</option></select>",
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
	
	/*$("#add_product").click(function(){
		
		var error_msg = "";

		if($('#product_name_input').val() == '')
		{
			error_msg = "<span>Please select a product.</span>";
			$('#product_input_error').html(error_msg);
		}
		else
		{
			var row_class ="nonswap_0";
			var qty = 0;
			var html = "";
			var product_details_modal = b.modal.new({});
			
			if($('#is_swappable_input').val() == 0)
			{
				html = '\
						<form class="form-horizontal" style="margin:0px;">\n\
							<div class="row-fluid">\n\
								<div class="control-group">\n\
									<label class="control-label" for="quantity" style="width: auto; margin-right: 20px;">Quantity </label>\n\
									<div class="controls" style="margin-left:0px;"><input type="text" class="span2 modal_qty" id="quantity" name="quantity" value="1"/></div>\n\
								</div>\n\
							</div>\n\
						</form>';
			}
			else if($('#is_swappable_input').val() == 1)
			{
				max_group = _.size(swap_group) + 1;
				
				var options = "";
				var modal_qty = 1;
				
				if(_.size(swap_group) == 0)
				{
					options = options.concat('<option value="1">1</option>');
				}
				else
				{
					options = options.concat('<option value="1">1</option>');
					modal_qty = swap_group[1];
					_.each(swap_group,function(num, key){ options = options.concat('<option value="'+(key+1)+'">'+(key+1)+'</option>'); });
					
				}
				
				html = '\
						<form class="form-horizontal">\n\
							<div class="row-fluid">\n\
								<div class="control-group">\n\
									<label class="control-label" for="quantity" style="width: auto; margin-right: 20px;">Variant Group </label>\n\
									<div class="controls" style="margin-left:0px;"><select id="variant" name="variant" class="modal_variant" style="width:auto;">'+options+'</select></div>\n\
								</div>\n\
							</div>\n\
							<div class="row-fluid">\n\
								<div class="control-group">\n\
									<label class="control-label" for="quantity" style="width: auto; margin-right: 20px;">Group Quantity </label>\n\
									<div class="controls" style="margin-left:0px;"><input type="text" class="span2 modal_qty" id="quantity" name="quantity" value="'+modal_qty+'"/></div>\n\
								</div>\n\
							</div>\n\
						</form>';
			}
			
			product_details_modal.init({
				title: "Product Details: "+$('#product_name_input option:selected').text(),
				width: 350,
				html: html,
				buttons: {
					"Add Product": function(){
						
						var variant = 0;
						var qty = $('#'+$(product_details_modal).attr("id")+' #quantity').val();
						var group_qty = 0;
						var group_text = "";
						
						if($('#is_swappable_input').val() == 1)
						{
							variant = $('#'+$(product_details_modal).attr("id")+' #variant').val();
							if(_.isUndefined(swap_group[variant]))
							{
								swap_group[variant] = 0;
								swap_group[variant] = qty;
								group_qty = swap_group[variant]
								qty = 1;
								group_text = "<br/>(Can select up to "+group_qty+" item(s))";
								row_class = "swap_"+variant;
								
							}
							else
							{
								swap_group[variant] = qty;
								group_qty = swap_group[variant]
								qty = 1;
								group_text = "<br/>(Can select up to "+group_qty+" item(s))";
								row_class = "swap_"+variant;
								
								$(".swap_"+variant).each(function(){
									$(this).find(".group").html(variant+''+group_text+'<input type="hidden" name="group[]" value="'+variant+'" style="width:auto;" readonly="readonly" ><input class="group_qty" type="hidden" name="group_qty[]" value="'+group_qty+'" style="width:auto;" readonly="readonly" >');
								});
							}
							
							
						}
						
						$("#products").append('\
							<tr class="'+row_class+'">\n\
								<td class="item" style="padding: 4px 5px;">'+$('#product_name_input option:selected').text()+'<input type="hidden" name="product_name[]" value="'+$('#product_name_input').val()+'" style="width:auto;" readonly="readonly" ></td>\n\
								<td class="swappable" style="width:150px; padding: 4px 5px;">'+$('#is_swappable_input option:selected').text()+'<input type="hidden" name="is_swappable[]" value="'+$('#is_swappable_input').val()+'" style="width:auto;" readonly="readonly" ></td>\n\
								<td class="quantity" style="width:150px; padding: 4px 5px;">'+qty+'<input type="hidden" name="quantity[]" value="'+qty+'" style="width:auto;" ></td>\n\
								<td class="group" style="width:150px; padding: 4px 5px;">'+variant+''+group_text+'<input type="hidden" name="group[]" value="'+variant+'" style="width:auto;" readonly="readonly" ><input class="group_qty" type="hidden" name="group_qty[]" value="'+group_qty+'" style="width:auto;" readonly="readonly" ></td>\n\
								<td style="padding: 4px 5px;"><a class="btn btn-danger rmv_product" title="Remove"><i class="icon-remove icon-white"></i></a></td>\n\
							</tr>');
						product_details_modal.hide();
						$('#product_input_error').html(error_msg);
						$('#product_name_input').val('');
						$('#is_swappable_input').val(0);
					}
				}
			});
			
			product_details_modal.show();
			
			
		}
	});
	*/
	$(".return-btn").click(function(){
		redirect('/admin/packages');	
		return false;
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

	$("#submit_package").click(function(){
		$('.cancel_edit_product').trigger('click');
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