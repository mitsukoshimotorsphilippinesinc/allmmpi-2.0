<?php echo css('jquery-ui/jquery-ui-1.9.1.css');?>
	
<?php echo css('base.css');?>
<?php echo css('main.css');?>
<?php echo css('google-code-prettify/prettify.css');?>
<?php echo css('switcherMenu.css');?>

<?php echo js('webroot_admin/assets/js/libs/jquery-ui-timepicker-addon.js'); ?>
<?php echo js('webroot_admin/assets/js/libs/jquery.clock.js'); ?>
<?php echo js('webroot_admin/assets/js/libs/underscore-min.js'); ?>
<?php echo js('libs/jquery-ui-timepicker-addon.js'); ?>
<?php echo js('libs/jquery.clock.js'); ?>
<?php echo js('libs/underscore-min.js'); ?>

<div class='alert alert-info'><h2>Commissions</h2></div>

<form id='frm_filter' class='form-horizontal' method='get' action ='/workbench/tmp_commissions/display_tmp_commissions'>
	<fieldset>
		<div class='row-fluid'>
			<div class='span span6'>
				<div class="date_range">
					<div class="control-group">
						<label class="control-label" for="use_date_range" id="from_date_label">From Date:</label>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="input-medium" id="from_date" name='from_date' readonly='readonly' style='cursor:pointer;' />
								<span id='from_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="control-group date_range" id="to_date_range">
						<label class="control-label" for="use_date_range">To Date:</label>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="input-medium" id="to_date" name='to_date' readonly='readonly' style='cursor:pointer;' />
								<span id='to_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button class='btn btn-primary'>&nbsp;&nbsp;Go&nbsp;&nbsp;</button>
					<button id='btn_today' class='btn btn-info'>Today</button>
					<a id='btn_download' href='#' target='_blank' class='btn btn-success' style='margin-left:20px;'>Download</a>
				</div>
			</div>
		</div>
	</fieldset>
</form>
<hr/>
<table  class='table table-striped table-bordered table-condensed credit_logs_list'>
	<thead>

	</thead>
	<tbody>

	</tbody>
</table>

<script type="text/javascript">
	$(function() {
		
		$("#from_date").datetimepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#from_date").datetimepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datetimepicker({
            timeFormat: 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",			
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datetimepicker('setDate', '<?= $to_date ?>');
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datetimepicker('setDate', b.dateFormat('yyyy-mm-dd') + ' 12:00 am');
			$("#to_date").datetimepicker('setDate', b.dateFormat('yyyy-mm-dd h:M:s tt'));
			$('#frm_filter').submit();
		});
		
		var dl_url = b.uri.path + (b.uri.query.length > 0 ? '?' + b.uri.query + '&export=excel' : '?export=excel');
		$('#btn_download').attr('href', dl_url);

	});
	
	
	
</script>