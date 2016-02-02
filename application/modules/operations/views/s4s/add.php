<!--?php	
	$breadcrumb_container = assemble_breadcrumb();
?-->

<!--?= $breadcrumb_container; ?-->
<h2>Add New S4S  <a href='/operations/s4s' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/operations/s4s/add' method='post' class='form-horizontal'>
	<fieldset >		
		<div class="control-group <?= $this->form_validation->error_class('reference_number') ?>">
			<label class="control-label" for="reference_number">Reference Code <em>*</em></label>
			<div class="controls">
				<input type="text" class='span3' placeholder="Reference Code" name="reference_number" id="reference_number" value="<?= set_value('reference_number') ?>">
				<p class="help-block"><?= $this->form_validation->error('reference_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('pp_name') ?>">
			<label class="control-label" for="pp_name">Policy Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Policy Name" name="pp_name" id="pp_name" value="<?= set_value('pp_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('pp_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('pp_description') ?>">
			<label class="control-label" for="pp_description">Description <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Description" name="pp_description" id="pp_description" value="<?= set_value('pp_description') ?>">
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

				<?= form_dropdown('department_id',$department_options, set_value('department_id'),'id="department_id"') ?>										

				<p class="help-block"><?= $this->form_validation->error('department_id'); ?></p>			
			</div>	
		</div>

		<?php
			$s4s_details = $this->human_relations_model->get_s4s(NULL, NULL, "document_sequence DESC");	

			if (empty($s4s_details)) {
				$document_sequence = 1;
			} else {
				$document_sequence = $s4s_details[0]->document_sequence;
			}

		?>

		<div class="control-group <?= $this->form_validation->error_class('document_sequence') ?>">
			<label class="control-label" for="document_sequence">Document Sequence </label>
			<div class="controls">
				<input type="text" class='span2' placeholder="<?= $document_sequence ?>" name="document_sequence" id="document_sequence" value="<?= set_value('document_sequence') ?>">
				<p class="help-block"><?= $this->form_validation->error('document_sequence'); ?></p>
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
				<button type="submit" class="btn btn-primary">Add New S4S</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript"></script>