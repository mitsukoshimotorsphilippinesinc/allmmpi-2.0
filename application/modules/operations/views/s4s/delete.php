<?php
	$upload_url = $this->config->item("media_url") . "/s4s";
	$_upload_url = urlencode($upload_url);

	//$breadcrumb_container = assemble_breadcrumb();
?>

<!--?= $breadcrumb_container; ?-->
<h2>Delete S4S  <a href='/operations/s4s' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($s4s_details)): ?>
	<h3>Agent not found.</h3>
<?php else: ?>
<form action='/operations/s4s/delete/<?= $s4s_details->s4s_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='s4s_id' name='s4s_id' value='<?= $s4s_details->s4s_id ?>' />
	
	<div class="control-group ">
		<label class="control-label" for="pp_name">Policy Name:</label>
		<div class="controls">
			<label class='data'><?= $s4s_details->pp_name ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="pp_description">Description:</label>
		<div class="controls">
			<label class='data'><?= $s4s_details->pp_description ?></label>
		</div>
	</div>

	<div class="control-group ">
		<label class="control-label" for="short_body">Content/s:</label>
		<div class="controls">			

			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Filename</th>
						<th>Type</th>												
					</tr>
				</thead>
				<tbody>

				<?php 
					// get assets
					$where = "s4s_asset_id = '{$s4s_details->s4s_id}'";
					$s4s_assets = $this->human_relations_model->get_s4s_asset($where);

					if (empty($s4s_assets)) {
						echo "<tr><td colspan='2' style='text-align:center;'><strong>No Content Found.</strong></td></tr>";
					} else {
						foreach($s4s_assets as $sa) {
							echo "<tr>
									<td>{$sa->asset_filename}</td>
									<td>{$sa->file_type}</td>
								 </tr>";
						}
					}
				?>	
			
				</tbody>		
			</table>
						
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active">Active?:</label>
		<div class="controls">
			<label class='data'><?= ($s4s_details->is_active) ? 'Yes' : 'No'  ?></label>
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

