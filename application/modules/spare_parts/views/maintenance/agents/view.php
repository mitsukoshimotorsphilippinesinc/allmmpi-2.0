<?php
	$upload_url = $this->config->item("media_url") . "/agents";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>View Agent  <a href='/spare_parts/maintenance/agents' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<?php if (empty($agent_details)): ?>
	<h3>Agent not found.</h3>
<?php else: ?>
<form action='' method='' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='result_id' name='result_id' value='<?= $agent_details->agent_id ?>' />	
	<div class="control-group ">
		<label class="control-label" for="member_name">Agent Name</label>
		<div class="controls">
			<label class='data'><?= $agent_details->complete_name ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php if($agent_details->image_filename):?>
				<img id="agent_image" style="width:180px; height:180px;" alt="" src="<?= $upload_url; ?>/<?= $agent_details->image_filename ?>">
			<?php endif; ?>
			</label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="gender">Gender</label>
		<div class="controls">
			<label class='data'><?= $agent_details->gender ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($agent_details->is_active) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
