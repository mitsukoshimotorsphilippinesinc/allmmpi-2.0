<?php
	$upload_url = $this->config->item("media_url") . "/branch_rack_location";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Branch Rack Location  <a href='/dpr/maintenance/branch_rack_location' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($branch_rack_location_view_details)): ?>
	<h3>Branch Rack Location not found.</h3>
<?php else: ?>
<form action='/dpr/maintenance/delete_branch_rack_location/<?= $branch_rack_location_view_details->branch_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='branch_id' name='branch_id' value='<?= $branch_rack_location_view_details->branch_id ?>' />
	
	<div class="control-group ">
		<label class="control-label" for="branch_name">Branch Name</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->branch_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="rack_location">Rack Location</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->rack_location ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($branch_rack_location_view_details->is_active_rack_location) ? 'Yes' : 'No'  ?></label>
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

