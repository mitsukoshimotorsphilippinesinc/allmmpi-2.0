<fieldset >		
	<div>
		<label class='control-label' for='payout type'><strong>Payout Type</strong></label>
		<div class="controls">
			<?php
				$options = array('IGPSM' => 'IGPSM', 'UNILEVEL' => 'UNILEVEL');
				$extra = "id='payout_type'";
				echo form_dropdown('payout_type', $options, set_value('payout_type', ''), $extra);
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
				$options = array('ACTIVE' => 'ACTIVE', 'COMPLETED' => 'COMPLETED');
				$extra = "id='status' disabled='disabled'";
				echo form_dropdown('status', $options, set_value('status', ''), $extra);
			?>
		</div>	
	</div>
	
	<div>
		<label class='control-label' for='is official'><strong>Is Official?</strong></label>
		<div class="controls">
			<?php
				$options = array('1' => 'YES', '0' => 'NO');
				$extra = "id='is_official'";
				echo form_dropdown('is_official', $options, set_value('is_official', ''), $extra);
			?>
		</div>	
	</div>
	
	<div>
		<label class='control-label'>		
			<strong>Remarks</strong>
		</label>
		<textarea id='add_remarks' class='span5' type='text' value='' style='height:100px;resize=none;' name='remarks' placeholder='Type remarks here...' maxlength='200'></textarea>
		<br/>
		<span id='error_add_remarks' class='label label-important' style='display:none;'>ERROR: EMPTY REMARKS.</span>
		
	</div>
													
</fieldset>

<script type="text/javascript">

$(document).ready(function(){
	
	// test		
	var currDate = new Date();
	var currYear = new Date().getFullYear();
	var yrRange = "2008:" + currYear;


	$("#start-date").datetimepicker({
		'dateFormat' : "yy-mm-dd",
		'timeFormat': 'HH:mm',
		'changeYear' : true,
		'yearRange' : yrRange,
		'changeMonth' : true
	});

	var _start_date = new Date();
	_start_date.setDate(_start_date.getDate() + '00:00:00');
	$("#start-date").datetimepicker('setDate', _start_date);
	
	$("#start-date").click(function(e) {
		$("#start-date").datetimepicker("show");
	});
	
	$("#start-date-icon").click(function(e) {
		$("#start-date").datetimepicker("show");
	});

	$("#end-date").datetimepicker({
		'dateFormat' : "yy-mm-dd",
		'timeFormat': 'HH:mm',
		'changeYear' : true,
		'yearRange' : yrRange,
		'changeMonth' : true
	});
	
	var _end_date = new Date();
	_end_date.setDate(_end_date.getDate()+6);
	$("#end-date").datetimepicker('setDate', _end_date);
	
	$("#end-date-icon").click(function(e) {
		$("#end-date").datetimepicker("show");
	});
	
	
});

</script>