<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Add New Runner  <a href='/spare_parts/maintenance/runners' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<div class="span6">
<form action='/spare_parts/maintenance/add_runner' method='post' class='form-horizontal'>
	<fieldset >				
		<div class="control-group <?= $this->form_validation->error_class('runner_name') ?>">
			<label class="control-label" for="runner_name">Runner Name <em>*</em></label>
			<div class="controls">
				<?php

				$where = "is_employed = 1 
						AND 
							position_id IN 
							(SELECT position_id from rf_position where position_name in ('Partsman', 'Stockman', 'Warehouseman'))";

				$runner_details = $this->human_relations_model->get_employment_information_view($where, NULL, "last_name");



				$runner_options = array();
				$runner_options = array('0' => 'None');
				foreach ($runner_details as $rd) {
				 	$runner_options[$rd->id_number] = $rd->complete_name;
				}				
				?>

				<?= form_dropdown('runner_name',$runner_options, NULL,'id="runner_name"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('runner_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('warehouse_name') ?>">
			<label class="control-label" for="warehouse_name">Warehouse Name <em>*</em></label>
			<div class="controls">
				<?php

				$where = "is_active = 1";

				$warehouse_details = $this->spare_parts_model->get_warehouse($where, NULL, "warehouse_name");



				$warehouse_options = array();
				$warehouse_options = array('0' => 'None');
				foreach ($warehouse_details as $wd) {
				 	$warehouse_options[$wd->warehouse_id] = $wd->warehouse_name;
				}				
				?>

				<?= form_dropdown('warehouse_name',$warehouse_options, NULL,'id="warehouse_name"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('warehouse_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active'),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Add New Runner</button>
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

	$("#runner_name").change(function(){

		var _id_number = $(this).attr("value");

		// ajax request
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