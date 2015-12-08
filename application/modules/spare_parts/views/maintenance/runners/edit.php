<?php
	echo js('libs/uploadrr.min.js');

	$upload_url = $this->config->item("media_url") . "/employee";	
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Edit Runner  <a href='/spare_parts/maintenance/runners' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($runner_details)): ?>
<h3>Results not found.</h3>
<?php else: ?>
<div class="span6">
<form action='/spare_parts/maintenance/edit_runner/<?= $runner_details->runner_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('runner_name') ?>">
			<label class="control-label" for="runner_name">Runner Name <em>*</em></label>
			<div class="controls">
				
				<?php
				$where = "id_number = " . $runner_details->id_number;
					
				$runner_info = $this->human_relations_model->get_employment_information_view($where);

				?>

				<input type="text" class='span4' placeholder="Runner Name" name="runner_name" id="runner_name" value="<?= $this->form_validation->set_value('runner_name',$runner_info[0]->complete_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('runner_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('_name') ?>">
			<label class="control-label" for="warehouse_name">Warehouse Name </label>
			<div class="controls">
				<?php

				$where = "is_active = 1";
				$warehouse_details = $this->spare_parts_model->get_warehouse($where, NULL, "warehouse_name");

				$warehouse_options = array();
				$warehouse_options = array('0' => 'None');
				foreach ($warehouse_details as $ad) {
				 	$warehouse_options[$ad->warehouse_id] = $ad->warehouse_name;
				}				
				?>

				<?= form_dropdown('warehouse_name',$warehouse_options, set_value('warehouse_name', $runner_details->warehouse_id),'id="warehouse_id"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('warehouse_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$runner_details->is_active),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update Runner</button>
			</div>
		</div>
	</fieldset>
</form>
</div>
<div class="span4">
	<label class="control-label" for="short_body">Image:</label>
	<div id="runner-image">		
	</div>
</div>	
<script type="text/javascript">
	
	$(document).ready(function(){

		var _id_number = <?= $runner_details->id_number ?>;
                		
		b.request({
			url : '/spare_parts/maintenance/get_runner_image',
			data : {				
				'id_number' : _id_number,								
			},
			on_success : function(data) {
				$("#runner-image").html(data.data.html);	 
			}

		})
		return false;

	});

	
</script>
<?php endif; ?>
