<?php
	echo js('libs/uploadrr.min.js');

	$department_details = $this->human_relations_model->get_department_by_id($s4s_details->department_id);

	$upload_url = $this->config->item("media_url") . "/s4s/" . $department_details->url;	
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
		
		<div class="control-group <?= $this->form_validation->error_class('reference_number') ?>">
			<label class="control-label" for="reference_number">Reference Code: <em>*</em></label>
			<div class="controls">
				<input readonly="readonly" type="text" class='span3' placeholder="Reference Code" name="reference_number" id="reference_number" value="<?= $this->form_validation->set_value('reference_number',$s4s_details->reference_number) ?>">
				<p class="help-block"><?= $this->form_validation->error('reference_number'); ?></p>
			</div>
		</div>

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

		<div class="control-group <?= $this->form_validation->error_class('department_id') ?>">
			<label class="control-label" for="department_id">Department Name <em>*</em></label>
			<div class="controls">
				<?php
				
				$where = "url is NOT NULL AND is_active = 1";
				$department_details = $this->human_relations_model->get_department($where, NULL, "department_name");

				$department_options = array();
				$department_options = array('' => 'Select Department...');
				foreach ($department_details as $wd) {
				 	$department_options[$wd->department_id] = $wd->department_name;
				}				
				?>

				<?= form_dropdown('department_id',$department_options, set_value('department_id', $s4s_details->department_id),'id="department_id" readonly="readonly" disabled="disabled"') ?>

				<p class="help-block"><?= $this->form_validation->error('department_id'); ?></p>			
			</div>	
		</div>

		<div class="control-group <?= $this->form_validation->error_class('document_sequence') ?>">
			<label class="control-label" for="document_sequence">Document Sequence: </label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Document Sequence" name="document_sequence" id="document_sequence" value="<?= $this->form_validation->set_value('document_sequence',$s4s_details->document_sequence) ?>">
				<p class="help-block"><?= $this->form_validation->error('document_sequence'); ?></p>
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
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

					<?php 
						// get assets
						$where = "s4s_asset_id = '{$s4s_details->s4s_id}'";
						$s4s_assets = $this->human_relations_model->get_s4s_asset($where);

						if (empty($s4s_assets)) {
							echo "<tr><td colspan='3' style='text-align:center;'><strong>No Content Found.</strong></td></tr>";
						} else {
							foreach($s4s_assets as $sa) {
								echo "<tr>
										<td>{$sa->asset_filename}</td>
										<td>{$sa->file_type}</td>
										<td><button class='btn btn-important'>-</button></td>
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
				<div id="asset_upload"  class="uploadBox_fu">
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
	$('#asset_upload').Uploadrr({
		singleUpload : true,
		progressGIF : '<?= image_path('pr.gif') ?>',
		//allowedExtensions: ['.gif','.jpg', '.png'],		
		allowedExtensions: ['.pdf'],	
		target : base_url + '/admin/upload/process?filename=s4s_<?= $s4s_details->reference_number?>&location=<?=$_upload_url?>&width=960&ts=<?=time()?>',
		onComplete: function() {

			b.request({
		        url: '/operations/s4s/update_asset',
		        data: {
					"filename": '<?= "s4s_" . $s4s_details->reference_number .".pdf"?>',
					"_id":<?= $s4s_details->s4s_id?>,
					"department_id": <?= $s4s_details->department_id?>
				},
		        on_success: function(data) {		
		        }
		    });		
		}
	});
	
	
</script>
<?php endif; ?>
