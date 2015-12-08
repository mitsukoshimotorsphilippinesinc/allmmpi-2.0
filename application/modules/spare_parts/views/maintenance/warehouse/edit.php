<?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/warehouse";
	//$upload_url ="c:/laragon/wwww/allmppi/webroot_admin/assets/media/warehouse";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Edit Warehouse  <a href='/spare_parts/maintenance/warehouse' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($warehouse_details)): ?>
<h3>Results not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/edit_warehouse/<?= $warehouse_details->warehouse_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('warehouse_name') ?>">
			<label class="control-label" for="warehouse_name">Warehouse Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Warehouse Name" name="warehouse_name" id="warehouse_name" value="<?= $this->form_validation->set_value('warehouse_name',$warehouse_details->warehouse_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('warehouse_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('description') ?>">
			<label class="control-label" for="description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="description" id="description" value="<?= $this->form_validation->set_value('description',$warehouse_details->description) ?>">
				<p class="help-block"><?= $this->form_validation->error('description'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('agent_name') ?>">
			<label class="control-label" for="agent_name">Warehouse Head </label>
			<div class="controls">
				<?php

				$where = "is_employed = 1 AND position_id = 126"; // warehouse supervisor
				$warehouse_head_details = $this->human_relations_model->get_employment_information_view($where, NULL, "complete_name");

				$warehouse_head_options = array();
				$warehouse_head_options = array('0' => 'None');
				foreach ($warehouse_head_details as $ad) {
				 	$warehouse_head_options[$ad->id_number] = $ad->complete_name;
				}				
				?>

				<?= form_dropdown('warehouse_head',$warehouse_head_options, $warehouse_details->warehouse_head,'id="warehouse_head"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('agent_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<div id="image_filename">					
					<?php if(!empty($warehouse_details->image_filename)):?>
						<img id="result_image" style="width:180px; height:180px;" alt="" src="<?= $upload_url; ?>/<?= $warehouse_details->image_filename ?>">
					<?php endif; ?>
					<input type='hidden' value='<?= $warehouse_details->image_filename ?>'>
				</div>
				<div id="image_upload"  class="uploadBox_fu">
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$warehouse_details->is_active),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update warehouse</button>
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
		allowedExtensions: ['.gif','.jpg', '.png'],		
		target : base_url + '/admin/upload/process?filename=warehouse_<?= $warehouse_details->warehouse_id?>&location=<?=$_upload_url?>&width=960&ts=<?=time()?>',
		onComplete: function() {

			$("#result_image").attr('src', '<?=$upload_url?>/warehouse_'+<?= $warehouse_details->warehouse_id?>+'.jpg?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/spare_parts/maintenance/update_image',
		        data: {
					"filename": '<?= "warehouse_" . $warehouse_details->warehouse_id .".jpg"?>',
					"_id":<?= $warehouse_details->warehouse_id?>,
					"maintenance_name": "warehouse"
				},
		        on_success: function(data) {		
		        }
		    });		
		}
	});
	
</script>
<?php endif; ?>
