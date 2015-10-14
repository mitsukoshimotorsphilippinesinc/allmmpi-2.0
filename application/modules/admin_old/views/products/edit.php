<div class='alert alert-info'><h3>Edit Product <a href="/admin/products" class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Products Dashboard</a></h3></div>
<?php if (empty($product)): ?>
<div class='alert alert-error' align="center"><h3>Product not found.</h3></div>
<?php else: ?>
<?php
	$upload_url = $this->config->item("media_url") . "/products";
	$_upload_url = urlencode($upload_url);
?>
<form action='/admin/products/edit/<?= $product->product_id ?>' method='post' class='form-inline'>
	<div class='alert alert-success'>
		<h4>Product Details</h4>
	</div>
	<fieldset >
		<input type="hidden" name="orig_item_id" id="orig_item_id" value="<?= $product->item_id ?>" readonly>
		<input type="hidden" name="product_type_id" id="product_type_id" value="<?= $product->product_type_id ?>" readonly>
		<input type="hidden" name="orig_product_code" id="orig_product_code" value="<?= $product->product_code ?>" readonly>
		<div class="control-group" style="width:1250px">
			<label class="control-label" for="item_type_id"><strong>Type <em>*</em></strong></label>
			<div class="controls">
				<?php
					$pt = $types[$product->product_type_id];
				?>
				<input type="text" value="<?= $pt->name; ?>" readonly>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('product_code') ?>">
			<label class="control-label" for="product_code"><strong>Product Code <em>*</em></strong></label>
			<div class="controls">
				<input type="text" placeholder="Product Code" name="product_code" id="product_code" value="<?= $product->product_code; ?>" >
				<p class="help-block"><?= $this->form_validation->error('product_code'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('item_id') ?>">
			<label class="control-label" for="item_id"><strong>Item <em>*</em></strong></label>
			<div class="controls">
				<?php if($product->item_id > 0): ?>
					<input type="text" name="item_name" id="item_name" value="<?= $product->item_name ?>" readonly>
				<?php else: ?>
					<input type="text" name="product_name" id="product_name" value="<?= $product->product_name ?>">
				<?php endif; ?>
				<input type="hidden" name="item_id" id="item_id" value="<?= $product->item_id ?>" readonly>
				<p class="help-block"><?= $this->form_validation->error('item_id'); ?></p>
			</div>
		</div>
		<?php
		$style = 'style="display:none"'; 
		if($product->item_id > 0)
			$style = '';
			
		?>
		<div class="control-group <?= $this->form_validation->error_class('product_line_id') ?>" <?= $style; ?>>
			<label class="control-label" for="product_line_id"><strong>Product Line <em>*</em></strong></label>
			<div class="controls">
				<?php

				$product_line_options = array('' => 'Please Select a Product Line');

				foreach($product_lines as $pl)
				{
					$product_line_options[0] = "MARKETING MATERIAL";
					$product_line_options[$pl->product_line_id] = $pl->product_line;
				}
				echo form_dropdown('product_line_id',$product_line_options,set_value('product_line_id',$product->product_line_id),'id="product_line_id" style="width:auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('product_line_id'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('product_description') ?>" <?= $style; ?> >
			<label class="control-label" for="product_description"><strong>Product Description <em>*</em></strong></label>
			<div class="controls">
				<textarea class='span4' placeholder="Product Description" name="product_description" id="product_description" rows="5" readonly><?= $product->product_description; ?></textarea>
				<p class="help-block"><?= $this->form_validation->error('product_description'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active"><strong>Active? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_active',$product_line_options,set_value('is_active',$product->is_active),'id="is_active" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_vatable') ?>">
			<label class="control-label" for="is_vatable"><strong>Vatable? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_vatable',$product_line_options,set_value('is_vatable',$product->is_vatable),'id="is_vatable" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_vatable'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_display') ?>">
			<label class="control-label" for="is_display"><strong>Displayed? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_display',$product_line_options,set_value('is_display',$product->is_display),'id="is_display" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_display'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_gc_buyable') ?>">
			<label class="control-label" for="is_gc_buyable"><strong>GC Buyable? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_gc_buyable',$product_line_options,set_value('is_gc_buyable',$product->is_gc_buyable),'id="is_gc_buyable" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_gc_buyable'); ?></p>
			</div>
		</div>
		<div id="gc_exclusive_sec" class="control-group <?= $this->form_validation->error_class('is_gc_exclusive') ?>" style="display:none;">
			<label class="control-label" for="is_gc_exclusive"><strong>GC Exclusive? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$product_line_options = array("0" => "No","1" => "Yes");
				echo form_dropdown('is_gc_exclusive',$product_line_options,set_value('is_gc_exclusive',$product->is_gc_exclusive),'id="is_gc_exclusive" style="width: auto;"');
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
				echo form_dropdown('is_cpoints_buyable', $product_line_options, set_value('is_cpoints_buyable', $product->is_cpoints_buyable), 'id="is_cpoints_buyable" style="width:auto;"');
				?>
				<p class='help-block'><?= $this->form_validation->error('is_cpoints_buyable'); ?></p>
			</div>
		</div>
		<?php endif; ?>
		<div class="control-group <?= $this->form_validation->error_class('is_product_rebate') ?>">
			<label class="control-label" for="is_product_rebate"><strong>Product Rebate Buyable? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_product_rebate',$product_line_options,set_value('is_product_rebate',$product->is_product_rebate),'id="is_product_rebate" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_product_rebate'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_visible') ?>">
			<label class="control-label" for="is_visible"><strong>Visible in the Corporate Site? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$product_line_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_visible',$product_line_options,set_value('is_visible',$product->is_visible),'id="is_visible" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_visible'); ?></p>
			</div>
		</div>
	</fieldset>
	<div class='alert alert-success'>
		<h4>Prices</h4>
	</div>
	<fieldset>
		<?php if($product->product_type_id == 13): ?>
		<div class="control-group <?= $this->form_validation->error_class('is_variable_price') ?>">
			<label class="control-label" for="is_variable_price"><strong>Is the price variable? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$variable_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_variable_price',$variable_options,set_value('is_variable_price',$product->is_variable_price),'id="is_variable_price" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_variable_price'); ?></p>
			</div>
		</div>
		<?php endif; ?>
		<div id="price_sec">
			<div class="control-group <?= $this->form_validation->error_class('standard_retail_price') ?>">
				<label class="control-label" for="standard_retail_price"><strong>Standard Retail Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="Standard Retail Price" name="standard_retail_price" id="standard_retail_price" value="<?= set_value('standard_retail_price',$product->standard_retail_price) ?>">
					<p class="help-block"><?= $this->form_validation->error('standard_retail_price'); ?></p>
				</div>
				<span class='label label-important' id='standard_retail_price_error' style='display:none;'>Standard Retail Price Field is required.</span>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('member_price') ?>">
				<label class="control-label" for="member_price"><strong>Member's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="Member's Price" name="member_price" id="member_price" value="<?= set_value('member_price',$product->member_price) ?>">
					<p class="help-block"><?= $this->form_validation->error('member_price'); ?></p>
				</div>
				<span class='label label-important' id='member_price_error' style='display:none;'>Member's Price Field is required.</span>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('employee_price') ?>">
				<label class="control-label" for="employee_price"><strong>Employee's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="Employee's Price" name="employee_price" id="employee_price" value="<?= set_value('employee_price',$product->employee_price) ?>">
					<p class="help-block"><?= $this->form_validation->error('employee_price'); ?></p>
				</div>
				<span class='label label-important' id='employee_price_error' style='display:none;'>Employee's Price Field is required.</span>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('giftcheque_standard_retail_price') ?>">
				<label class="control-label" for="giftcheque_standard_retail_price"><strong>GC Standard Retail Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="GC Standard Retail Price" name="giftcheque_standard_retail_price" id="giftcheque_standard_retail_price" value="<?= set_value('giftcheque_standard_retail_price',$product->giftcheque_standard_retail_price) ?>">
					<p class="help-block"><?= $this->form_validation->error('giftcheque_standard_retail_price'); ?></p>
				</div>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('giftcheque_member_price') ?>">
				<label class="control-label" for="giftcheque_member_price"><strong>GC Member's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="GC Member's Price" name="giftcheque_member_price" id="giftcheque_member_price" value="<?= set_value('giftcheque_member_price',$product->giftcheque_member_price) ?>">
					<p class="help-block"><?= $this->form_validation->error('giftcheque_member_price'); ?></p>
				</div>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('giftcheque_employee_price') ?>">
				<label class="control-label" for="giftcheque_employee_price"><strong>GC Employee's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="GC Employee's Price" name="giftcheque_employee_price" id="giftcheque_employee_price" value="<?= set_value('giftcheque_employee_price',$product->giftcheque_employee_price) ?>">
					<p class="help-block"><?= $this->form_validation->error('giftcheque_employee_price'); ?></p>
				</div>
			</div>
			<?php if($cpoints_switch): ?>
			<div class="control-group <?= $this->form_validation->error_class('cpoints_value') ?>">
				<label class="control-label" for="cpoints_value"><strong>C Points Value <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="C Points Value" name="cpoints_value" id="cpoints_value" value="<?= set_value('cpoints_value', $product->cpoints_value) ?>">
					<p class="help-block"><?= $this->form_validation->error('cpoints_value'); ?></p>
				</div>
			</div>
			<?php endif; ?>
			<div class="control-group <?= $this->form_validation->error_class('igpsm_points') ?>">
				<label class="control-label" for="igpsm_points"><strong>IGPSM Points <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="IGPSM Points" name="igpsm_points" id="igpsm_points" value="<?= set_value('igpsm_points',$product->igpsm_points) ?>">
				</div>
				<span class='label label-important' id='igpsm_points_error' style='display:none;'>IGPSM Points Field is required.</span>
			</div>
		</div>
		<hr/>
		<div class="control-group" align="right">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Save</button>
				<a href="/admin/products" class="btn return-btn">Cancel</a>
			</div>
		</div>
	</fieldset>
</form>
<?php endif; ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#is_variable_price").trigger("change");
		$("#is_gc_buyable").trigger("change");
	});
	var get_items = function(item_name,ajax_call)
	{
		beyond.request({
			url: '/admin/products/get_items',
			data: {
				'item_name': item_name
			},
			on_success: function(data)
			{
				if(data.status == "ok")
				{
					var items = _.map(data.data,function(item){
						return {
							label: item.item_name,
							value: item.item_name,
							item_id: item.item_id
						};
					});
					if(_.isFunction(ajax_call)) ajax_call.call(this,items);
				}
			}
		});
	};
	
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
	
	$("#item_name").keyup(function(){
		/*if($(this).val() != "")
		{
			get_items($(this).val(),function(data){
				$("#item_name").autocomplete({
					source: data,
					focus: function()
					{
						return false;
					},
					select: function(event, ui)
					{
						$("#item_name").val(ui.item.label);
					}
				});
			});
		}*/
	});
	
	$("#is_variable_price").change(function(){
		var is_variable_price = $(this).val();
		if(parseInt(is_variable_price) === 0 || _.isEmpty(is_variable_price))
		{
			$("#price_sec").show();
		}
		else
		{
			$("#price_sec").hide();
			$("#standard_retail_price").val(0.00);
			$("#member_price").val(0.00);
			$("#employee_price").val(0.00);
			$("#igpsm_points").val(0.00);
		}
	});
</script>