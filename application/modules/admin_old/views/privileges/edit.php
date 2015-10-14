<!--h2>Edit Privilege  <a href='/admin/privileges' class='btn btn-large'>Back</a></h2>
<hr/-->
<?php if (empty($privilege)): ?>
	<div class='alert alert-error'>Privilege not found.</div>
<?php 
	else: 
	$privilege_uri = implode(',',json_decode($privilege->privilege_uri));
?>
<form action='/admin/privileges/edit/<?= $privilege->privilege_id ?>' method='post' class='form-inline'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('system_code') ?>">
		<label class="control-label" for="system_code"><strong>System <em>*</em></strong></label>
		<div class="controls">
			<?php
				$options = array('' => 'Select a System');
				foreach($systems as $s)
					$options[$s->code] = $s->pretty_name;
				
				$extra = "id='system_code' style='width:auto;'";
				echo form_dropdown('system_code', $options, set_value('system_code', $privilege->system_code), $extra);
			?>			
		</div>
		<span class='label label-important' id='system_code_error' style='display:none;'>System Field is required.</span>
	</div>
	<input type='hidden' id='privilege_id' name='privilege_id' value='<?= $privilege->privilege_id ?>' />
	<div class="control-group <?= $this->form_validation->error_class('privilege_code') ?>">
		<label class="control-label" for="privilege_code"><strong>Code <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="privilege_code" id="privilege_code" value="<?= set_value('privilege_code', $privilege->privilege_code) ?>"> 			
		</div>
			<span class='label label-important' id='privilege_code_error' style='display:none;'>Privilege Code Field is required.</span>
	</div>
	<div class="control-group">
		<label class="control-label" for="textarea"><strong>Description <em>*</em></strong></label>
		<div class="controls">
			<textarea id="privilege_description" name="privilege_description" class="input-xlarge" rows="3"><?= set_value('privilege_description',$privilege->privilege_description) ?></textarea>
		</div>
	</div>
	<span class='label label-important' id='privilege_description_error' style='display:none;'>Privilege Description Field is required.</span>
	<div class="control-group <?= $this->form_validation->error_class('privilege_uri') ?>">
		<label class="control-label" for="privilege_uri"><strong>URI <em>*</em></strong></label>
		<div class="controls">
			<textarea id="privilege_uri" name="privilege_uri" class="input-xlarge" rows="3" placeholder="admin,admin/users"><?= set_value('privilege_uri',$privilege_uri) ?></textarea>
			<p class="help-block">Please enter comma delimited URIs. Add "/$" for modules/controllers with parameters.<?= $this->form_validation->error('privilege_uri'); ?></p>
		</div>
		<span class='label label-important' id='privilege_uri_error' style='display:none;'>Privilege URI Field is required.</span>
	</div>	
</fieldset>
</form>
<?php endif; ?>
