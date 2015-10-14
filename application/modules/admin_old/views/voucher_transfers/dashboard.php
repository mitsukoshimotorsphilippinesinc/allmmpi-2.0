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

<div class='alert alert-info'><h2>Voucher Transfers<a id="download" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Download</span></a></h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/voucher_transfers'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="voucher_id" <?=($search_by=='voucher_id')?'selected="selected"':''; ?>>Voucher ID</option>
			<option value="voucher_code" <?=($search_by=='voucher_code')?'selected="selected"':''; ?>>Voucher Code</option>
			<option value="last_name" <?=($search_by=='last_name')?'selected="selected"':''; ?>>Last Name</option>
			<option value="first_name" <?=($search_by=='first_name')?'selected="selected"':''; ?>>First Name</option>
		</select>                 
		
		<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="<?= $search_text ?>" maxlength='25' autofocus="" >	

		<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
		<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>
	
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
		</div>		
	</form>
</div>	



<hr/>

<br>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style=''>Voucher ID</th>
			<th style=''>Member Name</th>
			<th>To Member Name</th>
			<th style=''>Voucher Code</th>
			<th style=''>Type</th>
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

				$proper_voucher_id = $t->voucher_id;
			?>
			<td><?= $proper_voucher_id ?></td>
			<?php
				// get member details
				$from_member_details = $this->members_model->get_member_by_id($t->member_id);
				
				$proper_from_member_name = $from_member_details->last_name . ", ". $from_member_details->first_name . " " . $from_member_details->middle_name;
			?>
			<td><?= $proper_from_member_name ?></td>
			<?php
				if (($t->to_member_id == 0) || (trim($t->to_member_id) == "")) {
	
					$proper_to_member_name = "N/A";	
	
				} else {
					// get member details
					$to_member_details = $this->members_model->get_member_by_id($t->to_member_id);
				
					$proper_to_member_name = $to_member_details->last_name . ", ". $to_member_details->first_name . " " . $to_member_details->middle_name;
				}
			?>			
			<td><?= $proper_to_member_name ?></td>
			<td><?= $t->voucher_code; ?></td>
			<td><?= $t->voucher_type_id; ?></td>
			
			<?php			
			if ($t->status == 'TRANSFERRING') {
				echo "<td><span class='label label-info' >{$t->status}</span></td>";
			} else if ($t->status == 'CANCELLED') {
				echo "<td><span class='label label-warning' >{$t->status}</span></td>";
			} else {
				echo "<td><span class='label label-success' >{$t->status}</span></td>";
			}			
			?>	
			<td><?= $t->insert_timestamp; ?></td>
			<td><?= $t->updated_timestamp; ?></td>
			<td>
				<a class='btn btn-small btn-primary view-transfer-btn' data="<?= $t->voucher_id ?>" title="View"><i class="icon-info-sign icon-white"></i></a>
				
				<?php				
				if ($t->status == 'TRANSFERRING') {
				
					echo "<a class='btn btn-small btn-primary resend-code-btn' data='{$t->voucher_id}' title='Resend Code'><i 	class='icon-inbox icon-white'></i></a>
							<a class='btn btn-small btn-danger cancel-transfer-btn' data='{$t->voucher_id}' title='Cancel'><i class='icon-remove icon-white'></i></a>";
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
	

	
	$(".resend-code-btn").click(function(){
		var voucher_id = $(this).attr("data");
		
		// show add form modal					
		var confirmResendModal = b.modal.new({
			title: 'Transfer Resend Code :: Confirm',
			width:450,
			disableClose: true,
			html: "<p>You are about to resend the Confirmation Code for Voucher ID <strong>" + voucher_id + "</strong>.<br/>Would you like to proceed?</p>",
			buttons: {
				'Close' : function() {
					confirmResendModal.hide();								 							
				},
				'Proceed' : function() {
					
					resendCode(voucher_id);										
					confirmResendModal.hide();
				}									
			}
		});
		confirmResendModal.show();	
	
	});
	
	
	var resendCode = function(voucher_id) {
		b.request({
			url: "/admin/voucher_transfers/resend_code",
			data: {
				"voucher_id" : voucher_id
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
		var voucher_id = $(this).attr("data");
	
		b.request({
			url: "/admin/voucher_transfers/view_transaction",
			data: {
				"voucher_id" : voucher_id
			},
			on_success: function(data){
				if (data.status == "1")	{
					
					if (data.data.transfer_status == 'TRANSFERRING') {
				
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
									
									executeTransfer(voucher_id);										
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
	
	var executeTransfer = function(voucher_id) {

		// ajax request
		b.request({
			url : '/admin/voucher_transfers/confirm_transfer',
			data : {				
				'voucher_id' : voucher_id
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
									url : '/admin/voucher_transfers/proceed_transfer',
									data : {				
										'voucher_id' : voucher_id
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
														redirect('admin/voucher_transfers');
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
			<br/>\n',
			buttons:{
				"Proceed": function(e){
					var this_button = $("#"+$(download_modal).attr("id")+"_btn_proceed");
					//var start_date = $("#"+$(download_modal).attr("id")+" #start_date").val();
					var start_date =  $("#"+$(download_modal).attr("id")+" #withdraw_start_date").val();
					//var end_date = $("#"+$(download_modal).attr("id")+" #end_date").val();
					var end_date = $("#"+$(download_modal).attr("id")+" #withdraw_end_date").val();

					//alert(start_date + '-' + end_date);
					
					if(!$(this_button).hasClass("no_clicking"))
					{
						$(this_button).addClass("no_clicking");

						b.request({
							url: "/admin/voucher_transfers/download_check",
							data: {
								"start_date": start_date,
								"end_date": end_date
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
														url: "/admin/voucher_transfers/download_proceed",
														data: {
															"start_date": start_date,
															"end_date": end_date
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
																																		
																			redirect('/admin/voucher_transfers/export_xls/'+ start_date +'/' + end_date);
																
																			
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
		
		if (_search_string == '') {
			$("#search_error").show();
			$("#search_summary").hide();
		} else {
			$("#search_details").submit();
			$("#search_error").hide(); 
			$("#search_string").val("");
			$("#search_summary").show();           
		}
		
		return false;
	});
	
	$("#button_refresh").live("click",function() {
		//alert(1);
		$("#search_summary").hide();
		$("#search_string").val("");
		//redirect('/admin/voucher_transfers');
	});
	
	$(".cancel-transfer-btn").click(function(){
		var voucher_id = $(this).attr("data");
	
		b.request({
			url: "/admin/voucher_transfers/confirm_cancel",
			data: {
				"voucher_id" : voucher_id
			},
			on_success: function(data){
				if (data.status == "1")	{
					
					if (data.data.transfer_status == 'TRANSFERRING') {
				
						// show add form modal					
						withPendingModal = b.modal.new({
							title: 'Cancel Transfer Voucher :: Confirm',
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
										executeCancel(voucher_id, _remarks);										
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
						title: 'Cancel Transfer Voucher:: Error',
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
	
	
	var executeCancel = function(voucher_id, remarks) {
				
		// proceed with transfer
		// ajax request
		b.request({
			url : '/admin/voucher_transfers/proceed_cancel',
			data : {				
				'voucher_id' : voucher_id,
				'remarks' : remarks
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					// show add form modal					
					proceedCancelModal = b.modal.new({
						title: 'Cancel Transfer Voucher :: Successful',
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								proceedCancelModal.hide();
								redirect('admin/voucher_transfers');
							}
						}
					});
					proceedCancelModal.show();		
					
				} else {
					// show add form modal					
					errorCancelTransferModal = b.modal.new({
						title: 'Cancel Transfer Voucher:: Error',
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