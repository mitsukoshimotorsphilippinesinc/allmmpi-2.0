<!--h2>Delete Privilege  <a href='/admin/privileges' class='btn btn-large'>Back</a></h2>
<hr/-->

<?php if (empty($privilege)): ?>
	<div class='alert alert-error'>Privilege not found.</div>
<?php 
	else: 
	$privilege_uri = implode(',',json_decode($privilege->privilege_uri));
?>

	<label>You are about to delete a Privilege having the following details:</label>
	<br/>
	
	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:140px;'><label><strong>System</strong></label></td>
				<td><label class=''><?=$privilege->system_code?></label></td>		
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>Code</strong></label></td>
				<td><label class=''><?=$privilege->privilege_code?></label></td>		
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>Description</strong></label></td>
				<td><label class=''><?=$privilege->privilege_description?></label></td>		
			</tr>	
			<tr>
				<td style='width:140px;'><label><strong>URI</strong></label></td>
				<td><label class=''><?=$privilege->privilege_uri?></label></td>		
			</tr>					
		</tbody>
	</table>
	
	
	
<!--form action='/admin/privileges/delete/<?= $privilege->privilege_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='privilege_id' name='privilege_id' value='<?= $privilege->privilege_id ?>'/>
	<div class="control-group <?= $this->form_validation->error_class('privilege_code') ?>">
		<label class="control-label" for="privilege_code">Code <em>*</em></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="privilege_code" id="privilege_code" value="<?= set_value('privilege_code', $privilege->privilege_code) ?>" readonly> 
			<p class="help-block"><?= $this->form_validation->error('privilege_code'); ?></p>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="textarea">Description</label>
		<div class="controls">
			<textarea id="privilege_description" name="privilege_description" class="input-xlarge" rows="3" readonly><?= set_value('privilege_description',$privilege->privilege_description) ?></textarea>
		</div>
		<p class="help-block"><?= $this->form_validation->error('privilege_description'); ?></p>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('privilege_uri') ?>">
		<label class="control-label" for="privilege_uri">URI <em>*</em></label>
		<div class="controls">
			<textarea id="privilege_uri" name="privilege_uri" class="input-xlarge" rows="3" placeholder="admin,admin/users, admin/users/edit/$" readonly><?= set_value('privilege_uri',$privilege_uri) ?></textarea>
			<p class="help-block">Please enter comma delimited URIs. Add "/$" for modules/controllers with parameters.<?= $this->form_validation->error('privilege_uri'); ?></p>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	</div>
</fieldset>
</form-->
<?php endif; ?>
