<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Edit Branch Rack Location  <a href='/dpr/maintenance/branch_rack_location' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($branch_rack_location_view_details)): ?>
<h3>Results not found.</h3>
<?php else: ?>
<form action='/dpr/maintenance/edit_branch_rack_location/<?= $branch_rack_location_view_details->branch_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('branch_name') ?>">
			<label class="control-label" for="branch_name">Branch Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Branch Name" name="branch_name" disabled="disabled" id="branch_name" value="<?= $this->form_validation->set_value('branch_name',$branch_rack_location_view_details->branch_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('branch_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('rack_location') ?>">
			<label class="control-label" for="rack_location">Rack Location <em>*</em></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="Rack Location" name="rack_location" id="rack_location" value="<?= $this->form_validation->set_value('rack_location',$branch_rack_location_view_details->rack_location) ?>">
				<p class="help-block"><?= $this->form_validation->error('rack_location'); ?></p>
			</div>
		</div>

		
		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$branch_rack_location_view_details->is_active_rack_location),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update Branch Rack Location</button>
			</div>
		</div>
	</fieldset>
</form>

<?php endif; ?>
