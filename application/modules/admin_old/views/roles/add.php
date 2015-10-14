<h2>Add New User Role  <a href='/admin/roles' class='btn btn-large' >Back</a></h2>
<hr/>
<form action='/admin/roles/add' method='post' class='form-horizontal'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('user_role') ?>">
		<label class="control-label" for="user_role">User Role <em>*</em></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="user_role" id="user_role" value="<?= set_value('user_role') ?>"> 
			<p class="help-block"><?= $this->form_validation->error('user_role'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('default_url') ?>">
		<label class="control-label" for="default_url">Default URL <em>*</em></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="default_url" id="default_url" value="<?= set_value('default_url') ?>"> 
			<p class="help-block"><?= $this->form_validation->error('default_url'); ?></p>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Add New User</button>
		</div>
	</div>
</fieldset>
</form>
