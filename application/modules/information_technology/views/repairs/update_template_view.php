<div id="body_remarks_<?= $repair_detail->repair_detail_id ?>">
<?php
	$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($repair_detail->repair_hardware_id);
?>

<div class="alert alert-success"><h4><?= $repair_hardware_details->repair_hardware_name; ?> x <?= $repair_detail->quantity ?></h4></div>

<table class="table table-bordered table-condensed" id="remarks_<?= $repair_detail->repair_detail_id ?>">
	<thead id="">
		<tr>
			<th>Status</th>
			<th>Remarks</th>
			<th>Posted By</th>
			<th>Date Posted</th>			
		</tr>
	</thead>
	<tbody>
	<?php
		$where = "repair_detail_id = {$repair_detail->repair_detail_id}";
		$repair_remark_details = $this->information_technology_model->get_repair_remark($where);

		if (empty($repair_remark_details)) {
			echo "<tr><td colspan='4' style='text-align:center'>No Comment Yet.</td></tr>";
		} else {
			foreach ($repair_remark_details as $rrd) {

				$status_details = $this->information_technology_model->get_repair_status_by_id($rrd->repair_status_id);

				$creator_details = $this->human_relations_model->get_employment_information_view_by_id($rrd->created_by);

				echo "<tr>
					<td style='width:13em'>{$status_details->repair_status}</td>
					<td>{$rrd->remarks}</td>
					<td style='width:20em'>{$creator_details->complete_name}</td>
					<td style='width:12em'>{$rrd->insert_timestamp}</td>
				</tr>";
			}
		}
	?>

	</tbody>
</table>		

<?php

	$repair_status_options = array();
	
	$repair_status_details = $this->information_technology_model->get_repair_status();

	$repair_status_tag = "<select class='add_status' id='add_status_{$repair_detail->repair_detail_id}' style='width:13em;height:100%;' data='{$repair_detail->repair_detail_id}'>";
	
	foreach($repair_status_details as $wd){
	    $repair_status_tag .= "<option value='{$wd->repair_status_id}' title='{$wd->repair_status}'> $wd->repair_status</option>";
	}
	$repair_status_tag .= "</select>";

?>

<?=$repair_status_tag?>

<input id="add_tr_number_out_<?= $repair_detail->repair_detail_id ?>" style="width:143px; display:none;" placeholder="TR Number (OUT)"/>
<input id="add_po_price_<?= $repair_detail->repair_detail_id ?>" style="width:143px; display:none;" placeholder="Price"/>
<textarea maxlength="255" name="add_remark_<?= $repair_detail->repair_detail_id ?>" id ="add_remark_<?= $repair_detail->repair_detail_id ?>" class="span9" placeholder="Put Remarks Here..." style="height:50px;resize:none;"></textarea>
<a class="btn btn-primary post_to_history" data="<?= $repair_detail->repair_detail_id ?>">Post Remarks</a>
<div class="control-group error">
	<p id="input_errors" class="help-block">
		
	</p>
</div>							
<br/>
<hr/>
</div>