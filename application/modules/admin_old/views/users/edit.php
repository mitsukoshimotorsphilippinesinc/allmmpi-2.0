<div class='alert alert-info'><h3>Edit User <a class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Users Dashboard</a></h3></div>
<?php if (empty($user)): ?>
	<h3>User not found.</h3>
<?php else: ?>
<form action='/admin/users/edit/<?= $user->user_id ?>' method='post' class='form-inline'>
	
	<div class='alert alert-success'>
		<h4>User Details</h4>
	</div>

	<fieldset>	
		<input type='hidden' id='user_id' name='user_id' value='<?= $user->user_id ?>' readonly="readonly"/>
		<input type='hidden' id='orig_password' name='orig_password' value='<?= $user->password ?>' readonly="readonly"/>
		<input type='hidden' id='orig_facility' name='orig_facility' value='<?= $user_facility_id ?>' readonly="readonly"/>
		<div  class="control-group <?= $this->form_validation->error_class('username') ?>">
			<label class="control-label" for="username"><strong>Username <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="" name="username" id="username" value="<?= set_value('username', $user->username) ?>" readonly> 
				<p  class="help-block"><?= $this->form_validation->error('username'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('email') ?>">
			<label class="control-label" for="email"><strong>Email <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="" name="email" id="email" value="<?= set_value('email', $user->email) ?>"> 
				<p class="help-block"><?= $this->form_validation->error('email'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('first_name') ?>">
			<label class="control-label" for="first_name"><strong>First Name <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="" name="first_name" id="first_name" value="<?= set_value('first_name', $user->first_name) ?>"> 
				<p class="help-block"><?= $this->form_validation->error('first_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('last_name') ?>">
			<label class="control-label" for="last_name"><strong>Last Name <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="" name="last_name" id="last_name" value="<?= set_value('last_name', $user->last_name) ?>"> 
				<p class="help-block"><?= $this->form_validation->error('last_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('user_facility'); ?>">
			<label class="control-label" for="user_facility"><strong>Default Facility <em>*</em><strong></label>
			<div class="controls">
				<?php

					$options = array('' => 'Please Select a Facility');
		
					foreach($facilities as $f) $options[$f->facility_id] = $f->facility_name;
					
					$facility_id = set_value('user_facility',$user_facility_id);
					
					$default_facility_id = "";
					
					if(!empty($facility_id)) $default_facility_id = $facility_id;
					
					$extra = "id='user_facility' ";
					echo form_dropdown('user_facility', $options, $default_facility_id, $extra);

				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
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
		
			
		<br/>
		<div class='alert alert-success'>
			<h4>Change Password</h4>
		</div>
	
		<div class="control-group <?= $this->form_validation->error_class('password') ?>">
			<label class="control-label" for="password"><strong>Enter Current Password</strong></label>
			<div class="controls">
				<input type="password" class='span4' placeholder="" name="password" id="password" value="<?= set_value('password') ?>">
				<p class="help-block"><?= $this->form_validation->error('password'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('new_password') ?>">
			<label class="control-label" for="new_password"><strong>Enter New Password</strong></label>
			<div class="controls">
				<input type="password" class='span4' placeholder="" name="new_password" id="new_password" value="<?= set_value('new_password') ?>">
				<p class="help-block"><?= $this->form_validation->error('new_password'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('reenter_password') ?>">
			<label class="control-label" for="reenter_password"><strong>Re-enter New Password</strong></label>
			<div class="controls">
				<input type="password" class='span4' placeholder="" name="reenter_password" id="reenter_password" value="<?= set_value('reenter_password') ?>">
				<p class="help-block"><?= $this->form_validation->error('reenter_password'); ?></p>
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
	$(".return-btn").click(function(){
		redirect('/admin/users');	
		return false;
	});
</script>