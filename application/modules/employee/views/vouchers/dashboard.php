<?php
//var_dump($this->uri->segment_array());
$page = $this->uri->segment(5);
?>

<div class="page-header clearfix">
	<h2 >My Vouchers <small></small></h2>
	<input type="hidden" id="vitalc_page_holder" value="1" />
	<input type="hidden" id="cln_page_holder" value="1"/>
</div>

<div class="tabbable header-info-admin-child">
	<ul id="voucher_tabs" class="nav nav-tabs">
		<li class="active vitalc_tab"><a href="#vitalc_tab" data-toggle="tab">Vital-C</a></li>
		<li class="cln_tab"><a href="#cln_tab" data-toggle="tab">CLN and Others</a></li>
		<li class="vitalc_history_tab"><a href="#vitalc_history_tab" data-toggle="tab">Vital-C Vouchers Transfer History</a></li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane active summary" id="vitalc_tab">
			<form id='frm_filter' class='form-horizontal' method='post' action ='/members/myvouchers/page/vitalc'>
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
										<option class="vaucher_type_options" value="CPV">CPV</option>
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
									<option class="status_options" value="PENDING">PENDING</option>
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
					<span class="label label-success">Voucher Type:<?= $voucher_type; ?></span>
					<span class="label label-success">Status:<?= $status; ?></span>
					<span class="label label-success">Timestamp:<?= $between_timestamps; ?></span>
				</div>	
				
			</form>
			<div class="ui-element">
					<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
						<thead>
							<tr>
								<th>Code</th>
								<th>Type</th> 				
								<th>AR Number</th>
								<th>Transfered To</th>
								<th>Status</th>
								<th>Redeemed By</th>
								<th>Redeemed Date</th>
								<th>Valid Until</th>
								<th>Insert Date</th>
								<th style='width:115px;'>Action</th>
							</tr>
							
						</thead>
						<tbody id="order_tbody_html">
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

										echo "
											<a class='btn btn-small btn-primary view-history-btn' data='{$voucher->voucher_id}' data-code='{$voucher->voucher_code}' title='View Voucher History'><i class='icon-th-list icon-white'></i></a>";

										if($voucher->voucher_type_id == $cpv_id)
										echo "	<a class='btn btn-small btn-primary view-product-btn' data='{$voucher->voucher_id}' data-code='{$voucher->voucher_code}' title='View Voucher Details'><i class='icon-eye-open icon-white'></i></a>";
										?>


									</td>
								
								</tr>
							<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
			</div>
			<div>				
				<?= $this->pager->create_links($get_data); ?>
			</div>
		</div>
		
			<div class="tab-pane" id="cln_tab">
				<div>
				<!--form id='frm_filter' class='form-horizontal' method='post' action ='/members/myvouchers/page/cln'>
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
						<span class="label label-success">Voucher Type:<!--?= $voucher_type; ?></span>
						<span class="label label-success">Status:<!--?= $status; ?></span>
						<span class="label label-success">Timestamp:<!--?= $between_timestamps; ?></span>
					</div>	
					
				</form-->
				<div class='ui-element'>
					<table class='table table-bordered table-striped'>
						<thead>
							<tr>
								<th>Code</th>
								<th>Description</th> 
								<th>Status</th>
								<th>Valid Until</th>
								<th>Insert Date</th>
								<th style='width:90px;'>&nbsp;</th>
							</tr>
						</thead>
						<tbody style="font-size:12px;">
						<?php if(empty($member_cln_vouchers)): ?>
							<tr>
								<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
							</tr>
						<?php else: ?>
						<?php foreach ($member_cln_vouchers as $member_cln_voucher): ?>
							<tr data='<?= $member_cln_voucher->voucher_code ?>'>						
								<td><?= $member_cln_voucher->voucher_code; ?></td>
								<td><?= $member_cln_voucher->product_text; ?></td>								
								<?php
										if  (($member_cln_voucher->status == 'PENDING') || ($member_cln_voucher->status == 'TRANSFERRING'))  {
											$status_label =  "<span class='status label label-warning'>{$member_cln_voucher->status}</span>";
										} else if (($member_cln_voucher->status == 'INACTIVE') || ($member_cln_voucher->status == 'VOID')) {
											$status_label =  "<span class='status label label-important'>{$member_cln_voucher->status}</span>";
										} else {
											$status_label =  "<span class='status label label-success'>{$member_cln_voucher->status}</span>";
										}
									?>
									
								<td><?= $status_label; ?></td>	
								<td><?= $member_cln_voucher->end_timestamp; ?></td>
								<td><?= $member_cln_voucher->insert_timestamp; ?></td>					
								<td>
									<a href="/members/vouchers/display_pdf/<?= $member_cln_voucher->voucher_code?>" class='btn btn-small btn-primary btn_view_voucher' data='<?=$member_cln_voucher->voucher_id?>'><i class="icon-gift icon-white"></i> View</a>
								</td>
							
							</tr>
						<?php endforeach; ?>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
				<div>				
					<?= $this->pager2->create_links($get_data_cln); ?>
				</div>
			</div>	
		</div>

			<div class="tab-pane" id="vitalc_history_tab">
				<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
						<thead>
							<tr>
								<th>Code</th>
								<th>Type</th>
								<th>Received From</th>			
								<th>Transfered To</th>
								<th>Transfer Status</th>
								<th>Transfer Time</th>
							</tr>
							
						</thead>
						<tbody id="order_tbody_html">
							<?php if(empty($my_voucher_history)): ?>
								<tr>
									<tr><td colspan='10' style='text-align:center;'><strong>No Records Found</strong></td></tr>
								</tr>
							<?php else: 
								foreach($my_voucher_history as $voucher): 
									
									//print_r($voucher);	
							?>
								<tr>	
									<td>
										<?php echo $voucher->voucher_code; ?>
									</td>
		
									<td><?php echo $voucher->voucher_type; ?></td>
									<td><?php echo $voucher->received_from; ?></td>
			
									<td>
										<?php echo $voucher->transfer_to; ?>
									</td>
									<td>
										<?php 
											if($voucher->transfer_status == 'TRANSFERRING')
												echo $status_label =  "<span class='status label label-warning'>{$voucher->transfer_status}</span>";
											if($voucher->transfer_status == 'TRANSFER CANCELLED')
												echo $status_label =  "<span class='status label label-important'>{$voucher->transfer_status}</span>";
											if($voucher->transfer_status == 'TRANSFER SUCCESSFUL')
												echo $status_label =  "<span class='status label label-success'>{$voucher->transfer_status}</span>";
											if($voucher->transfer_status == 'RECEIVED')
												echo $status_label =  "<span class='status label label-success'>{$voucher->transfer_status}</span>";
										?>
									</td>
									<td>
										<?php echo $voucher->transfer_timestamp; ?>
									</td>
								</tr>
							<?php 	
								endforeach;
							endif; ?>
						</tbody>
					</table>
			</div>
		</div>
	</div>

</div>

<script id="test" type="text/javascript">

	$(document).ready(function(){
		<?php 
			$page = $this->uri->segment(5);
		?>
		var _page = <?= ($page) ? $page : 1  ?>;
		var _active_tab = '<?= $this->uri->segment(4)  ?>';
	    
		// reset active tab
		$('#voucher_tabs li').removeClass('active');
		$('.tab-content .tab-pane').removeClass('active');
		
		// get selected tab
		if (_active_tab == 'cln') {
			$('#voucher_tabs li.cln_tab').addClass('active');
			$('#cln_tab').addClass('active');
			//renderClnVouchers(_page);
			//renderVitalCVouchers($('#vitalc_page_holder').val());
		} else {
			$('#voucher_tabs li.vitalc_tab').addClass('active');
			$('#vitalc_tab').addClass('active');
			//renderVitalCVouchers(_page);
			//renderClnVouchers($('#cln_page_holder').val());
		}	

			//renderVitalCVouchers(1);
			//renderClnVouchers(1);
		
		
		function renderVitalCVouchers(_page) {
			b.request({
		        url: '/members/myvouchers/show_vitalc',
		        data: {
					"page" : _page
				},
				
				on_success: function(data, status) {
				
					if (data.status == 1) {
						$('#html_vitalc_vouchers_details').html(data.html);
						//alert('VITALC: ' + data.vitalc_current_page + '|' + data.cln_current_page);						
						$('#cln_page_holder').val(data.cln_current_page);
					
					} else {
						// create modal for errors
						var vouchers_modal = b.modal.new({
							title: 'Vouchers - VitalC :: Error',
							html:  "<p>"+data.message+"</p>",
						})
						vouchers_modal.show();
					}
			    }
			});		
			return;
		}
	
		function renderClnVouchers(_page){
		
			b.request({
		        url: '/members/myvouchers/show_cln',
		        data: {
					"page" : _page
				},
				
				on_success: function(data, status) {
				
				
					if (data.status == 1) {
						$('#html_cln_vouchers_details').html(data.html);
						//alert('CLN: ' +data.vitalc_current_page + '|' + data.cln_current_page);						
						$('#vitalc_page_holder').val(data.vitalc_current_page);
						
					} else {
						// create modal for errors
						var vouchers_modal = b.modal.new({
							title: 'Vouchers - CLN :: Error',
							html:  "<p>"+data.message+"</p>",
						})
						vouchers_modal.show();
					}
			    }
			});		
			return;
		}
		
		
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
		
		$('.view-history-btn').click(function(e){
			e.preventDefault();

			var voucher_id = $(this).attr('data');
			var voucher_code = $(this).attr('data-code');
			var voucher_logs;

			b.request({
				url : '/members/myvouchers/get_voucher_history',
				data : { "voucher_id" : voucher_id },
				on_success: function (data){
					if(data.status)
					{
						var history_modal = beyond.modal.create({
							'title':'Voucher History '+voucher_code,
							'html':_.template($('#voucher-history-template').html(),{
								voucher_logs : data.data.voucher_logs,
								status: data.data.status
							}),
							width: 700
						});
						history_modal.show();
					}

				}
			});	
		});

		$('.view-product-btn').click(function(e){
			e.preventDefault();

			var voucher_id = $(this).attr('data');
			var voucher_code = $(this).attr('data-code');

			b.request({
				url : '/members/myvouchers/get_voucher_product',
				data : { "voucher_id" : voucher_id },
				on_success: function (data){
					if(data.status)
					{
						var product_modal = beyond.modal.create({
							'title':'Voucher <strong>'+voucher_code+'</strong> contains',
							'html': data.msg
						});
						product_modal.show();
					}
				}
			});	
		});
	});
	
	$(function() {
		
		$("#from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'changeMonth' : true
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
            timeFormat: 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,	
			'changeMonth' : true
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datepicker('setDate', '<?= $to_date ?>');
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd') + ' 12:00 am');
			$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd h:M:s tt'));
			$('#frm_filter').submit();
		});
		
		var dl_url = b.uri.path + (b.uri.query.length > 0 ? '?' + b.uri.query + '&export=excel' : '?export=excel');
		$('#btn_download').attr('href', dl_url);

		$('.pagination a').removeAttr('class');

	});

    //end of js for accounts tab	
</script>

<script type="text/template" id="account_upgrade_confirm_template">
    <div>Upgrading account <strong><%= account_id %></strong> with the following details:</div>
    <table class="table table-bordered" style="margin-top: 20px;">
        <tbody>
            <tr>
                <td style="text-align: right;">Card Number</td>
                <td><strong><%= card_number %></strong></td>
            </tr>
            <tr>
                <td style="text-align: right;">Card Code</td>
                <td><strong><%= card_code %></strong></td>
            </tr>
            <tr>
                <td style="text-align: right;">Card Type</td>
                <td><strong><%= card_details.type + ' - ' + card_details.type_name %></strong></td>
            </tr>
        </tbody>
    </table>
</script>

<script type="text/template" id="transfer-voucher-template">
    <div class="row-fluid form-horizontal">
        <div class="control-group">
            <label class="control-label">To Account ID</label>
            <div class="controls">
                <input type="text" maxlength='10' placeholder="65********" class="account-id" />
            </div>
        </div>
		
        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
                <button class="btn btn-primary btn-check-account">Check</button>
            </div>
        </div>
		<div class="result-container">
        </div>
    </div>
</script>

<script type="text/template" id="voucher-history-template">
	<div class="row-fluid form-horizontal">
        <table class="table table-bordered" style="margin-top: 20px;">
	        <tbody>
	        	<thead>
		            <tr>
		                <th>From</th>
		                <th>To</th>
		                <th>Transfer Time</th>
		            </tr>
	            </thead>
	            <% 
	            	if(voucher_logs != '')
	            	{	
	            		for (i in voucher_logs) { %>
			            <tr>
			            	<td><%= voucher_logs[i].from %></td>
			            	<td><%=voucher_logs[i].to %></td>
			            	<td><%= voucher_logs[i].insert_timestamp %></td>
			            </tr>
					<% }
		            } else { %>
		            	<tr>
		            		<td colspan="3" style="text-align:center;">
		            			No transfers found
		            		</td>
		            	</tr>
		            <% } %>
	        </tbody>
	    </table>
	    <div style="text-align:center">
	    	Voucher Status: <%= status %>		    		
	    </div>
    </div>
</script>