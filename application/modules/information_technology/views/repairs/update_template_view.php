<div id="body_remarks_<?= $repair_detail->repair_detail_id ?>">
<?php
	$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($repair_detail->repair_hardware_id);
?>

<!--div class="alert alert-success"><h4><?= $repair_hardware_details->repair_hardware_name; ?> x <?= $repair_detail->quantity ?></h4></div-->

<table class="table table-striped" style="color:#004FCC;">
  <tbody>
    <tr>
      <td style="width:30em;"><h4><?= $repair_hardware_details->repair_hardware_name; ?> x <?= $repair_detail->quantity ?></h4></td>
      <td style="width:35em;"><?= $repair_detail->description ?></td>
      <td style="width:35em;"><?= $repair_detail->peripherals ?></td>
    </tr>
    
  </tbody>
</table>



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
	
	$repair_status_details = $this->information_technology_model->get_repair_status(NULL,NULL, "priority_order");

	$repair_status_tag = "<select class='add_status' id='add_status_{$repair_detail->repair_detail_id}' style='width:13em;height:100%;' data='{$repair_detail->repair_detail_id}'>";
	
	foreach($repair_status_details as $wd){
	    $repair_status_tag .= "<option value='{$wd->repair_status_id}' title='{$wd->repair_status}'> $wd->repair_status</option>";
	}
	$repair_status_tag .= "</select>";

?>

<?=$repair_status_tag?>

<input id="add_tr_number_in_<?= $repair_detail->repair_detail_id ?>" style="width:143px; display:none;" placeholder="TR Number (IN)"/>
<input id="add_tr_number_out_<?= $repair_detail->repair_detail_id ?>" style="width:143px; display:none;" placeholder="TR Number (OUT)"/>
<input class="number" id="add_proposed_price_<?= $repair_detail->repair_detail_id ?>" style="width:143px; display:none;text-align:right;" placeholder="Proposed Amt"/>
<input class="number" id="add_po_price_<?= $repair_detail->repair_detail_id ?>" style="width:100px; display:none;text-align:right;" placeholder="Approved Amt"/>
<input class="" id="add_approval_number_<?= $repair_detail->repair_detail_id ?>" style="width:85px; display:none;text-align:right;" placeholder="Approval#"/>
<input class="" id="add_authority_number_<?= $repair_detail->repair_detail_id ?>" style="width:85px; display:none;text-align:right;" placeholder="Authority#"/>
<textarea maxlength="255" name="add_remark_<?= $repair_detail->repair_detail_id ?>" id ="add_remark_<?= $repair_detail->repair_detail_id ?>" class="span9" placeholder="Put Remarks Here..." style="height:50px;resize:none;"></textarea>
<a class="btn btn-primary post_to_history" data="<?= $repair_detail->repair_detail_id ?>">Post Remarks</a>
<div id="is_branch_expense_container_<?= $repair_detail->repair_detail_id ?>" style="margin-top:-30px;display:none;">
	<input id="is_branch_expense_<?= $repair_detail->repair_detail_id ?>" name="is_branch_expense_<?= $repair_detail->repair_detail_id ?>" type="checkbox" value="is_branch_expense" name="is_branch_expense">
	<strong>Is Branch Expense</strong>
</div>

<div id="approval_container_<?= $repair_detail->repair_detail_id ?>" style="display:none;">
	<span>Approved By: </span>
	<?php
	$date_approved = date('Y-m-d');

	$where = "is_active = 1";
	$expense_signatory_details = $this->information_technology_model->get_expense_signatory($where, NULL, "complete_name");



	$expense_signatory_options = array();
	$expense_signatory_options = array('' => 'Select Signatory...');
	foreach ($expense_signatory_details as $es) {
	 	$expense_signatory_options[$es->expense_signatory_id] = $es->complete_name;
	}

	echo form_dropdown('approved_by',$expense_signatory_options, NULL,'id="approved_by" style="margin-top:1em;"');
	?>
	<span>Date Approved: </span>
	<input type="text" class="input-medium" id="date_approved" name='date_received' readonly='readonly' style='margin-top:1em;cursor:pointer;' />
</div>

<div class="control-group error">
	<p id="input_errors_<?= $repair_detail->repair_detail_id ?>" class="help-block">		
	</p>
</div>							
<br/>

</div>

<script>

	$("#date_approved").datepicker({
        timeFormat: 'hh:mm tt',
		'dateFormat' : "yy-mm-dd",			
	});
	
	$("#date_approved_icon").click(function(e) {
		$("#date_approved").datepicker("show");
	});
	
	$("#date_approved").datepicker('setDate', '<?= $date_approved ?>');
	$("#date_approved").datepicker("option", "changeMonth", true);
	$("#date_approved").datepicker("option", "changeYear", true);

</script>