<!--h2>Add New Privilege<a href='/admin/privileges' class='btn btn-large'>Back</a></h2>
<hr/-->


<form action='/admin/privileges/add' method='post' class='form-inline'>
<fieldset>
	<div class="control-group <?= $this->form_validation->error_class('system_code') ?>">
		<label class="control-label" for="system_code"><strong>System <em>*</em></strong></label>
		<div class="controls">
			<?php
				$options = array('' => 'Select a System');
				foreach($systems as $s)
					$options[$s->code] = $s->pretty_name;
				
				$extra = "id='system_code' style='width:auto;'";
				echo form_dropdown('system_code', $options, set_value('system_code'), $extra);
			?>			
		</div>
		<span class='label label-important' id='system_code_error' style='display:none;'>System Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('privilege_code') ?>">
		<label class="control-label" for="privilege_code"><strong>Code <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="privilege_code" id="privilege_code" value="<?= set_value('privilege_code') ?>"> 			
		</div>
		<span class='label label-important' id='privilege_code_error' style='display:none;'>Privilege Code Field is required.</span>
	</div>
	<div class="control-group">
		<label class="control-label" for="privilege_description"><strong>Description <em>*</em></strong></label>
		<div class="controls">
			<textarea id="privilege_description" name="privilege_description" class="input-xlarge" rows="3"><?= set_value('privilege_description') ?></textarea>			
		</div>
		<span class='label label-important' id='privilege_description_error' style='display:none;'>Description Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('privilege_uri') ?>">
		<label class="control-label" for="privilege_uri"><strong>URI <em>*</em></strong></label>
		<div class="controls">
			<textarea id="privilege_uri" name="privilege_uri" class="input-xlarge" rows="3" placeholder="admin,admin/users"><?= set_value('privilege_uri') ?></textarea>			
		</div>
		<span class='label label-important' id='privilege_uri_error' style='display:none;'>URI Field is required.</span>
	</div>		
</fieldset>
</form>
