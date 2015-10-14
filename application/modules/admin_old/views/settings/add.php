<!--h2>Add New Setting  <a href='/admin/setting' class='btn btn-large' >Back</a></h2-->

<div class='alert alert-info'><h3>Add New Setting <a a class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Settings Dashboard</a></h3></div>

<form action='/admin/setting/add' method='post' class='form-inline'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('slug') ?>">
		<label class="control-label" for="setting_name"><strong>Slug <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span6' placeholder="" name="slug" id="slug" value="<?= set_value('slug') ?>"> 
			<p class="help-block"><?= $this->form_validation->error('slug'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('value') ?>">
		<label class="control-label" for="setting_value"><strong>Value <em>*</em></strong></label>
		<div class="controls">
			<textarea name="value" id="value" class="input-xlarge span8" rows="8"><?= set_value('value') ?></textarea>
			<p class="help-block"><?= $this->form_validation->error('value'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('default') ?>">
		<label class="control-label" for="setting_value"><strong>Default <em>*</em></strong></label>
		<div class="controls">
			<textarea name="default" id="default" class="input-xlarge span8" rows="8"><?= set_value('default') ?></textarea>
			<p class="help-block"><?= $this->form_validation->error('default'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('system_code'); ?>">
		<label class="control-label" for="is_current"><strong>System Code <em>*</em> </strong></label>
		<div class="controls">
			<?php

				$options = array();
				foreach ($this->systems as $item) {
					$options[$item->code] = $item->pretty_name;
				}

				echo form_dropdown('system_code', $options, 'system');

			?>
			<p class="help-block"><?= $this->form_validation->error('system_code'); ?></p>
		</div>
	</div>
	<!--div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Add New Item</button>
		</div>
	</div-->		
</fieldset>
<hr/>
		<div class="control-group" align="right">				
				<button type="submit" class="btn btn-primary">Add New Setting</button>
				<a class="btn return-btn">Cancel</a>
			</div>
		</div>
</form>

<script type="text/javascript">
	
	$(".return-btn").click(function(){		
		redirect('/admin/setting');	
		return false;
	});

</script>
