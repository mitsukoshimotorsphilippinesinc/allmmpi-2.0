<fieldset >		
	<div>
		<table  class='table table-bordered'>
			<thead>				
			</thead>
			<tbody>
				<tr>
					<td><strong>Request Code:</strong></td>
					<td><?= $warehouse_claim->request_code; ?></td>
					<td><strong>Engine:</strong></td>
					<td><?= $warehouse_claim->engine; ?></td>
				</tr>	
				<tr>
					<td><strong>Requestor:</strong></td>
					<?php
					$requestor_details = $this->human_relations_model->get_employment_information_by_id($warehouse_claim->id_number);

					if (count($requestor_details) == 0) {
						echo "<td>N/A</td>";
					} else { 
						echo "<td>{$requestor_details->complete_name}</td>"; 
					}		
					?>					
					<td><strong>Chassis:</strong></td>
					<td><?= $warehouse_claim->chassis; ?></td>
				</tr>
				<tr>
					<td><strong>Status:</strong></td>
					<td><?= $warehouse_claim->status; ?></td>
					<td><strong>Brand/Model:</strong></td>					
					<?php
					$motor_brand_model_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id($warehouse_claim->motorcycle_brand_model_id);				

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
					$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($warehouse_claim->warehouse_id);

					if (count($warehouse_details) == 0) {
						echo "<td>N/A</td>";
					} else { 
						echo "<td>{$warehouse_details->warehouse_name}</td>"; 
					}
					?>
					<td><strong>Approved By (Warehouse):</strong></td>					
					<?php
					$warehouse_approvedby_details = $this->human_relations_model->get_employment_information_by_id($warehouse_claim->warehouse_approved_by);

					if (count($warehouse_approvedby_details) == 0) {
						echo "<td>N/A</td>";
					} else { 
						echo "<td>{$warehouse_approvedby_details->complete_name}</td>"; 
					}		
					?>					
				</tr>				
			<tbody>
		</table>	
	</div>

	<div>
		<table  class='table table-striped table-bordered'>
			<thead>
				<tr>			
					<th style=''>Item</th>
					<th>SRP</th>
					<th style='width:100px;'>Discount</th>
					<th style='width:100px;'>Discount Amount</th>
					<th style='width:100px;'>Good Qty</th>
					<th style='width:100px;'>Bad Qty</th>			
					<th style='width:120px;'>Total Amount</th>
					<th style='width:70px;'>Status</th>			
					<th style=''>Remarks</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($warehouse_claim_details)):?>
					<tr><td colspan='9' style='text-align:center;'><strong>No Record Found</strong></td></tr>
				<?php else: ?>
				<?php foreach ($warehouse_claim_details as $wrd): ?>
				<tr>
					<td><?= $wrd->item_id; ?></td>
					<td><?= $wrd->srp; ?></td>
					<td><?= $wrd->discount; ?></td>
					<td><?= $wrd->discount_amount; ?></td>
					<td><?= $wrd->good_quantity; ?></td>
					<td><?= $wrd->bad_quantity; ?></td>
					<td><?= $wrd->total_amount; ?></td>
					<td><?= $wrd->status; ?></td>
					<td><?= $wrd->remarks; ?></td>
				</tr>	
				<?php endforeach; ?>
				<?php endif; ?>
			<tbody>
		</table>	
	</div>
													
</fieldset>
