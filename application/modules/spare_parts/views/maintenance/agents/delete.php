<?php
	$upload_url = $this->config->item("media_url") . "/agents";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Agent  <a href='/spare_parts/maintenance/agents' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($agent_details)): ?>
	<h3>Agent not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/delete_agent/<?= $agent_details->agent_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='agent_id' name='agent_id' value='<?= $agent_details->agent_id ?>' />
	
	<div class="control-group ">
		<label class="control-label" for="complete_name">Complete Name</label>
		<div class="controls">
			<label class='data'><?= $agent_details->complete_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Complete Address</label>
		<div class="controls">
			<label class='data'><?= $agent_details->complete_address ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Contact Number</label>
		<div class="controls">
			<label class='data'><?= $agent_details->contact_number ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php if($agent_details->image_filename):?>
				<img id="member_image" style="width:260px; height:172px;" alt="" src="<?= $upload_url; ?>/<?= $agent_details->image_filename ?>">
			<?php endif; ?>
			</label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($agent_details->is_active) ? 'Yes' : 'No'  ?></label>
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

