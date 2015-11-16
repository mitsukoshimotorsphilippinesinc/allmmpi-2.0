<?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/spare_parts";
	//$upload_url ="c:/laragon/wwww/allmppi/webroot_admin/assets/media/spare_parts";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Edit Spare Part  <a href='/spare_parts/maintenance/spare_parts' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($spare_part_details)): ?>
<h3>results not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/edit_spare_part/<?= $spare_part_details->sku ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('sku') ?>">
			<label class="control-label" for="complete_name">SKU <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="SKU" name="sku" id="sku" value="<?= $this->form_validation->set_value('sku',$spare_part_details->sku) ?>">
				<p class="help-block"><?= $this->form_validation->error('sku'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('brand_name') ?>">
			<label class="control-label" for="card_type_code"><strong>Brand Name <em>*</em></strong></label>
			<div class="controls">
				<?php			

				$brand_name_options = array('' => 'Please Select...');

				foreach($brand_name_details as $brand_name)
				{
					$brand_name_options[$brand_name->motorcycle_brand_id] = $brand_name->motorcycle_brand_id . ' - ' . $brand_name->brand_name;
				}
				echo form_dropdown('motorcycle_brand_id', $brand_name_options, set_value('motorcycle_brand_id',$brand_name->motorcycle_brand_id),'id="motorcycle_brand_id" class="span5"');
				?>			
			</div>
			<span class='label label-important' id='card_type_id_error' style='display:none;'>Brand Name Field is required.</span>
		</div>		

		<div class="control-group <?= $this->form_validation->error_class('model_name')?>">
			<label class="control-label" for="card_type_code"><strong>Model Name <em>*</em></strong></label>
			<div class="controls">
				<?php			

				$brand_name_options = array('' => 'Please Select...');

				foreach($brand_name_details as $brand_name)
				{
					$brand_name_options[$brand_name->motorcycle_brand_id] = $brand_name->motorcycle_brand_id . ' - ' . $brand_name->brand_name;
				}
				echo form_dropdown('motorcycle_brand_id', $brand_name_options, set_value('motorcycle_brand_id'),'id="motorcycle_brand_id" class="span5"');
				?>			
			</div>
			<span class='label label-important' id='card_type_id_error' style='display:none;'>Brand Name Field is required.</span>
		</div>		








		
		<div class="control-group <?= $this->form_validation->error_class('complete_address') ?>">
			<label class="control-label" for="complete_address">Complete Address <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Complete Address" name="complete_address" id="complete_address" value="<?= $this->form_validation->set_value('complete_address',$spare_part_details->complete_address) ?>">
				<p class="help-block"><?= $this->form_validation->error('complete_address'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('contact_number') ?>">
			<label class="control-label" for="contact_number">Contact Number <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Contact Number" name="contact_number" id="contact_number" value="<?= $this->form_validation->set_value('contact_number',$spare_part_details->contact_number) ?>">
				<p class="help-block"><?= $this->form_validation->error('contact_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<div id="image_filename">					
					<?php if(!empty($spare_part_details->image_filename)):?>
						<img id="result_image" style="width:180px; height:180px;" alt="" src="<?= $upload_url; ?>/<?= $spare_part_details->image_filename ?>">
					<?php endif; ?>
					<input type='hidden' value='<?= $spare_part_details->image_filename ?>'>
				</div>
				<div id="image_upload"  class="uploadBox_fu">
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$spare_part_details->is_active),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update spare_part</button>
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
		target : base_url + '/admin/upload/process?filename=spare_part_<?= $spare_part_details->sku?>&location=<?=$_upload_url?>&width=960&ts=<?=time()?>',
		onComplete: function() {

			$("#result_image").attr('src', '<?=$upload_url?>/spare_part_'+<?= $spare_part_details->sku?>+'.jpg?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/spare_parts/maintenance/update_image',
		        data: {
					"filename": '<?= "spare_part_" . $spare_part_details->sku .".jpg"?>',
					"sku":<?= $spare_part_details->sku?>
				},
		        on_success: function(data) {		
		        }
		    });		
		}
	});
	
</script>
<?php endif; ?>
