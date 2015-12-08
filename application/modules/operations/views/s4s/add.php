<!--?php	
	$breadcrumb_container = assemble_breadcrumb();
?-->

<!--?= $breadcrumb_container; ?-->
<h2>Add New S4S  <a href='/operations/s4s' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/operations/s4s/add' method='post' class='form-horizontal'>
	<fieldset >		
		<div class="control-group <?= $this->form_validation->error_class('pp_name') ?>">
			<label class="control-label" for="pp_name">Policy Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Policy Name" name="pp_name" id="pp_name" value="<?= set_value('pp_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('pp_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('pp_description') ?>">
			<label class="control-label" for="pp_description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="pp_description" id="pp_description" value="<?= set_value('pp_description') ?>">
				<p class="help-block"><?= $this->form_validation->error('pp_description'); ?></p>
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
				<button type="submit" class="btn btn-primary">Add New S4S</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript"></script>