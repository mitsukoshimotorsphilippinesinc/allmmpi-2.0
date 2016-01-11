<fieldset >		
	<div>
		<table  class='table table-bordered'>
			<thead>				
			</thead>
			<tbody>
				<tr>
					<td><strong>Request Code:</strong></td>
					<td><?= $segment_request_summary->request_code; ?></td>
					<td><strong>Engine:</strong></td>
					<td><?= $segment_request_summary->engine; ?></td>
				</tr>	
				<tr>
					<td><strong>Requestor:</strong></td>
					<?php

					$module_code = substr($segment_request_summary->request_code, 0, 2);

					if ($module_code == 'DL') {
						$requestor_details = $this->spare_parts_model->get_dealer_by_id($segment_request_summary->dealer_id);
					} else {	
						$requestor_details = $this->human_relations_model->get_employment_information_view_by_id($segment_request_summary->id_number);
					}

					if (count($requestor_details) == 0) {
						echo "<td>N/A</td>";
					} else { 
						echo "<td>{$requestor_details->complete_name}</td>"; 
					}		
					?>					
					<td><strong>Chassis:</strong></td>
					<td><?= $segment_request_summary->chassis; ?></td>
				</tr>
				<tr>
					<td><strong>Status:</strong></td>
					<?php
					$status_class = strtolower(trim($segment_request_summary->status));
					$status_class = str_replace(" ", "-", $status_class);					
					?>
					<td><span class='label label-<?= $status_class ?>'><?= $segment_request_summary->status; ?></span></td>					
					<td><strong>Brand/Model:</strong></td>					
					<?php
					if ($module_code == 'DL') { 
						$motor_brand_model_details = array();
					} else {	
						$motor_brand_model_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id($segment_request_summary->motorcycle_brand_model_id);						
					}

					if (count($motor_brand_model_details) == 0) {
						echo "<td>N/A</td>";
					} else { 
						echo "<td>{$motor_brand_model_details->brand_name}" . " - " . "{$motor_brand_model_details->model_name}</td>"; 
					}
				
					?>
				</tr>								
				<tr>
					<td><strong>Warehouse:</strong></td>
					<?php
					$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($segment_request_summary->warehouse_id);

					if (count($warehouse_details) == 0) {
						echo "<td>N/A</td>";
					} else { 
						echo "<td>{$warehouse_details->warehouse_name}</td>"; 
					}
					?>
					<td><strong>Approved By (Warehouse):</strong></td>					
					<?php
					//$warehouse_approvedby_details = $this->human_relations_model->get_employment_information_by_id($segment_request_summary->warehouse_approved_by);

					//if (count($warehouse_approvedby_details) == 0) {
						echo "<td>N/A</td>";
					//} else { 
					//	echo "<td>{$warehouse_approvedby_details->complete_name}</td>"; 
					//}		
					?>					
				</tr>
				<tr>
					<?php
					if ($module_code == 'DL') {
						echo "<td><strong>P.O. Number:</strong></td>
								<td>{$segment_request_summary->purchase_order_number}</td>
								";	
					} else {			
						echo "<td><strong>MTR Number:</strong></td>
								<td>{$segment_request_summary->cross_reference_number}</td>
								";
					}
					?>

					<td><strong>Remarks:</strong></td>
					<?php
						if (strlen(trim($segment_request_summary->remarks)) > 0)
							echo "<td><a href='#' id='view-full-remarks' data='{$segment_request_summary_remarks}'><u>View Remarks</u></a></td>";
						else
							echo "<td><strong></strong></td>";
					?>
				</tr>					
			<tbody>
		</table>	
	</div>
	
	<div>
		<table  class='table table-striped table-bordered'>
			<thead>
				<tr>
					<th colspan="11" style="color:blue;font-size:16px">Requested Items</th>
				</tr>
				<tr>			
					<th style='width:80em;'>Description</th>
					<th>SRP</th>
					<th style=''>Discount</th>
					<th style=''>Discount Amount</th>
					<th style=''>Good Qty</th>
					<th style=''>Bad Qty</th>
					<th style=''>Total Qty</th>
					<th style=''>Total Amount</th>
					<th style=''>Rack Location</th>
					<th style=''>Status</th>			
					<th style=''>Remarks</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($segment_request_details)):?>
					<tr><td colspan='11' style='text-align:center;'><strong>No Record Found</strong></td></tr>
				<?php else: ?>
				<?php foreach ($segment_request_details as $srd): 

					$item_view_details = $this->spare_parts_model->get_item_view_by_id($srd->item_id);

					$complete_description = "[" . $item_view_details->sku . "][" . $item_view_details->model_name . " / " . $item_view_details->brand_name . "] " . $item_view_details->description;

					$status_class = strtolower(trim($srd->status));			
					$status_class = str_replace(" ", "-", $status_class);		
				?>
				<tr>
					<td><?= $complete_description; ?></td>
					<td style="text-align:right;"><?= $srd->srp; ?></td>
					<td style="text-align:right;"><?= $srd->discount; ?></td>
					<td style="text-align:right;"><?= number_format($srd->discount_amount, 2); ?></td>
					<td style="text-align:right;"><?= number_format($srd->good_quantity, 2); ?></td>
					<td style="text-align:right;"><?= number_format($srd->bad_quantity, 2); ?></td>
					<td style="text-align:right;"><?= number_format(($srd->good_quantity + $srd->bad_quantity), 2); ?></td>
					<td style="text-align:right;"><?= number_format($srd->total_amount, 2); ?></td>
					<td style="text-align:right;"><?= $item_view_details->rack_location; ?></td>
					<td><span class='label label-<?= $status_class ?>' ><?= $srd->status; ?></span></td>	
				
					<td class="items_row" style="width:20%;">
						<div class="items_list">
							<ul id="" class="unstyled">
						<?php
						$item_remarks = json_decode($srd->remarks);

						$html = "";
						if (count($item_remarks) > 0) {
						?>
							<li><p><strong><?= $item_remarks[0]->datetime ?></strong> - <?= $item_remarks[0]->message ?></li>							
						<?php 
						} 
						?>
						</ul>
						<?php if(count($item_remarks) >  1): ?>
							<a class="more_items">More...</a>	
							<?php endif ?>
						</div>
					</td>
				</tr>	
				<?php endforeach; ?>
				<?php endif; ?>
			<tbody>
		</table>	
	</div>

	<div>
		<?php		
		if (count($reprocessed_item_details) > 0) {			
			$data = array('reprocessed_item_details' => $reprocessed_item_details);
			$this->load->view("template_reprocessed_item" , $data);
		}
		?>
	</div>	
													
</fieldset>

<script style="text/javascript">
	
	$("#view-full-remarks").click(function(e){
		//alert($(this).attr("data"));

		var request_code = '<?= $segment_request_summary->request_code ?>';
		var segment_name = '<?= $segment_name ?>';

		b.request({
			url : '/spare_parts/display_request_remarks',
			data : {				
				'remarks' : $(this).attr("data"),
				'request_code' : request_code,
				'segment_name' : segment_name,
			},
			on_success : function(data) {
				
				if (data.status == "1")	{

					// show add form modal					
					proceedApproveRequestModal = b.modal.new({
						title: data.data.title,
						width:600,
						//disableClose: true,
						html: data.data.html,						
					});
					proceedApproveRequestModal.show();

				} else {
					// show add form modal
					//approveRequestModal.hide();					
					errorApproveRequestModal = b.modal.new({
						title: data.data.title,
						width:450,	
						html: data.data.html,
					});
					errorApproveRequestModal.show();	

				}
			}

		})	

	});

	$(document).on('click', '.more_items', function(e) {
		e.preventDefault();
		var item = $(this);
		//change 'More' to 'Less'
		item.parent().children('.more_items').html('Less...');
		item.parent().children('.more_items').attr('class', 'less_items');
		//display the rest of the items
		item.parent().attr('style', 'height:auto; overflow:auto');
	});

	/*
	$(document).on('click', '.less_items', function(e) {
		e.preventDefault();
		var item = $(this);
		//change 'Less' to 'More'
		item.parent().children('.less_items').html('More...');
		item.parent().children('.less_items').attr('class', 'more_items');
		//display only first two items
		item.parent().attr('style', 'height:35px; overflow:hidden');
	});
	*/

</script>