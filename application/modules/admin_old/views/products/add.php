
<div class='alert alert-info'><h3>Add New Product <a href="/admin/products" class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Products Dashboard</a></h3></div>

<form action='/admin/products/add/product/<?= $product_type_id; ?>' method='post' class='form-inline'>
	<div class='alert alert-success'>
		<h4>Product Details</h4>
	</div>
	<fieldset >
		<input type="hidden" name="product_type_id" id="product_type_id" value="<?= $product_type_id; ?>" readonly>
		<div class="control-group" style="width:1250px">
			<label class="control-label" for="item_type_id"><strong>Type <em>*</em></strong></label>
			<div class="controls">
				<?php
					$pt = $types[$product_type_id];
				?>
				<input type="text" value="<?= $pt->name; ?>" readonly>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('item_id') ?>">
			<label class="control-label" for="item_id"><strong>Item <em>*</em></strong></label>
			<div class="controls">
				<?php
				$item_options = array('' => 'Please Select an Item');
				if($product_type_id == 13 || $is_cpoints == 1)
				{
					$item_options[0] = 'NONE';
				}
				foreach($items as $item) $item_options[$item->item_id] = $item->item_name;
				
				echo form_dropdown('item_id',$item_options,set_value('item_id'),'id="item_id" style="width: auto;"');
				?>
				<input type='hidden' id='single_product_name' name='single_product_name' value=''>
				<p class="help-block"><?= $this->form_validation->error('item_id'); ?></p>
			</div>
		</div>
		<?php if($product_type_id == 13 || $is_cpoints == 1): ?>
		<div id="product_name_sec" class="control-group <?= $this->form_validation->error_class('product_name') ?>" style="width:1250px">
			<label class="control-label" for="product_name"><strong>Product Name <em>*</em></strong></label>
			<div class="controls">
				<input id="product_name" name="product_name" type="text" value="">
				<p class="help-block"><?= $this->form_validation->error('product_name'); ?></p>
			</div>
		</div>
		<?php endif; ?>
		<div id="product_line_sec" class="control-group <?= $this->form_validation->error_class('product_line_id') ?>">
			<label class="control-label" for="product_line_id"><strong>Product Line <em>*</em></strong></label>
			<div class="controls">
				<?php

				$product_line_options = array('' => 'Please Select a Product Line');

				foreach($product_lines as $pl)
				{
					$product_line_options[0] = "MARKETING MATERIAL";
					$product_line_options[$pl->product_line_id] = $pl->product_line;
				}
				echo form_dropdown('product_line_id',$product_line_options,set_value('product_line_id'),'id="product_line_id" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('product_line_id'); ?></p>
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
		<div class="control-group <?= $this->form_validation->error_class('is_cpoints_buyable')?>">
			<label class="control-label" for="is_cpoints_buyable"><strong>C Points Buyable? <em>*</em></strong></label>
			<div class="controls">
				<?php
				//$product_line_options = array('' => 'Please Select', "1" => "Yes", "0" => "No");
				$product_line_options = array("0" => "No");
				echo form_dropdown('is_cpoints_buyable', $product_line_options, set_value('is_cpoints_buyable'), 'id="is_cpoints_buyable" style="width:auto;"');
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
				echo form_dropdown('is_product_rebate',$product_line_options,set_value('is_product_rebate'),'id="is_product_rebate" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_product_rebate'); ?></p>
			</div>
		</div>
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
	</fieldset>
	<div class='alert alert-success'>
		<h4>Prices</h4>
	</div>
	<fieldset>
		<?php if($product_type_id == 13): ?>
		<div class="control-group <?= $this->form_validation->error_class('is_variable_price') ?>">
			<label class="control-label" for="is_variable_price"><strong>Is the price variable? <em>*</em></strong></label>
			<div class="controls">
				<?php
				$variable_options = array('' => 'Please Select',"1" => "Yes","0" => "No");
				echo form_dropdown('is_variable_price',$variable_options,set_value('is_variable_price'),'id="is_variable_price" style="width: auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('is_variable_price'); ?></p>
			</div>
		</div>
		<?php endif; ?>
		<div id="price_sec">
			<div class="control-group <?= $this->form_validation->error_class('standard_retail_price') ?>">
				<label class="control-label" for="standard_retail_price"><strong>Standard Retail Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="Standard Retail Price" name="standard_retail_price" id="standard_retail_price" value="<?= set_value('standard_retail_price') ?>">
					<p class="help-block"><?= $this->form_validation->error('standard_retail_price'); ?></p>
				</div>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('member_price') ?>">
				<label class="control-label" for="member_price"><strong>Member's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="Member's Price" name="member_price" id="member_price" value="<?= set_value('member_price') ?>">
					<p class="help-block"><?= $this->form_validation->error('member_price'); ?></p>
				</div>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('employee_price') ?>">
				<label class="control-label" for="employee_price"><strong>Employee's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="Employee's Price" name="employee_price" id="employee_price" value="<?= set_value('employee_price') ?>">
					<p class="help-block"><?= $this->form_validation->error('employee_price'); ?></p>
				</div>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('giftcheque_standard_retail_price') ?>">
				<label class="control-label" for="giftcheque_standard_retail_price"><strong>GC Standard Retail Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="GC Standard Retail Price" name="giftcheque_standard_retail_price" id="giftcheque_standard_retail_price" value="<?= set_value('giftcheque_standard_retail_price') ?>">
					<p class="help-block"><?= $this->form_validation->error('giftcheque_standard_retail_price'); ?></p>
				</div>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('giftcheque_member_price') ?>">
				<label class="control-label" for="giftcheque_member_price"><strong>GC Member's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="GC Member's Price" name="giftcheque_member_price" id="giftcheque_member_price" value="<?= set_value('giftcheque_member_price') ?>">
					<p class="help-block"><?= $this->form_validation->error('giftcheque_member_price'); ?></p>
				</div>
			</div>
			<div class="control-group <?= $this->form_validation->error_class('giftcheque_employee_price') ?>">
				<label class="control-label" for="giftcheque_employee_price"><strong>GC Employee's Price <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="GC Employee's Price" name="giftcheque_employee_price" id="giftcheque_employee_price" value="<?= set_value('giftcheque_employee_price') ?>">
					<p class="help-block"><?= $this->form_validation->error('giftcheque_employee_price'); ?></p>
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
				<label class="control-label" for="igpsm_points"><strong>IGPSM Points <em>*</em></strong></label>
				<div class="controls">
					<input type="text" class='span2' placeholder="IGPSM Points" name="igpsm_points" id="igpsm_points" value="<?= set_value('igpsm_points') ?>">
					<p class="help-block"><?= $this->form_validation->error('igpsm_points'); ?></p>
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
<script type="text/javascript">
	$(document).ready(function(){
		$("#item_id").trigger("change");
		$("#is_variable_price").trigger("change");
		$("#is_gc_buyable").trigger("change");
	});
	
	$("#item_id").on('change', function(){
		var item_name =  $("#item_id option:selected").text();
		$("#single_product_name").val(item_name);
	});
	
	var get_items = function(product_name,ajax_call)
	{
		beyond.request({
			url: '/admin/products/get_items',
			data: {
				'product_name': product_name
			},
			on_success: function(data)
			{
				if(data.status == "ok")
				{
					var items = _.map(data.data,function(item){
						return {
							label: item.product_name,
							value: item.product_name,
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

	$("#item_id").change(function(){
		var item_id = $(this).val();
		
		if(parseInt(item_id) === 0)
		{
			$("#product_line_sec").hide();
			$("#product_line_id").val("");
			$("#product_name_sec").show();
		}
		else
		{
			$("#product_line_sec").show();
			$("#product_name_sec").hide();
			$("#product_name").val("");
		}
	});
	
	$("#is_variable_price").change(function(){
		var is_variable_price = $(this).val();
		if(parseInt(is_variable_price) === 0 || _.isEmpty(is_variable_price))
		{
			$("#price_sec").show();
			$("#standard_retail_price").val("");
			$("#member_price").val("");
			$("#employee_price").val("");
			$("#igpsm_points").val("");
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