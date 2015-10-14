<?php

	$hash = hash_hmac('md5',$this->member->session_id,$this->config->item('encryption_key'));

?>
<div class="page-header clearfix">
	<h2>My Transfers<button class="btn-small btn-primary" id="btn-back" style="float:right;margin-right:10px;margin-top:8px;">Back To Transfer Funds</button></h2>
</div>
<form id='frm_filter' class='form-horizontal' method='post' action ='/members/transfers/page'>
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
					<label class="pull-left" for="for_transfer_type">Type:</label>
					<div class="controls">
                        <select id="transfer_type" name="transfer_type">
                            <option class="type_options" value="ALL">ALL</option>
							<option class="type_options" value="FUNDS">FUNDS</option>
                        </select>
                    </div>
				</div>
				
				<div class="control-group">
					<label class="pull-left" for="use_date_range">Status:</label>
					<div class="controls">
                        <select id="status_type" name="status_type">
                            <option class="status_options" value="ALL">ALL</option>
							<option class="status_options" value="PENDING">PENDING</option>
							<option class="status_options" value="COMPLETED">COMPLETED</option>			
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
		<span class="label label-success">Transfer Type: <?= $transfer_type; ?></span>
		<span class="label label-success">Status: <?= $status_type; ?> </span>
		<span class="label label-success">Between Timestamps: <?= $between_timestamps; ?> </span>
	</div>	
	
</form>
<div class="ui-element">
	<div>
		<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
			<thead>
				<tr>
					<th>Transaction ID</th>
					<th>From Member Name</th>
					<th>To Member Name</th>	
					<th>Type</th>
					<th>Amount</th>
					<th>Status</th>
					<th>Date Initiated</th>	
					<th>Date Completed</th>	
					<th style='width:120px;'>Action</th>	
				</tr>
			</thead>
			<tbody id="order_tbody_html">
				<?php if(empty($transactions)): ?>
					<tr><td colspan="10" style="text-align:center;"><strong>No Result</strong></td></tr>
				<?php else: ?>
					<?php foreach($transactions as $t): ?>

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
						<td><?= $t->type ?></td>
						<td><?= $t->amount ?></td>
						<?php
							$html_status = "";
							if ($t->status == 'PENDING') {
								$html_status = "<span class='label label-important'>{$t->status}</span>";
							} else if  ($t->status == 'COMPLETED') {
								$html_status = "<span class='label label-success'>{$t->status}</span>";
							} else {
								$html_status = "<span class='label label-warning'>{$t->status}</span>";
							}							
						
						?>
						<td><?= $html_status ?></td>
						<td><?= $t->insert_timestamp ?></td>
						<td><?= $t->update_timestamp ?></td> 
						<td>
						<?php
						
							if (($this->member->member_id == $t->from_member_id) && ($t->status == 'PENDING')) {
							
								echo "<button title='Confirm Transfer' class='btn btn-small btn-success btn-enter-code' data='{$t->member_transfer_id}' style=''><i class='icon-ok icon-white'></i></button>
									  <button title='Resend Code' class='btn btn-small btn-warning resend-code' data='{$t->member_transfer_id}' style=''><i class='icon-barcode icon-white'></i></button>
									  <button title='Cancel Transfer' class='btn btn-small btn-danger cancel-transfer' data='{$t->member_transfer_id}' style=''><i class='icon-remove icon-white'></i></button>
									  ";
							
							} else {
							
								echo "<button title='View Details' class='btn btn-small btn-primary btn-view-details' data='{$t->member_transfer_id}' style=''>View</button></td>";
								
							}
						?>
												
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

	var addCardTypeModal;
	var addCardTypeProceedModal;
	var member_id_to_transfer = 0;
	var member_id = "<?=$member_id?>";

	var current_page = 1;
	
	var showTransactions = function(page, filter_by) {
		
	   if (filter_by==null) filter_by = 'BOTH';
        
		b.request({
	        url: '/members/transfers/page',
	        data: {
				"page":page,
				"member_id" : member_id,
				"filter_by":filter_by
			},
		
		
			on_success: function(data, status) {
				if (data.total_records == 0) {
                    $("#order_tbody_html").html('<tr><td colspan="10" style="text-align:center;"><strong> - No Result - </strong></td></tr>');
                } else {
                    $("#order_tbody_html").html(data.html);
                }
				current_page = page;
		    }
		});		
	 }
	
  	$(document).ready(function() {
		showTransactions(1);               
	});			
	

	$(function() {
		
		$("#from_date").datepicker({
			'dateFormat' : "yy-mm-dd",
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
			'dateFormat' : "yy-mm-dd",			
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datepicker('setDate', '<?= $to_date ?>');
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd'));
			$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd'));
			$('#frm_filter').submit();
		});
			
	});
	
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
					withPendingModal = b.modal.new({
						title: 'Transfer Request :: Pending',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							//'Resend Confirmation Code' : function() {
							//	// TODO : resend code
							//	alert("This feature is not yet avaiable...");		
							//	withPendingModal.hide();
							//},
							'Confirm Transfer' : function() {
								var confirmation_code = $('#transfer_confirmation_code').val();
								
								if ($.trim(confirmation_code) == "") {
									$("#confirmation_code_error").html("Please enter the Confirmatiion Code.");
									$("#confirmation_code_error").show();
									return false;
								} else {
									// check if same confirmation code
									b.request({
										url : '/members/transfers/check_confirmation',
										data : {
											'confirmation_code' : confirmation_code,
											'member_transfer_id' : member_transfer_id
											},										
										on_success : function(data) {
											if (data.status == "1")	{
											
												b.request({
													url : '/members/transfers/commit_transfer_to_member',
													data : {
														'member_transfer_id' : member_transfer_id
														},										
													on_success : function(data) {
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
	
	$('.cancel-transfer').live("click",function() {
		member_transfer_id = $(this).attr("data");
		
		
		b.request({
			url : '/members/transfers/cancel_transfer',
			data : {
					"member_transfer_id" : member_transfer_id
					},
					
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var viewDetailsModal = b.modal.new({
						title: 'Transfer Request :: Cancel',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Yes' : function() {
													
								b.request({
									url : '/members/transfers/proceed_cancel_transfer',
									data : {
											"member_transfer_id" : member_transfer_id
											},		
									on_success : function(data) {
										if (data.status == "1")	{
											viewDetailsModal.hide();
											
											// show add form modal					
											var cancelTransferModal = b.modal.new({
												title: 'Cancel Transfer :: Successful',
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {
													'Close' : function() {
														cancelTransferModal.hide();
														redirect('/members/transfers');
														
													}
												}
											});
											cancelTransferModal.show();	
										} else {
											// show add form modal					
											var errorViewDetailsModal = b.modal.new({
												title: 'Cancel Transfer :: Error',
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
							},
							'No': function() {
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
	
	$('.resend-code').live("click",function() {
		member_transfer_id = $(this).attr("data");
		
		b.request({
			url : '/members/transfers/resend_code',
			data : {
					"member_transfer_id" : member_transfer_id
					},
					
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var viewDetailsModal = b.modal.new({
						title: 'Resend Code :: Successful',
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
						title: 'Resend Code :: Error',
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
	
	
	$('#btn-back').live("click",function() {
		redirect('/members/mytransfers');
	});
	
	$('#btn-transfer').live("click",function() {
						
		b.request({
			url : '/members/transfers/check',
			data : {},
			on_success : function(data) {
				//alert(data.status);
			
				if (data.status == "1")	{
					// show add form modal					
					addCardTypeModal = b.modal.new({
						title: 'Transfer Form',
						width:500,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Clear' : function() {
								addCardTypeModal.hide();
							},
							'Close' : function() {
								addCardTypeModal.hide();								 							
							}
						}
					});
					
					addCardTypeModal.show();
					
				} else if (data.status == "-1")	{
					// show add form modal					
					pendingErrorModal = b.modal.new({
						title: 'Transfer Form',
						width:500,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								pendingErrorModal.hide();								 							
							}
						}
					});
					pendingErrorModal.show();
					
				} else {
					// show add form modal					
					withPendingModal = b.modal.new({
						title: 'Transfer Request :: Pending',
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
	
	$('.btn-select-member').live("click",function() {
		member_id_to_transfer = $(this).attr("data");
		
		//alert(member_id_to_transfer);
		
		addCardTypeModal.hide();
		
		
		// ajax request to display member details
		b.request({
			url : '/members/transfers/transfer_form',
			data : {'member_id' : member_id_to_transfer},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					addCardTypeProceedModal = b.modal.new({
						title: 'Transfer Form',
						width: 500,
						disableClose: true,
						html: data.data.html,
						buttons: {							
							'Proceed' : function() {
								var _transfer_modal_type = $("#transfer_modal_type").val();
								var _amount = $("#amount").val();
								
								//alert(_transfer_modal_type + "|" + _amount);
								
								// check first if member has enough funds
								b.request({
									url : '/members/transfers/check_amount',
									data : {
										'_transfer_modal_type' : _transfer_modal_type,
										'_amount' : _amount,
										'_to_member_id' : member_id_to_transfer
									},
									
									on_success : function(data) {
										if (data.status == "1")	{
										
											//alert(_transfer_modal_type + " " + _amount + " " + member_id_to_transfer);	
											addCardTypeProceedModal.hide();
											transferFunds(_amount, _transfer_modal_type, member_id_to_transfer);
											//addCardTypeModal.hide();
										
										} else if (data.status == "-1")	{ 											
											$("#amount_error").html(data.data.html);
											$("#amount_error").show();
										} else {
											var errorToMemberModal = b.modal.new({
												title: 'Transfer Form :: Error',
												width: 450,
												disableClose: true,
												html: data.data.html,
												buttons: {																				
													'Ok' : function() {
														errorToMemberModal.hide();
													}
												}
											});
											errorToMemberModal.show();
										}
									}
								})
								
							},
							'Cancel' : function() {
								addCardTypeProceedModal.hide();
							}
						}
					});
					addCardTypeProceedModal.show();

					$("#amount").keypress(function (e) {
						if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
							return false;
						}
					});	

					
				} else {
					var errorSearchModal = b.modal.new({
						title: 'Transfer Form :: Error',
						width: 450,
						disableClose: true,
						html: data.data.html,
						buttons: {																				
							'Ok' : function() {
								errorSearchModal.hide();
							}
						}
					});
					errorSearchModal.show();
				}
			}
		})
		
	});
	
	
	var transferFunds = function(amount, transfer_type, to_member_id) {

		// ajax request
		b.request({
			url : '/members/transfers/proceed_transfer',
			data : {				
				'to_member_id' : to_member_id,
				'amount' : amount,
				'transfer_type' : transfer_type,
				'hash' : '<?=$hash?>'
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					// show add form modal					
					proceedTransfersModal = b.modal.new({
						title: 'Transfer Form',
						width:500,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								proceedTransfersModal.hide();
								//addCardTypeProceedModal.hide();
								b.request({
									url : '/members/transfers/execute_transfer',
									data : {				
										'to_member_id' : to_member_id,
										'amount' : amount,
										'transfer_type' : transfer_type,
										'hash' : '<?=$hash?>'
									},
									on_success : function(data) {
										
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
														redirect('/members/transfers');
													}
												}
											});
											proceedTransModal.show();					
										} else {
											// show add form modal					
											errorTransfersModal = b.modal.new({
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
								
							},
							'Cancel' : function() {
								proceedTransfersModal.hide();								 							
							}
						}
					});				
					proceedTransfersModal.show();	
					
				} else {
					// show add form modal					
					errorTransfersModal = b.modal.new({
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
		
	}	
	
//]]>
</script>