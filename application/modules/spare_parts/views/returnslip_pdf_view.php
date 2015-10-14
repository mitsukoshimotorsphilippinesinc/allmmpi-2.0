<?php echo css('base.css');?>
<?php echo css('main.css');?>
<?php echo css('google-code-prettify/prettify.css');?>
<?php echo css('switcherMenu.css');?>
<?php echo css('pdf.css');?>
<style type="text/css">
	.rightDiv {float:right;}
	.leftDiv {float:left;}
</style>
<div class="">
	<fieldset>					
		<table class='table table-condensed'>
			<tr>					
				<td colspan="2" align="left" class=""><h3>Return Slip - <?= $department_module_details->module_name ?></h3></td>
			</tr>				
			<tr>
				<td colspan='1' align='left' valign="bottom" ><h4>Request Code: <?= $request_code; ?></h4></td>
				<td></td>			
			</tr>				
		</table>				
		<hr>
		<table class='table	table-condensed'>
			<tr>
				<td align='left'>Requester:<b> <?= $requester->complete_name; ?></b></td>
				<td align='right'>Warehouse:<b> <?= $warehouse->warehouse_name; ?> </b></td>
			</tr>
			<tr>
				<td align='left'>ID Number:<b> <?= $requester->id_number; ?></b></td>				
				<?php
					$brand_model = "N/A";
					if ($request_summary->motorcycle_brand_model_id > 0) {
						$brand_model_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id($request_summary->motorcycle_brand_model_id);
						$brand_model = $brand_model_details->brand_name . " / " . $brand_model_details->model_name;
					}
				?>				
				<td align='right'>Brand/Model:<b> <?= $brand_model; ?></b></td>
			</tr>
			<tr>
				<td align='left'>Total Amount:<b> Php <?= number_format($total_amount, 2); ?></b></td>
				<td align='right'>Engine:<b> <?=(empty($request_summary->engine)) ? "N/A" : $request_summary->engine ?></b></td>				
			</tr>
			<tr>
				<td align='left'>Total Items:<b> <?= number_format($total_quantity_amount->total_items, 2); ?></b></td>
				<td align='right'>Chassis:<b> <?=(empty($request_summary->chassis)) ? "N/A" : $request_summary->chassis ?></b></td>				
			</tr>		
		</table>

		<h4>Items</h4>
			<br />
			<table class='table table-bordered' style="font-size:10px;">
				<thead>
					<tr>
						<th>Item</th>
						<th>Action</th>						
						<th>Good Qty</th>
						<th>Bad Qty</th>
						<th>Total</th>						
					</tr>
				</thead>
				<tbody>
					<?php foreach($reprocessed_item_details as $rid):?>
						<tr>
							<?php
								// get request_detail_details
								$request_detail_details_sql = "SELECT * FROM is_" . $department_module_details->segment_name . "_detail WHERE " . $department_module_details->segment_name . "_detail_id = " . $rid->request_detail_id;
								$request_detail_details = $this->db_spare_parts->query($request_detail_details_sql);
								$request_detail_details = $request_detail_details->result();		
								$request_detail_details = $request_detail_details[0];		

								// get item details 
								$item_details = $this->spare_parts_model->get_item_view_by_id($request_detail_details->item_id);
							?>
							<td>[<?=$item_details->model_name?> / <?=$item_details->brand_name?>] <?=$item_details->description?></td>
							<td style="text-align:center;"><?=$rid->action?></td>
							<td style="text-align:right;"><?=number_format($rid->good_quantity, 2)?></td>
							<td style="text-align:right;"><?=number_format($rid->bad_quantity, 2)?></td>
							<?php
								$total_quantity = $rid->good_quantity + $rid->bad_quantity;
							?>
							<td style="text-align:right;"><?=number_format($total_quantity, 2)?></td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>

		<hr>
		
	</fieldset>	
</div>


