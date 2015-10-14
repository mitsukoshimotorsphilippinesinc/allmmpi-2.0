<h2>Edit User Role  <a href='/admin/roles' class='btn btn-large'>Back</a></h2>
<hr/>
<?php if (empty($user_role)): ?>
	<h3>User not found.</h3>
<?php else: ?>
<form action='/admin/roles/edit/<?= $user_role->role_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='role_id' name='role_id' value='<?= $user_role->role_id ?>' />
	<div class="control-group <?= $this->form_validation->error_class('user_role') ?>">
		<label class="control-label" for="user_role">User Role <em>*</em></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="user_role" id="user_role" value="<?= set_value('user_role',$user_role->user_role) ?>"> 
			<p class="help-block"><?= $this->form_validation->error('user_role'); ?></p>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('default_url') ?>">
		<label class="control-label" for="default_url">Default URL <em>*</em></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="default_url" id="default_url" value="<?= set_value('default_url',$user_role->default_url) ?>"> 
			<p class="help-block"><?= $this->form_validation->error('default_url'); ?></p>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
