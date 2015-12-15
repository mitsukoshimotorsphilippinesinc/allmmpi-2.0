<!--?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/repair_hardwares";
	//$upload_url ="c:/laragon/wwww/allmppi/webroot_admin/assets/media/repair_hardwares";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?-->

<!--?= $breadcrumb_container; ?-->
<h2>Edit repair_hardware  <a href='/information_technology/maintenance/repair_hardware' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($repair_hardware_details)): ?>
<h3>results not found.</h3>
<?php else: ?>
<form action='/information_technology/maintenance/edit_repair_hardware/<?= $repair_hardware_details->repair_hardware_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('hardware_name') ?>">
			<label class="control-label" for="hardware_name">Hardware Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Repair Name" readonly="readonly" name="hardware_name" id="hardware_name" value="<?= $this->form_validation->set_value('hardware_name',$repair_hardware_details->repair_hardware_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('hardware_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('description') ?>">
			<label class="control-label" for="description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="description" id="description" value="<?= $this->form_validation->set_value('description',$repair_hardware_details->description) ?>">
				<p class="help-block"><?= $this->form_validation->error('description'); ?></p>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update Harware</button>
			</div>
		</div>
	</fieldset>
</form>
<?php endif; ?>
