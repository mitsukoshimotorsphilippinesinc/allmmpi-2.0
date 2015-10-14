<?php
	$upload_url = $this->config->item("media_url") . "/branch_rack_location";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>View Branch Rack Location <a href='/dpr/maintenance/branch_rack_location' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<?php if (empty($branch_rack_location_view_details)): ?>
	<h3>Branch Rack Location not found.</h3>
<?php else: ?>
<form action='' method='' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='result_id' name='result_id' value='<?= $branch_rack_location_view_details->agent_id ?>' />	
	<div class="control-group ">
		<label class="control-label" for="branch_name">Branch Name</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->branch_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="complete_address">Address</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->address_street ?> <?= $branch_rack_location_view_details->address_city ?> <?= $branch_rack_location_view_details->address_province ?> <?= $branch_rack_location_view_details->address_country ?> <?= $branch_rack_location_view_details->address_zip_code ?></label>
		</div>
	</div>
	
	<div class="control-group ">
		<label class="control-label" for="contact_number">Contact Number</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->contact_number ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="tin">Tin</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->tin ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="rack_location">Rack Location</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->rack_location ?></label>
		</div>
	</div>
	
	<div class="control-group ">
		<label class="control-label" for="is_active">Active Branch?</label>
		<div class="controls">
			<label class='data'><?= ($branch_rack_location_view_details->is_active_branch) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="is_active">Active Rack Location?</label>
		<div class="controls">
			<label class='data'><?= ($branch_rack_location_view_details->is_active_rack_location) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="rack_location">Remarks</label>
		<div class="controls">
			<label class='data'><?= $branch_rack_location_view_details->remarks ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
