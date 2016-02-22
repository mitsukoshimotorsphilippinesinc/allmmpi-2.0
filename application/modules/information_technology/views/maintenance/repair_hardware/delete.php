<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Hardware  <a href='/information_technology/maintenance/repair_hardware' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<?php if (empty($repair_hardware_details)): ?>
<h3>results not found.</h3>
<?php else: ?>
<form action='/information_technology/maintenance/delete_repair_hardware/<?= $repair_hardware_details->repair_hardware_id ?>' method='post' class='form-horizontal'>
	<fieldset >		
		<div class="control-group <?= $this->form_validation->error_class('hardware_name') ?>">
			<label class="control-label" for="hardware_name">Hardware Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Hardware Name" readonly="readonly" name="hardware_name" id="hardware_name" value="<?= $this->form_validation->set_value('hardware_name',$repair_hardware_details->repair_hardware_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('hardware_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('description') ?>">
			<label class="control-label" for="description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" readonly="readonly" name="description" id="description" value="<?= $this->form_validation->set_value('description',$repair_hardware_details->description) ?>">
				<p class="help-block"><?= $this->form_validation->error('description'); ?></p>
			</div>
		</div>
   
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Confirm Deletion</button>
			</div>
		</div>
	</fieldset>
</form>
<?php endif; ?>
<script type="text/javascript"></script>