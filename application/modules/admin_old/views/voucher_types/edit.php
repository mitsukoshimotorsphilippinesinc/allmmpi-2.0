<?php if (empty($voucher_type)): ?>
	<h3>Voucher Type not found.</h3>
<?php else: ?>
<form id="submit_form" action='/admin/voucher_types/edit/<?= $voucher_type->voucher_type_id ?>' method='post' class='form-inline'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('voucher_type_code') ?>">
		<label class="control-label" for="voucher_type_code"><strong>Code <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="voucher_type_code" disabled='disabled' id="voucher_type_code" value="<?= set_value('voucher_type_code', $voucher_type->code) ?>"> 		
		</div>
		<span class='label label-important' id='facility_name_error' style='display:none;'>Voucher Type Code Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('voucher_type_name') ?>">
		<label class="control-label" for="voucher_type_code"><strong>Name <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="voucher_type_name" id="voucher_type_name" value="<?= set_value('voucher_type_name', $voucher_type->name) ?>"> 		
		</div>
		<span class='label label-important' id='facility_name_error' style='display:none;'>Voucher Type Name Field is required.</span>
	</div>
	
	<div class="control-group <?= $this->form_validation->error_class('description') ?>">
		<label class="control-label" for="description"><strong>Description <em>*</em></strong></label>
		<div class="controls">
			<textarea rows="4" class='span4' placeholder="" name="description" id="description"><?= set_value('description', $voucher_type->description) ?></textarea>		
		</div>
		<span class='label label-important' id='description_error' style='display:none;'>Description Field is required.</span>
	</div>
</fieldset>
</form>
<?php endif; ?>
