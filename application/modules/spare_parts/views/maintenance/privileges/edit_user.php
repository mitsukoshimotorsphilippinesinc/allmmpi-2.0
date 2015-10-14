<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-warning'><h3>Edit User Details of <?= $employment_info_details->first_name; ?> <?= $employment_info_details->last_name; ?> <a class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Users List</a></h3></div>
<hr/>
<?php if (empty($user)): ?>
	<h3>User not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/edit_user/<?= $user->user_id ?>' method='post' class='form-inline'>
	
	<fieldset>	
		<input type='hidden' id='user_id' name='user_id' value='<?= $user->user_id ?>' readonly="readonly"/>		
		<div  class="control-group <?= $this->form_validation->error_class('username') ?>">
			<label class="control-label" for="username"><strong>Username <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="" name="username" id="username" value="<?= set_value('username', $user->username) ?>" readonly> 
				<p  class="help-block"><?= $this->form_validation->error('username'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active'); ?>">
			<label class="control-label" for="is_current"><strong>Is Active <em>*</em></strong></label>
			<div class="controls">
				<?php

					$options = array('0' => 'No', '1' => 'Yes');
					$extra = "id='is_active' class='span2'";
					echo form_dropdown('is_active', $options, set_value('is_active', $user->is_active), $extra);

				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('default_page') ?>">
			<label class="control-label" for="default_page"><strong>Default Page <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="/admin" name="default_page" id="default_page" value="<?= set_value('default_page', $user->default_page) ?>"> 
				<p class="help-block"><?= $this->form_validation->error('default_page'); ?></p>
			</div>
		</div>
				
	</fieldset>
	
	
	<hr>
	<div class="control-group">
		<div class="controls" align="right">
			<button type="submit" class="btn btn-primary">Update User Details</button>
			<button class="btn btn-primary return-btn">Cancel</button>
		</div>
	</div>
</form>
<?php endif; ?>

<script type="text/javascript">
	//$(".return-btn").click(function(){
	//	redirect('/admin/users');	
	//	return false;
	//});
</script>