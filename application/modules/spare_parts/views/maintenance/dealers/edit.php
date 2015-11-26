<?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/dealers";
	//$upload_url ="c:/laragon/wwww/allmppi/webroot_admin/assets/media/dealers";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Edit Dealer  <a href='/spare_parts/maintenance/dealers' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($dealer_details)): ?>
<h3>results not found.</h3>
<?php else: ?>
<form action='/spare_parts/maintenance/edit_dealer/<?= $dealer_details->dealer_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('complete_name') ?>">
			<label class="control-label" for="complete_name">Complete Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Complete Name" name="complete_name" id="complete_name" value="<?= $this->form_validation->set_value('complete_name',$dealer_details->complete_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('complete_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('complete_address') ?>">
			<label class="control-label" for="complete_address">Complete Address <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Complete Address" name="complete_address" id="complete_address" value="<?= $this->form_validation->set_value('complete_address',$dealer_details->complete_address) ?>">
				<p class="help-block"><?= $this->form_validation->error('complete_address'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('contact_number') ?>">
			<label class="control-label" for="contact_number">Contact Number <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Contact Number" name="contact_number" id="contact_number" value="<?= $this->form_validation->set_value('contact_number',$dealer_details->contact_number) ?>">
				<p class="help-block"><?= $this->form_validation->error('contact_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('agent_name') ?>">
			<label class="control-label" for="agent_name">Agent Name </label>
			<div class="controls">
				<?php

				$where = "is_active = 1";
				$agent_details = $this->spare_parts_model->get_agent($where, NULL, "complete_name");

				$agent_options = array();
				$agent_options = array('0' => 'None');
				foreach ($agent_details as $ad) {
				 	$agent_options[$ad->agent_id] = $ad->complete_name;
				}				
				?>

				<?= form_dropdown('agent_id',$agent_options, $dealer_details->agent_id,'id="agent_id"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('agent_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('max_discount') ?>">
			<label class="control-label" for="max_discount">Max Discount </label>
			<div class="controls">
				<?php		
				for($i = 0; $i<=100;$i++) {
				 	$discount_options[$i] = $i;
				}				
				?>

				<?= form_dropdown('max_discount',$discount_options, ($dealer_details->max_discount * 100),'id="max_discount"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('max_discount'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<div id="image_filename">					
					<?php if(!empty($dealer_details->image_filename)):?>
						<img id="result_image" style="width:180px; height:180px;" alt="" src="<?= $upload_url; ?>/<?= $dealer_details->image_filename ?>">
					<?php endif; ?>
					<input type='hidden' value='<?= $dealer_details->image_filename ?>'>
				</div>
				<div id="image_upload"  class="uploadBox_fu">
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$dealer_details->is_active),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update Dealer</button>
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
		target : base_url + '/admin/upload/process?filename=dealer_<?= $dealer_details->dealer_id?>&location=<?=$_upload_url?>&width=960&ts=<?=time()?>',
		onComplete: function() {

			$("#result_image").attr('src', '<?=$upload_url?>/dealer_'+<?= $dealer_details->dealer_id?>+'.jpg?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/spare_parts/maintenance/update_image',
		        data: {
					"filename": '<?= "dealer_" . $dealer_details->dealer_id .".jpg"?>',
					"_id":<?= $dealer_details->dealer_id?>,
					"maintenance_name": "dealer"
				},
		        on_success: function(data) {		
		        }
		    });		
		}
	});
	
</script>
<?php endif; ?>
