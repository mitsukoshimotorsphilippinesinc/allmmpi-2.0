<!--h2>Add New User  <a href='/admin/users' class='btn btn-large' >Back</a></h2>
<hr/-->
<form action='/admin/users/add' method='post' class='form-inline'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('username') ?>">
		<label class="control-label" for="username"><strong>Username <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="username" id="username" value="<?= set_value('username') ?>"> 			
		</div>
		<span class='label label-important' id='username_error' style='display:none;'>Username Field is required.</span>
		<span class='label label-important' id='username_exist_error' style='display:none;'>Username already exists.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('email') ?>">
		<label class="control-label" for="email"><strong>Email <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="email" id="email" value="<?= set_value('email') ?>"> 			
		</div>
		<span class='label label-important' id='email_error' style='display:none;'>Email Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('password') ?>">
		<label class="control-label" for="password"><strong>Password <em>*</em></strong></label>
		<div class="controls">
			<input type="password" class='span4' placeholder="" name="password" id="password" value="<?= set_value('password') ?>"> 
		</div>
		<span class='label label-important' id='password_error' style='display:none;'>Password Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('first_name') ?>">
		<label class="control-label" for="first_name"><strong>First Name <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="first_name" id="first_name" value="<?= set_value('first_name') ?>"> 
		</div>
		<span class='label label-important' id='first_name_error' style='display:none;'>First Name Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('last_name') ?>">
		<label class="control-label" for="last_name"><strong>Last Name <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="last_name" id="last_name" value="<?= set_value('last_name') ?>"> 			
		</div>
		<span class='label label-important' id='last_name_error' style='display:none;'>Last Name Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('email') ?>">
		<label class="control-label" for="mobile_number"><strong>Mobile Number <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="mobile_number" id="mobile_number" value="<?= set_value('mobile_number') ?>"> 			
		</div>
		<span class='label label-important' id='mobile_number_error' style='display:none;'>Mobile Number Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('user_facility'); ?>">
		<label class="control-label" for="user_facility"><strong>Default Facility <em>*</em></strong></label>
		<div class="controls">
			<?php

				$options = array('' => 'Please Select a Facility');
				
				foreach($facilities as $f) $options[$f->facility_id] = $f->facility_name;
				
				$extra = "id='user_facility'";
				echo form_dropdown('user_facility', $options, set_value('user_facility'), $extra);

			?>			
		</div>
		<span class='label label-important' id='user_facility_error' style='display:none;'>Default Facility Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('is_active'); ?>">
		<label class="control-label" for="is_current"><strong>Is Active <em>*</em></strong></label>
		<div class="controls">
			<?php

				$options = array('0' => 'No', '1' => 'Yes');
				$extra = "id='is_active' class='span2'";
				echo form_dropdown('is_active', $options, null, $extra);

			?>			
		</div>
		<span class='label label-important' id='is_active_error' style='display:none;'>Is Active Field is required.</span>
	</div>	
	<div class="control-group <?= $this->form_validation->error_class('email') ?>">
		<label class="control-label" for="mobile_number"><strong>Default Page <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="/admin" name="default_page" id="default_page" value="/admin"> 			
		</div>
		<span class='label label-important' id='default_page_error' style='display:none;'>Default Page Field is required.</span>
	</div>
	
</fieldset>
</form>
