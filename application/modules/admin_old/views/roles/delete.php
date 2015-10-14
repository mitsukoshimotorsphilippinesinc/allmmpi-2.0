<h2>Delete User Role <a href='/admin/roles' class='btn btn-large' >Back</a></h2>
<hr/>
<?php if (empty($user_role)): ?>
	<h3>User not found.</h3>
<?php else: ?>
<form action='/admin/roles/delete/<?= $user_role->role_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='role_id' name='role_id' value='<?= $user_role->role_id ?>' />
	<div id="username_control" class="control-group">
		<label class="control-label" for="username">User Role</label>
		<div class="controls">
			<label class='data'><?= $user_role->user_role ?></label>
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
