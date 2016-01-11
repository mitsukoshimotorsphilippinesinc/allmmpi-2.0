<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Expenses <a href='/information_technology/expenses/add' class='btn btn-small'  style="float:right;margin-right:-30px;margin-top:5px;"><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<hr/>

<form id='search_details' method='get' action =''>

	<strong>Search By:&nbsp;</strong>
	<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">		
		<option value="branch_name">Branch</option>
		<option value="department_name">Department</option>
		<option value="approved_by">Approved By</option>
		<option value="requested_by">Requested By</option>
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
			<th style="width: 5em;">Date Approved</th>
			<th>Total Amount</th>
			<th style="width:20em;">Particulars</th>
			<th style="width:15em;">Branch / Department</th>
			<th style="width:10em;">Approved By</th>
			<th style="width:8em;">App# / Auth#</th>
			<th style="">Requested By</th>
			<th style='width: 7em;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($transfers)): ?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Record Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($transfers as $r): ?>
		<tr>			
			<td><?= substr($r->date_approved, 0, 10); ?></td>
			
			<?php
			$total_amount = 0;

			// get number of items
			$where = "expense_summary_id = " . $r->expense_summary_id . "";
			$expenses_detail_info = $this->information_technology_model->get_expense_detail($where);
	
			$where = "expense_summary_id = " . $r->expense_summary_id;
			$expenses_details = $this->information_technology_model->get_expense_detail($where);

			$expense_detail_details = $this->information_technology_model->get_expense_detail("expense_summary_id = " . $r->expense_summary_id);				

			$items_html = "<table style='margin-bottom:0px;' class='table table-condensed table-bordered'>
								<thead>
								</thead>
								<tbody>";

			foreach ($expense_detail_details as $rdd) {
				$expense_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($rdd->repair_hardware_id);
				$amount = number_format($rdd->amount, 2, '.', ',');

				$items_html .= "<tr>
									<td style='width:110px;'>{$expense_hardware_details->repair_hardware_name} x {$rdd->quantity}</td>
									<td style='text-align:right;'>{$amount}</td>
								</tr>";

				$total_amount = $total_amount + $rdd->amount;	
				$total_amount = number_format($total_amount, 2, '.', ',');
								
			}

			$items_html .= "</tbody>
						</table>";

			echo "<td style='text-align:right;'>{$total_amount}</td>
				  <td>{$items_html}</td>";


			if ($r->branch_id <> 0) {
				$requestor_details = $this->human_relations_model->get_branch_by_id($r->branch_id);

				if (count($requestor_details) == 0) {
					echo "<td>N/A</td>";
				} else { 
					echo "<td>{$requestor_details->branch_name}</td>"; 
				}			

			} else {

				// get requestor details
				$id = str_pad($r->id_number, 7, '0', STR_PAD_LEFT);
				$requestor_details = $this->human_relations_model->get_employment_information_view_by_id($id);

				if (count($requestor_details) == 0) {
					echo "<td>N/A</td>";
				} else { 
					echo "<td>{$requestor_details->complete_name}</td>"; 
				}			
			}
			?>
			
			<?php
			// get signatory details
			$signatory_details = $this->information_technology_model->get_expense_signatory_by_id($r->approved_by);

			if (count($signatory_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$signatory_details->complete_name}</td>"; 
			}		
			?>
			
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
			
			<td><?= $approval_number ?> <b>/</b> <?= $authority_number ?></td>
			
			<?php
			// get requestor details
			$id = str_pad($r->requested_by, 7, '0', STR_PAD_LEFT);
			$requester_details = $this->human_relations_model->get_employment_information_view_by_id($id);

			if (count($requester_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$requester_details->complete_name}</td>"; 
			}		
			?>
			<td>				
				<a href='/information_technology/expenses/edit/<?= $r->expense_summary_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/information_technology/expenses/delete/<?= $r->expense_summary_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>