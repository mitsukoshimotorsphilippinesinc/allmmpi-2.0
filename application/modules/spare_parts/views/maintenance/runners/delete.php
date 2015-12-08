<?php
	$upload_url = $this->config->item("media_url") . "/employees";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Runner  <a href='/spare_parts/maintenance/runners' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($runner_details)): ?>
	<h3>Runner not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/delete_runner/<?= $runner_details->runner_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='runner_id' name='runner_id' value='<?= $runner_details->runner_id ?>' />
	
	<div class="control-group ">
		<label class="control-label" for="complete_name">Complete Name</label>
		<div class="controls">
			<?php

				$runner_view_details = $this->spare_parts_model->get_runner_view_by_id_number($runner_details->id_number);

			?>
			<label class='data'><?= $runner_view_details->complete_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Warehouse Name</label>
		<div class="controls">
			<?php
				if ($runner_details->warehouse_id == 0) {
					$warehouse_details = "N/A";
				} else {
					$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($runner_details->warehouse_id);
				}

			?>
			<label class='data'><?= $warehouse_details->warehouse_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php 

				$employment_view_details = $this->human_relations_model->get_employment_information_view_by_id($runner_details->id_number);

				if ((empty($employment_view_details->image_filename)) || ($employment_view_details->image_filename == NULL) || (trim($employment_view_details->image_filename) == "")) {
					$image_display = "ni_". strtolower($employment_view_details->gender) .".png";
				} else {
					$image_display = $employment_view_details->image_filename;
				}						
			?>

			<img id="runner_image" style="width:180px; height:180px;" alt="" src="<?= $upload_url; ?>/<?= $image_display ?>">

			</label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($runner_details->is_active) ? 'Yes' : 'No'  ?></label>
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

