<?php

$year_options = "";
$month_options = "";
$day_options = "";

foreach($years as $k => $year)
{
	$year_options .= "<option value='{$k}'>{$year}</option>";
}
foreach($months as $k => $month)
{
	$month_options .= "<option value='{$k}'>{$month}</option>";
}
foreach($days as $k => $day)
{
	$day_options .= "<option value='{$k}'>{$day}</option>";
}
?>

<div class='alert alert-info'><h2>Transfer Commissions<a id="download" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Download</span></a></h2></div>

<div >
	<div class='control-group'>
		<form id='search_details' method='get' action ='/admin/transfers'>
			<div>
				<label class="control-label" for="Search option"><strong>Search Option</strong></label>
				<div class="controls">
					<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
						<option value="member_transfer_id" <?= ($search_by=="member_transfer_id")?"selected='selected'":'' ?>>Transaction ID</option>						
						<option value="last_name" <?= ($search_by=="last_name")?"selected='selected'":'' ?>>Last Name</option>
						<option value="first_name" <?= ($search_by=="first_name")?"selected='selected'":'' ?>>First Name</option>
					</select>             
					<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="<?= $search_text ?>" maxlength='25' autofocus="">	
				</div>    
			</div>
			<div>
				<div style='float:left;'>
					<label class='control-label' for='Start Date'><strong>From Date</strong></label>
					<div class='controls'>
						<div class="input-append" >
							<input title="Start Date" class="input-medium" type="text" id="start-date" name="from_dt" value="" readonly="readonly">
							<span id='start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
						</div>
					</div>
				</div>
			
				<div style='float:left;margin-left:10px;'>
					<label class='control-label' for='End Date'><strong>To Date</strong></label>
					<div class='controls'>
						<div class="input-append" >
							<input title="End Date" class="input-medium" type="text" id="end-date" name="to_dt" value="" readonly="readonly">
							<span id='end-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div>
				<label class='control-label' for='Status'><strong>Status</strong></label>
				<div class='controls'>
					<select id='status' name='status'>
						<option value='ALL' >ALL</option>
						<option value='PENDING' <?= ($status == 'PENDING') ? "selected='selected'":"" ?>>PENDING</option>
						<option value='COMPLETED' <?= ($status == 'COMPLETED') ? "selected='selected'":"" ?>>COMPLETED</option>
						<option value='CANCELLED' <?= ($status == 'CANCELLED') ? "selected='selected'":"" ?>>CANCELLED</option>
					</select>
				</div>
			</div>
			<div>
				<button id="button_search" class='btn btn-primary' style="margin-left:5px;"><span>Process</span></button>
				<button id='button_refresh' class='btn' style=""><span>Refresh</span></button>
			</div>
			<br/>
			<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>	
		
			<?php
			if ($search_text == "") {
			?>	
				<div id="search_summary" style="display:none;">
			<?php
			} else {
			?>	
				<div id="search_summary">
			<?php
			};
			?>		
			
				<span class="label label-info">Search Results for:</span>
				<span class="label label-success"><?= $search_by ?></span>
				<span class="label label-success"><?= $search_text ?></span>
				<span class="label label-success"><?= $status ?></span>
				<span class="label label-success"><?= $from_dt ?></span>
				<span class="label label-success"><?= $to_dt ?></span>
			</div>		
		</form>
		<div class="clearfix"></div>
	</div>
</div>	



<hr/>

<br>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style=''>Transaction ID</th>
			<th style=''>From Member Name</th>
			<th>To Member Name</th>
			<th style=''>Type</th>
			<th style=''>Amount</th>
			<th style=''>Status</th>
			<th style=''>Date Initiated</th>
			<th style=''>Date Completed</th>
			<th style=''>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($transfers)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($transfers as $t): ?>
		<tr>
			<?php
				$proper_member_transfer_id = str_pad($t->member_transfer_id, 8, "0", STR_PAD_LEFT);
			?>
			<td><?= $proper_member_transfer_id ?></td>
			<?php
				// get member details
				$from_member_details = $this->members_model->get_member_by_id($t->from_member_id);
				
				$proper_from_member_name = $from_member_details->last_name . ", ". $from_member_details->first_name . " " . $from_member_details->middle_name;
			?>
			<td><?= $proper_from_member_name ?></td>
			<?php
				// get member details
				$to_member_details = $this->members_model->get_member_by_id($t->to_member_id);
				
				$proper_to_member_name = $to_member_details->last_name . ", ". $to_member_details->first_name . " " . $to_member_details->middle_name;
			?>			
			<td><?= $proper_to_member_name ?></td>
			<td><?= $t->type; ?></td>
			<td><?= $t->amount; ?></td>
			
			<?php
			if ($t->status == 'PENDING') {
				echo "<td><span class='label label-important' >{$t->status}</span></td>";
			} else if ($t->status == 'PROCESSING') {
				echo "<td><span class='label label-info' >{$t->status}</span></td>";
			} else if ($t->status == 'CANCELLED') {
				echo "<td><span class='label label-warning' >{$t->status}</span></td>";
			} else {
				echo "<td><span class='label label-success' >{$t->status}</span></td>";
			}			
			?>	
			<td><?= $t->insert_timestamp; ?></td>
			<td><?= $t->update_timestamp; ?></td>
			<td>
				<a class='btn btn-small btn-primary view-transfer-btn' data="<?= $t->member_transfer_id ?>" title="View"><i class="icon-info-sign icon-white"></i></a>
				
				<?php				
				if ($t->status == 'PENDING') {
				
					echo "<a class='btn btn-small btn-primary resend-code-btn' data='{$t->member_transfer_id}' title='Resend Code'><i 	class='icon-inbox icon-white'></i></a>
							<a class='btn btn-small btn-danger cancel-transfer-btn' data='{$t->member_transfer_id}' title='Cancel'><i class='icon-remove icon-white'></i></a>";
				}?>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>
<script type="text/javascript">
	
	$(function(){
		$("#start-date").datepicker({
			'dateFormat' : "yy-mm-dd"
		});

		$("#start-date").datepicker('setDate', '<?= $from_dt ?>');
		
		$("#start-date-icon").click(function(e) {
			$("#start-date").datepicker("show");
		});
		
		$("#end-date").datepicker({
			'dateFormat' : "yy-mm-dd"	
		});
		
		$("#end-date-icon").click(function(e) {
			$("#end-date").datepicker("show");
		});
		
		$("#end-date").datepicker('setDate', '<?= $to_dt ?>');	
	});
	
	
	$(".resend-code-btn").click(function(){
		var member_transfer_id = $(this).attr("data");
		
		// show add form modal					
		var confirmResendModal = b.modal.new({
			title: 'Transfer Resend Code :: Confirm',
			width:450,
			disableClose: true,
			html: "<p>You are about to resend the Confirmation Code for Transaction ID <strong>" + member_transfer_id + "</strong>.<br/>Would you like to proceed?</p>",
			buttons: {
				'Close' : function() {
					confirmResendModal.hide();								 							
				},
				'Proceed' : function() {
					
					resendCode(member_transfer_id);										
					confirmResendModal.hide();
				}									
			}
		});
		confirmResendModal.show();	
	
	});
	
	
	var resendCode = function(member_transfer_id) {
		b.request({
			url: "/admin/transfers/resend_code",
			data: {
				"member_transfer_id" : member_transfer_id
			},
			on_success: function(data){
				if (data.status == "1")	{
					
					// show add form modal					
						var resendCodeModal = b.modal.new({
							title: 'Transfer Resend Code :: Successful',
							width:450,
							disableClose: true,
							html: data.data.html,
							buttons: {
								'Close' : function() {
									resendCodeModal.hide();								 							
								}									
							}
						});
						resendCodeModal.show();						
				} else {
					// show add form modal					
					var errorResendCodeModal = b.modal.new({
						title: 'Transfer Details :: Re-send Code Error',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorResendCodeModal.hide();								 							
							}
						}
					});
					errorResendCodeModal.show();		
				}
			}					
		})
		return false;				
	}
	
	
	$(".view-transfer-btn").click(function(){
		var member_transfer_id = $(this).attr("data");
	
		b.request({
			url: "/admin/transfers/view_transaction",
			data: {
				"member_transfer_id" : member_transfer_id
			},
			on_success: function(data){
				if (data.status == "1")	{
					
					if (data.data.transfer_status == 'PENDING') {
				
						// show add form modal					
						withPendingModal = b.modal.new({
							title: data.data.title,
							width:450,
							disableClose: true,
							html: data.data.html,
							buttons: {
								'Close' : function() {
									withPendingModal.hide();								 							
								},
								'Execute Transfer' : function() {
									
									executeTransfer(member_transfer_id);										
									withPendingModal.hide();
								}									
							}
						});
						withPendingModal.show();	
						
					} else {
						// show add form modal					
						withPendingModal = b.modal.new({
							title: data.data.title,
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
				} else {
					// show add form modal					
					var errorViewTransactionModal = b.modal.new({
						title: 'Transfer Details :: Error',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorViewTransactionModal.hide();								 							
							}
						}
					});
					errorViewTransactionModal.show();		
				}
			}	
				
		})
		return false;			
	});
	
	var executeTransfer = function(member_transfer_id) {

		// ajax request
		b.request({
			url : '/admin/transfers/confirm_transfer',
			data : {				
				'member_transfer_id' : member_transfer_id
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					// show add form modal					
					confirmTransferModal = b.modal.new({
						title: 'Transfer :: Confirm',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'No' : function() {
								confirmTransferModal.hide();								 							
							},	
							'Yes' : function() {
								confirmTransferModal.hide();

								// proceed with transfer
								// ajax request
								b.request({
									url : '/admin/transfers/proceed_transfer',
									data : {				
										'member_transfer_id' : member_transfer_id
									},
									on_success : function(data) {
										
										if (data.status == "1")	{
											// show add form modal					
											confirmTransferModal = b.modal.new({
												title: 'Transfer :: Successful',
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {
													'Ok' : function() {
														confirmTransferModal.hide();
														redirect('admin/transfers');
													}
												}
											});
											confirmTransferModal.show();		
											
										} else {
											// show add form modal					
											errorTransfersModal = b.modal.new({
												title: 'Transfer :: Error',
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
						}
					});
					confirmTransferModal.show();		
					
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
		return false;				
	}	
	
	$("#download").click(function(){
		var download_modal = b.modal.new({});
		var years = "<?= $year_options ?>";
		var months = "<?= $month_options ?>";
		var days = "<?= $day_options ?>";

		download_modal.init({

			title: "Download Request Details",
			width: 300,
			html: '<label for="start_date">Start Date: </label>\n<div class="form-inline wc-date">\n<div class="input-append"><input type="text" class="input-medium" id="withdraw_start_date" name="withdraw_start_date" readonly="readonly" style="cursor:pointer;" /><span id="withdraw_start_date_icon" class="add-on" style="cursor:pointer;"><i class="icon-calendar"></i></span></div>\n</div>\n\
			<br>\n\
			<label for="end_date">End Date: </label>\n<div class="form-inline wc-date">\n<div class="input-append"><input type="text" class="input-medium" id="withdraw_end_date" name="withdraw_end_date" readonly="readonly" style="cursor:pointer;" /><span id="withdraw_end_date_icon" class="add-on" style="cursor:pointer;"><i class="icon-calendar"></i></span></div>\n</div>\n\
			<br/>\n\
			<label for="end_date">Status: </label>\n<div class="form-inline wc-date">\n<div class="input-append"><select id="status"><option value="ALL">ALL</option><option value="PENDING">PENDING</option><option value="COMPLETED">COMPLETED</option><option value="CANCELLED">CANCELLED</option></select></div>\n</div>\n',
			buttons:{
				"Proceed": function(e){
					var this_button = $("#"+$(download_modal).attr("id")+"_btn_proceed");
					//var start_date = $("#"+$(download_modal).attr("id")+" #start_date").val();
					var start_date =  $("#"+$(download_modal).attr("id")+" #withdraw_start_date").val();
					//var end_date = $("#"+$(download_modal).attr("id")+" #end_date").val();
					var end_date = $("#"+$(download_modal).attr("id")+" #withdraw_end_date").val();
					var status = $("#"+$(download_modal).attr("id")+" #status").val();

					//alert(start_date + '-' + end_date);
					
					if(!$(this_button).hasClass("no_clicking"))
					{
						$(this_button).addClass("no_clicking");

						b.request({
							url: "/admin/transfers/download_check",
							data: {
								"start_date": start_date,
								"end_date": end_date,
								"status": status
							},
							on_success: function(data){
								var download_confirm_modal = b.modal.new({});
								if(data.status == "error")
								{
									download_confirm_modal.init({
										title: "Error Notification",
										html: "<p>"+data.msg+"</p>",
										width: 250
									});

									download_confirm_modal.show();
								}
								else if(data.status == "ok")
								{
									download_modal.hide();

									download_confirm_modal.init({
										title: "Download Confirmation",
										html: "<p>"+data.msg+"</p>",
										disableClose: true,
										buttons: {
											"Cancel": function(){
												download_confirm_modal.hide();
											},
											"Proceed": function(){
												var download_proceed_modal = b.modal.new({});

												var this_button = $("#"+$(download_confirm_modal).attr("id")+"_btn_proceed");

												if(!$(this_button).hasClass("no_clicking"))
												{
													$(this_button).addClass("no_clicking")
													b.request({
														url: "/admin/transfers/download_proceed",
														data: {
															"start_date": start_date,
															"end_date": end_date,
															"status": status
														},
														on_success: function(data){
															var download_xls_modal = b.modal.new({});
															if(data.status == "error")
															{
																download_xls_modal.init({
																	title: "Error Notification",
																	html: "<p>"+data.msg+"</p>",
																	width: 250
																});

																download_xls_modal.show();
															}
															else if(data.status == "ok")
															{
																download_xls_modal.init({
																	title: "Download Pending Requests",
																	html: "<p>"+data.msg+"</p>",
																	disableClose: true,
																	buttons:{
																		"Cancel": function(){
																			download_xls_modal.hide();
																		},
																		"Download": function(){
																			download_xls_modal.hide();
																																		
																			redirect('/admin/transfers/export_xls/'+ start_date +'/' + end_date+'/' + status);
																
																			
																		}
																	}
																});

																download_xls_modal.show();
															}
															$(this_button).removeClass("no_clicking")
														},
														on_error: function(){
															$(this_button).removeClass("no_clicking")
														}
													});
												}
												download_confirm_modal.hide();
											}
										}
									});
									download_confirm_modal.show();
								}
								$(this_button).removeClass("no_clicking");
							},
							on_error: function(){
								$(this_button).removeClass("no_clicking");
							}
						});
					}
				}
			}
		});
		
		download_modal.show();
	
		// from date
 		$("#withdraw_start_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		
		var _end_date = new Date();
		_end_date.setDate(_end_date.getDate() - 7);
		$("#withdraw_start_date").datepicker('setDate', _end_date);
		
		$("#withdraw_start_date_icon").click(function(e) {
			$("#withdraw_start_date").datepicker("show");
		});
		
		
		// end date
 		$("#withdraw_end_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#withdraw_end_date").datepicker('setDate', '<?= date("Y-m-d") ?>');
		
		$("#withdraw_end_date_icon").click(function(e) {
			$("#withdraw_end_date").datepicker("show");
		});
		
	});


	
	$("#button_search").live("click",function() {
		var _search_string = $.trim($("#search_string").val());
        var _search_option = $("#search_option").val();
		
		$("#search_details").submit();
		return false;
	});
	
	$("#button_refresh").live("click",function() {
		//windows.location.href = '/admin/transfers';		
		$("#start-date").val('');
		$("#end-date").val('');
		$("#status").val('');
		$("#search_string").val('');
		redirect('/admin/transfers');
	});
	
	$(".cancel-transfer-btn").click(function(){
		var member_transfer_id = $(this).attr("data");
	
		b.request({
			url: "/admin/transfers/confirm_cancel",
			data: {
				"member_transfer_id" : member_transfer_id
			},
			on_success: function(data){
				if (data.status == "1")	{
					
					if (data.data.transfer_status == 'PENDING') {
				
						// show add form modal					
						withPendingModal = b.modal.new({
							title: 'Cancel Transfer Funds :: Confirm',
							width:450,
							disableClose: true,
							html: data.data.html,
							buttons: {
								'Close' : function() {
									withPendingModal.hide();								 							
								},
								'Proceed' : function() {
									
									$("#cancel_remarks_error").hide();									
									var _remarks = $("#cancel_remarks").html();
		
									if ($.trim($("#cancel_remarks").val()) == "") {		
										$("#cancel_remarks_error").show();
										return false;
									} else {
										$("#cancel_remarks_error").hide();									
										executeCancel(member_transfer_id, _remarks);										
										withPendingModal.hide();
									}
								}									
							}
						});
						withPendingModal.show();	
						
					} else {
						// show add form modal					
						withPendingModal = b.modal.new({
							title: data.data.title,
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
				} else {
					// show add form modal					
					var errorViewTransactionModal = b.modal.new({
						title: 'Cancel Transfer Funds:: Error',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorViewTransactionModal.hide();								 							
							}
						}
					});
					errorViewTransactionModal.show();		
				}
			}	
				
		})
		return false;			
	});
	
	
	var executeCancel = function(member_transfer_id, remarks) {
				
		// proceed with transfer
		// ajax request
		b.request({
			url : '/admin/transfers/proceed_cancel',
			data : {				
				'member_transfer_id' : member_transfer_id,
				'remarks' : remarks
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					// show add form modal					
					proceedCancelModal = b.modal.new({
						title: 'Cancel Transfer Funds :: Successful',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								proceedCancelModal.hide();
								redirect('admin/transfers');
							}
						}
					});
					proceedCancelModal.show();		
					
				} else {
					// show add form modal					
					errorCancelTransferModal = b.modal.new({
						title: 'Cancel Transfer Funds:: Error',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {						
							'Close' : function() {
								errorCancelTransferModal.hide();								 							
							}
						}
					});
					errorCancelTransferModal.show();				
				}
			}
		})									
	
		return false;				
	}	
	
	
	
</script>