<!--h2>Add New employee  <a href='/admin/employees' class='btn btn-large' >Back</a></h2>
<hr/-->
<form action='/admin/employees/add' method='post' class='form-inline'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('employee_code') ?>">
		<label class="control-label" for="employee_code"><strong>Employee ID <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="employee_code" id="employee_code" value="<?= set_value('employee_code') ?>"> 			
		</div>
		<span class='label label-important' id='employee_code_error' style='display:none;'>Employee ID Field is required.</span>
		<span class='label label-important' id='employee_code_exist_error' style='display:none;'>This Employee ID already exists.</span>
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
		<label class="control-label" for="email"><strong>Email <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="email" id="email" value="<?= set_value('email') ?>"> 			
		</div>
		<span class='label label-important' id='email_error' style='display:none;'>Email Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('email') ?>">
		<label class="control-label" for="mobile_number"><strong>Mobile Number <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4 numeric-entry' placeholder="" name="mobile_number" id="mobile_number" value="<?= set_value('mobile_number') ?>"> 			
		</div>
		<span class='label label-important' id='mobile_number_error' style='display:none;'>Mobile Number Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('employee_type_id') ?>">
		<label class="control-label" for="employee_type_id"><strong>Employee Type<em>*</em></strong></label>
		<div class="controls">
			<select id='employee_type_id' name='employee_type_id'>
				<?php foreach($employee_types as $e): ?>
					<option value="<?= $e->employee_type_id ?>"><?= $e->name ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<span class='label label-important' id='mobile_number_error' style='display:none;'>Mobile Number Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('is_active'); ?>">
		<label class="control-label" for="is_current"><strong>Is Active <em>*</em></strong></label>
		<div class="controls">
			<?php

				$options = array('' => 'Please Select', '1' => 'Yes','0' => 'No');
				$extra = "id='is_active' class='span2'";
				echo form_dropdown('is_active', $options, null, $extra);

			?>			
		</div>
		<span class='label label-important' id='is_active_error' style='display:none;'>Is Active Field is required.</span>
	</div>	
</fieldset>
</form>
