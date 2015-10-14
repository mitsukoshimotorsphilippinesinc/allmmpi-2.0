<?php echo js('libs/jquery.tablesorter.min.js'); ?>

<div class='alert alert-info'>
	<h2>Payout</h2>
</div>
<div style="margin:20px 20px;">
	<table class="table table-striped table-bordered">
		<tr>
			<th style="width:20%;" ><i id="step-one" ></i> <span>Select Payout Type and Dates</span></th>
			<th style="width:20%;" ><i class="step-two" ></i> <span>Choose/Customize Adjustments</span></th>
			<th style="width:20%;" ><i class="step-two" ></i> <span>Member Deductions</span></th>
			<th style="width:20%;" ><i id="step-three" ></i> <span>Review/Revise Output</span></th>
			<th style="width:20%;" ><i id="step-four" ></i> <span>Download and Credit Payout</span></th>
		</tr>
		<tr>
			<td>
				<strong>Type:</strong> <span id="payout-type-name" ></span><br />
				<strong>Start:</strong> <span id="payout-start" ></span><br />
				<strong>End:</strong> <span id="payout-end" /></span>
			</td>

			<td class="step2-head muted">
				<div class="step2-head-control hide">
					<strong>GCEP Benchmark:</strong><br/>
					<select id="psf-limit" class="span5" disabled="disabled">
						<option value="5000">5K</option>
						<option value="4000">4K</option>
						<option value="3000">3K</option>
					</select><br />
					<strong>GCEP Type:</strong> <br/>
					<select id="psf-type" class="span5" disabled="disabled">
						<option value="percent">Percent</option>
						<option value="value">value</option>
					</select><br />
					<strong>GCEP:</strong> <br/>
					<input title="GCEP" class="span5" type="text" id="psf" name="psf" value="0" disabled="disabled" /><br/>
					<div>
						<div class="btn btn-success btn-apply-gcep" disabled="disabled"><i class="icon-ok icon-white"></i> Apply GCEP</div>
						<div class="btn btn-danger btn-reset-gcep" disabled="disabled"><i class="icon-remove icon-white"></i> Reset GCEP</div>
					</div>
				</div>
			</td>
			<td class="deduction-step muted">
				<div class="step2-head-control hide">
					<div class="clearfix" style="margin-top: 10px;"></div>
					<center><div class="btn btn-success btn-apply-deduction span10" disabled="disabled"><i class="icon-ok icon-white"></i> Apply Deductions</div></center>
					<div class="clearfix" style="margin-top: 10px;"></div>
					<center><div class="btn btn-danger deduction-conflict-resolve-btn span10 hide"><i class="icon-warning-sign icon-white"></i> View Deduction Conflicts (<span class="deduction-conflict-count">3</span>)</div></center>
				</div>
			</td>
			<td></td>
			<td></td>
		</tr>
	</table>
</div>
<div class="row" style="margin:20px 20px;">
	<span class="pull-left">
		<a class="prev-step btn btn-primary hide">Back <i class="icon-step-backward icon-white"></i></a>
		<a class="next-step btn btn-primary">Next <i class="icon-step-forward icon-white"></i></a>
		<a class="cancel-step btn btn-danger hide">Cancel <i class="icon-remove icon-white"></i></a>
	</span>
</div>
<div style="padding:20px; margin:20px 20px; border:1px solid #999999; min-height:300px;">
	<div id="date-and-type-tab" class="payout-wizard-tabs" >
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
		<div class="control-group ">
			<label for="start-date" class="control-label"><strong>Start Date</strong></label>
			<div class="controls">
				<div class="input-append" >
					<input title="Start Date" class="input-medium" type="text" id="start-date" name="start-date" value="" readonly="readonly">
					<span id='start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
				</div>
			</div>
		</div>
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
	<div id="adjustments-tab" class="payout-wizard-tabs" style="display:none;" >
		<div class="control-group ">
			<label for="payout-type" class="control-label"><strong>GCEP Benchmark</strong></label>
			<div class="controls">
				<div class="input-append" >
					
				</div>
			</div>
		</div>
		<div class="control-group ">
			<label for="payout-type" class="control-label"><strong>GCEP Type</strong></label>
			<div class="controls">
				<div class="input-append" >
					
				</div>
			</div>
		</div>
		<div class="control-group ">
			<label for="payout-type" class="control-label"><strong>GCEP Value</strong></label>
			<div class="controls">
				<div class="input-append" >

				</div>
			</div>
		</div>
	</div>
	<div id="review-revise-tab" class="payout-wizard-tabs" style="display:none;" >
		<div style='padding-bottom:10px;' class='clearfix'>
			<button id="btn-download-payout-reports" class="btn btn-primary btn-large pull-right" ><span>Generate</span></button>
		</div>
		<div id="inset_form" ></div>
		<div id="deduction_inset_form" ></div>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#member-payout" data-toggle="tab">Member Payout</a></li>
			<li><a href="#commissions" data-toggle="tab">Commissions per Account</a></li>
			<li><a href="#commissions-new-accounts" data-toggle="tab">3 Month Old Accounts with Commission</a></li>
			<li><a href="#commissions-per-members" data-toggle="tab">Members with Commission</a></li>
			<li><a href="#gift-cheques" data-toggle="tab">Gift Cheques per Account</a></li>
			<li><a href="#transaction-logs" data-toggle="tab">Transaction Logs</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="member-payout">
				<form id='payout-balance-form' onsubmit='return false;' >
					<table class='table table-striped table-bordered table-condensed tablesorter'>
						<thead>
							<tr>
								<th>Last Name</th>
								<th style="width: 100px;">First Name</th>
								<th>Middle Name</th>
								<th>Gross</th>
								<th>Less <span id="payout-psf_value" ></span></th>
								<th>Net Gross</th>
								<th>Tax</th>
								<th>Net of Tax</th>
								<th>Balance</th>
								<th>Total</th>
								<th>Cash Card</th>
								<th>Account Number</th>
								<th>Service Depot</th>
                				<th>Group Name</th>
								<th></th>
							</tr>
						</thead>
						<tbody id='member_payout_details'>
							<tr><td colspan='15'><strong><center>No Records Found</center></strong></td></tr>
						</tbody>
					</table>
				</form>
			</div>
			<div class="tab-pane" id="commissions-new-accounts">
				<table class='table table-striped table-bordered table-condensed tablesorter'>
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
							<th>Date Registered</th>
							<th>Commission Date</th>
						</tr>
					</thead>
					<tbody id='payout-new-accounts-details'>
						<tr><td colspan='13'><strong><center>No Records Found</center></strong></td></tr>
					</tbody>
				</table>
			</div>
			<div class="tab-pane" id="commissions-per-members">
				<table class='table table-striped table-bordered table-condensed tablesorter'>
					<thead>
						<tr>
							<th>Last Name</th>
							<th>First Name</th>
							<th>Middle Name</th>
							<th>Gross</th>
							<th>Tax</th>
							<th>Net</th>
							<th>CD Balance</th>
							<th>TOTAL</th>
							<th>Cash Card</th>
							<th>Commission Date</th>
						</tr>
					</thead>
					<tbody id='commissions-per-members-details'>
						<tr><td colspan='10'><strong><center>No Records Found</center></strong></td></tr>
					</tbody>
				</table>
			</div>
			<div class="tab-pane" id="commissions">
				<table class='table table-striped table-bordered table-condensed tablesorter'>
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
							<th>Date Registered</th>
							<th>Commission Date</th>
						</tr>
					</thead>
					<tbody id='payout-details'>
						<tr><td colspan='12'><strong><center>No Records Found</center></strong></td></tr>
					</tbody>
				</table>
			</div>
			<div class="tab-pane" id="gift-cheques">
				<table class='table table-striped table-bordered table-condensed tablesorter'>
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
			<div class="tab-pane" id="transaction-logs">
				<div status="vertical-align:middle;" >
					<strong>Member:</strong> <select id="tl-member-id" >
						<option value="0" >Select Member</option>
					</select>
				</div>
				<table class='table table-striped table-bordered table-condensed tablesorter'>
					<thead>
						<tr>
							<th>Account ID</th>
							<th>Details</th>
							<th>Type</th>
							<th>Level</th>
							<th>Amount</th>
							<th>Date Time</th>
						</tr>
					</thead>
					<tbody id='transaction_logs_details'>
						<tr><td colspan='6'><strong><center>No Records Found</center></strong></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div style="margin:20px 20px;">
	<span class="pull-left">
		<a class="prev-step btn btn-primary hide">Back <i class="icon-step-backward icon-white"></i></a>
		<a class="next-step btn btn-primary">Next <i class="icon-step-forward icon-white"></i></a>
		<a class="cancel-step btn btn-danger hide">Cancel <i class="icon-remove icon-white"></i></a>
	</span>
</div>
<script>
	$(document).ready(function(){

		var currDate = new Date();
		var currYear = new Date().getFullYear();
		var yrRange = "2005:" + currYear;

		$("#start-date").datepicker({
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		$("#start-date").datepicker('setDate', currDate);
		
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
		$("#end-date").datepicker('setDate', _end_date);
		
		$("#end-date-icon").click(function(e) {
			$("#end-date").datepicker("show");
		});

		$("#start-date").on('change', function(e){
			var _end_count = 6;
			if($("#payout-type").val()=="UNILEVEL"){
				var _start_date = $("#start-date").val().split('-')[0] + '-' + $("#start-date").val().split('-')[1] + '-01';
				$("#start-date").val(_start_date);
				$("#start-date").datepicker('setDate', _start_date);
				_end_count = new Date($("#start-date").val().split('-')[0],$("#start-date").val().split('-')[1],0).getDate()-1;
			}			
			var _end_date = new Date($("#start-date").val());
			_end_date.setDate(_end_date.getDate()+_end_count);
			$("#end-date").datepicker('setDate', _end_date);
		});

		$("#payout-type").on('change', function(e){
			var _end_count = 7;
			if($(this).val()=="UNILEVEL"){
				var _start_date = $("#start-date").val().split('-')[0] + '-' + $("#start-date").val().split('-')[1] + '-01';
				$("#start-date").val(_start_date);
				$("#start-date").datepicker('setDate', _start_date);
				_end_count = new Date($("#start-date").val().split('-')[0],$("#start-date").val().split('-')[1],0).getDate()-1;
			}
			var _end_date = new Date($("#start-date").val());
			_end_date.setDate(_end_date.getDate()+_end_count);
			$("#end-date").datepicker('setDate', _end_date);
		});

		$(".next-step").on('click', function(){
			nextStep();
		});

		$('.prev-step').click(function(){
			prevStep();
		});
		$('.cancel-step').click(function(){
			payout.step = 1;

			$('.step2-head').addClass('muted');
			$('#psf-limit').attr('disabled','disabled');
			$('#psf-type').attr('disabled','disabled');
			$('#psf').attr('disabled','disabled');
			$('.btn-apply-gcep').attr('disabled','disabled');
			$('.btn-reset-gcep').attr('disabled','disabled');
			$('.btn-apply-deduction').attr('disabled','disabled');
			$('.btn-reset-deduction').attr('disabled','disabled');
			$('.deduction-conflict-resolve-btn').attr('disabled','disabled');

			$(".payout-wizard-tabs").hide();
			$('#date-and-type-tab').show();
			$('#payout-type-name').html('');
			$('#payout-start').html('');
			$('#payout-end').html('');
			payout.type = '';
			payout.start_date = '';
			payout.end_date = '';
			payout.checkedDates = false;
			payout.psf_limit = 5000;
			payout.psf_type = '';
			payout.psf_value = 0;
			payout.payout_id = 0;
		});

		$("#tl-member-id").on("change",function(){
			getTransactionLogs($(this).val());
		});

		$("#btn-download-payout-reports").on('click', function(e){
			download.init();
		});

		$('.deduction-conflict-resolve-btn').click(function(){
			beyond.request({
				url: '/admin/members_payout/get_deducted_conflicts',
				data: {
					'start_date':payout.start_date,
					'end_date':payout.end_date,
					'payout_id': payout.payout_id
				},
				on_success: function(data){
					if(data.status==1){
						var getDeductionsModal = beyond.modal.create({
							title: "Payout: Deduction Conflicts",
							width: 600,
							html: data.data
						});
						getDeductionsModal.show();
					}
				}
			});
		});
	});

	var root = this;
	var download = {};
	root['download'] = download;

	download._dlList = [];
	download._pollSpeed = 2000;
	download.regenerate = false;
	download.currentPollingID = 0;
	download.currentJobID = 0;
	download.form = function(){};
	download.init = function(){
		beyond.request({
			url: 'members_payout/get_download_form',
			data: {
				'type': payout.type,
				'start_date': payout.start_date,
				'end_date': payout.end_date
			},
			on_success: function(data){
				if(data.status == 1){
					download.form = beyond.modal.create({
						title: 'Payout Download',
						width: 500,
						html: data.data.html,
						disableClose: true
					});
					download.form.show();

					if(data.data.hasExisting){
						var previousDL_form = beyond.modal.create({
							title: 'Payout Download: Previous Generation',
							width: 500,
							html: 'This payout has been generated before. Press <b>Generate Again</b> to download an updated copy or <b>Download</b> to get the last generated.',
							disableClose: true,
							buttons: {
								'Download': function(){
									previousDL_form.hide();

									download._dlList = [];
									$('.dl-sheets').each(function(){
										download._dlList.push($(this).attr('data-dl'));
									});
									download.regenerate = false;
									if(download._dlList.length > 0){
										download.processList();
									} else {
										download.mergeDownload();
									}
								},
								'Generate Again': function(){
									$('.all-sheets').each(function(){
										if(!$(this).hasClass('dl-sheets')){
											$(this).attr('class','all-sheets dl-sheets');
										}
									});
									$('.dl-sheets div').each(function(){
										$(this).html('Pending').attr('class','label');
									});
									previousDL_form.hide();

									download._dlList = [];
									$('.dl-sheets').each(function(){
										download._dlList.push($(this).attr('data-dl'));
									});
									download.regenerate = true;
									if(download._dlList.length > 0){
										download.processList();
									} else {
										download.mergeDownload();
									}
								}
							}
						});
						previousDL_form.show();
					} else {
						download._dlList = [];
						$('.dl-sheets').each(function(){
							download._dlList.push($(this).attr('data-dl'));
						});
						if(download._dlList.length > 0){
							download.processList();
						} else {
							download.mergeDownload();
						}
					}
				}
			}
		});
	};

	download.processList = function(){
		var sheet_id = download._dlList.splice(0,1);
		console.log('start_download_job: ' + sheet_id);
		beyond.request({
			url: 'members_payout/start_download_job',
			data: {
				'sheet_id' : sheet_id[0],
				'type': payout.type,
				'start_date': payout.start_date,
				'end_date': payout.end_date,
				'payout_id': payout.payout_id
			},
			with_overlay: false,
			on_success: function(data){
				if(data.status == 1){
					$('.dl-sheets[data-dl="'+sheet_id+'"] div').html('Processing...').addClass('label-info');
					$('.dl-sheets[data-dl="'+sheet_id+'"]').addClass('current-job-sheet');
					download.currentJobID = data.data.job_id;
					download.currentPollingID = setInterval(download.pollingCheck, download._pollSpeed);
				}
			}
		});
	};

	download.pollingCheck = function(){
		console.log('polling check: ' + download.currentJobID);
		beyond.request({
			url: 'members_payout/check_job_status',
			data: {
				'job_id': download.currentJobID
			},
			with_overlay: false,
			on_success: function(data){
				console.log('polling return: ' + data.status);
				if(data.status == 1){
					$('.current-job-sheet div').html('Completed: ' + data.data.date_generated).attr('class','label label-success');
					$('.current-job-sheet').removeClass('current-job-sheet');
					clearInterval(download.currentPollingID);

					if(download._dlList.length > 0){
						download.processList();
					} else {
						download.mergeDownload();
					}
				}
			}
		});
	};

	download.mergeDownload = function(){
		beyond.request({
			url: 'members_payout/merge_download_segmented_excel',
			data: {
				'type': payout.type,
				'start_date': payout.start_date,
				'end_date': payout.end_date,
				'generate_again': download.regenerate
			},
			on_success: function(data){
				if(data.status == 1){
					window.location = base_url + '/assets/media/payout/' + data.data.filename;
					download.form.hide();
				}
			}
		});
		download.regenerate = false;
	};
	
	var payout = {};
	payout.step = 1;
	payout.type = '';
	payout.start_date = '';
	payout.end_date = '';
	payout.checkedDates = false;
	payout.psf_limit = 5000;
	payout.psf_type = '';
	payout.psf_value = 0;
	payout.payout_id = 0;
	payout.initial_member_payout = true;
	payout.initial_transaction_logs = true;

	/*
	STEP 1: select type/dates
	STEP 2: customize adjustments
	STEP 3: revise/review outputs
	STEP 4: download / credit payout
	 */
	var nextStep = function(){
		$('.prev-step').removeClass('hide');
		$('.cancel-step').removeClass('hide');
		switch (payout.step){
			case 1:
				if(!payout.checkedDates){
					checkPayoutDates();
					return false;
				}else{ // payout type and date passed
					payout.type = $("#payout-type").val();
					$("#payout-type-name").html(payout.type);
					$("#payout-start").html(payout.start_date);
					$("#payout-end").html(payout.end_date);
					$(".payout-wizard-tabs").hide();
					$("#adjustments-tab").show();
					$("#step-one").addClass("icon-check");
					payout.step++;
					if(payout.type == "UNILEVEL") {
						$(".payout-wizard-tabs").hide();
						$("#adjustments-tab").hide();
						$("#payout-psf_value").parent().addClass('muted');
						nextStep();
					} else {
						$(".payout-wizard-tabs").hide();
						$("#adjustments-tab").hide();
						$("#payout-psf_value").parent().removeClass('muted');
						nextStep();
					}
				}
				break;
			case 2:
				$('.step2-head-control').show();
				processPayout();
				payout.step++;
				break;
			case 3:
				$('.step2-head-control').hide();
				savePayoutRevisions();
				break;
			case 4:
				var confirmCreditPayout = beyond.modal.create({
					title: "Payout: Confirm Credit Payout",
					width: 400,
					html: "<p>This will credit all commissions to members for "+payout.start_date+" to "+payout.end_date+" Payout and lock Payout period for viewing.</p>",
					buttons: {
						'Continue': function(){
							//payout.step++;
							//creditPayout();
							beyond.request({
								url: '/admin/members_payout/get_admin_login',
								on_success: function(data){
									var userConfirmCreditPayout = beyond.modal.create({
										title: "Payout: Verify Username and Password",
										width: 400,
										html: data.data.html,
										buttons: {
											'Verify' : function(){
												beyond.request({
													url: '/admin/members_payout/verify_admin_login',
													data: {
														'username': $('.payout-confirm-username').val(),
														'password': $('.payout-confirm-password').val()
													},
													on_success: function(data){
														userConfirmCreditPayout.hide();
														if(data.status == 1) {
															payout.step++;
															creditPayout();
														} else {
															var userConfirmFailed = beyond.modal.create({
																title: "Payout: User Verification Failed",
																width: 400,
																html: "<div style='text-align: center;'>Invalid Login</div>"
															});
															userConfirmFailed.show();
														}
													}
												});												
											}
										}
									});
									userConfirmCreditPayout.show();
								}
							});
							confirmCreditPayout.hide();
						}
					}
				});
				confirmCreditPayout.show();
				break;
		}
	};

	var prevStep = function(){
		//$(".payout-wizard-tabs").hide();
		switch(payout.step) {
			case 1:
				break;
			case 2:
				payout.step--;
				$('.step2-head-control').hide();
				$('#review-revise-tab').hide();
				$('#date-and-type-tab').show();
				$('#payout-type-name').html('');
				$('#payout-start').html('');
				$('#payout-end').html('');
				payout.type = $('#payout-type').val();
				payout.start_date = $('#start-date').val();
				payout.end_date = $('#end-date').val();
				payout.checkedDates = false;
				payout.psf_limit = 5000;
				payout.psf_type = '';
				payout.psf_value = 0;
				payout.payout_id = 0;

				$('.step2-head').addClass('muted');
				$('#psf-limit').attr('disabled','disabled');
				$('#psf-type').attr('disabled','disabled');
				$('#psf').attr('disabled','disabled');
				$('.btn-apply-gcep').attr('disabled','disabled');
				$('.btn-reset-gcep').attr('disabled','disabled');
				$('.btn-apply-deduction').attr('disabled','disabled');
				$('.btn-reset-deduction').attr('disabled','disabled');
				$('.deduction-conflict-resolve-btn').hide();

				$('.prev-step').hide();
				$('.cancel-step').hide();

				break;
			case 3:
				$('.step2-head-control').show();
				payout.step--;
				processPayout();
				break;
			case 4:
				payout.step--;
				prevStep();
				break;
			case 5:
				payout.step--;
				prevStep();
				break;
		}
	};

	var checkPayoutDates = function(){
		var start_date = $.trim($("#start-date").val());
		var end_date = $.trim($("#end-date").val());
		beyond.request({
			url: '/admin/members_payout/check_payout',
			data: {
				'start_date': start_date,
				'end_date': end_date
			},
			on_success: function(data){
				if(data.status==0){
					beyond.modal.create({
						title: "Payout: Type and Dates",
						width: 300,
						html: "<p>"+data.msg+"</p>"
					}).show();
				}else{
					if(!data.data.new_payout){
						payout.payout_id = data.data.member_payout.payout_id;
						var payoutCompletedModal = beyond.modal.create({
							title: "Payout: Type and Dates",
							width: 400,
							html: "<p>"+data.msg+" Payout on "+start_date+" to "+end_date+" has already been completed. Do you want to view reports for this payout?</p>",
							buttonClose: false,
							buttons: {
								'View': function(){
									$(".next-step").remove();
									payout.step = 4;
									payoutCompletedModal.hide();

									payout.start_date = start_date;
									payout.end_date = end_date;
									payout.checkedDates = true;

									payout.type = data.data.member_payout.type;
									payout.psf_type = data.data.member_payout.psf_type;
									payout.psf_value = data.data.member_payout.psf_value;
									payout.psf_limit = data.data.member_payout.psf_limit;
									
									$("#payout-type-name").html(payout.type);
									$("#payout-start").html(payout.start_date);
									$("#payout-end").html(payout.end_date);

									//$("#payout-psf-limit").html($("#psf-limit").val());
									if($("#psf-type").val()=="percent"){
										$("#payout-psf_value").html($("#psf").val() + "%");
										//$("#payout-psf").html($("#psf").val() + "%");
									}else{
										$("#payout-psf_value").html($("#psf").val());
										//$("#payout-psf").html($("#psf").val());
									}
									
									$(".payout-wizard-tabs").hide();
									$("#review-revise-tab").show();
									$(".step-two").addClass("icon-check");

									$('.step2-head').removeClass('muted');
									$('#psf-limit').removeAttr('disabled');
									$('#psf-type').removeAttr('disabled');
									$('#psf').removeAttr('disabled');
									$('.btn-apply-gcep').removeAttr('disabled');
									$('.btn-reset-gcep').removeAttr('disabled');
									$('.btn-apply-deduction').removeAttr('disabled');
									$('.btn-reset-deduction').removeAttr('disabled');

									viewReports();

								}
							}
						});
						payoutCompletedModal.show();
					}else{
						payout.start_date = start_date;
						payout.end_date = end_date;
						payout.checkedDates = true;
						nextStep();
					}
				}
			}
		});
	};

	var processPayout = function(){
		if(payout.type == "UNILEVEL") {
			payout.psf_limit = 5000;
			payout.psf_type = 'percent';
			payout.psf_value = 0;
		} else {
			payout.psf_limit = $("#psf-limit").val();
			payout.psf_type = $("#psf-type").val();
			payout.psf_value = parseFloat($("#psf").val());
		}
		beyond.request({
			url: '/admin/members_payout/process_commissions',
			data: {
				'type': payout.type,
				'start_date': payout.start_date,
				'end_date': payout.end_date,
				'psf_type': payout.psf_type,
				'psf_value': payout.psf_value,
				'psf_limit': payout.psf_limit
			},
			on_success: function(data){
				if(data.status==1){
					payout.payout_id = data.data.payout_id;
					getCommissionPerAccount();
					getCommissionPerNewAccount();
					getCommissionPerMember();
					getGcPerAccount();
					getMemberPayout();
					getTransactionLogs();

					//$("#payout-psf-limit").html(payout.psf_limit);
					if(payout.psf_type=="percent"){
						$("#payout-psf_value").html(payout.psf_value + "%");
						//$("#payout-psf").html(payout.psf_value + "%");
					}else{
						$("#payout-psf_value").html(payout.psf_value);
						//$("#payout-psf").html(payout.psf_value);
					}
					
					$(".payout-wizard-tabs").hide();
					$("#review-revise-tab").show();
					$(".step-two").addClass("icon-check");

					$('.step2-head').removeClass('muted');
					$('#psf-limit').removeAttr('disabled');
					$('#psf-type').removeAttr('disabled');
					$('#psf').removeAttr('disabled');
					$('.btn-apply-gcep').removeAttr('disabled');
					$('.btn-reset-gcep').removeAttr('disabled');
					$('.btn-apply-deduction').removeAttr('disabled');
					$('.btn-reset-deduction').removeAttr('disabled');
					
				}else{
					beyond.modal.create({
						title: "Payout Process: Error",
						width: 300,
						html: "<p>"+data.msg+"</p>"
					}).show();
				}
			}
		});
	};

	var creditPayout = function(){
		beyond.request({
			url: '/admin/members_payout/credit_payout',
			data: {
				'type': payout.type,
				'start_date': payout.start_date,
				'end_date': payout.end_date
			},
			on_success: function(data){
				if(data.status==1){
					$(".payout-wizard-tabs").hide();
					$("#review-revise-tab").show();
					$("#credit-tab").show();
					$("#step-four").addClass("icon-check");
					$("#btn-edit-balance").remove();
					$("#btn-save-balance").remove();
					$(".next-step").remove();
					beyond.modal.create({
						title: "Payout Credit: Success",
						width: 300,
						html: "<p>Crediting was Successful</p>"
					}).show();
				}else{
					beyond.modal.create({
						title: "Payout Credit: Error",
						width: 300,
						html: "<p>"+data.msg+"</p>"
					}).show();
				}
			}
		});
	};
	
	var getCommissionPerAccount = function(){
		beyond.request({
			url: '/admin/members_payout/get_commission_per_account',
			data:{'type':payout.type,'start_date':payout.start_date,'end_date':payout.end_date},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") {
						$("#payout-details").html(data.data.html);
						$("#payout-details").parent().tablesorter({ sortList: [[0,0]] });

						$(".commission-per-account-edit-account-status-view").each(function(){
							var account_id = $(this).attr('data-account_id');
							$(this).click(function(){
								$(".commission-per-account-edit-account-status[data-account_id='"+account_id+"']").show();
								$(".commission-per-account-edit-account-status-cancel[data-account_id='"+account_id+"']").show();
								$(this).hide();
							});
						});

						$(".commission-per-account-edit-account-status").each(function(){
							var account_id = $(this).attr('data-account_id');
							$(this).change(function(){
								var editAccountStatusModal = beyond.modal.create({
									title: "Confirm: Edit Account Status",
									width: 400,
									html: "<p>Are you sure you want to edit the account status?</p>",
									disableClose: true,
									buttons: {
										'No': function(){
											editAccountStatusModal.hide();
										},
										'Yes': function(){
											editAccountStatusModal.hide();

											var newAccountStatus = $('.commission-per-account-edit-account-status[data-account_id="'+account_id+'"]').val();
											beyond.request({
												url: '/admin/members_payout/commission_per_account_edit_account_status',
												data: {
													'type':payout.type,
													'start_date':payout.start_date,
													'end_date':payout.end_date,
													'account_id':account_id,
													'new_status':newAccountStatus
												},
												on_success: function(data){
													if(data.status==1){
														$(".commission-per-account-edit-account-status-view[data-account_id='"+account_id+"']").attr('data-old_value',$(".commission-per-account-edit-account-status-view[data-account_id='"+account_id+"']").html());
														$(".commission-per-account-edit-account-status-view[data-account_id='"+account_id+"']").html(data.data.new_status_name).show();
														$(".commission-per-account-edit-account-status[data-account_id='"+account_id+"']").hide();
														$(".commission-per-account-edit-account-status-cancel[data-account_id='"+account_id+"']").hide();
													}
												}
											});
										}
									}
								});
								editAccountStatusModal.show();
							});
						});

						$(".commission-per-account-edit-account-status-cancel").each(function(){
							var member_id = $(this).attr('data-member_id');
							$(this).click(function(){
								var old_value = $(".commission-per-account-edit-account-status[data-member_id='"+member_id+"']").attr('data-old_value');
								$(".commission-per-account-edit-account-status-view[data-member_id='"+member_id+"']").html(old_value).show();
								$(".commission-per-account-edit-account-status[data-member_id='"+member_id+"']").hide();
								$(".commission-per-account-edit-account-status-cancel[data-member_id='"+member_id+"']").hide();
							});
						});
					}
				}
			}
		});
	};

	var getCommissionPerNewAccount = function(){
		beyond.request({
			url: '/admin/members_payout/get_commission_per_account',
			data:{'type':payout.type,'start_date':payout.start_date,'end_date':payout.end_date,'filter':"new_accounts"},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#payout-new-accounts-details").html(data.data.html);
					$("#payout-new-accounts-details").parent().tablesorter({ sortList: [[0,0]] });
				}
			}
		});
	};

	var getCommissionPerMember = function(){
		beyond.request({
			url: '/admin/members_payout/get_commission_per_member',
			data:{'type':payout.type,'start_date':payout.start_date,'end_date':payout.end_date},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#commissions-per-members-details").html(data.data.html);
					$("#commissions-per-members-details").parent().tablesorter({ sortList: [[0,0]] });
				}
			}
		});
	};
	
	var getGcPerAccount = function(){
		beyond.request({
			url: '/admin/members_payout/get_gc_per_account',
			data:{'start_date':payout.start_date,'end_date':payout.end_date},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#gc_per_account_details").html(data.data.html);
					$("#gc_per_account_details").parent().tablesorter({ sortList: [[0,0]] });
				}
			}
		});
	};
	
	var getMemberPayout = function(){
	    var payoutGenerationModal = beyond.modal.create({
			title: "Generating Payout",
			width: 400,
			html: "<p><h4 style='text-align: center;'><i class='icon-download-alt'></i> Loading Member Payout</h4></p>",
			disableClose: true
		});
		payoutGenerationModal.show();
		beyond.request({
			url: '/admin/members_payout/get_member_payout',
			data:{
				'type':payout.type,
				'start_date':payout.start_date,
				'end_date':payout.end_date,
				'psf_limit': payout.psf_limit,
				'psf_type': payout.psf_type,
				'psf_value': payout.psf_value,
				'payout_id': payout.payout_id
			},
			on_success: function(data){
				if(data.status==1){
					payoutGenerationModal.hide();
					$("#member_payout_details").html('');
					if(data.data.html!="") {
						$("#member_payout_details").html(data.data.html);
						if(payout.initial_member_payout){
							$("#member_payout_details").parent().tablesorter({ sortList: [[0,0]], headers: { 14: {sorter: false} } });
							payout.initial_member_payout = false;
						} else {
							$("#member_payout_details").parent().trigger("update");
						}
					}

					getDeductionConflicts();

					$('.payout_tax_input').each(function(){
						var member_id = $(this).attr('data-member_id');
						$(this).keyup(function(){
							if($('.payout_cash_card_input[data-member_id="'+member_id+'"] option').length > 1) {
								$('.payout_cash_card_input[data-member_id="'+member_id+'"] option').each(function(){
									$(this).removeAttr('selected');
								});
								if($(this).val() > 0){
									var opt = $('.payout_cash_card_input[data-member_id="'+member_id+'"] option')[1];
									$(opt).attr('selected','selected');
								} else {
									var opt = $('.payout_cash_card_input[data-member_id="'+member_id+'"] option')[0];
									$(opt).attr('selected','selected');
								}
							} else {
								$(this).val(0);
							}
						});
					});

					$('.payout_cash_card_input').each(function(){
						var member_id = $(this).attr('data-member_id');
						$(this).change(function(){
							if($(this).val() == "TO FUNDS") {
								$('.payout_tax_input[data-member_id="'+member_id+'"]').attr('data-old_value',$('.payout_tax_input[data-member_id="'+member_id+'"]').val());
								$('.payout_tax_input[data-member_id="'+member_id+'"]').val(0);
							} else {
								$('.payout_tax_input[data-member_id="'+member_id+'"]').val($('.payout_tax_input[data-member_id="'+member_id+'"]').attr('data-old_value'));
							}
						});
					});

					$('.btn-save-cancel').click(function(){
						var member_id = $(this).attr('data-member_id');

						$('.payout_tax_input[data-member_id="'+member_id+'"]').val($('.payout_tax_input[data-member_id="'+member_id+'"]').attr('data-old_value'));
						$('.payout_cash_card_input[data-member_id="'+member_id+'"] option').each(function(){
							$(this).removeAttr('selected');
						});
						$('.payout_cash_card_input[data-member_id="'+member_id+'"] option[value="'+$('.payout_cash_card_input[data-member_id="'+member_id+'"]').attr('data-old_value')+'"]').attr('selected','selected');

						$('.payout-control-input[data-member_id="'+member_id+'"]').each(function(){
							$(this).addClass('hide');
						});
						$('.payout-control-view[data-member_id="'+member_id+'"]').each(function(){
							$(this).removeClass('hide');
						});
						$('.btn-edit-reset[data-member_id="'+member_id+'"]').removeClass('hide');
						$('.btn-save-cancel[data-member_id="'+member_id+'"]').addClass('hide');
					});

					$('.member-payout-edit-row').click(function(){
						var member_id = $(this).attr('data-member_id');
						$('.payout-control-input[data-member_id="'+member_id+'"]').each(function(){
							$(this).removeClass('hide');
						});
						$('.payout-control-view[data-member_id="'+member_id+'"]').each(function(){
							$(this).addClass('hide');
						});
						$('.btn-save-cancel[data-member_id="'+member_id+'"]').removeClass('hide');
						$('.btn-edit-reset[data-member_id="'+member_id+'"]').addClass('hide');
					});

					$('.member-payout-reset-row').click(function(){
						var member_id = $(this).attr('data-member_id');
						var rowReset = beyond.modal.create({
							title: "Payout: Reset Confirmation",
							width: 400,
							disableClose: true,
							html: '<div>Are you sure you want to reset this?</div>',
							buttons : {
								'No': function(){
									rowReset.hide();
								},
								'Yes': function(){
									payoutRowResetting(member_id);
									rowReset.hide();
								}
							}
						});

						if($(this).attr('confirm') == 1) {
							rowReset.show();
						} else {
							payoutRowResetting(member_id);
						}
						$(this).attr('confirm', 1);
					});

					$('.member-payout-save-row').click(function(){
						var member_id = $(this).attr('data-member_id');

						var rowEditing = beyond.modal.create({
							title: "Payout: Save Confirmation",
							width: 400,
							disableClose: true,
							html: '<div>Are you sure you want to save this?</div>',
							buttons : {
								'No': function(){
									rowEditing.hide();
								},
								'Yes': function(){
									payoutRowSaving(member_id, member_id);
									rowEditing.hide();
								}
							}
						});

						if($(this).attr('confirm') == 1) {
							rowEditing.show();
						} else {
							payoutRowSaving(member, member_id);
						}
						$(this).attr('confirm', 1);
					});

					if(payout.type == "IGPSM") {
						var updateLastEncashmentTimestamp = beyond.modal.create({
							title: "Update Last Encashment Timestamp",
							width: 400,
							html: "<p>Do you want to update the last encashment timestamp?</p>",
							disableClose: true,
							buttons: {
								'No': function(){
									updateLastEncashmentTimestamp.hide();
								},
								'Yes': function(){
									beyond.request({
										url: '/admin/members_payout/update_last_encashment_timestamp',
										data:{
											'end_date':payout.end_date
										},
										on_success: function(data){
											updateLastEncashmentTimestamp.hide();
											if(data.status==1){
												var updateLastEncashmentTimestampReturn = beyond.modal.create({
													title: "Update Last Encashment Timestamp",
													width: 400,
													html: "<p>Timestamp was updated to " + data.data.value + "</p>"
												});
												updateLastEncashmentTimestampReturn.show();
											}
										}
									});
								}								
							}
						});
						updateLastEncashmentTimestamp.show();
					}					

					$('.btn-apply-gcep').click(function(){
						var applyGCEP = beyond.modal.create({
							title: "Payout: Apply GCEP",
							width: 400,
							disableClose: true,
							html: '<div>Are you sure you want to apply this GCEP settings?</div>',
							buttons : {
								'No': function(){
									applyGCEP.hide();
								},
								'Yes': function(){
									applyGCEP.hide();

									var save_queue = [];

									var gcep_limit = $('#psf-limit').val();
									var gcep_type = $('#psf-type').val();
									var gcep_value = $('#psf').val();

									$('.payout_gross_value').each(function(){							
										var member_id = $(this).attr('data-member_id');
										var member_id = $('.member-payout-save-row[data-member_id="'+member_id+'"]').attr('data-member_id');
										save_queue.push([member_id,member_id]);
									});

									processApplyGCEP(gcep_limit, gcep_type, gcep_value, save_queue);
								}
							}
						});
						applyGCEP.show();
					});

					$('.btn-reset-gcep').click(function(){
						var resetGCEP = beyond.modal.create({
							title: "Payout: Reset GCEP",
							width: 400,
							disableClose: true,
							html: '<div>Are you sure you want to reset GCEP settings?</div>',
							buttons : {
								'No': function(){
									resetGCEP.hide();
								},
								'Yes': function(){
									resetGCEP.hide();
									resetGCEP.hide();
									var reset_queue = [];

									$('.payout_gross_value').each(function(){							
										var member_id = $(this).attr('data-member_id');
										var member_id = $('.member-payout-save-row[data-member_id="'+member_id+'"]').attr('data-member_id');
										reset_queue.push([member_id,member_id]);
									});

									processResetGCEP(reset_queue);
								}
							}
						});
						resetGCEP.show();
					});

					$('.btn-apply-deduction').click(function(){
						var apply_deduction_modal = beyond.modal.create({
							title: "Payout: Apply Member Deductions",
							width: 400,
							disableClose: true,
							html: '<div>Are you sure you want to apply member deductions?</div>',
							buttons : {
								'No': function(){
									apply_deduction_modal.hide();
								},
								'Yes': function(){
									apply_deduction_modal.hide();

									beyond.request({
										url: '/admin/members_payout/get_deductions',
										data:{
											'start_date':payout.start_date,
											'end_date':payout.end_date,
											'payout_id': payout.payout_id
										},
										on_success: function(data){
											if(data.status==1){
												var review_deductions_modal = beyond.modal.create({
													title: "Payout: Deductions Summary",
													width: 600,
													disableClose: true,
													html: data.data,
													buttons : {
														'Cancel': function(){
															review_deductions_modal.hide();
														},
														'Apply Deductions': function(){

															var deductions = [];
															$('.deduction-deductions,.deduction-conflicts').each(function(){
																deductions.push($(this).attr('data-deduction_id'));
															});

															var conflicts = [];
															$('.deduction-conflicts').each(function(){
																conflicts.push($(this).attr('data-deduction_id'));
															});

															review_deductions_modal.hide();

															beyond.request({
																url: '/admin/members_payout/apply_deductions',
																data:{
																	'start_date':payout.start_date,
																	'end_date':payout.end_date,
																	'deductions':deductions,
																	'conflicts':conflicts,
																	'payout_id': payout.payout_id
																},
																on_success: function(data){
																	if(data.status==1){
																		var deduction_success_modal = beyond.modal.create({
																			title: "Payout: Deductions Success",
																			width: 600,
																			html: "Member Deductions was Successful",
																			disableClose: true,
																			buttons: {
																				'Ok': function(){
																					deduction_success_modal.hide();
																					getDeductionConflicts();
																					getMemberPayout();
																				}
																			}
																		});
																		deduction_success_modal.show();
																	}
																}
															});
														},
														'Download': function(){
															$('#deduction_inset_form').html('<form action="/admin/members_payout/download_deductions" method="post" style="display:none;" id="deduction_download_form"> \
																<input type="text" id="start_date" name="start_date" value="' + payout.start_date + '" /> \
																<input type="text" id="end_date" name="end_date" value="' + payout.end_date + '" /> \
																<input type="text" id="payout_id" name="payout_id" value="' + payout.payout_id + '" /> \
																</form>');
													    	$('#deduction_download_form').submit();
														}
													}
												});
												review_deductions_modal.show();
											}
										}
									});
								}
							}
						});
						apply_deduction_modal.show();
					});
				}
			}
		});
	};

	var getDeductionConflicts = function(){
		beyond.request({
			url: '/admin/members_payout/get_deduction_conflicts',
			data:{
				'start_date':payout.start_date,
				'end_date':payout.end_date,
				'payout_id': payout.payout_id
			},
			on_success: function(data){
				if(data.status==1 && data.data.deduction_conflict_count > 0){
					$('.deduction-conflict-resolve-btn').show();
					$('.deduction-conflict-count').html(data.data.deduction_conflict_count);

					var conflict_list = data.data.deduction_conflict_list;
					for(conflict in conflict_list){
						var member_id = $('.member-payout-save-row[data-member_id="'+conflict_list[conflict]+'"]').attr('data-member_id');
						$('.payout_payout_amount_value[data-member_id="'+member_id+'"]').css({'color':'red'});
					}
				}
			}
		});
	};

	var getEditedMemberPayout = function(){
		beyond.request({
			url: '/admin/members_payout/get_edited_member_payout',
			data:{
				'start_date':payout.start_date,
				'end_date':payout.end_date,
				'payout_id': payout.payout_id
			},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#member_payout_details").html(data.data.html);
				}
			}
		});
	};
	
	var getTransactionLogs = function(member_id){
		if(typeof(member_id)=="undefined") member_id = 0;
		beyond.request({
			url: '/admin/members_payout/get_transaction_logs',
			data:{'type':payout.type,'start_date':payout.start_date,'end_date':payout.end_date,'member_id':member_id},
			on_success: function(data){
				if(data.status==1){
					if(data.data.html!="") $("#transaction_logs_details").html(data.data.html);
					$("#tl-member-id").html(data.data.html_member_option);
					if(payout.initial_transaction_logs) {
						payout.initial_transaction_logs = false;
						$("#transaction_logs_details").parent().tablesorter({ sortList: [[0,0]] });
					} else {
						$("#transaction_logs_details").parent().trigger("update");
					}
				}
			}
		});
	};

	var savePayoutRevisions = function(){

		balance_data = {};
		$.each($("#payout-balance-form").serializeArray(), function(i, field){
			if(field.value > 0){
				balance_data[field.name] = field.value;
			}
		})

		beyond.request({
			url: '/admin/members_payout/save_balance',
			data: balance_data,
			on_success: function(data){
				if(data.status==1){
					getEditedMemberPayout();
					$("#step-three").addClass("icon-check");
					payout.step++;
				}
				$(".payout_balance_value").show();
				$(".payout_balance_input").hide();
			}
		});

	};

	var viewReports = function(){
		$("#step-one").addClass("icon-check");
		$(".step-two").addClass("icon-check");
		$("#step-three").addClass("icon-check");
		$("#step-four").addClass("icon-check");
		getCommissionPerAccount();
		getCommissionPerNewAccount();
		getGcPerAccount();
		getEditedMemberPayout();
		getTransactionLogs();
	};

	var payoutUpdating = beyond.modal.create({
		title: "Payout: Updating Payout",
		width: 400,
		disableClose: true,
		html: '<div>Updating...</div><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>'
	});
	var payoutRowResetting = function(member_id){
		var gross_amount = $('.payout_gross_value[data-member_id="'+member_id+'"]').attr('data-gross_amount')*1;
		var psf_input = (gross_amount >= payout.psf_limit)?((gross_amount/100)*payout.psf_value):0;
		$('.payout_psf_input[data-member_id="'+member_id+'"]').val(psf_input);
		var net_gross = gross_amount - psf_input;
		var tax_input = net_gross*0.1;
		$('.payout_tax_input[data-member_id="'+member_id+'"]').val(tax_input);
		var balance_input = 0;
		$('.payout_balance_input[data-member_id="'+member_id+'"]').val(balance_input);
		if($('.payout_cash_card_input[data-member_id="'+member_id+'"] option').length > 1){
			var opt = $('.payout_cash_card_input[data-member_id="'+member_id+'"] option')[1];
			$(opt).attr('selected','selected');
		}
		$('.member-payout-save-row[data-member_id="'+member_id+'"]').attr('confirm',0);
		$('.member-payout-save-row[data-member_id="'+member_id+'"]').click();
	}

	var payoutRowSaving = function(member_id, member_id){
		$('.member-payout-save-row[data-member_id="'+member_id+'"]').attr('disabled','disabled');

		beyond.request({
			url: '/admin/members_payout/set_member_commissions_report',
			data:{
				'psf':$('.payout_psf_input[data-member_id="'+member_id+'"]').val(),
				'tax':$('.payout_tax_input[data-member_id="'+member_id+'"]').val(),
				'balance':$('.payout_balance_input[data-member_id="'+member_id+'"]').val(),
				'cash_card':$('.payout_cash_card_input[data-member_id="'+member_id+'"]').val(),
				'member_id':member_id,
				'start_date':payout.start_date,
				'end_date':payout.end_date,
				'payout_id': payout.payout_id
			},
			on_success: function(data){
				if(data.status==1) {
					for(field_type in data.data) {
						if($('.payout_'+field_type+'_input[data-member_id="'+member_id+'"]').length > 0){
							$('.payout_'+field_type+'_input[data-member_id="'+member_id+'"]').val(data.data[field_type]);
						}
						var value = $('.payout_'+field_type+'_value[data-member_id="'+member_id+'"]');
						value.attr('data-old_value',data.data[field_type]);
						value.html(data.data[field_type]);
						var orig_color = value.parent().css('background-color');
						value.parent().css({
							'background-color': '#5fbe60'
						}).animate({
			                backgroundColor: orig_color
			            }, 1000 );
					}
				} else {
					var errorPayoutCommissionEdit = beyond.modal.create({
						title: "Payout: Commission Edit",
						width: 400,
						html: "<p>"+data.data+"</p>"
					});
					errorCommissionEdit.show();
				}

				$('.member-payout-save-row[data-member_id="'+member_id+'"]').removeAttr('disabled').html("<i class='icon-ok icon-white'>");

				$('.payout-control-input[data-member_id="'+member_id+'"]').each(function(){
					$(this).addClass('hide');
				});
				$('.payout-control-view[data-member_id="'+member_id+'"]').each(function(){
					$(this).removeClass('hide');
				});
				$('.btn-edit-reset[data-member_id="'+member_id+'"]').removeClass('hide');
				$('.btn-save-cancel[data-member_id="'+member_id+'"]').addClass('hide');

				$('.payout_psf_input[data-member_id="'+member_id+'"]').trigger('saving-row-finished');
				$('.payout_psf_input[data-member_id="'+member_id+'"]').trigger('resetting-row-finished');
			}
		});
	}

	var processApplyGCEP = function(gcep_limit, gcep_type, gcep_value, save_queue){
		beyond.request({
			url: '/admin/members_payout/apply_gcep',
			data:{
				'gcep_limit':gcep_limit,
				'gcep_type':gcep_type,
				'gcep_value':gcep_value,
				'save_queue':save_queue,
				'start_date':payout.start_date,
				'end_date':payout.end_date,
				'payout_id': payout.payout_id
			},
			on_success: function(data){
				if(data.status == 1){
					getMemberPayout();
				}
			}
		});
	}

	var processResetGCEP = function(reset_queue){
		beyond.request({
			url: '/admin/members_payout/reset_gcep',
			data:{
				'reset_queue':reset_queue,
				'start_date':payout.start_date,
				'end_date':payout.end_date,
				'payout_id': payout.payout_id
			},
			on_success: function(data){
				if(data.status == 1){
					getMemberPayout();
				}
			}
		});
	}

</script>
