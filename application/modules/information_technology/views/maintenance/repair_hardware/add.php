<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Add New Hardware  <a href='/information_technology/maintenance/repair_hardware' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/information_technology/maintenance/add_repair_hardware' method='post' class='form-horizontal'>
	<fieldset >		
		<div class="control-group <?= $this->form_validation->error_class('hardware_name') ?>">
			<label class="control-label" for="hardware_name">Hardware Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Hardware Name" name="hardware_name" id="hardware_name" value="<?= set_value('hardware_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('hardware_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('description') ?>">
			<label class="control-label" for="description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="description" id="description" value="<?= set_value('description') ?>">
				<p class="help-block"><?= $this->form_validation->error('description'); ?></p>
			</div>
		</div>
   
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Add New Hardware</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript"></script>