<?php
	$hash = hash_hmac('md5',$this->member->session_id,$this->config->item('encryption_key'));
	$contact_error_url ='/pages/contact-us';
		
	$withdrawal_tax_details = $this->settings_model->get_setting_by_slug("withdrawal_tax");
	
	$withdrawal_tax = ($withdrawal_tax_details->value * 100) . "%";
	
	$withdrawal_limit_details = $this->settings_model->get_setting_by_slug("withdrawal_limit");	
	$withdrawal_limit = json_decode($withdrawal_limit_details->value);
	
	$paycard_number = $this->member->metrobank_paycard_number;
?>

<div class="page-header clearfix">
	<h2>My Withdrawals<button class="btn-small btn-primary" id="btn-withdraw" style="float:right;margin-right:10px;margin-top:8px;">Request a Withdraw</button></h2>
</div>
<form id='frm_filter' class='form-horizontal' method='post' action ='/members/withdraws/page'>
	<fieldset>
		<div class='clearfix'>
			<div class='span6'>			
				<div class="control-group">
					<label class="pull-left" for="use_date_range">From Date:</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" class="input-medium" id="from_date" name='from_date' readonly='readonly' style='cursor:pointer;' />
							<span id='from_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>					
				<div class="control-group">
					<label class="pull-left" for="use_date_range">To Date:</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" class="input-medium" id="to_date" name='to_date' readonly='readonly' style='cursor:pointer;' />
							<span id='to_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="pull-left" for="use_date_range">Payout:</label>
					<div class="controls">
                        <select id="preferred_payout" name="preferred_payout">
                            <option class="payout_options" value="all">ALL</option>
							<option class="payout_options" value="paycard">Paycard</option>									
                        </select>
                    </div>
				</div>
				
				<div class="control-group">
					<label class="pull-left" for="use_date_range">Status:</label>
					<div class="controls">
                        <select id="status_type" name="status_type">
                            <option class="status_options" value="all">ALL</option>
							<option class="status_options" value="pending">Pending</option>
							<option class="status_options" value="completed">Completed</option>			
                        </select>
                    </div>
				</div>
				
			</div>
		</div>
		<div class="clearfix">
			<div class="span12">
				<button class='btn btn-primary' style='margin-right: 10px;'>&nbsp;&nbsp;Go&nbsp;&nbsp;</button>
				<button id='btn_today' class='btn btn-info'>Today</button>				
			</div>
		</div>
	</fieldset>
	
	<br/>
	<div id="search-result-display">
		<span class="label label-info">Results for:</span>
		<span class="label label-success">Preferred Payout: <?= $preferred_payout; ?></span>
		<span class="label label-success">Status: <?= $status_type; ?> </span>
		<span class="label label-success">Timestamps: <?= $between_timestamps; ?> </span>
	</div>	
	
</form>
<div class="ui-element">
	<div>
		<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
			<thead>
				<tr>
					<th>Transaction ID</th>
					<th>Requested Amount</th>	
					<th>Amount After Tax</th>
					<th>Preferred Payout</th>
					<th>Status</th>
					<th>Date Requested</th>							
				</tr>
			</thead>
			<tbody id="list_tbody_html">
				<?php if(empty($transactions)): ?>
					<tr><td colspan="10" style="text-align:center;"><strong>No Result</strong></td></tr>
				<?php else: ?>
					<?php foreach($transactions as $t): ?>

					<tr> 					
						<td><?= $t->transaction_id ?></td>
						<td><?= $t->amount ?></td>
						<td><?= $t->amount_after_tax ?></td>
						<td><?= $t->preferred_payout ?></td>
						<td><?= $t->status ?></td>						
						<td><?= $t->insert_timestamp ?></td> 						
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<div>
	<?= $this->pager->create_links($get_data); ?>
</div>

<script type="text/javascript">
  //<![CDATA[

	var withdrawal_tax = "<?=$withdrawal_tax?>";
	
	var member_id = "<?=$member_id?>";

	var current_page = 1;
	
	var showTransactions = function(page, filter_by) {
		
	   if (filter_by==null) filter_by = 'BOTH';
        
		b.request({
	        url: '/members/withdraws/page',
	        data: {
				"page":page,
				"member_id" : member_id,
				"filter_by":filter_by
			},
		
		
			on_success: function(data, status) {
				if (data.total_records == 0) {
                    $("#list_tbody_html").html('<tr><td colspan="10" style="text-align:center;"><strong> - No Result - </strong></td></tr>');
                } else {
                    $("#list_tbody_html").html(data.html);
                }
				current_page = page;
		    }
		});		
	 }
	
  	$(document).ready(function() {
		showTransactions(1);               
	});			
	

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
			
	});
	
	$('#btn-withdraw').live("click",function() {
						
		b.request({
			url : '/members/withdraws/pending_withdraw',
			data : {'hash':'<?=$hash?>'},
			on_success : function(data) {
				
				if (data.status == "1")	{
					withdrawFunds();										
				} else {
					var errorWithdrawModal = b.modal.new({
						title: 'Withdraw Funds',
						width:400,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								errorWithdrawModal.hide();
							}
						}
					});
					errorWithdrawModal.show();
				}
			}
		})	

	});
	
	var withdrawFunds = function() {
			
						
			var page_html = "";					
			page_html = "<label>Enter the amount you want to withdraw:</label><input title='Withdraw Amount' class='large' type='withdraw_amount' id='withdraw_amount' name='withdraw_amount' value=''/><input id='payout_option' type='hidden' value='PAYCARD' />";
			page_html = page_html + "<br/><strong><span style='color:red;font-size:11px;' id='withdraw_error'></span></strong><br/><strong>Note:</strong><span> Amount that you wish to withdraw is subjected to <strong>" + withdrawal_tax + "</strong> tax. You are allowed to withdraw amount of <?=number_format($withdrawal_limit->minimum,2)?> to <?=number_format($withdrawal_limit->maximum,2)?>.</span>";																	
		
			var requestWithdrawModal = b.modal.new({
				title: 'Withdraw Funds Request',
				width:400,
				disableClose: true,
				html: page_html,
				buttons: {
					'Proceed' : function() {
						//requestWithdrawModal.hide();
						var withdraw_amount = $.trim($('#withdraw_amount').val());
						var payout_option = $.trim($('#payout_option').val());
						
						if($.trim(withdraw_amount)==''){
						
							$('#withdraw_error').html('Please enter an amount.');
							$('#withdraw_error').show();
							return;							
						}
						
						//$('#withdraw_error').html("");
						//$('#withdraw_error').hide();	
						
						// ajax request here 
						b.request({
							url  :  '/members/withdraws/preprocess_withdraw',
							data : {'process':'check','withdraw_amount' : withdraw_amount, 'hash':'<?=$hash?>', 'payout_option':payout_option},
							on_success : function(data) {								
								if (data.status == "1")	{
									requestWithdrawModal.hide();

									
									var confirmWithdrawModal = b.modal.new({
										title: 'Withdraw Funds Request :: Confirm',
										width:500,
										disableClose: true,
										html: "<div>You are about to withdraw an amount of <strong>"  + data.data.withdraw_amount + "</strong>. Tax amount of "+ data.data.actual_withdrawable_amount_tax_text+" will be deducted.</div><br/><div>By clicking yes, you have verified and agree that your Paycard Number is <b><?=$paycard_number?></b></div><br/><div>If this is not your Paycard Number, <a href='<?=$contact_error_url?>' ><b>click here</b></a>.</div><br/><div>Are you sure you like to continue with your request?</div>",
										buttons: {
											'Yes' : function() {
												//confirmWithdrawModal.hide();
												
												b.request({
													url : '/members/withdraws/preprocess_withdraw',
													data : {'process':'queue','withdraw_amount' : withdraw_amount, 'hash':'<?=$hash?>','payout_option':payout_option},
													on_success : function(data) {
														
														if (data.status == "1")	{
															var successWithdrawModal = b.modal.new({
																title: 'Withdraw Funds :: Successful',
																width:500,
																disableClose: true,
																html: data.data.html,
																buttons: {
																	'Ok' : function() {
																		successWithdrawModal.hide();
																		
																		redirect("members/withdraws");																		
																	}
																}
															});
															successWithdrawModal.show();
			
														} else {
															var errorWithdrawModal = b.modal.new({
																title: 'Withdraw Funds : Error',
																width:400,
																disableClose: true,
																html: data.data.html,
																buttons: {
																	'Ok' : function() {
																		errorWithdrawModal.hide();
																	}
																}
															});
															errorWithdrawModal.show();
														}
													}
												})	
												
												
												
											},
											'Cancel' : function() {
												confirmWithdrawModal.hide();
											}
										}
									});
									confirmWithdrawModal.show();
									
									
								} else {								
									requestWithdrawModal.hide();									
									
									var errorWithdrawModal = b.modal.new({
										title: 'Withdraw Funds :: Error',
										width:400,
										disableClose: true,
										html: data.data.html,
										buttons: {
											'Ok' : function() {
												errorWithdrawModal.hide();
											}
										}
									});
									errorWithdrawModal.show();
								}
							}
						})	
						
						
					},
					'Cancel' : function() {
						requestWithdrawModal.hide();
					}
				}
			});
			requestWithdrawModal.show();
			//$('#withdraw_amount').numeric({negative:false, decimal:false});	
			$("#withdraw_amount").keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
			
	}	
	
	
//]]>
</script>