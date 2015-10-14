<!--h2>Delete User  <a href='/admin/users' class='btn btn-large' >Back</a></h2>
<hr/-->
<?php if (empty($user)): ?>
	<h3>User not found.</h3>
<?php else: ?>
	<label>You are about to delete a User having the following details:</label>
	<br/>
	
	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:100px;'><label><strong>Username</strong></label></td>
				<td><label class=''><?= $user->username ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>Email</strong></label></td>
				<td><label class=''><?= $user->email ?></label></td>		
			</tr>		
			<tr>
				<td style='width:100px;'><label><strong>First Name</strong></label></td>
				<td><label class=''><?= $user->first_name ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>Last Name</strong></label></td>
				<td><label class=''><?= $user->last_name ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>Mobile Number</strong></label></td>
				<td><label class=''><?= $user->mobile_number ?></label></td>		
			</tr>
			<tr>
				<td style='width:100px;'><label><strong>User Facility</strong></label></td>
				<td><label class=''><?= $user_facility_details->facility_name ?></label></td>		
			</tr>
			<?php
			 	if ($user->is_active == 0) {
					$is_active = 'No';
				} else {
					$is_active = 'Yes';
				}
			?>			
			<tr>
				<td style='width:100px;'><label><strong>Active</strong></label></td>
				<td><label class=''><?= $is_active ?></label></td>		
			</tr>
		</tbody>
	</table>
	
<!--form action='/admin/users/delete/<?= $user->user_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='user_id' name='user_id' value='<?= $user->user_id ?>' />
	<div id="username_control" class="control-group">
		<label class="control-label" for="username">Username</label>
		<div class="controls">
			<label class='data'><?= $user->username ?></label>
		</div>
	</div>
	<div id="email_control" class="control-group">
		<label class="control-label" for="email">Email</label>
		<div class="controls">
			<label class='data'><?= $user->email ?></label>
		</div>
	</div>
	<div id="first_name_control" class="control-group">
		<label class="control-label" for="first_name">First Name</label>
		<div class="controls">
			<label class='data'><?= $user->first_name ?></label>
		</div>
	</div>
	<div id="last_name_control" class="control-group">
		<label class="control-label" for="last_name">Last Name</label>
		<div class="controls">
			<label class='data'><?= $user->last_name ?></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="last_name">Is Active</label>
		<div class="controls">
			<label class='data'><?= $user->is_active == 1 ? 'Yes' : 'No'; ?></label>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Confirm Deletion</button>
		</div>
	</div>
</fieldset>
</form-->
<?php endif; ?>
