<h4>Reprocessed Item/s</h4>
<table class="table table-condensed table-bordered">
	<thead>
		<tr>
			<th colspan="11" style="color:blue;font-size:16px">Reprocessed Items</th>
			</tr>
		<tr>
			<th style=''>Action</th>			
			<th style='width:80em;'>Description</th>						
			<th style=''>Good Qty</th>
			<th style=''>Bad Qty</th>
			<th style=''>Total Qty</th>
			<th style=''>Recipient</th>
			<th style=''>Charge Discount</th>
			<th style=''>Charge Amount</th>			
			<th style=''>Total Amount</th>			
			<th style=''>Status</th>			
			<th style=''>Remarks</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if(empty($reprocessed_item_details)):?>
			<tr><td colspan='11' style='text-align:center;'><strong>No Record Found</strong></td></tr>
		<?php else: ?>
		<?php foreach ($reprocessed_item_details as $rid): 

			// check where to look the request details
			$request_detail_details_sql = "SELECT 
											* 
										FROM is_". $department_module_details->segment_name ."_detail 
										WHERE ". $department_module_details->segment_name ."_detail_id = '" . $rid->request_detail_id . "'";									

			$request_detail_details = $this->db_spare_parts->query($request_detail_details_sql);
			$request_detail_details = $request_detail_details->result();		
			$request_detail_details = $request_detail_details[0];					

			$item_view_details = $this->spare_parts_model->get_item_view_by_id($request_detail_details->item_id);

			$complete_description = "[" . $item_view_details->sku . "][" . $item_view_details->model_name . " / " . $item_view_details->brand_name . "] " . $item_view_details->description;

			$status_class = strtolower(trim($rid->status));			
			$status_class = str_replace(" ", "-", $status_class);		

			$recipient_name = NULL;
			if (!(($rid->id_number) == NULL)) {
				$recipient_details = $this->human_relations_model->get_employment_information_view_by_id($rid->id_number);
				$recipient_name = $recipient_details->complete_name;				
			}

		?>
		<tr>
			<td><?= $rid->action; ?></td>	
			<td><?= $complete_description; ?></td>
			<td style="text-align:right;"><?= number_format($rid->good_quantity, 2); ?></td>
			<td style="text-align:right;"><?= number_format($rid->bad_quantity, 2); ?></td>
			<td style="text-align:right;"><?= number_format(($rid->good_quantity + $rid->bad_quantity), 2)  ?></td>
			<td style="text-align:right;"><?= $recipient_name; ?></td>
			<td style="text-align:right;"><?= $rid->charge_discount; ?></td>
			<td style="text-align:right;"><?= number_format($rid->charge_discount_amount, 2); ?></td>
			<td style="text-align:right;"><?= number_format($rid->total_amount, 2); ?></td>			
			<td><span class='label label-<?= $status_class ?>' ><?= $rid->status; ?></span></td>	
		
			<td class="items_row" style="width:20%;">
				<div class="items_list">
					<ul id="" class="unstyled">
				<?php
				$item_remarks = json_decode($rid->remarks);

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
	</tbody>	
</table>	