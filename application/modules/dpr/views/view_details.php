<?php
	
	// tin number
	$branch_details = $this->human_relations_model->get_branch_by_id($request_detail_details->branch_id);
	if (empty($branch_details)) {
		$branch_name = "N/A";
		$branch_tin = "N/A";
	} else {
		$branch_name = $branch_details->branch_name;
		$branch_tin = $branch_tin->tin;
	}

	// form type
	$form_type_details = $this->dpr_model->get_form_type_by_id($request_detail_details->form_type_id);

	$is_accountable = "(NON-ACCOUNTABLE)";
	if ($form_type_details->is_accountable == 1)
		$is_accountable = "(ACCOUNTABLE)";

	// printing press
	$printing_press_details = $this->dpr_model->get_printing_press_by_id($request_detail_details->printing_press_id);

?>
<fieldset >		
	<div>
		<table  class='table table-bordered'>
			<thead>
				<th colspan=4>Summary</th>
			</thead>
			<tbody>
				<tr>
					<td><strong>Request Code:</strong></td>
					<td><?= $request_summary_details->request_code; ?></td>
					<td><strong>Overall Status:</strong></td>
					<td><?= $request_summary_details->status; ?></td>
				</tr>	
				<tr>
					<td><strong>Requested By:</strong></td>
					<td>N/A</td>		
					<td><strong>Date Requested:</strong></td>
					<td><?= $request_summary_details->insert_timestamp; ?></td>
				</tr>
				<tr>
					<td><strong>Approved By:</strong></td>
					<td>N/A</td>
					<td><strong>Date Approved:</strong></td>					
					<td>N/A</td>					
				</tr>								
				<tr>
					<td><strong>Remarks:</strong></td>
					<td colspan=3>N/A</td>							
				</tr>				
			<tbody>
		</table>	
	</div>

	<div>
		<table  class='table table-bordered'>
			<thead>
				<th colspan=4>Details</th>
			</thead>
			<tbody>				
				<tr>
					<td><strong>Branch:</strong></td>
					<td><?= $branch_name; ?></td>
					<td><strong>Tin Number:</strong></td>
					<td><?= $branch_tin; ?></td>
				</tr>	
				<tr>
					<td><strong>Form Type:</strong></td>
					<td><?= $form_type_details->name; ?> <?= $is_accountable ?></td>		
					<td><strong>Last Serial Number:</strong></td>
					<td><?= $request_detail_details->last_serial_number; ?></td>
				</tr>
				<tr>
					<td><strong>Quantity:</strong></td>
					<td><?= $request_detail_details->quantity; ?></td>
					<td><strong>Printing Press:</strong></td>					
					<td><?= $printing_press_details->complete_name; ?></td>					
				</tr>								
				<tr>
					<td><strong>Sent ATP:</strong></td>
					<td><?= $request_detail_details->send_atp; ?></td>		
					<td><strong>Received ATP:</strong></td>
					<td><?= $request_detail_details->receive_atp; ?></td>		
				</tr>				
				<tr>
					<td><strong>Faxed to Printer:</strong></td>
					<td><?= $request_detail_details->faxed_to_printer; ?></td>		
					<td><strong>Received from Printer</strong></td>
					<td><?= $request_detail_details->received_from_printer; ?></td>		
				</tr>
				<tr>
					<td><strong>Sent for Stamping:</strong></td>
					<td><?= $request_detail_details->send_for_stamping; ?></td>		
					<td><strong>Received from Stamping</strong></td>
					<td><?= $request_detail_details->received_from_stamping; ?></td>		
				</tr>
				<tr>
					<td><strong>Date Delivered:</strong></td>
					<td><?= $request_detail_details->date_delivered ; ?></td>		
					<td><strong>Remarks:</strong></td>
					<td><?= $request_detail_details->remarks; ?></td>		
				</tr>
			<tbody>
		</table>	
	</div>
						
</fieldset>
