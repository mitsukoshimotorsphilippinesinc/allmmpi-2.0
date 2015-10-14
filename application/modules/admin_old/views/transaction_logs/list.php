<?php
	$start_date = date("Y-m-d");
	$end_date = date("Y-m-d");

	$payout_type_select = "<select id='payout-type'>";
	$payout_type_select .= "<option value='IGPSM'>IGPSM</option>" ;
	$payout_type_select .= "<option value='UNILEVEL'>UNILEVEL</option>" ;
	$payout_type_select .= "</select>" ;

?>
<div class='alert alert-info'><h2>Transaction Logs</h2></div>

<div>
	<div class='control-group'>
		<div style='float:left;'>
			<label class='control-label' for='Payout Type'><strong>Payout Type</strong></label>
			<div class='controls'>
					<?= $payout_type_select?>
			</div>
		</div>
		<div style='float:left;margin-left:10px;'>
			<label class='control-label' for='Start Date'><strong>Start Date</strong></label>
			<div class='controls'>
				<div class="input-append" >
					<input title="Start Date" class="input-medium" type="text" id="start-date" name="start-date" value="" readonly="readonly">
					<span id='start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
				</div>
			</div>
		</div>
		<div style='float:left;margin-left:10px;'>
			<label class='control-label' for='End Date'><strong>End Date</strong></label>
			<div class='controls'>
				<div class="input-append" >
					<input title="End Date" class="input-medium" type="text" id="end-date" name="end-date" value="" readonly="readonly">
					<span id='end-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
				</div>
			</div>
		</div>
		<div style='float:left;margin-left:10px;'>
			<label class='control-label' for='Process'><strong>&nbsp;</strong></label>
			<div class='controls'>
				<button id="btn-process" class='btn btn-primary'><span>Process</span></button>
				<button id="btn-download" class='btn btn-primary'><span>Download</span></button>
			</div>
		</div>
		<div class='clearfix'></div>
	</div>
</div>

<div id="transaction-logs">
	<div status="vertical-align:middle;" >
		<strong>Member:</strong> <select id="tl-member-id" >
			<option value="0" >Select Member</option>
		</select>
	</div>
	<table class='table table-striped table-bordered table-condensed'>
		<thead>
			<tr>
				<th>Name</th>
				<th>Account ID</th>
				<th>Details</th>
				<th>Type</th>
				<th>Amount</th>
				<th>Date Time</th>
			</tr>
		</thead>
		<tbody id='transaction_logs_details'>
			<tr><td colspan='10'><strong><center>No Records Found</center></strong></td></tr>
		</tbody>
	</table>
</div>
<div id='pagination'>

</div>
<div id='inset_form'></div>

<script type="text/javascript" charset="utf-8">
	$(function() {
		
		$("#start-date").datepicker({
			'dateFormat' : "yy-mm-dd"
		});

		$("#start-date").datepicker('setDate', '<?= $start_date ?>');
		
		$("#start-date-icon").click(function(e) {
			$("#start-date").datepicker("show");
		});
		
		$("#end-date").datepicker({
			'dateFormat' : "yy-mm-dd"	
		});
		
		$("#end-date-icon").click(function(e) {
			$("#end-date").datepicker("show");
		});
		
		$("#end-date").datepicker('setDate', '<?= $end_date ?>');		
	});


	$("#btn-process").on("click",function(){
		
		var start_date = $("#start-date").val();
		var end_date = $("#end-date").val();
		var payout_type = $("#payout-type").val();
		
		processPayoutTables(payout_type,start_date,end_date);
	});

	$("#btn-download").on("click",function(){
		
		var start_date = $("#start-date").val();
		var end_date = $("#end-date").val();
		var payout_type = $("#payout-type").val();
		
		downloadPayoutTables(payout_type,start_date,end_date);
	});

	$("#tl-member-id").on("change",function(){
		
		var start_date = $("#start-date").val();
		var end_date = $("#end-date").val();
		var payout_type = $("#payout-type").val();
		var page = 1;
		
		viewTransactionLogs(payout_type,start_date,end_date,$(this).val(),page);
	});
	
	var processPayoutTables = function(type,start_date,end_date) 
	{

		var processPayoutModal = b.modal.create({
			title: 'Process Payout',
			disableClose: false,
			html: "You are processing the payout for " + start_date + " to " + end_date + " for " + type + " commissions.",
			buttons: {
				'Ok' : function() {	
					beyond.request({
						url : '/admin/transactions/process',
						data : {
							'start_date': start_date,
							'end_date': end_date,
							'type': type
						},
						on_success : function(data) {
							viewTransactionLogs(type,start_date,end_date);
							processPayoutModal.hide();
						}
					})		
					processPayoutModal.hide();
				}
			}
		});
		processPayoutModal.show();
	}

	var viewCommissionPerAccount = function(type)
	{
		beyond.request({
			url: '/admin/transactions/commission_per_account',
			data:{'type':type},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#payout-details").html(data.data.html);
				}
			}
		});
	}

	var viewCommissionPerNewAccount = function(type)
	{
		beyond.request({
			url: '/admin/transactions/commission_per_new_account',
			data:{'type':type},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#payout-details-new-accounts").html(data.data.html);
				}
			}
		});
	}

	var viewGCPerAccount = function(start_date,end_date)
	{
		beyond.request({
			url: '/admin/transactions/gc_per_account',
			data:{'start_date':start_date,'end_date':end_date},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#gc_per_account_details").html(data.data.html);
				}
			}
		});
	}

	var viewMemberPayout = function(type,start_date,end_date)
	{
		beyond.request({
			url: '/admin/transactions/member_payout',
			data:{'type':type,'start_date':start_date,'end_date':end_date},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#member_payout_details").html(data.data.html);
				}
			}
		});
	}

	$("#pagination").on('click', '.goto_page', function() {
		var page = parseInt($(this).attr('page'));
		var start_date = $("#start-date").val();
		var end_date = $("#end-date").val();
		var payout_type = $("#payout-type").val();
		var member_id = $("#tl-member-id").val();
		viewTransactionLogs(payout_type,start_date,end_date,member_id,page);
	});

	var viewTransactionLogs = function(type,start_date,end_date,member_id,page)
	{
		beyond.request({
			url: '/admin/transactions/transaction_logs',
			data:{'type':type,'start_date':start_date,'end_date':end_date,'member_id':member_id, 'page':page},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#transaction_logs_details").html(data.data.html);

					$("#tl-member-id").html(data.data.html_member_option);
					$("#pagination").html(data.data.pagination);
				}
			}
		});
	}
	
	var downloadPayoutTables = function(type,start_date,end_date) 
	{
		
		
	    $('#inset_form').html('<form action="/admin/transactions/download" method="post" style="display:none;" id="download_form"><input type="text" id="start_date" name="start_date" value="' + start_date + '" /><input type="text" id="end_date" name="end_date" value="' + end_date + '" /><input type="text" id="type" name="type" value="' + type + '" /></form>');

	    $('#download_form').submit();

		/*
		var downloadPayoutModal = b.modal.new({
			title: 'Process Payout',
			disableClose: false,
			html: "You are downloading the payout for " + start_date + " to " + end_date + " for " + type + " commissions.",
			buttons: {
				'Ok' : function() {	
					beyond.request({
						url : '/admin/transactions/download',
						data : {
							'start_date': start_date,
							'end_date': end_date,
							'type': type
						},
						on_success : function(data) {
							downloadPayoutModal.hide();	
						} // end on_success
					})		
					downloadPayoutModal.hide();
				}
			}
		});
		downloadPayoutModal.show();
		*/
	}	
</script>