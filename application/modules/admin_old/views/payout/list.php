<?php
	$start_date = date("Y-m-d");
	$end_date = date("Y-m-d");

	$payout_type_select = "<select id='payout-type'>";
	$payout_type_select .= "<option value='IGPSM'>IGPSM</option>" ;
	$payout_type_select .= "<option value='UNILEVEL'>UNILEVEL</option>" ;
	$payout_type_select .= "</select>" ;

?>
<div class='alert alert-info'><h2>Payout</h2></div>

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

<div class="tabbable">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#commissions" data-toggle="tab">Commissions per Account</a></li>
        <!-- <li><a href="#commissions-new-accounts" data-toggle="tab">Commissions per New Accounts</a></li> -->
		<li><a href="#gift-cheques" data-toggle="tab">Gift Cheques per Account</a></li>
		<li><a href="#member-payout" data-toggle="tab">Member Payout</a></li>
		<li><a href="#transaction-logs" data-toggle="tab">Transaction Logs</a></li>
    </ul>
	<div class="tab-content">
		<div class="tab-pane active" id="commissions">
			<table class='table table-striped table-bordered table-condensed'>
				<thead>
					<tr>
						<th>Last Name</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Account ID</th>
						<th>Gross</th>
						<th>Tax</th>
						<th>Net</th>
						<th>CD Balance</th>
						<th>TOTAL</th>
						<th>Cash Card</th>
						<th>Account Status</th>
						<th>Commission Date</th>
					</tr>
				</thead>
				<tbody id='payout-details'>
					<tr><td colspan='12'><strong><center>No Records Found</center></strong></td></tr>
				</tbody>
			</table>
		</div>
		<!-- <div class="tab-pane" id="commissions-new-accounts">
			<table class='table table-striped table-bordered table-condensed'>
				<thead>
					<tr>
						<th>Last Name</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Account ID</th>
						<th>Gross</th>
						<th>Tax</th>
						<th>Net</th>
						<th>CD Balance</th>
						<th>TOTAL</th>
						<th>Cash Card</th>
						<th>Account Status</th>
						<th>Commission Date</th>
					</tr>
				</thead>
				<tbody id='payout-details-new-accounts'>
					<tr><td colspan='12'><strong><center>No Records Found</center></strong></td></tr>
				</tbody>
			</table>
		</div> -->
		<div class="tab-pane" id="gift-cheques">
			<table class='table table-striped table-bordered table-condensed'>
				<thead>
					<tr>
						<th>Last Name</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Account ID</th>
						<th>Type</th>
						<th>Amount</th>
						<th>Account Status</th>
					</tr>
				</thead>
				<tbody id='gc_per_account_details'>
					<tr><td colspan='10'><strong><center>No Records Found</center></strong></td></tr>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="member-payout">
			<table class='table table-striped table-bordered table-condensed'>
				<thead>
					<tr>
						<th>Last Name</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Amount</th>
						<th>Cash Card</th>
					</tr>
				</thead>
				<tbody id='member_payout_details'>
					<tr><td colspan='10'><strong><center>No Records Found</center></strong></td></tr>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="transaction-logs">
			<div status="vertical-align:middle;" >
				<strong>Member:</strong> <select id="tl-member-id" >
					<option value="0" >Select Member</option>
				</select>
			</div>
			<table class='table table-striped table-bordered table-condensed'>
				<thead>
					<tr>
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
	</div>
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
		
		viewTransactionLogs(payout_type,start_date,end_date,$(this).val());
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
						url : '/admin/payout/process',
						data : {
							'start_date': start_date,
							'end_date': end_date,
							'type': type
						},
						on_success : function(data) {
							viewCommissionPerAccount(type);
							viewCommissionPerNewAccount(type);
							viewGCPerAccount(start_date,end_date);
							viewMemberPayout(type,start_date,end_date);
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
			url: '/admin/payout/commission_per_account',
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
			url: '/admin/payout/commission_per_new_account',
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
			url: '/admin/payout/gc_per_account',
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
			url: '/admin/payout/member_payout',
			data:{'type':type,'start_date':start_date,'end_date':end_date},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#member_payout_details").html(data.data.html);
				}
			}
		});
	}

	var viewTransactionLogs = function(type,start_date,end_date,member_id)
	{
		beyond.request({
			url: '/admin/payout/transaction_logs',
			data:{'type':type,'start_date':start_date,'end_date':end_date,'member_id':member_id},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#transaction_logs_details").html(data.data.html);

					$("#tl-member-id").html(data.data.html_member_option);

				}
			}
		});
	}
	
	var downloadPayoutTables = function(type,start_date,end_date) 
	{
		
		
	    $('#inset_form').html('<form action="/admin/payout/download" method="post" style="display:none;" id="download_form"><input type="text" id="start_date" name="start_date" value="' + start_date + '" /><input type="text" id="end_date" name="end_date" value="' + end_date + '" /><input type="text" id="type" name="type" value="' + type + '" /></form>');

	    $('#download_form').submit();

		/*
		var downloadPayoutModal = b.modal.new({
			title: 'Process Payout',
			disableClose: false,
			html: "You are downloading the payout for " + start_date + " to " + end_date + " for " + type + " commissions.",
			buttons: {
				'Ok' : function() {	
					beyond.request({
						url : '/admin/payout/download',
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