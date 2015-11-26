<?php
	$upload_url = $this->config->item("media_url") . "/dealers";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Dealer  <a href='/spare_parts/maintenance/dealers' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($dealer_details)): ?>
	<h3>Dealer not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/delete_dealer/<?= $dealer_details->dealer_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='dealer_id' name='dealer_id' value='<?= $dealer_details->dealer_id ?>' />
	
	<div class="control-group ">
		<label class="control-label" for="complete_name">Complete Name</label>
		<div class="controls">
			<label class='data'><?= $dealer_details->complete_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Complete Address</label>
		<div class="controls">
			<label class='data'><?= $dealer_details->complete_address ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Contact Number</label>
		<div class="controls">
			<label class='data'><?= $dealer_details->contact_number ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Agent Name</label>
		<div class="controls">
			<?php
				if ($dealer_details->agent_id == 0) {
					$agent_name = "N/A";
				} else {
					$agent_name = $this->spare_parts_model->get_agent_by_id($dealer_details->agent_id);
				}

			?>
			<label class='data'><?= $agent_name->complete_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Max Discount</label>
		<div class="controls">
			<label class='data'><?= $dealer_details->max_discount ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php if($dealer_details->image_filename):?>
				<img id="member_image" style="width:260px; height:172px;" alt="" src="<?= $upload_url; ?>/<?= $dealer_details->image_filename ?>">
			<?php endif; ?>
			</label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($dealer_details->is_active) ? 'Yes' : 'No'  ?></label>
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

