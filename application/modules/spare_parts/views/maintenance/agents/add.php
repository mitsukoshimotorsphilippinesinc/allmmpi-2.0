<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Add New Agent  <a href='/spare_parts/maintenance/agents' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/spare_parts/maintenance/add_agent' method='post' class='form-horizontal'>
	<fieldset >		
		<div class="control-group <?= $this->form_validation->error_class('complete_name') ?>">
			<label class="control-label" for="complete_name">Complete Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Complete Name" name="complete_name" id="complete_name" value="<?= set_value('complete_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('complete_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('complete_address') ?>">
			<label class="control-label" for="complete_address">Complete Address <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Complete Address" name="complete_address" id="complete_address" value="<?= set_value('complete_address') ?>">
				<p class="help-block"><?= $this->form_validation->error('complete_address'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('contact_number') ?>">
			<label class="control-label" for="contact_number">Contact Number <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Contact Number" name="contact_number" id="contact_number" value="<?= set_value('contact_number') ?>">
				<p class="help-block"><?= $this->form_validation->error('contact_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active'),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Add New Agent</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript"></script>