<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Reports</h2></div>

<br>

</div>
<hr />
<div>
	<form id='frm_filter' class='form-horizontal' method='get' action ='/information_technology/repairs/reports'>
		<fieldset>
			<input type='hidden' id='filter' name='filter' value='yes' />
			<div class='row-fluid'>
				<div class='span span5'>
					<div style='padding-left:91px'>
					</div>
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
				<div class='span span6	'>
					<?php
						$branch_details = $this->human_relations_model->get_branch(NULL, NULL, "branch_name");

						$branch_options = array();
						$branch_options = array('0' => 'All', '-1' => 'Head Office');
						foreach ($branch_details as $bd) {
						 	$branch_options[$bd->branch_id] = $bd->branch_name;
						}									
					?>	

					<div class="control-group">
						<label class="control-label" for="use_date_range" id="from_date_label">Branch:&nbsp;&nbsp;</label>
						<div class="input-append">
							<?= form_dropdown('branch_id',$branch_options, NULL,'id="branch_id" class="span8"') ?>							
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
		</fieldset>
	</form>
</div>
<hr/>
<div class="span12" id="display_details">
	<?= $display_data ?>
</div>
<div class="datetime_values" style="display:none;">
	
</div>
<script type="text/javascript">
	$(document).on('ready', function(){ 		
	});

	$(function() {
		
		$("#from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		$("#from_date").datepicker("option", "changeMonth", true);
		$("#from_date").datepicker("option", "changeYear", true);
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
            timeFormat: 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",			
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datepicker('setDate', '<?= $to_date ?>');
		$("#to_date").datepicker("option", "changeMonth", true);
		$("#to_date").datepicker("option", "changeYear", true);
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd') + ' 12:00 am');
			$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd h:M:s tt'));
			$('#frm_filter').submit();
		});
		
		var dl_url = b.uri.path + (b.uri.query.length > 0 ? '?' + b.uri.query + '&export=excel' : '?export=excel');
		$('#btn_download').attr('href', dl_url);

		//$('#btn_download').click(function(e) {
		//	e.preventDefault();
			
			//$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd') + ' 12:00 am');
			//$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd h:M:s tt'));
			


			//alert($("#from_date").val());

		//});

	});
	
	
	
</script>

