<form action='/admin/employee_slots/edit' method='post' class='form-inline'>
<fieldset >
	<input type='hidden' id='orig_product_id' name='orig_product_id' value='<?= $product->product_id ?>' readonly/>
	<div class="control-group <?= $this->form_validation->error_class('product_name') ?>">
		<label class="control-label" for="product_name"><strong>Product</strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="product_name" id="product_name" value="<?= set_value('product_name',$product->product_name) ?>" readonly> 			
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('qty') ?>">
		<label class="control-label" for="qty"><strong>Quantity <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="qty" id="qty" value="<?= set_value('qty',$product->employee_slots) ?>"> 
		</div>
		<span class='label label-important' id='qty_error' style='display:none;'>Quantity is required</span>
	</div>
</fieldset>
</form>
