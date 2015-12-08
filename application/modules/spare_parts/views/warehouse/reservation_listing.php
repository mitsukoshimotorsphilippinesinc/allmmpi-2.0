<?php
	//$breadcrumb_container = assemble_breadcrumb();
?>

<!--?= $breadcrumb_container; ?-->
<div class='alert alert-danger'><h2>Reservation<a class='btn btn-small btn-default'id="download-btn" style="float:right;margin-top:5px;display:none;" title='Download'><i class='icon-download' disabled="disabled"></i>&nbsp;Download Result</a></h2></div>

<br>

<div >
	<form id='search_details' method='get' action =''>

		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:250px;" value="<?= $search_by ?>">
			<option value="request_code">Code</option>
			<option value="name">Name</option>
		</select>                 

		<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

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
		
			<span class="label label-important">Search Results for:</span>
			<span class="label label-default"><?= $search_status ?></span>
			<span class="label label-default"><?= $search_by ?></span>
			<span class="label label-default"><?= $search_text ?></span>
		</div>		
	</form>
</div>

<table class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th style='width:80px;'>Request Code</th>
			<th>Status</th>
			<th style=''>Requested By</th>			
			<th style='width:50px;'>Total Items</th>			
			<th style='width:70px;'>Date Created</th>
			<th style='width:118px;'>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($transfers)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($transfers as $t): ?>
		<tr>
									
			<td><?= $t->transaction_number; ?></td>
			
			<?php									

			$status_class = strtolower(trim($t->status));			

			if (substr($status_class, 0, 12) == "cancellation") {
				$status_class = substr($status_class, 13);
			}

			$status_class = str_replace(" ", "-", $status_class);
		
			echo "<td style='text-align:center'><span class='label label-" . $status_class . "' >{$t->status}</span></td>";

			$request_summary_details = $this->spare_parts_model->get_request_summary_by_code($t->transaction_number);


			// get requestor details
			$id = str_pad($request_summary_details->id_number, 7, '0', STR_PAD_LEFT);
			$requestor_details = $this->human_relations_model->get_employment_information_view_by_id($id);

			if (count($requestor_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$requestor_details->complete_name}</td>"; 
			}			

			// get number of items
			$where = "request_summary_id = " . $request_summary_details->request_summary_id . " AND status IN ('PENDING')";
			$detail_info = $this->spare_parts_model->get_request_detail($where);

			$total_items = 0;
			$total_items = count($detail_info);			
			$total_items = number_format($total_items);

			echo "<td  style='text-align:right;'>{$total_items}</td>";
			?>			

			<td><?= $t->insert_timestamp; ?></td>

			<td data1="<?= $request_summary_details->request_summary_id ?>" data2="<?= $request_summary_details->request_code ?>">				
				<a class='btn btn-small btn-info view-details' data='info' title="View Details"><i class="icon-white icon-list"></i></a>	
				<?php
				if ($t->status == 'PENDING') {
					echo "<a class='btn btn-small btn-warning process-btn' data='assign_runner' title='Process Reservation'><i class='icon-white icon-star-empty'></i></a>							
						";
				}

				if ($t->status == 'PROCESSING') {
					echo "<a class='btn btn-small btn-success process-btn' data='set_completed' title='Process Completion'><i class='icon-white icon-star'></i></a>						
					";
				}

				?>

				<a class='btn btn-small btn-danger process-btn' data='cancel_request' title='Cancel'><i class='icon-white icon-remove'></i></a>

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

	$(document).ready(function(){
                		
		var _search_by = '<?= $search_by; ?>';
		$("#search_option").val(_search_by);

		var _search_status = '<?= $search_status; ?>';
		$("#search_status").val(_search_status);             

	});

	
	$(".process-btn").click(function(){
		processButtonAction($(this).parent().attr("data1"), $(this).parent().attr("data2"), $(this).attr("data"));	
	});

	var processButtonAction = function(request_summary_id, request_code, process_action) {

		b.request({
			url: "/spare_parts/warehouse/initial_process",
			data: {
				'request_summary_id' : request_summary_id,
				'request_code' : request_code,
				'process_action' : process_action,
			},
			on_success: function(data){

				if (data.status == "1")	{
				
					// show add form modal					
					approveRequestModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Cancel' : function() {
								approveRequestModal.hide();								 							
							},
							'Proceed' : function() {

								$("#error-runner").hide();
									
								if ($("#runner_name").val() == 0) {
									$("#error-runner").show();
									return;
								}

								$("#error-reasonremarks").hide();

								// ajax request
								b.request({
									url : '/spare_parts/warehouse/proceed_request',
									data : {				
										'request_summary_id' : request_summary_id,
										'request_code' : request_code,
										'process_action' : process_action,
										'runner_id' : $("#runner_name").val(),										
									},
									on_success : function(data) {
										
										if (data.status == "1")	{
											approveRequestModal.hide();
											
											// show add form modal					
											proceedApproveRequestModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {
													'Ok' : function() {
														proceedApproveRequestModal.hide();
														redirect('spare_parts/warehouse/reservation');
													}
												}
											});
											proceedApproveRequestModal.show();

											
										} else {
											// show add form modal
											approveRequestModal.hide();					
											errorApproveRequestModal = b.modal.new({
												title: data.data.title,
												width:450,	
												html: data.data.html,
											});
											errorApproveRequestModal.show();	

										}
									}

								})
								return false;
								
							}									
						}
					});
					approveRequestModal.show();
					
				} else {
					// show add form modal					
					var errorApproveRequestModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Close' : function() {
								errorApproveRequestModal.hide();								 							
							}
						}
					});
					errorApproveRequestModal.show();		
				}
			}	
				
		})
		return false;
	}
	
	$(".view-details").click(function(){
		var salary_deduction_id = $(this).parent().attr("data1");
		var salary_deduction_code = $(this).parent().attr("data2");
		var listing_action = $(this).attr("data");
	
		b.request({
			url: "/spare_parts/salary_deduction/view_details",
			data: {
				"salary_deduction_id" : salary_deduction_id,
				"salary_deduction_code" : salary_deduction_code,
				"listing_action" : listing_action,				
			},
			on_success: function(data){
				if (data.status == "1")	{
				
					// show add form modal
					if (data.data.request_status == "PENDING")	{	
						viewDetailsModal = b.modal.new({
							title: data.data.title,
							width:800,
							//disableClose: true,
							html: data.data.html,
							buttons: {
								'Cancel' : function() {
									processButtonAction(salary_deduction_id, salary_deduction_code, 'cancel');
								},
								'For Approval' : function() {
									processButtonAction(salary_deduction_id, salary_deduction_code, 'for approval');
								},
								'Edit' : function() {
									//processButtonAction(salary_deduction_id, salary_deduction_code, 'edit');
									redirect("/spare_parts/salary_deduction/edit/" + salary_deduction_id);
								}									
							}
						});			
					} else if (data.data.request_status == "APPROVED") {
						viewDetailsModal = b.modal.new({
							title: data.data.title,
							width:800,							
							html: data.data.html,
							buttons: {
								'Forward To Warehouse' : function() {
									processButtonAction(salary_deduction_id, salary_deduction_code, 'forward to warehouse');
								}									
							}
						});
					} else if (((data.data.request_status).substr(0, 9)) == "COMPLETED") {
						viewDetailsModal = b.modal.new({
							title: data.data.title,
							width:800,							
							html: data.data.html,
							buttons: {
								'Reprocess Items' : function() {
									redirect("/spare_parts/salary_deduction/reprocess_items/" + salary_deduction_id);
								}									
							}
						});
					} else {
						viewDetailsModal = b.modal.new({
							title: data.data.title,
							width:800,
							//disableClose: true,
							html: data.data.html,  
						});
					}

					viewDetailsModal.show();
				} else {
					// show add form modal					
					var errorViewDetailsModal = b.modal.new({
						title: data.data.title,
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
		return false;			
	});
	
	$("#download-btn").click(function(){
		var download_modal = b.modal.new({});
		var years = "";
		var months = "";
		var days = "";

		var _search_status = '<?= $search_status ?>';
		var _search_by = '<?= $search_by ?>';
		var _search_text = '<?= $search_text ?>';

		download_modal.init({

			title: "Download Salary Deductions",
			width: 350,
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
							url: "/spare_parts/salary_deduction/download_check",
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
														url: "/spare_parts/salary_deduction/download_proceed",
														data: {
															"start_date": start_date,
															"end_date": end_date,															
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
																																		
																			redirect('/spare_parts/salary_deduction/export_xls/'+ start_date +'/' + end_date +'/' + _search_status +'/' + _search_by +'/' + _search_text);
																
																			
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