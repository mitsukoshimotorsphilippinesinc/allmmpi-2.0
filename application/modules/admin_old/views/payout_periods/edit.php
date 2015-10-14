<fieldset >		
	<div>
		<label class='control-label' for='payout type'><strong>Payout Type</strong></label>
		<div class="controls">
			<?php
				$options = array('IGPSM' => 'IGPSM', 'UNILEVEL' => 'UNILEVEL');
				$extra = "id='payout_type'";
				echo form_dropdown('payout_type', $options, set_value('payout_type', $payout_period->payout_type), $extra);
			?>
		</div>	
	</div>
	
	<div class='control-group'>
		<label for='start-date' class='control-label'><strong>Start Date</strong></label>
		<div class='controls'>
			<div class='input-append' >
				<input title='Start Date' style='z-index:2050' class='input-medium' type='text' id='start-date' name='start-date' value='' readonly='readonly'>
				<span id='start-date-icon' class='add-on' style='cursor:pointer;'><i class='icon-calendar'></i></span>					
			</div>
		</div>
	</div>								
	
	<div class='control-group'>
		<label for='end-date' class='control-label'><strong>End Date</strong></label>
		<div class='controls'>
			<div class='input-append' >
				<input title='End Date' style='z-index:2050' class='input-medium' type='text' id='end-date' name='end-date' value='' readonly='readonly'>
				<span id='end-date-icon' class='add-on' style='cursor:pointer;'><i class='icon-calendar'></i></span>					
			</div>
		</div>
	</div>										
		
	<div>
		<label class='control-label' for='payout type'><strong>Status</strong></label>
		<div class="controls">
			<?php
				$options = array('ACTIVE' => 'ACTIVE', 'COMPLETED' => 'COMPLETED', 'CANCELLED' => 'CANCELLED');
				$extra = "id='status'";
				echo form_dropdown('status', $options, set_value('status', $payout_period->status), $extra);
			?>
		</div>	
	</div>
	
	<div>
		<label class='control-label' for='is official'><strong>Is Official?</strong></label>
		<div class="controls">
			<?php
				$options = array('1' => 'YES', '0' => 'NO');
				$extra = "id='is_official'";
				echo form_dropdown('is_official', $options, set_value('is_official', $payout_period->is_official), $extra);
			?>
		</div>	
	</div>
	
	<div>
		<label class='control-label'>		
			<strong>Remarks*</strong>
		</label>
		<textarea id='remarks' class='span5' type='text' value='' style='height:100px;resize=none;' name='remarks' placeholder='Type remarks here...' maxlength='200'></textarea>
		<br/>
		<span id='error_remarks' class='label label-important' style='display:none;'>ERROR: EMPTY REMARKS.</span>
		
	</div>
													
</fieldset>

<script type="text/javascript">

$(document).ready(function(){
	
	// test		
	var currDate = new Date();
	var currYear = new Date().getFullYear();
	var yrRange = "2005:" + currYear;


	$("#start-date").datetimepicker({
		'dateFormat' : "yy-mm-dd",
		'timeFormat': 'HH:mm:ss',
		'changeYear' : true,
		'yearRange' : yrRange,
		'changeMonth' : true
	});

	$("#start-date").datetimepicker('setDate', '<?= $payout_period->start_date ?>');
	
	$("#start-date").click(function(e) {
		$("#start-date").datetimepicker("show");
	});
	
	$("#start-date-icon").click(function(e) {
		$("#start-date").datetimepicker("show");
	});

	$("#end-date").datetimepicker({
		'dateFormat' : "yy-mm-dd",
		'timeFormat': 'HH:mm:ss',
		'changeYear' : true,
		'yearRange' : yrRange,
		'changeMonth' : true
	});

	var _end_date = new Date();
	_end_date.setDate(_end_date.getDate()+6);
	
	$("#end-date").datetimepicker('setDate', '<?= $payout_period->end_date ?>');
	
	$("#end-date-icon").click(function(e) {
		$("#end-date").datetimepicker("show");
	});
	
	
});

</script>