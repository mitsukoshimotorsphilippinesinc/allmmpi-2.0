<?php

$year_options = "";
$month_options = "";
$day_options = "";

foreach($years as $k => $year)
{
	$year_options .= "<option value='{$k}'>{$year}</option>";
}
foreach($months as $k => $month)
{
	$month_options .= "<option value='{$k}'>{$month}</option>";
}
foreach($days as $k => $day)
{
	$day_options .= "<option value='{$k}'>{$day}</option>";
}
?>

<div class='alert alert-info'>
	<h2>Member Earnings
	<?php if(!empty($earnings)): ?>
	<a id="process_earnings" class='btn btn-primary' style='float:right;margin-left:5px;margin-right:-20px;margin-top:5px;'><span>Process Pending Earnings</span></a>
	<?php endif; ?>
	<a id="print_earnings" class='btn btn-primary' style='float:right;margin-top:5px;'><span>Print Pending Earnings</span></a>
	</h2></div>

<div >	           
	<?php if($search_text == "")
	{
		$start_date_year = 0;
		$start_date_month = 0;
		$start_date_day = 0;

		$end_date_year = 0;
		$end_date_month = 0;
		$end_date_day = 0;
	}
	else
	{
		$period = explode("+",$search_text);
		
		$start_date = $period[0];
		$end_date = $period[1];
		
		$start_date_year = date('Y',strtotime($start_date));
		$start_date_month = date('m',strtotime($start_date));
		$start_date_day = date('d',strtotime($start_date));

		$end_date_year = date('Y',strtotime($end_date));
		$end_date_month = date('m',strtotime($end_date));
		$end_date_day = date('d',strtotime($end_date));
	}	
	?>
	<div class="row-fluid">
		<div class="span3">
			<div class="row">
				<div class="span12">
					<div class="control-group ">
						<label class="control-label" for="status_dropdown">Status</label>
						<div class="controls form-inline">
							<?php
							
							$options = array("all" => "All","pending"=>"Pending","processing"=>"Processing","released" => "Released","processed_for_payout" => "Processed for Payout","forfeited"=>"Forfeited");
							
							echo form_dropdown('status_dropdown', $options, $status, 'id="status_dropdown" class="status_dropdown"');?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="span12">
					<div class="control-group ">
						<label class="control-label" for="start_date">Start Date</label>
						<div id="start_date_container" class="controls form-inline wc-date">
							<?= form_dropdown('start_date_month', $months, $start_date_month, 'id="start_date_month" class="wc-date-month"') ?>
							<?= form_dropdown('start_date_day', $days, $start_date_day, 'id="start_date_day" class="wc-date-day"') ?>
							<?= form_dropdown('start_date_year', $years, $start_date_year, 'id="start_date_year" class="wc-date-year"') ?>
							<input type="hidden" id="start_date" name="start_date" value="" readonly />
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="span12">
					<div class="control-group ">
						<label class="control-label" for="start_date">End Date</label>
						<div id="start_date_container" class="controls form-inline wc-date">
							<?= form_dropdown('end_date_month', $months, $end_date_month, 'id="end_date_month" class="wc-date-month"') ?>
							<?= form_dropdown('end_date_day', $days, $end_date_day, 'id="end_date_day" class="wc-date-day"') ?>
							<?= form_dropdown('end_date_year', $years, $end_date_year, 'id="end_date_year" class="wc-date-year"') ?>
							<input type="hidden" id="end_date" name="end_date" value="" readonly />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="span2">
			<div class="control-group">
				<div class="controls" style="margin-top: 23px;">
					<form id='search_details' method='get' action ='/admin/earnings/view'>
						<input type="hidden" id="status" name="status" value="" readonly />
						<input type="hidden" id="search_option" name="search_option" value="insert_timestamp" readonly />
						<input type="hidden" id="search_string" name="search_string" value="" readonly />
					</form>
					<a id='button_search' class="btn btn-primary"><strong>View</strong></a>
					<a id='button_refresh' class='btn'><span>Refresh</span></a>
				</div>
			</div>
		</div>
	</div>

	<br/>
	<span id="search_empty" class="label label-important" style="display:none">The dates must be entered.</span>	
	<span id="search_incomplete" class="label label-important" style="display:none">Both the Start date and the End date must be entered.</span>
	<span id="search_invalid_order" class="label label-important" style="display:none">The Start date must be earlier than the End date.</span>
	
	<?php if ($search_text == "") :?>	
		<div id="search_summary" style="display:none;">
	<?php else: ?>	
		<div id="search_summary">
	<?php endif; ?>		
	
		<span class="label label-info">Search Results for:</span>
		<span class="label label-success"><?= $search_by ?></span>
		<span class="label label-success"><?= $search_text ?></span>
	</div>
</div>	
<hr/>
<br>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Account ID</th>
			<th>Member Name</th>
			<th>Amount</th>
			<th>Status</th>
			<th>Period</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($earnings)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($earnings as $earning): ?>
		<tr>
			<td><?= $earning->account_id; ?></td>
			<td><?= "{$earning->first_name} {$earning->last_name}"; ?></td>
			<td><?= number_format($earning->amount,2); ?></td>
			<td><?= $earning->status; ?></td>
			<td><?= $earning->period; ?></td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>
<script type="text/javascript">

	$(document).ready(function(){
		$(document).on("change","#start_date_month",function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$(document).on("change","#start_date_day",function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$(document).on("change","#start_date_year",function() {
			beyond.webcontrol.updateDateControl('start_date');
		});

		$('#start_date_month').trigger('change');
		$('#start_date_day').trigger('change');
		$('#start_date_year').trigger('change');
		
		$(document).on("change","#end_date_month",function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$(document).on("change","#end_date_day",function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$(document).on("change","#end_date_year",function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		$('#end_date_month').trigger('change');
		$('#end_date_day').trigger('change');
		$('#end_date_year').trigger('change');

	});


	$(document).on("click","#button_search",function() {
		
		$("#search_empty").hide(); 
		$("#search_incomplete").hide();
		$("#search_invalid_order").hide();
		
		
		$("#search_string").val($("#start_date").val()+"+"+$("#end_date").val());
		
		var _search_string = $.trim($("#search_string").val());
		
		if (_search_string == '+' || _search_string == '') {
			$("#search_empty").show();
			$("#search_summary").hide();
			$("#search_string").val("");
		}
		else if($("#start_date").val() == "" || $("#end_date").val() == "")
		{
			$("#search_incomplete").show();
			$("#search_summary").hide();
			$("#search_string").val("");
		}
		else if($("#start_date").val() > $("#end_date").val())
		{
			$("#search_invalid_order").show();
			$("#search_summary").hide();
			$("#search_string").val("");
		} 
		else {
			
			$("#status").val($("#status_dropdown").val());
			
			$("#search_details").submit();
			$("#search_empty").hide(); 
			$("#search_summary").show();           
		}
		
		return false;
	});
	
	$("#button_refresh").live("click",function() {
		redirect('admin/earnings');
	});
	
	$("#print_earnings").click(function(){
		print_modal = b.modal.new({
			title: "Print Confirmation",
			html: "Would you like to print all <strong>PENDING</strong> earnings?",
			width: 300,
			disableClose: true,
			buttons: {
				"No": function(){
					print_modal.hide();
				},
				"Yes": function(){
					redirect("admin/earnings/print_earnings");
					print_modal.hide();
				}
			}
		});
		
		print_modal.show();
	});
	
	$("#process_earnings").click(function(){
		var period = ('<?= $search_text ?>').split("+");
		
		var process_modal = b.modal.new({
			title: "Confirmation",
			html: "<p>Are you sure you want to process member earnings from <strong>"+period[0]+"</strong> to <strong>"+period[1]+"</strong></p>",
			width: 300,
			disableClose: true,
			buttons: {
				"No": function(){
					process_modal.hide();
				},
				"Yes": function(){
					b.request({
						url: "/admin/earnings/process_earnings",
						data: {
							"start_date": period[0],
							"end_date": period[1]
						},
						on_success: function(data){
							
						}
					});
					process_modal.hide();
				}
			}
		});
		
		process_modal.show();
		
		
		
		
	});
	
</script>