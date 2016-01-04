<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Expense  <a href='/information_technology/expenses/listing' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<?php if (empty($expense_details)): ?>
	<h3>Dealer not found.</h3>
<?php else: ?>
<form action='/information_technology/expenses/delete_expense/<?= $expense_details->expense_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='expense_id' name='expense_id' value='<?= $expense_details->expense_id ?>' />
		<?php
			$branch_dept_type = "";
			
			if ($expense_details->branch_id == 0) {
				if ($expense_details->department_id == 0) {
					$branch_dept_type = "N/A";
					$branch_dept_name = "N/A";
				} else {
					$branch_dept_type = "Department";
					$branch_dept_name = $expense_details->department_name;
				}
			} else {
				$branch_dept_type = "Branch";
				$branch_dept_name = $expense_details->branch_name;
			}

		?>

		<div class="control-group">
			<label class="control-label" for="branch_dept_type">Type </label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Type" name="branch_dept_type" readonly="readonly" id="branch_dept_type" value="<?= $branch_dept_type ?>">				
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="branch_dept_name">Name </label>
			<div class="controls">
				<input type="text" class='span4' placeholder="Type" name="branch_dept_name" readonly="readonly" id="branch_dept_name" value="<?= $branch_dept_name ?>">				
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="Particulars">Particulars <em>*</em></label>
			<div class="controls">
				<textarea type="text" class='span8' placeholder="Particulars" name="particulars" id="particulars" style="resize:none;" readonly="readonly"><?= $expense_details->particulars ?></textarea>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="amount">Amount </label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Amount" name="amount" readonly="readonly" id="amount" value="<?= $expense_details->amount ?>">				
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="expense_signatory_name">Approved By </label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Approved By" name="expense_signatory_name" readonly="readonly" id="expense_signatory_name" value="<?= $expense_details->expense_signatory_name ?>">				
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="approval_number">Approval Number </label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Approval Number" name="approval_number" readonly="readonly" id="approval_number" value="<?= $expense_details->approval_number ?>">				
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="authority_number">Authority Number <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Authority Number" readonly="readonly" name="authority_number" id="authority_number" value="<?= $expense_details->authority_number ?>">
				<p class="help-block"><?= $this->form_validation->error('authority_number'); ?></p>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="requested_by">Requested By </label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Requested By" name="requested_by" readonly="readonly" id="requested_by" value="<?= $expense_details->requester_name ?>">				
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="date_approved">Date of Approval </label>
			<div class="controls">
				<input type="text" class='span2' placeholder="Date of Approval" name="date_approved" readonly="readonly" id="date_approved" value="<?= substr($expense_details->date_approved, 0, 10) ?>">
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Delete Expense</button>
			</div>
		</div>
	</fieldset>
</form>
<?php endif; ?>

