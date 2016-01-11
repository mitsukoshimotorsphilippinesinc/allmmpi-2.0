<?php	
	$breadcrumb_container = assemble_breadcrumb();
	$date_approved = date('Y-m-d');
?>

<?= $breadcrumb_container; ?>
<h2>Add New Expense  <a href='/information_technology/expenses/listing' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/information_technology/expenses/add_expense' method='post' class='form-horizontal'>

	<fieldset >		
		<div class="control-group <?= $this->form_validation->error_class('branch_dept_type') ?>">
			<label class="control-label" for="branch_dept_type">Type <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('branch' => 'Branch', 'department' => 'Department');
				
				echo form_dropdown("branch_dept_type", $options, set_value('branch_dept_type'),"id='branch_dept_type' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('branch_dept_type'); ?></p>
			</div>
		</div>

		<div class="control-group" id="branch_name_container">
			<label class="control-label" for="branch_name">Branch Name <em>*</em></label>
			<div class="controls">
				<?php

				$where = "is_active = 1";
				$branch_details = $this->human_relations_model->get_branch($where, NULL, "branch_name");

				$branch_options = array();
				//$branch_options = array('0' => 'None');
				foreach ($branch_details as $bd) {
				 	$branch_options[$bd->branch_id] = $bd->branch_name;
				}				
				?>

				<?= form_dropdown('branch_name', $branch_options, set_value('branch_name'),'id="branch_name"') ?>
				
				<p class="help-block"><?= $this->form_validation->error('branch_name'); ?></p>
			</div>
		</div>

		<div class="control-group" id="department_name_container" style="display:none;">
			<label class="control-label" for="department_name">Department Name <em>*</em></label>
			<div class="controls">
				<?php

				$where = "is_active = 1";
				$department_details = $this->human_relations_model->get_department($where, NULL, "department_name");

				$department_options = array();
				//$department_options = array('0' => 'None');
				foreach ($department_details as $dd) {
				 	$department_options[$dd->department_id] = $dd->department_name;
				}				
				?>

				<?= form_dropdown('department_name', $department_options, set_value('department_name'),'id="department_name"') ?>
				
				<p class="help-block"><?= $this->form_validation->error('department_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('particulars') ?>">
			<label class="control-label" for="Particulars">Particulars <em>*</em></label>
			<div class="controls">
				<textarea type="text" class='span8' placeholder="Particulars" name="particulars" id="particulars" value=""><?= set_value('particulars') ?></textarea>
				<p class="help-block"><?= $this->form_validation->error('particulars'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('amount') ?>">
			<label class="control-label" for="amount">Amount <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Amount" name="amount" id="amount" value="<?= set_value('amount') ?>">
				<p class="help-block"><?= $this->form_validation->error('amount'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('expense_signatory_name') ?>">
			<label class="control-label" for="expense_signatory_name">Approved By <em>*</em></label>
			<div class="controls">
				<?php

				$where = "is_active = 1";
				$expense_signatory_details = $this->information_technology_model->get_expense_signatory($where, NULL, "complete_name");



				$expense_signatory_options = array();
				$expense_signatory_options = array('' => 'Select Signatory...');
				foreach ($expense_signatory_details as $es) {
				 	$expense_signatory_options[$es->expense_signatory_id] = $es->complete_name;
				}				
				?>

				<?= form_dropdown('expense_signatory_name', $expense_signatory_options, set_value('expense_signatory_name'), 'id="expense_signatory_name"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('expense_signatory_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('approval_number') ?>">
			<label class="control-label" for="approval_number">Approval Number <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Approval Number" name="approval_number" id="approval_number" value="<?= set_value('approval_number') ?>">
				<p class="help-block"><?= $this->form_validation->error('approval_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('authority_number') ?>">
			<label class="control-label" for="authority_number">Authority Number <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Authority Number" name="authority_number" id="authority_number" value="<?= set_value('authority_number') ?>">
				<p class="help-block"><?= $this->form_validation->error('authority_number'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('requested_by') ?>">
			<label class="control-label" for="requested_by">Requested By <em>*</em></label>
			<div class="controls">
				<?php

				$where = "department_id = 5 AND is_employed = 1";
				$it_personnel_details = $this->human_relations_model->get_employment_information_view($where, NULL, "complete_name");



				$it_personnel_options = array();
				$it_personnel_options = array('' => 'Select Requester...');
				foreach ($it_personnel_details as $ipd) {
				 	$it_personnel_options[$ipd->id_number] = $ipd->complete_name;
				}				
				?>

				<?= form_dropdown('requested_by', $it_personnel_options, set_value('requested_by'), 'id="requested_by"') ?>
				
				
				<p class="help-block"><?= $this->form_validation->error('requested_by'); ?></p>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="remarks">Date of Approval <em>*</em></label>			
			<div class="controls">
				<input type="text" class="input-medium" id="date_approved" name='date_approved' readonly='readonly' style='cursor:pointer;' />
				<span id='date_approved_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
			</div>	
		</div>


		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Add New Expense</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	$("#date_approved").datepicker({
        timeFormat: 'hh:mm tt',
		'dateFormat' : "yy-mm-dd",			
	});
	
	$("#date_approved_icon").click(function(e) {
		$("#date_approved").datepicker("show");
	});
	
	$("#date_approved").datepicker('setDate', '<?= $date_approved ?>');
	$("#date_approved").datepicker("option", "changeMonth", true);
	$("#date_approved").datepicker("option", "changeYear", true);
	

	$("#branch_dept_type").click(function(){
		if ($(this).val() == "branch") {
			$("#branch_name_container").show();
			$("#department_name_container").hide();
		} else {
			$("#branch_name_container").hide();
			$("#department_name_container").show();
		}
	});

	$("#amount").keypress(function(event) {
		if ((event.which != 0) && (event.which != 8) && (event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        	event.preventDefault();
    	}
    
    	/*var text = $(this).val();
	    if ((text.indexOf('.') != -1) && (text.substring(text.indexOf('.')).length > 2)) {
	        event.preventDefault();
	    }*/
	});	

</script>