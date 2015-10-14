<form id="submit_form" action='/admin/voucher_types/add' method='post' class='form-inline'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('facility_name') ?>">
		<label class="control-label" for="code"><strong>Code <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Voucher Type Code here..." name="voucher_type_code" id="voucher_type_code" value="<?= set_value('code') ?>">
		</div>
		<span class='label label-important' id='voucher_type_code_error' style='display:none;'>Voucher Type Code Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('facility_description') ?>">
		<label class="control-label" for="facility_description"><strong>Name <em>*</em></strong></label>
		<div class="controls">
			<textarea class='span4' rows="2" placeholder="Voucher Type Name here..." name="voucher_type_name" id="voucher_type_name" ></textarea>		
		</div>
		<span class='label label-important' id='voucher_type_name_error' style='display:none;'>Voucher Type Name Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('facility_address') ?>">
		<label class="control-label" for="description"><strong>Description <em>*</em></strong></label>
		<div class="controls">
			<textarea class='span4' rows="3" placeholder="Description here..." name="description" id="description"></textarea> 			
		</div>
		<span class='label label-important' id='description_error' style='display:none;'>Description Field is required.</span>
	</div>	
</fieldset>
</form>
