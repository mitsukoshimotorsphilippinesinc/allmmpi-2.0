<?php
	$upload_url = $this->config->item("media_url") . "/dealers";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>View Dealer  <a href='/spare_parts/maintenance/dealers' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<?php if (empty($dealer_details)): ?>
	<h3>dealer not found.</h3>
<?php else: ?>
<form action='' method='' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='result_id' name='result_id' value='<?= $dealer_details->dealer_id ?>' />	
	<div class="control-group ">
		<label class="control-label" for="member_name">Dealer Name</label>
		<div class="controls">
			<label class='data'><?= $dealer_details->complete_name ?></label>
		</div>
	</div>
	
	<div class="control-group ">
		<label class="control-label" for="member_name">Address</label>
		<div class="controls">
			<label class='data'><?= $dealer_details->complete_address ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php if($dealer_details->image_filename):?>
				<img id="dealer_image" style="width:180px; height:180px;" alt="" src="<?= $upload_url; ?>/<?= $dealer_details->image_filename ?>">
			<?php endif; ?>
			</label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="gender">Max Discount</label>
		<div class="controls">
			<label class='data'><?= $dealer_details->max_discount * 100 ?>%</label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($dealer_details->is_active) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
