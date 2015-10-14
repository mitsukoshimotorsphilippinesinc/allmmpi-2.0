<?php

	$hash = hash_hmac('md5',$this->member->session_id,$this->config->item('encryption_key'));

?>	
<div class="page-header clearfix">
	<h2 class=''>Transfer Commissions<small id="header_account_id"></small><button class="btn-small btn-primary" id="btn-transfer-history" style="float:right;margin-right:10px;margin-top:8px;">View Transfer History</button></h2>		
</div>
	<?php
		if ((($this->member->email == NULL) || empty($this->member->email) || (trim($this->member->email) == '')) && ($this->member->is_email_verified = 0)) {
	
			echo "<div class='alert alert-danger alert-msg-item'><b>Note:</b> You need to have an Email in order to use this feature. Please go to the Profile tab to add an email.</div>";
		
		}
	 ?>

	<div class='clearfix'>
	
	<div class="span6" style='width: 400px;'>
		
		<div class="control-group">					
			<label class="control-label">To Account ID: <em>*</em></label>
				
			<span id="to_account_id_name" style="float:left;display:none;margin-top:-5px;" class="label label-success"></span>			
			<br/>
			<div class='clearfix'></div>
			
			<input class='input-large' type='text' style='float:left;' id='to_account_id' placeholder="65********" name='to_account_id'maxlength='10'>							              				
			<div style='float:left;margin:5px;cursor:pointer;' title='Check Recipient'><button class='btn btn-success' id='check-to-account-id' style='margin-top:-5px;' type='button'>
			<span>Check</span></button>	
			<br/>			
			<span id="to_account_id_error" style="margin-top:10px;margin-left:-230px;float:left;display:none;" class="label label-important" ></span> 			
											
		</div>
		<div class='clearfix'></div>	           				
		</div>     		
		
		<div class="control-group">	
			<label class="control-label" for="amount">Amount: <em>*</em></label>
			<input type="text" class='input-large'  placeholder="<?= $min_max_placeholder; ?>" name="amount" id="amount" value="">
			<br/>
			<span id="amount_error" style="display:none;" class="label label-important" ></span> 
		</div>

		<div class="control-group">	
			<label class="control-label" for="position" >Via: <em>*</em></label>
			<select id="transfer_type">
				<option value="FUNDS">FUNDS</option>
				<option value="GIFT CHEQUES">GIFT CHEQUES</option>
				<option value="GCEP">GCEP</option>
			</select>		
		</div>
		
		<hr/>
		
		<?php
			if ($this->member->is_on_hold == 0) {
		?>
		<div align="left">    
			<button type='button' id='btn-submit' class='btn btn-medium btn-primary'><span>Transfer</span></button>
		</div>			 
		
		<?php 
			} else {
		?>
		<div class='span5' align='left' style='margin-left:0px;'>    
			<div class="alert alert-info alert-msg-item">
			<p>Your commissions are currently <strong>ON HOLD</strong>. For more information, kindly contact the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com. Thank you.</p>
			</div>
		</div>			 
		
		<?php
			}
		?>
		
	</div>
	
	<div class="span6" style='width: 500px;'>
				
		<h3>Latest Pending Transactions</h3>
		<span id="amount_error" style="margin-bottom:10px;" class="label label-important" >You have <?= $total_pending_count; ?> PENDING transactions.</span> 
		
		<div>
			<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
				<thead>
					<tr>
						<th>ID</th>
						<th>From Member</th>
						<th>To Member</th>					
						<th>Amount</th>
						<th>Via</th>
						<th>Date Initiated</th>	
						<th>Action</th>	
					</tr>
				</thead>
				<tbody id="order_tbody_html" style="font-size:9px">
					<?php if(empty($pending_transactions)): ?>
						<tr><td colspan="10" style="text-align:center;"><strong>No Result</strong></td></tr>
					<?php else: ?>
						<?php foreach($pending_transactions as $t): ?>

						<tr> 
							<?php
								// get member details
								$from_member_details = $this->members_model->get_member_by_id($t->from_member_id);
								
								$proper_from_member_name = $from_member_details->last_name . ", ". $from_member_details->first_name . " " . $from_member_details->middle_name;
								
								$proper_member_transfer_id = str_pad($t->member_transfer_id, 8, "0", STR_PAD_LEFT);
								
							?>
							<td><?= $proper_member_transfer_id ?></td>	
							<td><?= $proper_from_member_name ?></td>	

							<?php
								// get member details
								$to_member_details = $this->members_model->get_member_by_id($t->to_member_id);
								
								$proper_to_member_name = $to_member_details->last_name . ", ". $to_member_details->first_name . " " . $to_member_details->middle_name;
							?>
							
							<td><?= $proper_to_member_name ?></td>
							
							<td><?= $t->amount ?></td>
							<td><?= $t->type ?></td>
							<td><?= $t->insert_timestamp ?></td>
							<td>
							<?php
								if (($this->member->member_id == $t->from_member_id) && ($t->status == 'PENDING') && ($this->member->is_on_hold == 0)) {
								
									echo "<button class='btn btn-small btn-success btn-enter-code' data='{$t->member_transfer_id}' style=''>Confirm</button></td>";
								
								} else {
								
									echo "<button class='btn btn-small btn-primary btn-view-details' data='{$t->member_transfer_id}' style=''>View</button></td>";
									
								}
							?>
													
						</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
			<a id="link-transfers" style="cursor:pointer;cursor:hand;"><span style="float:right;color:#008E00l"><i>[ Go To Transfer History ]</i></span></a>
		</div>		
	</div>
	</div>


<script type="text/javascript">
  //<![CDATA[

	var errorModal = b.modal.new({
		title: 'Transfer :: Processing',
		width: 450,
		disableClose: true,
		html: "We are currently processing your request. Please wait ..."
	});
	
  	$(document).ready(function() {
		$("#to_account_id").keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
		
		$("#amount").keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
				
	});			
	
	$('#btn-transfer-history').live("click",function() {
		redirect('/members/transfers');
	});
	
	$('#link-transfers').live("click",function() {
		redirect('/members/transfers');
	});
	
	$("#check-to-account-id").bind("click",function(e){
        e.preventDefault();
        checkAccountIdAvailability("false");
        return false;
    });
	
	var checkAccountIdAvailability = function(from_submit, callback) {
		var _to_account_id = $("#to_account_id");
		
		if ($.trim(_to_account_id.val()) == "") {		
			$('#to_account_id_error').html("Account ID is required.");
			$('#to_account_id_error').show();
			return false;
		};
		
		b.request({
			url: '/members/mytransfers/check_account',
			data: {
				'_to_account_id': _to_account_id.val(),
				'from_submit': from_submit
			},

			on_success: function(data, status) {

				if (data.status==1) {
					
					if (from_submit == "false") {
						$('#to_account_id_error').hide();
						var checkAccountModal = b.modal.new({
							title: 'Transfer :: Check Account ID',
							width: 600,
							disableClose: true,
							html: data.data.html,
							buttons: {																				
								'Ok' : function() {
									checkAccountModal.hide();
									$('#to_account_id_name').html("MEMBER NAME: " + data.data.proper_name);
									$('#to_account_id_name').show();
								},
								'Cancel' : function() {
									checkAccountModal.hide();
								}
							}
						});
						checkAccountModal.show();
					} else {
						$('#to_account_id_name').html("MEMBER NAME: " + data.data.proper_name);
						$('#to_account_id_name').show();
					}

				} else {
					//$('#to_account_id_error').html("Invalid Account ID.");
					$('#to_account_id_error').html(data.data.html);
					$('#to_account_id_error').show();
				
					/*var errorCheckAccountModal = b.modal.new({
						title: 'Transfer :: Error',
						width: 450,
						disableClose: true,
						html: data.data.html,
						buttons: {																				
							'Ok' : function() {
								errorCheckAccountModal.hide();
							}
						}
					});
					errorCheckAccountModal.show();*/
				}
				if(_.isFunction(callback)) callback.call(this, data.status);
			}

		});

		//return false;
	};

	
	$('#btn-submit').live("click",function() {
		var _amount = $("#amount");		
		var _to_account_id = $("#to_account_id");
		var _transfer_type = $("#transfer_type");
		
		$('#amount_error').hide();
		$('#to_account_id_error').hide();			
		
		// check account id first
		checkAccountIdAvailability("true", function(_status) {
							
			//alert(_status);
			if (_status == 0) {
			
				if (($.trim(_amount.val()) == "") || (_amount.val() == 0)) {
					$('#amount_error').html("Please enter a valid amount.");
					$('#amount_error').show();		
				}
			
				return false;
			} else {
			
				// if _amount is "" or 0, alert user
				if (($.trim(_amount.val()) == "") || (_amount.val() == 0)) {
					$('#amount_error').html("Please enter a valid amount.");
					$('#amount_error').show();		
					return false;
				}
							
				errorModal.show();

				// check first if member has enough funds
				b.request({
					url : '/members/mytransfers/check_details',
					data : {
						'_amount' : _amount.val(),
						'_to_account_id' : _to_account_id.val(),
						'_transfer_type' : _transfer_type.val(),
					},
					
					on_success : function(data) {
						if (data.status == "1")	{				
							transferFunds(_amount.val(), _to_account_id.val(), _transfer_type.val());															
						} else {
							var errorToMemberModal = b.modal.new({
								title: data.data.title,
								width: 450,
								disableClose: true,
								html: data.data.html,
								buttons: {																				
									'Ok' : function() {
										errorToMemberModal.hide();
										errorModal.hide();
									}
								}
							});
							errorToMemberModal.show();
						}
					}
				})				
				return false;				
			}			
		});
		
	});
	
	
	var transferFunds = function(amount, to_account_id, transfer_type) {
	
		b.request({
			url : '/members/mytransfers/execute_transfer',
			data : {				
				'to_account_id' : to_account_id,
				'amount' : amount,
				'transfer_type' : transfer_type
				
			},
			on_success : function(data) {
				
				errorModal.hide();
				
				if (data.status == "1")	{
					// show add form modal					
					proceedTransModal = b.modal.new({
						title: 'Transfer Form',
						width:500,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								proceedTransModal.hide();	
								redirect('/members/mytransfers');
							}
						}
					});
					proceedTransModal.show();					
				} else {
					// show add form modal					
					var errorTransfersModal = b.modal.new({
						title: 'Transfer Form :: Error',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {						
							'Close' : function() {
								errorTransfersModal.hide();								 							
							}
						}
					});
					errorTransfersModal.show();				
				}
			}
		})
					
		return false;	
		
	}	
	
	
	$('.btn-enter-code').live("click",function() {
		member_transfer_id = $(this).attr("data");
		
		b.request({
			url : '/members/transfers/enter_code',
			data : {
					"member_transfer_id" : member_transfer_id
					},
					
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					 var withPendingModal = b.modal.new({
						title: 'Transfer Request :: Pending',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							
							'Confirm Transfer' : function() {
								var confirmation_code = $('#transfer_confirmation_code').val();
								
								if ($.trim(confirmation_code) == "") {
									$("#confirmation_code_error").html("Please enter the Confirmatiion Code.");
									$("#confirmation_code_error").show();
									return false;
								} else {
									// check if same confirmation code
									errorModal.show();
									b.request({
										url : '/members/transfers/check_confirmation',
										data : {
											'confirmation_code' : confirmation_code,
											'member_transfer_id' : member_transfer_id
											},										
										on_success : function(data) {
											errorModal.hide();
											if (data.status == "1")	{
												errorModal.show();	
												b.request({
													url : '/members/transfers/commit_transfer_to_member',
													data : {
														'member_transfer_id' : member_transfer_id
														},										
													on_success : function(data) {
														errorModal.hide();
														
														if (data.status == "1")	{
														
															var commitTransferModal = b.modal.new({
																title: 'Transfer :: Successful',
																width: 450,
																disableClose: true,
																html: data.data.html,
																buttons: {																
																	'Ok' : function() {
																		commitTransferModal.hide();
																		redirect('/members/transfers');
																	}
																}
															});
															commitTransferModal.show();
															
														
														} else {
															var errorCommitTransferModal = b.modal.new({
																title: 'Transfer :: Error',
																width: 450,
																disableClose: true,
																html: data.data.html,
																buttons: {																
																	'Ok' : function() {
																		errorCommitTransferModal.hide();
																	}
																}
															});
															errorCommitTransferModal.show();
														}
													}
												})
												
											} else {
												var errorConfirmationModal = b.modal.new({
													title: 'Transfer :: Confirmation Error',
													width: 450,
													disableClose: true,
													html: data.data.html,
													buttons: {																				
														'Ok' : function() {
															errorConfirmationModal.hide();
														}
													}
												});
												errorConfirmationModal.show();
											}
										}
									})
								}
								
								withPendingModal.hide();
							},
							'Close' : function() {
								withPendingModal.hide();								 							
							}
						}
					});
					
					$("#confirmation_code_error").hide();
					withPendingModal.show();	
					
				} else {
					// show add form modal					
					withPendingModal = b.modal.new({
						title: 'Transfer Request :: Notice',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								withPendingModal.hide();								 							
							}
						}
					});
					withPendingModal.show();					
				}
			}
		})	
		
	});
	
	$('.btn-view-details').live("click",function() {
		member_transfer_id = $(this).attr("data");
		
		b.request({
			url : '/members/transfers/view_details',
			data : {
					"member_transfer_id" : member_transfer_id
					},
					
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var viewDetailsModal = b.modal.new({
						title: 'Transfer Request :: Details',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								viewDetailsModal.hide();								 							
							}
						}
					});
					viewDetailsModal.show();	
					
				} else {
					// show add form modal					
					var errorViewDetailsModal = b.modal.new({
						title: 'Transfer Request :: Error',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorViewDetailsModal.hide();								 							
							}
						}
					});
					errorViewDetailsModal.show();					
				}
			}
		})	
		
	});
	
//]]>
</script>