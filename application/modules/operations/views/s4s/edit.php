<?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/s4s";	
	$_upload_url = urlencode($upload_url);

	//$breadcrumb_container = assemble_breadcrumb();
?>

<!--?= $breadcrumb_container; ?-->
<h2>Edit S4S  <a href='/operations/s4s' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($s4s_details)): ?>
<h3>Result not found.</h3>
<?php else: ?>
<form action='/operations/s4s/edit/<?= $s4s_details->s4s_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('pp_name') ?>">
			<label class="control-label" for="pp_name">Policy Name: <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Policy Name" name="pp_name" id="pp_name" value="<?= $this->form_validation->set_value('pp_name',$s4s_details->pp_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('pp_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('pp_description') ?>">
			<label class="control-label" for="pp_description">Description: <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="pp_description" id="pp_description" value="<?= $this->form_validation->set_value('pp_description',$s4s_details->pp_description) ?>">
				<p class="help-block"><?= $this->form_validation->error('pp_description'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Content/s: </label>
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

				<div id="image_filename">
					<?php if(!empty($s4s_details->image_filename)):?>
						<img id="result_content" style="width:180px; height:180px;" alt="" src="<?= $upload_url; ?>/<?= $s4s_details->image_filename ?>">
					<?php endif; ?>
					<input type='hidden' value='<?= $s4s_details->asset_filename ?>'>
				</div>
				<div id="image_upload"  class="uploadBox_fu">
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active?: <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$s4s_details->is_active),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update S4S</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	var base_url = "<?=$this->config->item('base_url');?>";
	
	// uploader	
	$('#image_upload').Uploadrr({
		singleUpload : true,
		progressGIF : '<?= image_path('pr.gif') ?>',
		//allowedExtensions: ['.gif','.jpg', '.png'],		
		allowedExtensions: ['.pdf'],	
		target : base_url + '/admin/upload/process?filename=s4s_<?= $s4s_details->s4s_id?>&location=<?=$_upload_url?>&width=960&ts=<?=time()?>',
		onComplete: function() {

			$("#result_image").attr('src', '<?=$upload_url?>/s4s_'+<?= $s4s_details->s4s_id?>+'.pdf?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/spare_parts/maintenance/update_image',
		        data: {
					"filename": '<?= "s4s_" . $s4s_details->s4s_id .".pdf"?>',
					"_id":<?= $s4s_details->s4s_id?>,
					"maintenance_name": "warehouse"
				},
		        on_success: function(data) {		
		        }
		    });		
		}
	});
	
	
</script>
<?php endif; ?>
