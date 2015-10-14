<?php
	$from_date = date('Y-m-d H:i a',strtotime($member->insert_timestamp));
	$to_date = date('Y-m-d H:i a');
?>
<div class="page-header clearfix">
	<h2>Total Earnings <small>as of <?= date("F d, Y"); ?></small></h2>
</div>
<div  style="margin-bottom:30px;">
	
	<div style='margin: 5px 0px;'>
		<label style="display:inline;"><strong>Funds: </strong></label><span id="funds_amount"><?= number_format($member->funds, 2); ?></span>
	</div>
	<div style='margin: 5px 0px;'>
		<label style=" display:inline;"><strong>Gift Cheques: </strong></label><span id="gc_amount"><?= number_format($member->gift_cheques, 0); ?></span>
	</div>
	<div style='margin: 5px 0px;'>
		<label style=" display:inline;"><strong>IGPSM Earnings Since Last Cut-Off: </strong></label><span id="weekly_igpsm">0.00</span>
	</div>
	<div style='margin: 5px 0px;'>
		<label style=" display:inline;"><strong>Monthly Unilevel Earnings: </strong></label><span id="monthly_unilevel">0.00</span>
	</div>
	<hr/>
	<div class='clearfix' >
		<div class='span span5' style>
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
		<div class='span span5'>
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
		</div>
	</div>
</div>
<hr/>
<div>
	<h2>Encashments</h2>

	<div class="tabbable">	
		<ul class="nav nav-tabs">
            <li class="active" data="per_account"><a href="#per_account" id='per_account_button' data-toggle="tab">Per Account</a></li>
			<li data="epm"><a href="#epm" id='epm_button' data-toggle="tab">Encashments</a></li>
        </ul>
	
		<div class="tab-content">
			<div class="tab-pane active" id="per_account">
				<div class="clearfix">
					<div class="span span3">
						<div class="control-group">
							<label class="control-label" for="encashment_from_date">Start Date</label>
							<div class="input-append">
								<input type="text" class="input-medium" id="encashment_from_date" name='encashment_from_date' readonly='readonly' style='cursor:pointer;' />
								<span id='encashment_from_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="span span3">
						<div class="control-group">
							<label class="control-label" for="encashment_to_date">End Date</label>
							<div class="input-append">
								<input type="text" class="input-medium" id="encashment_to_date" name='encashment_to_date' readonly='readonly' style='cursor:pointer;' />
								<span id='encashment_to_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					
					<div class="span span3">
						<div class="control-group ">
							<label class="control-label" for="payout_type">Payout Type</label>
							<div class="input-append">
								<select id='payout_type'>
								<option value='IGPSM' selected='selected'>IGPSM</option>
								<option value='UNILEVEL'>Unilevel</option>
								</select>
																
							</div>
						</div>
					</div>
					
					
					<div class="span1">
						<div class="control-group">
							<label class="control-label">&nbsp;</label>
							<div class="controls">
								<a id='get_encashment_history' class="btn btn-primary" data="<?= $member_id; ?>"><strong>View</strong></a>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div class='span span12'>
						<table class='table table-condensed table-striped table-bordered'>
							<thead>
								<tr>									
									<th style="text-align:center;">Account ID</th>
									<th style="text-align:center;">Gross</th>
									<th style="text-align:center;">Withholding Tax</th>	
									<th style="text-align:center;">Net of Tax</th>	
									<th style="text-align:center;">Balance</th>	
									<th style="text-align:center;">Total</th>
									<th style="text-align:center;">Cash Card</th>
									<th style="text-align:center;">Status</th>																		
								</tr>
							</thead>
							<tbody id="earnings_history">
								<tr><td colspan="8" style="text-align: center;">No Entries Found</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="epm">
				<div class="clearfix">
					<div class="span span3">
						<div class="control-group ">
							<label class="control-label" for="epm_from_date">Start Date</label>
							<div class="input-append">
								<input type="text" class="input-medium" id="epm_from_date" name='epm_from_date' readonly='readonly' style='cursor:pointer;' />
								<span id='epm_from_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="span span3">
						<div class="control-group ">
							<label class="control-label" for="epm_to_date">End Date</label>
							<div class="input-append">
								<input type="text" class="input-medium" id="epm_to_date" name='epm_to_date' readonly='readonly' style='cursor:pointer;' />
								<span id='epm_to_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>		

					<div class="span span3">
						<div class="control-group ">
							<label class="control-label" for="epm_payout_type">Payout Type</label>
							<div class="input-append">
								<select id='epm_payout_type'>
								<option value='IGPSM' selected='selected'>IGPSM</option>
								<option value='UNILEVEL'>Unilevel</option>
								</select>
																
							</div>
						</div>
					</div>
					
					<div class="span1">
						<div class="control-group">
							<label class="control-label">&nbsp;</label>
							<div class="controls">
								<a id='get_epm_history' class="btn btn-primary" data="<?= $member_id; ?>"><strong>View</strong></a>
							</div>
						</div>
					</div>
				</div>
								
				<div class="clearfix">
					<div class='span span12'>
						<table class='table table-condensed table-striped table-bordered'>
							<thead>
								<tr>									
									<th style="text-align:center;">Gross</th>
									<th style="text-align:center;">GCEP</th>	
									<th style="text-align:center;">Withholding Tax</th>	
									<th style="text-align:center;">Balance</th>	
									<th style="text-align:center;">Total Amount</th>
									<th style="text-align:center;">Cash Card</th>
									<th style="text-align:center;">Status</th>				
								</tr>
							</thead>
							<tbody id="funds_history">
								<tr><td colspan="7" style="text-align: center;">No Entries Found</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="gc">
				<div class="clearfix">
					<div class="span span3">
						<div class="control-group">
							<label class="control-label" for="gc_from_date">Start Date</label>
							<div class="input-append">
								<input type="text" class="input-medium" id="gc_from_date" name='gc_from_date' readonly='readonly' style='cursor:pointer;' />
								<span id='gc_from_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="span span3">
						<div class="control-group">
							<label class="control-label" for="gc_to_date">End Date</label>
							<div class="input-append">
								<input type="text" class="input-medium" id="gc_to_date" name='gc_to_date' readonly='readonly' style='cursor:pointer;' />
								<span id='gc_to_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="span span1">
						<div class="control-group">
							<label class="control-label">&nbsp;</label>
							<div class="controls">
								<a id='get_gc_history' class="btn btn-primary" data="<?= $member_id; ?>"><strong>View</strong></a>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div class='span span12'>
						<table class='table table-condensed table-striped table-bordered'>
							<thead>
								<tr>
									<th style="width:50px;">Type</th>
									<th style="width:100px;">Amount</th>
									<th>Remarks</th>
									<th style="width: 195px;">Date</th>
								</tr>
							</thead>
							<tbody id="gc_history">
								<tr><td colspan="7" style="text-align: center;">No Entries Found</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$(document).ready(function(){
		
		// encashment from
		$("#encashment_from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#encashment_from_date").datepicker('setDate', '<?= date("Y-m-d") . " 12:00 am" ?>');
		
		$("#encashment_from_date_icon").click(function(e) {
			$("#encashment_from_date").datepicker("show");
		});
		
		// encashment to
		$("#encashment_to_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#encashment_to_date").datepicker('setDate', (new Date()));
		
		$("#encashment_to_date_icon").click(function(e) {
			$("#encashment_to_date").datepicker("show");
		});
		
		// epm from
		$("#epm_from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#epm_from_date").datepicker('setDate', '<?= date("Y-m-d") . " 12:00 am" ?>');
		
		$("#epm_from_date_icon").click(function(e) {
			$("#epm_from_date").datepicker("show");
		});
		
		// epm to
		$("#epm_to_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#epm_to_date").datepicker('setDate', (new Date()));
		
		$("#epm_to_date_icon").click(function(e) {
			$("#epm_to_date").datepicker("show");
		});
		
		// gc from
		$("#gc_from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#gc_from_date").datepicker('setDate', '<?= date("Y-m-d") . " 12:00 am" ?>');
		
		$("#gc_from_date_icon").click(function(e) {
			$("#gc_from_date").datepicker("show");
		});
		
		// funds to
		$("#gc_to_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#gc_to_date").datepicker('setDate', (new Date()));
		
		$("#gc_to_date_icon").click(function(e) {
			$("#gc_to_date").datepicker("show");
		});
		
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
				}
				hideLoading();
			}
		});
		
		getHistory();

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

