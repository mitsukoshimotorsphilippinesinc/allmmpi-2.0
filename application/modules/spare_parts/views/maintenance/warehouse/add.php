<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Add New Warehouse  <a href='/spare_parts/maintenance/warehouse' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/spare_parts/maintenance/add_warehouse' method='post' class='form-horizontal'>
	<fieldset >		
		<div class="control-group <?= $this->form_validation->error_class('warehouse_name') ?>">
			<label class="control-label" for="warehouse_name">Warehouse Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Warehouse Name" name="warehouse_name" id="warehouse_name" value="<?= set_value('warehouse_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('warehouse_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('description') ?>">
			<label class="control-label" for="description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="description" id="description" value="<?= set_value('description') ?>">
				<p class="help-block"><?= $this->form_validation->error('description'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('warehouse_head') ?>">
			<label class="control-label" for="warehouse_head">Warehouse Head <em>*</em></label>
			<div class="controls">
				<?php

				$where = "is_employed = 1 AND position_id = 126"; // warehouse supervisor
				$employment_info_view_details = $this->human_relations_model->get_employment_information_view($where, NULL, "complete_name");

				$warehouse_head_options = array();
				$warehouse_head_options = array('0' => 'None');
				foreach ($employment_info_view_details as $ad) {
				 	$warehouse_head_options[$ad->id_number] = $ad->complete_name;
				}				
				?>

				<?= form_dropdown('warehouse_head',$warehouse_head_options, NULL,'id="warehouse_head"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('warehouse_head'); ?></p>
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
				<button type="submit" class="btn btn-primary">Add New Warehouse</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript"></script>