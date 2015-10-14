<div class='alert alert-info'><h2>Payout Periods<a id="download" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Download</span></a><a id="add-btn" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Add New</span></a></h2></div>

<br>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style=''>Start Date</th>
			<th style=''>End Date</th>
			<th>Status</th>
			<th style=''>Type</th>
			<th style=''>Is Official</th>
			<th style=''>Insert Timestamp</th>
			<th style=''>Re-run Status</th>
			<th style=''></th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($transfers)):?>
		<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($transfers as $t): ?>
		<tr>
						
			<td><?= $t->start_date; ?></td>
			<td><?= $t->end_date; ?></td>
			
			<?php
			if ($t->status == 'ACTIVE') {
				echo "<td><span class='label label-important' >{$t->status}</span></td>";
			} else if ($t->status == 'PROCESSING') {
				echo "<td><span class='label label-info' >{$t->status}</span></td>";
			} else if ($t->status == 'CANCELLED') {
				echo "<td><span class='label label-warning' >{$t->status}</span></td>";
			} else {
				echo "<td><span class='label label-success' >{$t->status}</span></td>";
			}			
			?>	
			<td><?= $t->payout_type; ?></td>
			<td><?= $t->is_official; ?></td>
			<td><?= $t->insert_timestamp; ?></td>

			<?php
			if ($t->rerun_status == 'ACTIVE') {
				echo "<td><span class='label label-important' >{$t->rerun_status}</span></td>";
			} else if ($t->rerun_status == 'PROCESSING') {
				echo "<td><span class='label label-info' >{$t->rerun_status}</span></td>";
			} else if ($t->rerun_status == 'COMPLETED') {
				echo "<td><span class='label label-warning' >{$t->rerun_status}</span></td>";
			} else {
				echo "<td><span class='label label-success' >{$t->rerun_status}</span></td>";
			}			
			?>

			<td>
				<a class='btn btn-small btn-primary info-btn' data="<?= $t->payout_period_id ?>" title="View"><i class="icon-info-sign icon-white"></i>&nbsp;Info</a>
				
				<?php				
				if (($t->status == 'ACTIVE') || ($t->status == 'COMPLETED')) {
				
					echo "<a class='btn btn-small btn-primary edit-btn' data='{$t->payout_period_id}' title='Edit'><i class='icon-list-alt icon-white'></i>&nbsp;Edit</a>";
				}				
				?>
				<?php
				if (($t->status == 'COMPLETED') && ($t->rerun_status != 'PROCESSING')) {
				
					echo "<a class='btn btn-small btn-success rerun-btn' data='{$t->payout_period_id}' title='Re-Run'><i class='icon-play icon-white'></i>&nbsp;Re-run</a>";
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
	
	$(".edit-btn").click(function(){
		var payout_period_id = $(this).attr("data");
	
		b.request({
			url: "/admin/payout_periods/edit",
			data: {
				"payout_period_id" : payout_period_id
			},
			on_success: function(data){
				if (data.status == "1")	{
				
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
							'Confirm Changes' : function() {
								
								if ($.trim($("#remarks").val()) == "") {
									$("#error_remarks").show();					
								} else {											
									//editPayoutSchedule(payout_period_id, $("#payout_type").val(),$("#start-date").val(),$("#end-date").val(),$("#status").val(),$("#is_official").val(), $("#remarks").val());										
									//withPendingModal.hide();

									// ajax request
									b.request({
										url : '/admin/payout_periods/confirm_edit',
										data : {				
											'payout_period_id' : payout_period_id,
											'payout_type' : $("#payout_type").val(),
											'start_date' : $("#start-date").val(),
											'end_date' : $("#end-date").val(),
											'payout_status' : $("#status").val(),
											'is_official' : $("#is_official").val(),
											'remarks' : $("#remarks").val()
										},
										on_success : function(data) {
											
											if (data.status == "1")	{
												withPendingModal.hide();
												
												// show add form modal					
												confirmEditModal = b.modal.new({
													title: data.data.title,
													width:450,
													disableClose: true,
													html: data.data.html,
													buttons: {
														'Cancel' : function() {
															confirmEditModal.hide();								 							
														},	
														'Proceed' : function() {
															confirmEditModal.hide();

															// proceed with transfer
															// ajax request
															b.request({
																url : '/admin/payout_periods/proceed_edit',
																data : {				
																	'payout_period_id' : data.data.data.payout_period_id,
																	'payout_type' : data.data.data.payout_type,
																	'start_date' : data.data.data.start_date,
																	'end_date' : data.data.data.end_date,
																	'payout_status' : data.data.data.payout_status,
																	'is_official' : data.data.data.is_official,
																	'remarks' : data.data.data.remarks
																},
																on_success : function(data) {
																	
																	if (data.status == "1")	{
																		// show add form modal					
																		proceedEditModal = b.modal.new({
																			title: data.data.title,
																			width:450,
																			disableClose: true,
																			html: data.data.html,
																			buttons: {
																				'Ok' : function() {
																					proceedEditModal.hide();
																					redirect('admin/payout_periods');
																				}
																			}
																		});
																		proceedEditModal.show();		
																		
																	} else {
																		// show add form modal					
																		errorEditModal = b.modal.new({
																			title: data.data.title,
																			width:450,
																			disableClose: true,
																			html: data.data.html,
																			buttons: {						
																				'Close' : function() {
																					errorEditModal.hide();								 							
																				}
																			}
																		});
																		errorEditModal.show();				
																	}
																}
															})									
														}
													}
												});
												confirmEditModal.show();		
												
											} else {
												// show add form modal					
												errorEditModal = b.modal.new({
													title: data.data.title,
													width:450,
													disableClose: true,
													html: data.data.html,
													buttons: {						
														'Close' : function() {
															errorEditModal.hide();								 							
														}
													}
												});
												errorEditModal.show();				
											}
										}
									})
									return false;


								}
							}									
						}
					});
					withPendingModal.show();
				} else {
					// show add form modal					
					var errorPendingModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorPendingModal.hide();								 							
							}
						}
					});
					errorPendingModal.show();		
				}
			}	
				
		})
		return false;			
	});
	
	
	
	$("#add-btn").click(function(){
	
		b.request({
			url: "/admin/payout_periods/add",
			data: {},
			on_success: function(data){
				if (data.status == "1")	{
				
					// show add form modal					
					var pendingAddModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								pendingAddModal.hide();								 							
							},
							'Confirm' : function() {	
							
								//addPayoutSchedule($("#payout_type").val(),$("#start-date").val(),$("#end-date").val(),$("#status").val(),$("#is_official").val(),$("#add_remarks").val());										
								
								// ajax request
								b.request({
									url : '/admin/payout_periods/confirm_add',
									data : {								
										'payout_type' : $("#payout_type").val(),
										'start_date' : $("#start-date").val(),
										'end_date' : $("#end-date").val(),
										'payout_status' : $("#status").val(),
										'is_official' : $("#is_official").val(),
										'remarks' : $("#add_remarks").val()
									},
									on_success : function(data) {
										
										if (data.status == "1")	{
											pendingAddModal.hide();
											// show add form modal					
											confirmAddModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {
													'Cancel' : function() {
														confirmAddModal.hide();								 							
													},	
													'Proceed' : function() {
														confirmAddModal.hide();

														// proceed with transfer
														// ajax request
														b.request({
															url : '/admin/payout_periods/proceed_add',
															data : {														
																'payout_type' : data.data.data.payout_type,
																'start_date' : data.data.data.start_date,
																'end_date' : data.data.data.end_date,
																'payout_status' : data.data.data.payout_status,
																'is_official' : data.data.data.is_official,
																'remarks' : data.data.data.remarks,
															},
															on_success : function(data) {
																
																if (data.status == "1")	{
																	// show add form modal					
																	proceedAddModal = b.modal.new({
																		title: data.data.title,
																		width:450,
																		disableClose: true,
																		html: data.data.html,
																		buttons: {
																			'Ok' : function() {
																				proceedAddModal.hide();
																				redirect('admin/payout_periods');
																			}
																		}
																	});
																	proceedAddModal.show();		
																	
																} else {
																	// show add form modal					
																	errorAddModal = b.modal.new({
																		title: data.data.title,
																		width:450,
																		disableClose: true,
																		html: data.data.html,
																		buttons: {						
																			'Close' : function() {
																				errorAddModal.hide();								 							
																			}
																		}
																	});
																	errorAddModal.show();				
																}
															}
														})									
													}
												}
											});
											confirmAddModal.show();		
											
										} else {
											// show add form modal					
											errorAddModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {						
													'Close' : function() {
														errorAddModal.hide();								 							
													}
												}
											});
											errorAddModal.show();				
										}
									}
								})
								return false;



							}									
						}
					});
					pendingAddModal.show();
				} else {
					// show add form modal					
					var errorAddModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorAddModal.hide();								 							
							}
						}
					});
					errorAddModal.show();		
				}
			}	
				
		})
		return false;			
	});
	
	$(".info-btn").click(function(){
		var payout_period_id = $(this).attr("data");
	
		b.request({
			url: "/admin/payout_periods/info",
			data: {
				"payout_period_id" : payout_period_id
			},
			on_success: function(data){
				if (data.status == "1")	{
				
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
				} else {
					// show add form modal					
					var errorPendingModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorPendingModal.hide();								 							
							}
						}
					});
					errorPendingModal.show();		
				}
			}	
				
		})
		return false;			
	});
	
	$(".rerun-btn").click(function(){
		var payout_period_id = $(this).attr("data");
	
		b.request({
			url: "/admin/payout_periods/confirm_rerun",
			data: {
				"payout_period_id" : payout_period_id
			},
			on_success: function(data){
				if (data.status == "1")	{
				
					// show add form modal					
					confirmRerunModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								confirmRerunModal.hide();								 							
							},
							'Proceed' : function() {
								confirmRerunModal.hide();
								proceedRerun(payout_period_id);								 							
							}									
						}
					});
					confirmRerunModal.show();
				} else {
					// show add form modal					
					var errorConfirmRerunModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorConfirmRerunModal.hide();								 							
							}
						}
					});
					errorConfirmRerunModal.show();		
				}
			}	
				
		})
		return false;			
	});

	var proceedRerun = function(_payoutPeriodID) {
		
		b.request({
			url: "/admin/payout_periods/proceed_rerun",
			data: {
				"payout_period_id" : _payoutPeriodID
			},
			on_success: function(data){
				if (data.status == "1")	{
				
					// show add form modal					
					proceedRerunModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								proceedRerunModal.hide();								 							
							}								
						}
					});
					proceedRerunModal.show();
				} else {
					// show add form modal					
					var errorProceedRerunModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorProceedRerunModal.hide();								 							
							}
						}
					});
					errorProceedRerunModal.show();		
				}
			}	
				
		})
		return false;	

	}


	/*
	var editPayoutSchedule = function(_payoutPeriodID, _payoutType, _startDate, _endDate, _payoutStatus, _isOfficial, _remarks) {

		// ajax request
		b.request({
			url : '/admin/payout_periods/confirm_edit',
			data : {				
				'payout_period_id' : _payoutPeriodID,
				'payout_type' : _payoutType,
				'start_date' : _startDate,
				'end_date' : _endDate,
				'payout_status' : _payoutStatus,
				'is_official' : _isOfficial,
				'remarks' : _remarks
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					
					// show add form modal					
					confirmEditModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditModal.hide();								 							
							},	
							'Proceed' : function() {
								confirmEditModal.hide();

								// proceed with transfer
								// ajax request
								b.request({
									url : '/admin/payout_periods/proceed_edit',
									data : {				
										'payout_period_id' : _payoutPeriodID,
										'payout_type' : _payoutType,
										'start_date' : _startDate,
										'end_date' : _endDate,
										'payout_status' : _payoutStatus,
										'is_official' : _isOfficial,
										'remarks' : _remarks
									},
									on_success : function(data) {
										
										if (data.status == "1")	{
											// show add form modal					
											proceedEditModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {
													'Ok' : function() {
														proceedEditModal.hide();
														redirect('admin/payout_periods');
													}
												}
											});
											proceedEditModal.show();		
											
										} else {
											// show add form modal					
											errorEditModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {						
													'Close' : function() {
														errorEditModal.hide();								 							
													}
												}
											});
											errorEditModal.show();				
										}
									}
								})									
							}
						}
					});
					confirmEditModal.show();		
					
				} else {
					// show add form modal					
					errorEditModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {						
							'Close' : function() {
								errorEditModal.hide();								 							
							}
						}
					});
					errorEditModal.show();				
				}
			}
		})
		return false;				
	}	
	*/
	
	/*
	var addPayoutSchedule = function(_payoutType, _startDate, _endDate, _payoutStatus, _isOfficial, _remarks) {

		// ajax request
		b.request({
			url : '/admin/payout_periods/confirm_add',
			data : {								
				'payout_type' : _payoutType,
				'start_date' : _startDate,
				'end_date' : _endDate,
				'payout_status' : _payoutStatus,
				'is_official' : _isOfficial,
				'remarks' : _remarks
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
				
					// show add form modal					
					confirmAddModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddModal.hide();								 							
							},	
							'Proceed' : function() {
								confirmAddModal.hide();

								// proceed with transfer
								// ajax request
								b.request({
									url : '/admin/payout_periods/proceed_add',
									data : {														
										'payout_type' : _payoutType,
										'start_date' : _startDate,
										'end_date' : _endDate,
										'payout_status' : _payoutStatus,
										'is_official' : _isOfficial,
										'remarks' : _remarks
									},
									on_success : function(data) {
										
										if (data.status == "1")	{
											// show add form modal					
											proceedAddModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {
													'Ok' : function() {
														proceedAddModal.hide();
														redirect('admin/payout_periods');
													}
												}
											});
											proceedAddModal.show();		
											
										} else {
											// show add form modal					
											errorAddModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {						
													'Close' : function() {
														errorAddModal.hide();								 							
													}
												}
											});
											errorAddModal.show();				
										}
									}
								})									
							}
						}
					});
					confirmAddModal.show();		
					
				} else {
					// show add form modal					
					errorAddModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {						
							'Close' : function() {
								errorAddModal.hide();								 							
							}
						}
					});
					errorAddModal.show();				
				}
			}
		})
		return false;				
	}
	*/	
	
	
	$("#download").click(function(){
		var download_modal = b.modal.new({});
		var years = "";
		var months = "";
		var days = "";

		download_modal.init({

			title: "Download Request Details",
			width: 300,
			html: '<label for="start_date">Start Date: </label>\n<div class="form-inline wc-date">\n<div class="input-append"><input type="text" class="input-medium" id="pp_start_date" name="pp_start_date" readonly="readonly" style="cursor:pointer;z-index:2050" /><span id="pp_start_date_icon" class="add-on" style="cursor:pointer;"><i class="icon-calendar"></i></span></div>\n</div>\n\
			<br>\n\
			<label for="end_date">End Date: </label>\n<div class="form-inline wc-date">\n<div class="input-append"><input type="text" class="input-medium" id="pp_end_date" name="pp_end_date" readonly="readonly" style="cursor:pointer;z-index:2050" /><span id="pp_end_date_icon" class="add-on" style="cursor:pointer;"><i class="icon-calendar"></i></span></div>\n</div>\n\
			<br/>\n',
			buttons:{
				"Proceed": function(e){
					var this_button = $("#"+$(download_modal).attr("id")+"_btn_proceed");
					var start_date =  $("#"+$(download_modal).attr("id")+" #pp_start_date").val();
					var end_date = $("#"+$(download_modal).attr("id")+" #pp_end_date").val();
		
					if(!$(this_button).hasClass("no_clicking"))
					{
						$(this_button).addClass("no_clicking");

						b.request({
							url: "/admin/payout_periods/download_check",
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
														url: "/admin/payout_periods/download_proceed",
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
																																		
																			redirect('/admin/payout_periods/export_xls/'+ start_date +'/' + end_date);
																
																			
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
	
		var currDate = new Date();
		var currYear = new Date().getFullYear();
		var yrRange = "2008:" + currYear;
		
		// from date
 		$("#pp_start_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		
		var _end_date = new Date();
		_end_date.setDate(_end_date.getDate() - 7);
		$("#pp_start_date").datepicker('setDate', _end_date);
		
		$("#pp_start_date_icon").click(function(e) {
			$("#pp_start_date").datepicker("show");
		});
		
		
		// end date
 		$("#pp_end_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		$("#pp_end_date").datepicker('setDate', '<?= date("Y-m-d") ?>');
		
		$("#pp_end_date_icon").click(function(e) {
			$("#pp_end_date").datepicker("show");
		});
		
	});
	
</script>