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
				<td colspan="2" align="left" class=""><h3>Merchandise Transfer Receipt - <?= $department_module_details->module_name ?></h3></td>
			</tr>
			<tr>
				<td colspan='1' align='left' valign="bottom" ><h4>MTR No: <?= str_pad($request_summary->mtr_number, 6, '0', STR_PAD_LEFT); ?></h4></td>									
				<td colspan="1" align='right'><h4><?= $copy ?> Copy</h4></td>					
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
				<td align='left'>Total Items:<b><?= number_format($total_quantity, 2); ?></b></td>
				<td align='right'>Chassis:<b> <?=(empty($request_summary->chassis)) ? "N/A" : $request_summary->chassis ?></b></td>				
			</tr>		
		</table>

		<h4>Items</h4>
			<br />
			<table class='table table-bordered'>
				<thead>
					<tr>
						<th>Item</th>
						<th>Unit</th>
						<th>Good Qty</th>
						<th>Bad Qty</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($request_details as $rd):?>
						<tr>
							<?php
								$item_details = $this->spare_parts_model->get_item_view_by_id($rd->item_id);
							?>
							<td>[<?=$item_details->model_name?> / <?=$item_details->brand_name?>] <?=$item_details->description?></td>
							<td><?=$item_details->unit?></td>
							<td><?=number_format($rd->good_quantity, 2)?></td>
							<td><?=number_format($rd->bad_quantity, 2)?></td>							
							<td ><?=number_format($rd->total_amount, 2)?></td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>

		<hr>

		<h4>Signatories</h4>
			<br />
			<table class='table table-bordered table-condensed'>
				<thead>
					<tr>						
						<th class="workflow_user">Name</th>
						<th class="workflow_status">Department</th>
						<th class="workflow_status">Position</th>
						<th class="workflow_remarks">Signature</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($approvals as $approval): ?>
						<tr>
							<?php
								$department_details = $this->human_relations_model->get_department_by_id($approval->department_id);
								$position_details = $this->human_relations_model->get_position_by_id($approval->position_id);
							?>
							<td><?= $approval->complete_name; ?></td>
							<td><?=(empty($department_details->department_name)) ? "N/A" : $department_details->department_name ?></td>
							<td><?=(empty($position_details->position_name)) ? "N/A" : $position_details->position_name ?></td>
							<td></td>							
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>

	</fieldset>	
</div>


