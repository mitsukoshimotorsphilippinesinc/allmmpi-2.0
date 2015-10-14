<?php

	$from_date = date('F d, Y h:i:s a',strtotime($payout_start_date));
	$to_date = date('F d, Y',strtotime($payout_end_date));
	
	$display_sms_count = 1;
	if($this->settings->display_sms_count == '0') {
		$display_sms_count = 0;
	}
	
	$display_monthly_igpsm_earnings = 1;
	if($this->settings->display_monthly_igpsm_earnings == '0') {
		$display_monthly_igpsm_earnings = 0;
	}
	
?>
<div class="page-header clearfix">
	<h2>Gross this Cut-Off<small id="header_payout_start_date"> (from <?= $from_date ?>)</small></h2> 
</div>
<div  style="margin-bottom:30px;">
	<div class='span3' style="margin-left:15px;">
		<table class='table table-bordered table-condensed table-striped'>
			<thead>
				<tr><td bgcolor="#AED0B4"><h4><center>IGPSM</center></h4></td></tr>
			</thead>
			<tbody>
				<tr><td style='text-align:center;'><h3 id='weekly_igpsm_gross'><?= $gross_igpsm ?></h4></td></tr>
			</tbody>
		</table>
	</div>
	
	<div class='span3' style=''>
		<table class='table table-bordered table-condensed table-striped'>
			<thead>
				<tr><td bgcolor="#AED0B4"><h4><center>Gift Cheques</center></h4></td></tr>
			</thead>
			<tbody>
				<tr><td style='text-align:center;'><h3 id='weekly_unilevel_gross'><?= $gross_gc ?></h4></td></tr>
			</tbody>
		</table>
	</div>
	
	<div class='span3' style=''>
		<table class='table table-bordered table-condensed table-striped'>
			<thead>
				<tr><td bgcolor="#AED0B4"><h4><center>Unilevel</center></h4></td></tr>
			</thead>
			<tbody>
				<tr><td style='text-align:center;'><h3 id='weekly_unilevel_gross'><?= $gross_unilevel ?></h4></td></tr>
			</tbody>
		</table>
	</div>
	
	<div class='span3' style=''>
		<table class='table table-bordered table-condensed table-striped'>
			<thead>
				<tr><td bgcolor="##2F96B4"><h4><center>Total Gross</center></h4></td></tr>
			</thead>
			<tbody>
				<tr><td style='text-align:center;'><h3 id='weekly_total_gross'><?= $gross_total ?></h4></td></tr>
			</tbody>
		</table>
	</div>
	
	<hr/>
</div>
	
<div class='clearfix'></div>
	
<div class="page-header clearfix">
	<h2>Total Earnings <small>as of <?= date("F d, Y"); ?></small></h2>
</div>
<div  style="margin-bottom:30px;">
	<div class='clearfix' >		
		<!--div class='span6' style='margin: 5px 0px;'>
			<table class='table table-bordered table-condensed table-striped'>
				<thead>
					<tr><td><h4>IGPSM Earnings This Cut-Off</h4></td></tr>
				</thead>
				<tbody>
					<tr><td style='text-align:center;'><h3 id='weekly_igpsm'>0.00</h4></td></tr>
				</tbody>
			</table>
		</div>
		<div class='span6' style='margin: 5px 20px;'>
			<table class='table table-bordered table-condensed table-striped'>
				<thead>
					<tr><td><h4>Monthly Unilevel Earnings</h4></td></tr>
				</thead>
				<tbody>
					<tr><td style='text-align:center;'><h3 id='monthly_unilevel'>0.00</h4></td></tr>
				</tbody>
			</table>
		</div-->
	</div>
	
	<div class='clearfix' >		
		<div class='span span6' style='margin-left:-1px;'>
			
			<table id='monthy-igpsm-earnings-area' class='table table-bordered table-condensed table-striped'>
				<thead>
					<tr><td><h4>Monthly IGPSM Earnings</h4></td></tr>
				</thead>
				<tbody>
					<tr><td style='text-align:center;'><h3 id='monthly_igpsm'>0.00</h4></td></tr>
				</tbody>
			</table>
		
			<table class='table table-condensed table-striped table-bordered'>
				<thead>
					<tr><th colspan="2" align="center">IGPSM Earnings</th></tr>
					<tr>
						<th >Type</th>
						<th style="width:175px;">Earnings</th>
					</tr>
				</thead>
				<tbody id="igpsm_earnings">
				</tbody>
			</table>
		</div>
		<div class='span span6'>
		
			<table class='table table-bordered table-condensed table-striped'>
				<thead>
					<tr><td><h4>Monthly Unilevel Earnings</h4></td></tr>
				</thead>
				<tbody>
					<tr><td style='text-align:center;'><h3 id='monthly_unilevel'>0.00</h4></td></tr>
				</tbody>
			</table>
			
			<table class='table table-condensed table-striped table-bordered'>
				<thead>
					<tr><th colspan="2" align="center">Unilevel Earnings</th></tr>
					<tr>
						<th >Type</th>
						<th style="width:175px;">Earnings</th>
					</tr>
				</thead>
				<tbody id="unilevel_earnings">
				</tbody>
			</table>
			
			<table id='sms-notification-area' class='table table-condensed table-striped table-bordered'>
				<thead>
					<tr><th colspan="5" align="center">Sms Notifications Count</th></tr>
					<tr>
						<th ></th>
						<th colspan="2" style="width:175px;">This Cut-Off</th>
						<th colspan="2" style="width:175px;">Total</th>
					</tr>
					<tr>
						<th >Type</th>
						<th style="width:175px;">Referral</th>
						<th style="width:175px;">Pairing</th>
						<th style="width:175px;">Referral</th>
						<th style="width:175px;">Pairing</th>
					</tr>
				</thead>
				<tbody id="sms_notifications_count">
				</tbody>
			</table>
		</div>
	</div>
</div>
<hr/>


<h2>Payout History</h2>
<div>
	<div class="span span3">
		<div class="control-group ">
			<label for="start-date" class="control-label"><strong>Start Date</strong></label>
			<div class="controls">
				<div class="input-append" >
					<input title="Start Date" class="input-medium" type="text" id="start-date" name="start-date" value="" readonly="readonly">
					<span id='start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
				</div>
			</div>
		</div>
	</div>
	<div class="span span3">
		<div class="control-group ">
			<label for="end-date" class="control-label"><strong>End Date</strong></label>
			<div class="controls">
				<div class="input-append" >
					<input title="End Date" class="input-medium" type="text" id="end-date" name="end-date" value="" readonly="readonly">
					<span id='end-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
				</div>
			</div>
		</div>
	</div>
	<div class="span span3">
		<div class="control-group ">
			<label for="payout-type" class="control-label"><strong>Payout Type</strong></label>
			<div class="controls">
				<div class="input-append" >
					<select id="payout-type">
						<option value="IGPSM">IGPSM</option>
						<option value="UNILEVEL">UNILEVEL</option>
					</select>
				</div>
			</div>
		</div>	
	</div>

</div>


<div class="span3">
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<a id='get_history' class="btn btn-primary" data="<?= $member_id; ?>"><strong>View</strong></a>
		</div>
	</div>
</div>
<br/>
<div class='clearfix'></div>

<div class="tabbable header-info-admin-child">
	<ul id="profile_tab" class="nav nav-tabs">
		<li class="active summary_tab"><a href="#summary_tab" data-toggle="tab">Summary</a></li>
		<li class="encashments_tab"><a href="#encashments_tab" data-toggle="tab">Encashments</a></li>
		<li class="earnings_per_account_tab"><a href="#earnings_per_account_tab" data-toggle="tab">EPA</a></li>
		<li class="gcep_tab"><a href="#gcep_tab" data-toggle="tab">GCEP</a></li>
		<li class="giftcheques_tab"><a href="#giftcheques_tab" data-toggle="tab">Gift Cheques</a></li>
		<li class="deductions_tab"><a href="#deductions_tab" data-toggle="tab">Deductions</a></li>
		<li class="adjustments_tab"><a href="#adjustments_tab" data-toggle="tab">Adjustments</a></li>
		<li class="fundstopaycard_tab"><a href="#fundstopaycard_tab" data-toggle="tab">F2P</a></li>
		<li class="transfers_tab"><a href="#transfers_tab" data-toggle="tab">Transfers</a></li>
		<li class="corpo_sharing_tab"><a href="#corpo_sharing_tab" data-toggle="tab">Corpo Sharing</a></li>
		
	</ul>
	<div class="tab-content">
		<div class="tab-pane active summary" id="summary_tab">
			<div id="html_summary_details"></div>	
		</div>
		<div class="tab-pane" id="encashments_tab">
			<div id="html_encashments_details"></div>	
		</div>
		<div class="tab-pane" id="earnings_per_account_tab">
		 	<div id="html_earnings_per_account_details"></div>	
		</div>		
		<div class="tab-pane" id="gcep_tab">
		 	<div id="html_gcep_details"></div>	
		</div>
		<div class="tab-pane" id="giftcheques_tab">
			<div id="html_giftcheques_summary_details"></div>			
		 	<div id="html_giftcheques_details"></div>	
		</div>
		<div class="tab-pane" id="deductions_tab">
		 	<div id="html_deductions_details"></div>	
		</div>
		<div class="tab-pane" id="adjustments_tab">
			<div id="html_adjustments_details"></div>	
		</div>
		<div class="tab-pane" id="fundstopaycard_tab">
			<div id="html_fundstopaycard_details"></div>	
		</div>
		<div class="tab-pane" id="transfers_tab">
			<div id="html_transfers_details"></div>	
		</div>
		<div class="tab-pane" id="corpo_sharing_tab">
			<div id="html_corpo_sharing_details"></div>	
		</div>
	</div>
</div>

<br/>

<script type="text/javascript">

	$(document).ready(function(){
	
		// test		
		var currDate = new Date();
		var currYear = new Date().getFullYear();
		var yrRange = "2005:" + currYear;

		$("#start-date").datepicker({
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});
		
		$("#start-date").datepicker('setDate', '<?= $datepick_start_date; ?>');
		
		$("#start-date-icon").click(function(e) {
			$("#start-date").datepicker("show");
		});

		$("#end-date").datepicker({
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		var _end_date = new Date();
		_end_date.setDate(_end_date.getDate()+6);		
		$("#end-date").datepicker('setDate', '<?= $datepick_end_date; ?>');
		
		$("#end-date-icon").click(function(e) {
			$("#end-date").datepicker("show");
		});
		
		if (<?=$display_sms_count;?> == 0) {
			$("#sms-notification-area").hide();
		} else {
			$("#sms-notification-area").show();	
		}
		
		if (<?=$display_monthly_igpsm_earnings;?> == 0) {
			$("#monthy-igpsm-earnings-area").hide();
		} else {
			$("#monthy-igpsm-earnings-area").show();	
		}
		
		renderSummary($('#start-date').val(), $('#end-date').val(), 'IGPSM', 'ALL');
		renderEncashments($('#start-date').val(), $('#end-date').val(), 'IGPSM');
		renderEarningsPerAccount($('#start-date').val(), $('#end-date').val(), 'IGPSM', 'ALL');
		renderGCEP($('#start-date').val(), $('#end-date').val());	
		renderGiftCheques($('#start-date').val(), $('#end-date').val(), 'IGPSM', 'ALL');
		renderDeductions($("#start-date").val(),$("#end-date").val(), 'IGPSM');
		renderAdjustments($("#start-date").val(),$("#end-date").val());
		renderFundsToPaycard($("#start-date").val(),$("#end-date").val(), 'IGPSM');	
		renderTransfers($('#start-date').val(), $('#end-date').val());
		renderCorpoSharing($('#start-date').val(), $('#end-date').val(), 'IGPSM');
	
		showLoading();
		//get igpsm earnings
		b.request({
			url: "earnings/get_member_earnings",
			data: {
				"type": "igpsm",
				"member_id": "<?= $member_id ?>"
			},
			on_success: function(data){
				if(data.status == "ok")
				{
					$("#igpsm_earnings").html(data.data.igpsm_earnings);
					$("#unilevel_earnings").html(data.data.unilevel_earnings);
					$("#weekly_igpsm").html(data.data.weekly_igpsm);
					$("#monthly_unilevel").html(data.data.monthly_unilevel);
					$("#monthly_igpsm").html(data.data.monthly_igpsm);
					$("#sms_notifications_count").html(data.data.sms_notifications_count);
				}
				hideLoading();
			}
		});
		
	});
	
	
	function renderSummary(generic_start_date, generic_end_date, _payout_type, _account_id){
	
		b.request({
	        url: '/members/earnings/summary',
	        data: {
				"start_date": generic_start_date,
				'end_date': generic_end_date,
				'payout_type': _payout_type,
				'account_id': _account_id,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_summary_details').html(data.html);
				} else {
					// create modal for errors
					var summary_modal = b.modal.new({
						title: 'Encashments - Summary :: Error',
			        	html:  "<p>"+data.message+"</p>",
					})
					summary_modal.show();
				}
		    }
		});		
		return;
	}
	
	function renderEncashments(generic_start_date, generic_end_date, _payout_type){
	
		b.request({
	        url: '/members/earnings/encashments',
	        data: {
				"start_date": generic_start_date,
				'end_date': generic_end_date,
				'payout_type': _payout_type,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_encashments_details').html(data.html);
				} else {
					// create modal for errors
					var encashments_modal = b.modal.new({
						title: 'Encashments :: Error',
			        	html:  "<p>"+data.message+"</p>",
					})
					encashments_modal.show();
				}
		    }
		});		
		return;
	}
	
	function renderEarningsPerAccount(generic_start_date, generic_end_date, _payout_type, _account_id){
	
		b.request({
	        url: '/members/earnings/earnings_per_account',
	        data: {
				"start_date": generic_start_date,
				'end_date': generic_end_date,
				'payout_type': _payout_type,
				'account_id': _account_id,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_earnings_per_account_details').html(data.html);
				} else {
					// create modal for errors
					var earningsPerMemberModal = b.modal.new({
						title: 'Encashments - Earnings :: Error',
			        	html:  "<p>"+data.message+"</p>",
					})
					earningsPerMemberModal.show();
				}
		    }
		});		
		return;
	}
	
	function renderGCEP(generic_start_date, generic_end_date){
		b.request({
	        url: '/members/earnings/gcep',
	        data: {
				"start_date": generic_start_date,
				'end_date': generic_end_date,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_gcep_details').html(data.html);
				} else {
					//// create modal for errors
					//var gcepModal = b.modal.new({
					//	title: 'Encashments - GCEP :: Error',
			        //	html:  "<p>"+data.message+"</p>",
					//})
					//gcepModal.show();
				}
		    }
		});		
		return;
	}
	
	
	function renderGiftCheques(generic_start_date, generic_end_date, _payout_type, _account_id){
	
		b.request({
	        url: '/members/earnings/giftcheques',
	        data: {
				"start_date": generic_start_date,
				'end_date': generic_end_date,
				'payout_type': _payout_type,
				'account_id': _account_id,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_giftcheques_details').html(data.html);
				} else {
					// create modal for errors
					var giftChequesModal = b.modal.new({
						title: 'Encashments - Gift Cheques :: Error',
			        	html:  "<p>"+data.message+"</p>",
					})
					giftChequesModal.show();
				}
		    }
		});		
		return;
	}
	
	
	
	function renderDeductions(generic_start_date, generic_end_date, _payout_type){
		b.request({
	        url: '/members/earnings/deductions',
	        data: {
				'start_date': generic_start_date,
				'end_date': generic_end_date,
				'payout_type': _payout_type,
				'member_id': <?=$member_id;?>				
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_deductions_details').html(data.html);
				} else {
					//// create modal for errors
					//var deductionsModal = b.modal.new({
					//	title: 'Encashments - Deductions :: Error',
			        //	html:  "<p>"+data.message+"</p>",
					//})
					//deductionsModal.show();
				}
		    }
		});		
		return;
	}
	
	function renderAdjustments(generic_start_date, generic_end_date, _payout_type){
		b.request({
	        url: '/members/earnings/adjustments',
	        data: {
				'start_date': generic_start_date,
				'end_date': generic_end_date,	
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_adjustments_details').html(data.html);
				} else {
					//// create modal for errors
					//var adjustmentsModal = b.modal.new({
					//	title: 'Encashments - Adjustments :: Error',
			        //	html:  "<p>"+data.message+"</p>",
					//})
					//adjustmentsModal.show();
				}
		    }
		});		
		return;
	}
	
	function renderFundsToPaycard(generic_start_date, generic_end_date, _payout_type){
	
		
		b.request({
	        url: '/members/earnings/funds_to_paycard',
	        data: {
				'start_date': generic_start_date,
				'end_date': generic_end_date,
				'payout_type': _payout_type,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
				
					$('#html_fundstopaycard_details').html(data.html);
				} else {
					//// create modal for errors
					//var fundstopaycard_modal = b.modal.new({
					//	title: 'Funds to Paycard :: Error',
			        //	html:  "<p>"+data.message+"</p>",
					//})
					//fundstopaycard_modal.show();
				}
		    }
		});		
		return;
	}
	
	
	function renderTransfers(generic_start_date, generic_end_date){
		b.request({
	        url: '/members/earnings/transfers',
	        data: {
				"start_date": generic_start_date,
				'end_date': generic_end_date,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_transfers_details').html(data.html);
				} else {
					//// create modal for errors
					//var transfersModal = b.modal.new({
					//	title: 'Encashments - Transfers :: Error',
			        //	html:  "<p>"+data.message+"</p>",
					//})
					//transfersModal.show();
				}
		    }
		});		
		return;
	}
	
	function renderCorpoSharing(generic_start_date, generic_end_date, _payout_type){
	
		b.request({
	        url: '/members/earnings/corpo_sharing',
	        data: {
				"start_date": generic_start_date,
				'end_date': generic_end_date,
				'payout_type': _payout_type,
				'member_id': <?=$member_id;?>
			},
		
			on_success: function(data, status) {
				if (data.status == 1) {
					$('#html_corpo_sharing_details').html(data.html);
				} else {
					// create modal for errors
					var corpo_sharing_modal = b.modal.new({
						title: 'Corpo Sharing :: Error',
			        	html:  "<p>"+data.message+"</p>",
					})
					corpo_sharing_modal.show();
				}
		    }
		});		
		return;
	}
	
	
	$(document).on("change","#account_id_val",function(){		
	
		renderEarningsPerAccount($("#start-date").val(),$("#end-date").val(),$("#payout-type").val(),$("#account_id_val").val());
		renderGiftCheques($("#start-date").val(),$("#end-date").val(),$("#payout-type").val(),$("#account_id_val").val());
	});
		
	$(document).on("change","#account_id_val2",function(){		

		renderEarningsPerAccount($("#start-date").val(),$("#end-date").val(),$("#payout-type").val(),$("#account_id_val2").val());
		renderGiftCheques($("#start-date").val(),$("#end-date").val(),$("#payout-type").val(),$("#account_id_val2").val());
	});
	
	
	$(document).on("click","#get_encashment_history",function(){		
		getHistory();
	});
	
	$(document).on("click","#get_epm_history",function(){		
		getEpmHistory();
	});
	
	$(document).on("click","#get_gc_history",function(){
		getGCHistory();
	});
	
	$(document).on("click","#get_history", function(){
		renderSummary($("#start-date").val(),$("#end-date").val(),$("#payout-type").val(),$("#account_id_val").val());
		renderEncashments($("#start-date").val(),$("#end-date").val(),$("#payout-type").val());		
		renderEarningsPerAccount($("#start-date").val(),$("#end-date").val(),$("#payout-type").val(),$("#account_id_val").val());
		renderGiftCheques($("#start-date").val(),$("#end-date").val(),$("#payout-type").val(),$("#account_id_val").val());
		renderGCEP($("#start-date").val(),$("#end-date").val());
		renderAdjustments($("#start-date").val(),$("#end-date").val());
		renderDeductions($("#start-date").val(),$("#end-date").val(),$("#payout-type").val());
		renderFundsToPaycard($("#start-date").val(),$("#end-date").val(),$("#payout-type").val());	
		renderTransfers($("#start-date").val(),$("#end-date").val());		
		renderCorpoSharing($("#start-date").val(),$("#end-date").val(),$("#payout-type").val());		
	});
	
	
	$(".history").click(function(){
		var tab = $(this).attr("data");
		
		if(tab === "encashment")
		{
			getHistory();
		}
		else if(tab === "epm")
		{
			getEpmHistory();
		}
		else if(tab === "gc")
		{
			getGCHistory();
		}
	});
	
	var getHistory = function(){
		showLoading();
		b.request({
			url: "/members/earnings/get_member_history",
			data: {
				"member_id": <?= $member_id; ?>,
				"start_date": $("#encashment_from_date").val(),
				"end_date": $("#encashment_to_date").val(),
				"payout_type": $("#payout_type").val()
			},
			on_success: function(data){
				$("#earnings_history").html(data.data.html);
				hideLoading();
			},
			on_error: function(){

			}
		});
	}

	var getEpmHistory = function(){
		showLoading();
		b.request({
			url: "/members/earnings/get_epm_history",
			data: {
				"member_id": <?= $member_id; ?>,
				"start_date": $("#epm_from_date").val(),
				"end_date": $("#epm_to_date").val(),
				"payout_type": $("#epm_payout_type").val()
			},
			on_success: function(data){
				$("#funds_history").html(data.data.html);
				
				hideLoading();
			},
			on_error: function(){

			}
		});
	}
	
	var getGCHistory = function(){
		showLoading();
		b.request({
			url: "/members/earnings/get_gc_history",
			data: {
				"member_id": <?= $member_id; ?>,
				"start_date": $("#gc_from_date").val(),
				"end_date": $("#gc_to_date").val()
			},
			on_success: function(data){
				$("#gc_history").html(data.data.html);
				
				hideLoading();
			},
			on_error: function(){

			}
		});
	}
	
	var getMemberFunds = function(){
		showLoading();
		b.request({
			url: "/members/earnings/get_member_funds",
			data: {
				"member_id": <?= $member_id; ?>
			},
			on_success: function(data){
				$("#funds_amount").html(data.data.funds);
				
				hideLoading();
			},
			on_error: function(){

			}
		});
	}
	
	var getMemberGC = function(){
		showLoading();
		b.request({
			url: "/members/earnings/get_member_gc",
			data: {
				"member_id": <?= $member_id; ?>
			},
			on_success: function(data){
				$("#gc_amount").html(data.data.gc);
				
				hideLoading();
			},
			on_error: function(){

			}
		});
	}
</script>

