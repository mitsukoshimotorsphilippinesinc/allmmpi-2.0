<?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/spare_parts";
	//$upload_url ="c:/laragon/wwww/allmppi/webroot_admin/assets/media/spare_parts";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Add Spare Part  <a href='/spare_parts/maintenance/spare_parts' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<form action='/spare_parts/maintenance/add_spare_part' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('sku') ?>">
			<label class="control-label" for="sku">SKU <em>*</em> </label>
			<div class="controls">
				<input type="text" class='span2' placeholder="SKU" name="sku" id="sku" value="<?= set_value('sku') ?>">
				<p class="help-block"><?= $this->form_validation->error('sku'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('description') ?>">
			<label class="control-label" for="description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="description" id="description" value="<?= set_value('description') ?>">
				<p class="help-block"><?= $this->form_validation->error('description'); ?></p>
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
				echo form_dropdown('motorcycle_brand_id', $brand_name_options, NULL,'id="motorcycle_brand_id" class="span5"');
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
				echo form_dropdown('motorcycle_brand_model_id', $model_name_options, NULL,'id="motorcycle_brand_model_id" class="span5"');
				?>	
				<p class="help-block"><?= $this->form_validation->error('motorcycle_brand_model_id'); ?></p>		
			</div>
			
		</div>			

		<div class="control-group <?= $this->form_validation->error_class('part_number') ?>">
			<label class="control-label" for="part_number">Part Number </label>
			<div class="controls">
				<input type="text" class='span4' placeholder="Part Number" name="part_number" id="part_number" value="<?= set_value('part_number') ?>">
				<p class="help-block"><?= $this->form_validation->error('part_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('srp') ?>">
			<label class="control-label" for="srp">Stock Limit <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2 number' placeholder="Stock Limit" name="stock limit" id="stock limit" value="<?= set_value('stock_limit') ?>">
				<p class="help-block"><?= $this->form_validation->error('stock limit'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('srp') ?>">
			<label class="control-label" for="srp">SRP <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2 number' placeholder="SRP" name="srp" id="srp" value="<?= set_value('srp') ?>">
				<p class="help-block"><?= $this->form_validation->error('srp'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('remarks') ?>">
			<label class="control-label" for="remarks">Remarks </label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Remarks" name="remarks" id="remarks" value="<?= set_value('remarks') ?>">
				<p class="help-block"><?= $this->form_validation->error('remarks'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, NULL,"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Add Spare Part</button>
			</div>
		</div>
	</fieldset>
</form>

<script>

$('.number').keypress(function(event) {
		if ((event.which != 0) && (event.which != 8) && (event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        	event.preventDefault();
    	}
    
    	/*var text = $(this).val();
	    if ((text.indexOf('.') != -1) && (text.substring(text.indexOf('.')).length > 2)) {
	        event.preventDefault();
	    }*/
	});

</script>