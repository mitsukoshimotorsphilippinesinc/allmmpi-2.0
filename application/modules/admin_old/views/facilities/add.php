<h2>Add New Facility  <a href='/admin/facilities' class='btn btn-large' >Back</a></h2>
<hr/>
<form action='/admin/facilities/add' method='post' class='form-horizontal'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('facility_name') ?>">
		<label class="control-label" for="facility_name">Facility Name <em>*</em></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="facility_name" id="facility_name" value="<?= set_value('facility_name') ?>"> 
			<p class="help-block"><?= $this->form_validation->error('facility_name'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('facility_description') ?>">
		<label class="control-label" for="facility_description">Description </label>
		<div class="controls">
			<textarea class='span4' rows="4" placeholder="" name="facility_description" id="facility_description" ></textarea>
			<p class="help-block"><?= $this->form_validation->error('facility_description'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('facility_address') ?>">
		<label class="control-label" for="facility_address">Address</label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="facility_address" id="facility_address" value="<?= set_value('facility_address') ?>"> 
			<p class="help-block"><?= $this->form_validation->error('facility_address'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('facility_contact_details') ?>">
		<label class="control-label" for="facility_contact_details">Contact Details</label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="facility_contact_details" id="facility_contact_details" value="<?= set_value('facility_contact_details') ?>"> 
			<p class="help-block"><?= $this->form_validation->error('facility_contact_details'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('facility_type_id'); ?>">
		<label class="control-label" for="is_current">Facility Type </label>
		<div class="controls">
			<?php

				$options = array('1' => 'Warehouse', '2' => 'Depot');
				$extra = "id='facility_type_id' style='width:auto;'";
				echo form_dropdown('facility_type_id', $options, null, $extra);

			?>
			<p class="help-block"><?= $this->form_validation->error('facility_type_id'); ?></p>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Add New Facility</button>
		</div>
	</div>
</fieldset>
</form>
