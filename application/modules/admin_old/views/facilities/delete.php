<h2>Delete Facility  <a href='/admin/facilities' class='btn btn-large' >Back</a></h2>
<hr/>
<?php if (empty($facility)): ?>
	<h3>Facility not found.</h3>
<?php else: ?>
<form action='/admin/facilities/delete/<?= $facility->facility_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='facility_id' name='facility_id' value='<?= $facility->facility_id ?>' />
	<div id="facility_name_control" class="control-group">
		<label class="control-label" for="facility_name">Facility Name</label>
		<div class="controls">
			<label class='data'><?= $facility->facility_name ?></label>
		</div>
	</div>
	<div id="facility_description_control" class="control-group">
		<label class="control-label" for="facility_description">Description</label>
		<div class="controls">
			<label class='data'><?= $facility->facility_description ?></label>
		</div>
	</div>
	<div id="facility_address_control" class="control-group">
		<label class="control-label" for="first_name">Address</label>
		<div class="controls">
			<label class='data'><?= $facility->facility_address ?></label>
		</div>
	</div>
	<div id="facility_contact_details_control" class="control-group">
		<label class="control-label" for="facility_contact_details">Contact Details</label>
		<div class="controls">
			<label class='data'><?= $facility->facility_contact_details ?></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="facility_type_id">Type</label>
		<div class="controls">
			<label class='data'><?= $facility->facility_type_id == 1 ? 'Warehouse' : 'Depot'; ?></label>
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
