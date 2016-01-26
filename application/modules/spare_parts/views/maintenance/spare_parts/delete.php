<?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/spare_parts";
	//$upload_url ="c:/laragon/wwww/allmppi/webroot_admin/assets/media/spare_parts";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Spare Part  <a href='/spare_parts/maintenance/spare_parts' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($spare_part_details)): ?>
<h3>results not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/delete_spare_part/<?= $spare_part_details->spare_part_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('sku') ?>">
			<label class="control-label" for="complete_name">SKU <em>*</em></label>
			<div class="controls">
				<input readonly="readonly" type="text" class='span2' placeholder="SKU" name="sku" id="sku" value="<?= $this->form_validation->set_value('sku',$spare_part_details->sku) ?>">
				<p class="help-block"><?= $this->form_validation->error('sku'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('motorcycle_brand_id') ?>">
			<label class="control-label" for="card_type_code">Brand Name <em>*</em></label>
			<div class="controls">
				<?php			

				$brand_name_options = array('' => 'Please Select...');

				$brand_name_details = $this->warehouse_model->get_motorcycle_brand();

				foreach($brand_name_details as $brand_name)
				{
					$brand_name_options[$brand_name->motorcycle_brand_id] = $brand_name->motorcycle_brand_id . ' - ' . $brand_name->brand_name;
				}
				echo form_dropdown('motorcycle_brand_id', $brand_name_options, set_value('motorcycle_brand_id',$brand_name->motorcycle_brand_id),'id="motorcycle_brand_id" class="span5" readonly="readonly"');
				?>			
				<p class="help-block"><?= $this->form_validation->error('motorcycle_brand_id'); ?></p>
			</div>
			
		</div>		

		<div class="control-group <?= $this->form_validation->error_class('motorcycle_brand_model_id')?>">
			<label class="control-label" for="card_type_code">Model Name <em>*</em></label>
			<div class="controls">
				<?php			

				$model_name_options = array('' => 'Please Select...');

				$model_name_details = $this->warehouse_model->get_motorcycle_brand_model_class_view();			

				foreach($model_name_details as $mnd)
				{
					$model_name_options[$mnd->motorcycle_brand_model_id] = $mnd->model_name;
				}
				echo form_dropdown('motorcycle_brand_model_id', $model_name_options, set_value('motorcycle_brand_model_id', $mnd->motorcycle_brand_model_id),'id="motorcycle_brand_model_id" class="span5" readonly="readonly"');
				?>	
				<p class="help-block"><?= $this->form_validation->error('motorcycle_brand_model_id'); ?></p>		
			</div>
			
		</div>	

		<div class="control-group <?= $this->form_validation->error_class('part_number') ?>">
			<label class="control-label" for="part_number">Part Number </label>
			<div class="controls">
				<input readonly="readonly" type="text" class='span4' placeholder="Part Number" name="part_number" id="part_number" value="<?= $this->form_validation->set_value('sku',$spare_part_details->part_number) ?>">	
				<p class="help-block"><?= $this->form_validation->error('part_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('srp') ?>">
			<label class="control-label" for="srp">Stock Limit <em>*</em></label>
			<div class="controls">
				<input readonly="readonly" type="text" class='span2 number' placeholder="Stock Limit" name="stock limit" id="stock limit" value="<?= $this->form_validation->set_value('sku',$spare_part_details->stock_limit) ?>">
				<p class="help-block"><?= $this->form_validation->error('stock limit'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('srp') ?>">
			<label class="control-label" for="srp">SRP <em>*</em></label>
			<div class="controls">
				<input readonly="readonly" type="text" class='span2 number' placeholder="SRP" name="srp" id="srp" value="<?= $this->form_validation->set_value('sku',$spare_part_details->srp) ?>">
				<p class="help-block"><?= $this->form_validation->error('srp'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('remarks') ?>">
			<label class="control-label" for="remarks">Remarks </label>
			<div class="controls">
				<input readonly="readonly" type="text" class='span6' placeholder="Remarks" name="remarks" id="remarks" value="<?= $this->form_validation->set_value('sku',$spare_part_details->remarks) ?>">
				<p class="help-block"><?= $this->form_validation->error('remarks'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$spare_part_details->is_active),"id='is_active' style='width:auto;' readonly='readonly'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Delete Spare Part</button>
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
