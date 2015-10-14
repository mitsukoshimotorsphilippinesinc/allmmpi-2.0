<fieldset >		
	<div>
		<label class='control-label' for='payout type'><strong>Payout Type</strong></label>
		<div class="controls">
			<input disabled="disabled" value="<?= $payout_period->payout_type; ?>">
		</div>	
	</div>
	
	<div class='control-group'>
		<label for='start-date' class='control-label'><strong>Start Date</strong></label>
		<div class='controls'>
			<input disabled="disabled" value="<?= $payout_period->start_date; ?>">
		</div>
	</div>								
	
	<div class='control-group'>
		<label for='end-date' class='control-label'><strong>End Date</strong></label>
		<div class='controls'>
			<input disabled="disabled" value="<?= $payout_period->end_date; ?>">
		</div>
	</div>										
		
	<div>
		<label class='control-label' for='payout type'><strong>Status</strong></label>
		<div class="controls">
			<input disabled="disabled" value="<?= $payout_period->status; ?>">
		</div>	
	</div>
	
	<div>
		<label class='control-label' for='is official'><strong>Is Official?</strong></label>
		<div class="controls">
			<input disabled="disabled" value="<?= $payout_period->is_official; ?>">
		</div>	
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