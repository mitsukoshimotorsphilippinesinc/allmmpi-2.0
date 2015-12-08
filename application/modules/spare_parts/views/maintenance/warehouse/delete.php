<?php
	$upload_url = $this->config->item("media_url") . "/warehouse";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Warehouse  <a href='/spare_parts/maintenance/warehouse' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($warehouse_details)): ?>
	<h3>warehouse not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/delete_warehouse/<?= $warehouse_details->warehouse_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='warehouse_id' name='warehouse_id' value='<?= $warehouse_details->warehouse_id ?>' />
	
	<div class="control-group ">
		<label class="control-label" for="warehouse_name">Warehouse Name</label>
		<div class="controls">
			<label class='data'><?= $warehouse_details->warehouse_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Description</label>
		<div class="controls">
			<label class='data'><?= $warehouse_details->description ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="prize">Warehouse Head</label>
		<div class="controls">
			<?php
				if ($warehouse_details->warehouse_head == 0) {
					$warehouse_head = "N/A";
				} else {
					$warehouse_head = $this->human_relations_model->get_employment_information_view_by_id($warehouse_details->warehouse_head);
				}

			?>
			<label class='data'><?= $warehouse_head->complete_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php 

			$image_display = "ni_warehouse.png";
			if($warehouse_details->image_filename)
				$image_display = $warehouse_details->image_filename;
			
			echo "<img id='member_image' style='width:180px; height:180px;' alt='' src='{$upload_url}/{$image_display}'>";
			?>
			</label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($warehouse_details->is_active) ? 'Yes' : 'No'  ?></label>
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

