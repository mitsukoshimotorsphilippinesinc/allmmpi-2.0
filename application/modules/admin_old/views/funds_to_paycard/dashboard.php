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

<div class='alert alert-info'><h2>Funds To Paycard<a id="upload" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Upload</span></a><a id="download" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Download</span></a></h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/funds_to_paycard'>
		<strong>Filter By:&nbsp;&nbsp;&nbsp;</strong>
		<select name="filter_option" id="filter_option_wo" style="width:150px;" value="<?= $filter_by ?>">
			<option value="ALL">All</option>
			<option value="PENDING">Pending</option>
			<option value="PROCESSING">Processing</option>
			<option value="PROCESSED">Processed</option>
			<option value="CANCELLED">Cancelled</option>
		</select>   
		
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="funds_to_paycard_id">Transaction ID</option>
			<option value="member_id">Member ID</option>
			<option value="last_name">Last Name</option>
			<option value="first_name">First Name</option>
		</select>     
		
		<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

		<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Go</span></button>
		<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>
		
		<br/>
		<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>	
	
		<?php
		if (($search_text == "") && ($filter_by == "ALL")) {
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
			<span class="label label-success"><?= $filter_by ?></span>
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
			<th style=''>Transaction ID</th>
			<th style=''>Member ID</th>
			<th>Name</th>
			<th style=''>Gross</th>
			<?php
			//<th style=''>GCEP</th>
			//<th style=''>Net Of GCEP</th>
			?>
			<th style=''>W. Tax</th>
			<th style=''>Net Of Tax</th>
			<?php
			//<th style=''>Balance</th>
			//<th style=''>Deduction</th>
			//<th style=''>Card Fee</th>
			?>
			<th style=''>Net</th>			
			<th style=''>Paycard</th>
			<th style=''>Date Requested</th>
			<th style=''>Status</th>
			<th style=''>Payout Period</th>
			<th style=''>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($transfers)):?>
		<tr><td colspan='16' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($transfers as $f2p): ?>
		<tr>
			<?php
				$proper_funds_to_paycard_id = str_pad($f2p->funds_to_paycard_id, 8, "0", STR_PAD_LEFT);
			?>
			<td><?= $proper_funds_to_paycard_id ?></td>
			<td style='text-align:right;'><?= $f2p->member_id; ?></td>
			<?php
				// get member details
				$from_member_details = $this->members_model->get_member_by_id($f2p->member_id);
				
				$proper_member_name = $from_member_details->last_name . ", ". $from_member_details->first_name . " " . $from_member_details->middle_name;
			?>
			<td><?= $proper_member_name ?></td>
			<td style='text-align:right;'><?= $f2p->gross; ?></td>			
			<td style='text-align:right;'><?= $f2p->wtax; ?></td>	
			<td style='text-align:right;'><?= $f2p->net_of_wtax; ?></td>	
			<td style='text-align:right;'><?= $f2p->final_commission; ?></td>
			<td style='text-align:right;'><?= $f2p->cash_card; ?></td>
			<td><?= $f2p->insert_timestamp; ?></td>							
			<?php
				if  ($f2p->status == 'PENDING')  {
					$status_label =  "<span class='status label'>{$f2p->status}</span>";
				} else if (($f2p->status == 'CANCELLED') || ($f2p->status == 'VOID')) {
					$status_label =  "<span class='status label label-important'>{$f2p->status}</span>";
				} else if ($f2p->status == 'PROCESSING') {
					$status_label =  "<span class='status label label-warning'>{$f2p->status}</span>";
				} else {
					$status_label =  "<span class='status label label-success'>{$f2p->status}</span>";
				}
			?>
			
			<?php
				$payout_period = $f2p->start_date . ' to ' . $f2p->end_date; 
			?>
			
			<td><?= $status_label; ?></td>
			<td><?= $payout_period; ?></td>
			<td>
				<?php				
				if ($f2p->status == 'PROCESSED') {
				
					//echo "
					//		<a class='btn btn-small btn-primary transfer-voucher-btn' data='{$f2p->funds_to_paycard_id}' title='View'><i class='icon-info-sign icon-white'></i></a>";
				} 
				
				if (($f2p->status == 'PENDING') || ($f2p->status == 'PROCESSING')) {
				
					echo "												
							<a class='btn btn-small btn-success update-f2p-btn' data='{$f2p->funds_to_paycard_id}' title='Update'><i class='icon-ok icon-white'></i></a>
							<a class='btn btn-small btn-danger cancel-f2p-btn' data='{$f2p->funds_to_paycard_id}' title='Cancel'><i class='icon-remove icon-white'></i></a>";
				}					
				?>
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

	$('.cancel-f2p-btn').live("click",function() {
		funds_to_paycard_id = $(this).attr("data");
		
		b.request({
			url : '/network/fundstopaycard/cancel_transfer',
			data : {
					"funds_to_paycard_id" : funds_to_paycard_id
					},
					
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var withPendingCancelModal = b.modal.new({
						title: 'Funds To Paycard :: Cancel',
						width:400,
						disableClose: true,
						html: data.data.html,
						buttons: {								
							'Proceed' : function() {									
								if (data.status == "1")	{
									withPendingCancelModal.hide();	
									b.request({
										url : '/network/fundstopaycard/proceed_cancel_f2p',
										data : {
											'funds_to_paycard_id' : funds_to_paycard_id
											},										
										on_success : function(data) {
											
											if (data.status == "1")	{
											
												var cancelTransferModal = b.modal.new({
													title: 'Funds To Paycard :: Cancelled',
													width: 450,
													disableClose: true,
													html: data.data.html,
													buttons: {																
														'Ok' : function() {
															cancelTransferModal.hide();
															redirect('/members/fundstopaycard');
														}
													}
												});
												cancelTransferModal.show();
												
											
											} else {
												var errorCancelTransferModal = b.modal.new({
													title: 'Funds To Paycard :: Error',
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
										title: 'Funds To Paycard :: Error',
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
						title: 'Funds To Paycard :: Error',
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
			<label for="status_select">Status: </label>\n<div class="form-inline">\n<div class="input-append"><select name="status_select" id="status_select" style="width:150px;" value="">\
			<option value="ALL">All</option>\
			<option value="PENDING">Pending</option>\
			<option value="PROCESSING">Processing</option>\
			<option value="PROCESSED">Processed</option>\
			<option value="CANCELLED">Cancelled</option>\
		</select><br/>\n',
			buttons:{
				"Proceed": function(e){
					var this_button = $("#"+$(download_modal).attr("id")+"_btn_proceed");
					//var start_date = $("#"+$(download_modal).attr("id")+" #start_date").val();
					var start_date =  $("#"+$(download_modal).attr("id")+" #withdraw_start_date").val();
					//var end_date = $("#"+$(download_modal).attr("id")+" #end_date").val();
					var end_date = $("#"+$(download_modal).attr("id")+" #withdraw_end_date").val();
					var status_select = $("#"+$(download_modal).attr("id")+" #status_select").val();
					
					//alert(start_date + '-' + end_date);
					
					if(!$(this_button).hasClass("no_clicking"))
					{
						$(this_button).addClass("no_clicking");

						b.request({
							url: "/admin/funds_to_paycard/download_check",
							data: {
								"start_date": start_date,
								"end_date": end_date,
								"status_select": status_select
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
														url: "/admin/funds_to_paycard/download_proceed",
														data: {
															"start_date": start_date,
															"end_date": end_date,
															"status_select": status_select
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
																	title: "Download Request",
																	html: "<p>"+data.msg+"</p>",
																	disableClose: true,
																	buttons:{
																		"Cancel": function(){
																			download_xls_modal.hide();
																		},
																		"Download": function(){
																			download_xls_modal.hide();

																			redirect('/admin/funds_to_paycard/export_xls/'+ start_date +'/' + end_date +'/' + status_select);																			
																			
																			// show add form modal					
																			dloadModal = b.modal.new({
																				title: 'Download Request :: Successful',
																				width:450,
																				disableClose: true,
																				html: 'Download successful. Please check the downloaded excel file.',
																				buttons: {
																					'Ok' : function() {
																						dloadModal.hide();
																						redirect('/admin/funds_to_paycard');
																					}																														
																				}
																			});
																			dloadModal.show();																			
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
		$("#search_error").hide(); 
		$("#search_summary").show();           
		
		return false;
	});
	
	$("#button_refresh").live("click",function() {
		windows.location.href = '/admin/transfers';
	});
	
	$(".cancel-transfer-btn").click(function(){
		var funds_to_paycard_id = $(this).attr("data");
	
		b.request({
			url: "/admin/transfers/confirm_cancel",
			data: {
				"funds_to_paycard_id" : funds_to_paycard_id
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
										executeCancel(funds_to_paycard_id, _remarks);										
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
	
	$('.update-f2p-btn').live("click",function() {
		funds_to_paycard_id = $(this).attr("data");
		
		b.request({
			url : '/network/fundstopaycard/update_transfer',
			data : {
					"funds_to_paycard_id" : funds_to_paycard_id
					},
					
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var withPendingUpdateModal = b.modal.new({
						title: 'Funds To Paycard :: Update',
						width:400,
						disableClose: true,
						html: data.data.html,
						buttons: {							
							'Cancel' : function() {
								withPendingUpdateModal.hide();								 							
							}, 
							'Proceed' : function() {									
								if (data.status == "1")	{
									var _update_to_option = ($('#update_to_option').val());
									withPendingUpdateModal.hide();	
									b.request({
										url : '/network/fundstopaycard/proceed_update_f2p',
										data : {
											'funds_to_paycard_id' : funds_to_paycard_id,
											'update_to_option' : _update_to_option,									
											},										
										on_success : function(data) {
											
											if (data.status == "1")	{
											
												var cancelTransferModal = b.modal.new({
													title: 'Funds To Paycard :: Update to Processed',
													width: 450,
													disableClose: true,
													html: data.data.html,
													buttons: {																
														'Ok' : function() {
															cancelTransferModal.hide();
															redirect('/members/fundstopaycard');
														}
													}
												});
												cancelTransferModal.show();
												
											
											} else {
												var errorCancelTransferModal = b.modal.new({
													title: 'Funds To Paycard :: Error',
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
										title: 'Funds To Paycard :: Error',
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
						}
					});
					
					withPendingUpdateModal.show();	
					
				} else {
					// show add form modal					
					withPendingCancelModal = b.modal.new({
						title: 'Funds To Paycard :: Error',
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
	
	var executeCancel = function(funds_to_paycard_id, remarks) {
				
		// proceed with transfer
		// ajax request
		b.request({
			url : '/admin/transfers/proceed_cancel',
			data : {				
				'funds_to_paycard_id' : funds_to_paycard_id,
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

	function upload_processed_xls() {
		var upload_modal = b.modal.new({});
		var upload_html = "<div id='response_upload' style='display:none;text-align:center;font-weight:16px;font-weight:bold;margin-bottom:25px;'>Excel file has been successfully processed.</div><div id='fileUpload' class='uploadBox_fu'></div>";
		upload_modal.init({
			title: "Upload",
			width: 500,
			html: upload_html,
			disableClose : true,
			buttons : {
				'Close' : function() {
					upload_modal.hide();
				}
			}
		
		});

		upload_modal.show();

  		// uploader
		$("#"+$(upload_modal).attr("id")+' #fileUpload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.xls','.xlsx'],
			target : base_url+'/admin/funds_to_paycard/upload',
			onComplete: function() {
				//$("#"+$(upload_modal).attr("id")+" #response_upload").show();
				successfulModal = b.modal.new({
					title: 'Upload Funds to Paycard :: Successful',
					width:450,
					disableClose: true,
					html: 'Upload Successful!',
					buttons: {
						'Ok' : function() {
							successfulModal.hide();
							redirect('admin/funds_to_paycard');
						}
					}
				});
				successfulModal.show();	
			}
		});
		
	}

	$("#upload").click(function(){
		upload_processed_xls();		
	});	

	
</script>