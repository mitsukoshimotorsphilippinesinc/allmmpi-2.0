<?php
 //var_dump($this->uri->segment_array());
 
?>

<form id='frm_filter' class='form-horizontal' method='post' action ='/members/myvouchers/view/vitalc'>
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
					<label class="pull-left" for="use_date_range">Voucher Type:</label>
					<div class="controls">
						<select id="voucher_type" name="voucher_type">
							<option class="vaucher_type_options" value="ALL">ALL</option>
							<option class="vaucher_type_options" value="FPV">FPV</option>
							<option class="vaucher_type_options" value="MPV">MPV</option>
							<option class="vaucher_type_options" value="P2P">P2P</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="pull-left" for="use_date_range">Status:</label>
					<div class="controls">
						<select id="status" name="status">
						<option class="status_options" value="ALL">ALL</option>
						<option class="status_options" value="ACTIVE">ACTIVE</option>
						<option class="status_options" value="INACTIVE">INACTIVE</option>
						<option class="status_options" value="TRANSFERRING">TRANSFERRING</option>
						<option class="status_options" value="REDEEMED">REDEEMED</option>
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
		<span class="label label-success">Voucher Type:</span>
		<span class="label label-success">Status:</span>
		<span class="label label-success">Timestamp:</span>
	</div>	
	
</form>
<div class="tab-pane active" id="vitalc_vouchers">
	<div class='ui-element'>
		<table class='table table-bordered table-striped'>
			<thead>
				<tr>				
					<th>Code</th>
					<th>Type</th> 				
					<th>AR Number</th>
					<th>Transfer To</th>
					<th>Status</th>
					<th>Redeemed By</th>
					<th>Redeemed Date</th>
					<th>Valid Until</th>
					<th>Insert Date</th>
					<th style='width:100px;'>Action</th>
				</tr>
			</thead>
			<tbody style="font-size:12px;">
			<?php if(empty($member_vouchers)): ?>
				<tr>
					<tr><td colspan='10' style='text-align:center;'><strong>No Records Found</strong></td></tr>
				</tr>
			<?php else: ?>
			<?php foreach ($member_vouchers as $voucher): ?>
				<tr data='<?= $voucher->voucher_code ?>'>										
					<td><?= $voucher->voucher_code; ?></td>
					
					<?php
						$account_voucher_type = "N/A";
						$voucher_type_details = $this->vouchers_model->get_account_voucher_type_by_id($voucher->voucher_type_id);
						if (!empty($voucher_type_details)) {
							$account_voucher_type = $voucher_type_details->code;
						}
					?>
					<td><?= $account_voucher_type; ?></td>
					
					<td><?= $voucher->ar_number; ?></td>	
					
					<?php
						$to_member_name = "N/A";
						$to_member_details = $this->members_model->get_member_by_id($voucher->to_member_id);
						if (!empty($to_member_details)) {
							$to_member_name = $to_member_details->last_name . '. ' . $to_member_details->first_name . ' ' . $to_member_details->middle_name;
							$to_member_name = strtoupper($to_member_name);
						}
					?>
					
					
					<td><?= $to_member_name; ?></td>
					
					<?php
						if  (($voucher->status == 'PENDING') || ($voucher->status == 'TRANSFERRING'))  {
							$status_label =  "<span class='status label label-warning'>{$voucher->status}</span>";
						} else if (($voucher->status == 'INACTIVE') || ($voucher->status == 'VOID')) {
							$status_label =  "<span class='status label label-important'>{$voucher->status}</span>";
						} else {
							$status_label =  "<span class='status label label-success'>{$voucher->status}</span>";
						}
					?>
					
					<td><?= $status_label; ?></td>	
					<td><?= $voucher->redeemed_by; ?></td>				
					<td><?= $voucher->redeemed_timestamp; ?></td>
					<td><?= $voucher->validity_timestamp; ?></td>
					<td><?= $voucher->insert_timestamp; ?></td>
					
					
					<?php
						//$raffle_entries = "";
						//if(isset($voucher_raffle_entries[$voucher->voucher_id])){
						//	foreach($voucher_raffle_entries[$voucher->voucher_id] as $rf){
						//		if(!empty($raffle_entries)) $raffle_entries .= ", ";
						//		$raffle_entries .= $rf->raffle_number;
						//	}
						//}
						//if(!empty($raffle_entries)) $raffle_entries = "<br /><br /><label>Raffle Entries</label>".$raffle_entries;
					?>										
					<td>					
						<?php				
						if ($voucher->status == 'ACTIVE') {
						
							echo "
									<a class='btn btn-small btn-primary transfer-voucher-btn' data='{$voucher->voucher_id}' title='Transfer'><i class='icon-share icon-white'></i></a>";
						} 
						
						if ($voucher->status == 'TRANSFERRING') {
						
							echo "
									<a class='btn btn-small btn-success btn-enter-code' data='{$voucher->voucher_id}' title='Confirm Transfer'><i class='icon-ok icon-white'></i></a>
									<a class='btn btn-small btn-danger cancel-transfer-btn' data='{$voucher->voucher_id}' title='Cancel'><i class='icon-remove icon-white'></i></a>";
						}					
						?>
					</td>
				
				</tr>
			<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
	
<div>
	<?= $this->pager->create_links();  ?>
</div>

<script type="text/javascript">

	$(document).ready(function(){

	var _hasError = 1;

	$('.transfer-voucher-btn').click(function(e){
		e.preventDefault();
		var voucher_id = $(this).attr("data");

		var account_upgrade_modal = beyond.modal.create({
			title: 'Transfer Voucher',
			html: _.template($('#transfer-voucher-template').html(), {}),
			disableClose : true,
			buttons: {
				'Confirm': function() {						
				
					var account_id = $('.account-id').val();
				
					if (_hasError == 1) {
						
						beyond.request({
							url: '/members/myvouchers/check_account',
							data: {
								account_id: account_id
							},
							on_success: function(data){
						
								if(data.status == 1) {										
									_hasError = 0;
									account_upgrade_modal.hide();
									// proceed with transfer
									transferVoucher(voucher_id, account_id);
									
									
								} else {
									$('.result-container').html(data.data.html);
									_hasError = 1;   										
								}
							}
						});
					} else {
						transferVoucher(voucher_id, account_id);
					}
				
				}, 
				'Close': function() {
					//alert(_hasError);
					account_upgrade_modal.hide();
		
				} 
			}
		});
		account_upgrade_modal.show();
		
		
		$('.account-id').keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
		

		$('.btn-check-account').click(function(e){
			e.preventDefault();

			var account_id = $('.account-id').val();
			//alert(account_id);	
			beyond.request({
				url: '/members/myvouchers/check_account',
				data: {
					account_id: account_id
				},
				on_success: function(data){
					
					if(data.status) {
						$('.result-container').html(data.data.html);
						_hasError = 0;                          
					} else {
						$('.result-container').html(data.data.html);
						_hasError = 1;   
					}
				}
			});    
		});			
	});
	
	
	$('.cancel-transfer-btn').live("click",function() {
		voucher_id = $(this).attr("data");
		
		b.request({
			url : '/members/myvouchers/cancel_transfer',
			data : {
					"voucher_id" : voucher_id
					},
					
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var withPendingCancelModal = b.modal.new({
						title: 'Voucher Transfer :: Cancel',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {								
							'Proceed' : function() {									
								if (data.status == "1")	{
									withPendingCancelModal.hide();	
									b.request({
										url : '/members/myvouchers/proceed_cancel_transfer',
										data : {
											'voucher_id' : voucher_id
											},										
										on_success : function(data) {
											
											if (data.status == "1")	{
											
												var cancelTransferModal = b.modal.new({
													title: 'Voucher Transfer :: Cancelled',
													width: 450,
													disableClose: true,
													html: data.data.html,
													buttons: {																
														'Ok' : function() {
															cancelTransferModal.hide();
															redirect('/members/myvouchers');
														}
													}
												});
												cancelTransferModal.show();
												
											
											} else {
												var errorCancelTransferModal = b.modal.new({
													title: 'Voucher Transfer :: Error',
													width: 450,
													disableClose: true,
													html: data.data.html,
													buttons: {																
														'Ok' : function() {
															errorCancelTransferModal.hide();
														}
													}
												});
												errorCancelTransferModal.show();
											}
										}
									})
									
								} else {
									var errorConfirmationModal = b.modal.new({
										title: 'Voucher Transfer :: Error',
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
							},
							'Close' : function() {
								withPendingCancelModal.hide();								 							
							}
						}
					});
					
					withPendingCancelModal.show();	
					
				} else {
					// show add form modal					
					withPendingCancelModal = b.modal.new({
						title: 'Voucher Transfer :: Error',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								withPendingCancelModal.hide();								 							
							}
						}
					});
					withPendingCancelModal.show();					
				}
			}
		})	
		
	});
	
	$('.btn-enter-code').live("click",function() {
		voucher_id = $(this).attr("data");
	
		b.request({
			url : '/members/myvouchers/enter_code',
			data : {
					"voucher_id" : voucher_id
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
									//errorModal.show();
									b.request({
										url : '/members/myvouchers/check_confirmation',
										data : {
											'confirmation_code' : confirmation_code,
											'voucher_id' : voucher_id
											},										
										on_success : function(data) {
											//errorModal.hide();
											if (data.status == "1")	{
												//errorModal.show();	
												b.request({
													url : '/members/myvouchers/commit_transfer_to_member',
													data : {
														'voucher_id' : voucher_id
														},										
													on_success : function(data) {
														//errorModal.hide();
														
														if (data.status == "1")	{
														
															var commitTransferModal = b.modal.new({
																title: 'Voucher Transfer :: Successful',
																width: 450,
																disableClose: true,
																html: data.data.html,
																buttons: {																
																	'Ok' : function() {
																		commitTransferModal.hide();
																		redirect('/members/myvouchers');
																	}
																}
															});
															commitTransferModal.show();
															
														
														} else {
															var errorCommitTransferModal = b.modal.new({
																title: 'Voucher Transfer :: Error',
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
	
	var transferVoucher = function(voucher_id, account_id) {

		b.request({
			url : '/members/myvouchers/execute_transfer',
			data : {				
				'account_id' : account_id,
				'voucher_id' : voucher_id
				
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					// show add form modal					
					proceedTransModal = b.modal.new({
						title: 'Transfer Voucher',
						width:500,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								proceedTransModal.hide();	
								redirect('/members/myvouchers');
							}
						}
					});
					proceedTransModal.show();					
				} else {
					// show add form modal					
					var errorTransfersModal = b.modal.new({
						title: 'Transfer Voucher :: Error',
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
		
	});	
</script>