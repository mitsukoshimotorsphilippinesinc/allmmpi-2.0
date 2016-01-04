<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Expenses <a href='/information_technology/expenses/add_expense' class='btn btn-small'  style="float:right;margin-right:-30px;margin-top:5px;"><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<hr/>

<form id='search_details' method='get' action =''>

	<strong>Search By:&nbsp;</strong>
	<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">		
		<option value="branch_name">Branch</option>
		<option value="department_name">Department</option>
		<option value="expense_signatory_name">Approved By</option>
		<option value="requester_name">Requester</option>
		<option value="authority">Authority Number</option>
		<option value="approval_number">Approval Number</option>
	</select>                 

	<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

	<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
	<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>

	<br/>
	<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>	

	<?php
	if ($search_text == "") {
	?>	
		<div id="search_summary" style="display:none;">
	<?php
	} else {
	?>	
		<div id="search_summary">
	<?php
	};
	?>		
	
		<span class="label label-important">Search Results for:</span>		
		<span class="label label-default"><?= $search_by ?></span>
		<span class="label label-default"><?= $search_text ?></span>
	</div>		
</form>

<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style="width: 5em;">Approval Date</th>
			<th>Amount</th>
			<th style="">Particulars</th>
			<th style="width:15em;">Branch / Department</th>
			<th style="width:10em;">Approved By</th>
			<th style="width:8em;">App# / Auth#</th>
			<th style="">Requested By</th>
			<th style='width: 6.5em;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($expenses)): ?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Record Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($expenses as $r): ?>
		<tr>			
			<td><?= substr($r->date_approved, 0, 10); ?></td>
			<td style="text-align:right;"><?= $r->amount ?></td>
			<td><?= $r->particulars; ?></td>
			<td><?= $r->branch_name; ?></td>
			<td><?= $r->expense_signatory_name; ?></td>
			<?php
				$approval_number = "-";
				$authority_number = "-";

				if (trim($r->approval_number) <> "") {
					$approval_number = $r->approval_number;
				}

				if (trim($r->authority_number) <> "") {
					$authority_number = $r->authority_number;
				}

			?>
			<td><?= $approval_number ?> / <?= $authority_number ?></td>
			<td><?= $r->requester_name; ?></td>
			<td>				
				<a href='/information_technology/expenses/edit_expense/<?= $r->expense_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/information_technology/expenses/delete_expense/<?= $r->expense_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>