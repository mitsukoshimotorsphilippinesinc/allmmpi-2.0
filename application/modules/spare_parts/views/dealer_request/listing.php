<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Request List<a href='/spare_parts/dealer_request/add' class='btn btn-small btn-default'id="add-btn" style="float:right;margin-right:-30px;margin-top:5px;" title='Add New'><i class='icon-plus'></i>&nbsp;Add New</a>&nbsp;&nbsp;<a class='btn btn-small btn-default'id="download-btn" style="float:right;margin-top:5px;" title='Download' disabled="disabled"><i class='icon-download'></i>&nbsp;Download Result</a></h2></div>

<br>

<div >
	<form id='search_details' method='get' action =''>

		<strong>Status:&nbsp;</strong>
		<select name="search_status" id="search_status" style="width:150px;margin-left:20px" value="<?= $search_status ?>">
			<option value="ALL">ALL</option>						
			<option value="APPROVED">APPROVED</option>
			<option value="CANCELLED">CANCELLED</option>
			<option value="CANCELLED (COMPLETED">CANCELLED (COMPLETED)</option>
			<option value="COMPLETED">COMPLETED</option>
			<option value="DENIED">DENIED</option>
			<option value="DENIED (COMPLETED)">DENIED (COMPLETED)</option>			
			<option value="FORWARDED">FORWARDED</option>
			<option value="FOR APPROVAL">FOR APPROVAL</option>
			<option value="PENDING">PENDING</option>
			<option value="PROCESSING">PROCESSING</option>					
		</select>  
	
		<br/>

		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">
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
			<th style=''>Request Code</th>
			<th>Status</th>
			<th style='width:200px;'>Requested By</th>
			<th style='width:50px;'>Number of Items</th>
			<th style='width:100px;'>Total Amount</th>
			<th style=''>Agent Name</th>			
			<th style='width:70px;'>Date Created</th>			
			<th style='width:150px;'>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($transfers)):?>
		<tr><td colspan='9' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($transfers as $t): ?>
		<tr>
									
			<td><?= $t->request_code; ?></td>
			
			<?php
			$status_class = strtolower(trim($t->status));			
			$status_class = str_replace(" ", "-", $status_class);
		
			echo "<td><span class='label label-" . $status_class . "' >{$t->status}</span></td>";
			//if ($t->status == 'PENDING') {
			//	echo "<td><span class='label label-important' >{$t->status}</span></td>";
			//} else if ($t->status == 'FORWARDED') {
			//	echo "<td><span class='label label-info' >{$t->status}</span></td>";
			//} else if ($t->status == 'FOR APPROVAL') {
			//	echo "<td><span class='label label-warning' >{$t->status}</span></td>";
			//} else {
			//	echo "<td><span class='label label-success' >{$t->status}</span></td>";
			//}			

			// get requestor details
			$requestor_details = $this->spare_parts_model->get_dealer_by_id($t->dealer_id);

			if (count($requestor_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$requestor_details->complete_name}</td>"; 
			}			

			// agent name 
			$agent_name = "N/A";
			$agent_details = $this->spare_parts_model->get_agent_by_id($t->agent_id);
			if (count($agent_details) > 0) {
				$agent_name = $agent_details->complete_name;
			}	

			// get number of items
			$where = "dealer_request_id = " . $t->dealer_request_id . " AND status IN ('PENDING', 'COMPLETED')";
			$dealer_request_detail_info = $this->spare_parts_model->get_dealer_request_detail($where);

			$total_items = 0;
			foreach ($dealer_request_detail_info as $wrdi) {
				$total_items = $total_items + ($wrdi->good_quantity + $wrdi->bad_quantity);
			}
			$total_items = number_format($total_items);

			echo "<td  style='text-align:right;'>{$total_items}</td>";

			// total amount
			$where = "status IN ('PENDING') AND dealer_request_id = " . $t->dealer_request_id;
			$dealer_request_details = $this->spare_parts_model->get_dealer_request_detail($where);

			$total_amount = 0;
			if (count($dealer_request_details) > 0) {
				foreach ($dealer_request_details as $sdd) {
					$total_amount = $total_amount + $sdd->total_amount;
				}
			}
			?>	
			<td style='text-align:right'><?= number_format($total_amount, 2); ?></td>
			<td><?= $agent_name; ?></td>		
			<td><?= $t->insert_timestamp; ?></td>

			

			<td data1="<?= $t->dealer_request_id ?>" data2="<?= $t->request_code ?>">				
				<a class='btn btn-small btn-info view-details' data='info' title="View Details"><i class="icon-white icon-list"></i></a>	
				<?php
				if ($t->status == 'PENDING') {
					echo "<a class='btn btn-small btn-warning process-btn' data='for approval' title='For Approval'><i class='icon-white icon-file'></i></a>
							<a class='btn btn-small btn-danger process-btn' data='cancel' title='Cancel'><i class='icon-white icon-remove'></i></a>
						";
				}

				if ($t->status == 'APPROVED') {
					echo "<a class='btn btn-small btn-success process-btn' data='forward to warehouse' title='Forward to Warehouse'><i class='icon-white icon-home'></i></a>";
				}

				if ($t->status == 'COMPLETED') {
					
					if (($t->purchase_order_number == 0) || ($t->purchase_order_number == NULL)) {
						echo "<a class='btn btn-small btn-primary process-btn' data='assign po' title='Assign P.O. Number'><i class='icon-white icon-pencil'></i></a>						
								<a class='btn btn-small btn-primary process-btn' data='cancel completed' title='Cancel Override'><i class='icon-white icon-remove'></i></a>";

					} else {
						echo "<a href='/spare_parts/display_po/" . $t->request_code . "' target = '_blank' class='btn btn-small btn-success print-mtr' data='print mtr' title='Print P.O.' data='<?= $t->request_code ?>'><i class='icon-white icon-print'></i></a>
								<a class='btn btn-small btn-primary process-btn' data='cancel completed' title='Cancel Override'><i class='icon-white icon-remove'></i></a>";
					}
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

	$(document).ready(function(){
                		
		var _search_by = '<?= $search_by; ?>';
		$("#search_option").val(_search_by);

		var _search_status = '<?= $search_status; ?>';
		$("#search_status").val(_search_status);             

	});


	$(".process-btn").click(function(){

		processButtonAction($(this).parent().attr("data1"), $(this).parent().attr("data2"), $(this).attr("data"));
	
	});

	var processButtonAction = function(dealer_request_id, dealer_request_code, listing_action) {

		b.request({
			url: "/spare_parts/dealer_request/for_listing_confirm",
			data: {
				'dealer_request_id' : dealer_request_id,
				'dealer_request_code' : dealer_request_code,
				'listing_action' : listing_action,
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

								if (listing_action == 'cancel') {
									
									if ($.trim($("#txt-remarks").val()) == "") {
										$("#error-reasonremarks").show();
										return;
									}
								}	
								$("#error-reasonremarks").hide();

								if (listing_action == 'assign po') {
									
									if ($.trim($("#txt-mtrnumber").val()) == "") {
										$("#error-mtrnumber").show();
										return;
									}
								}	
								$("#error-reasonremarks").hide();

								// ajax request
								b.request({
									url : '/spare_parts/dealer_request/for_listing_proceed',
									data : {				
										'dealer_request_id' : dealer_request_id,
										'dealer_request_code' : dealer_request_code,
										'listing_action' : listing_action,
										'remarks' : $("#txt-remarks").val(),
										'purchase_order_number' : $("#txt-mtrnumber").val(),
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
														redirect('spare_parts/dealer_request/listing');
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
		var dealer_request_id = $(this).parent().attr("data1");
		var dealer_request_code = $(this).parent().attr("data2");
		var listing_action = $(this).attr("data");
	
		b.request({
			url: "/spare_parts/dealer_request/view_details",
			data: {
				"dealer_request_id" : dealer_request_id,
				"dealer_request_code" : dealer_request_code,
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
									processButtonAction(dealer_request_id, dealer_request_code, 'cancel');
								},
								'For Approval' : function() {
									processButtonAction(dealer_request_id, dealer_request_code, 'for approval');
								}
								//'Edit' : function() {
								//	//processButtonAction(dealer_request_id, dealer_request_code, 'edit');
								//	redirect("/spare_parts/dealer_request/edit/" + dealer_request_id);
								//}									
							}
						});			
					} else if (data.data.request_status == "APPROVED") {
						viewDetailsModal = b.modal.new({
							title: data.data.title,
							width:800,
							//disableClose: true,
							html: data.data.html,
							buttons: {
								'Forward To Warehouse' : function() {
									processButtonAction(dealer_request_id, dealer_request_code, 'forward to warehouse');
								}									
							}
						});
					} else if (data.data.request_status == "COMPLETED") {						
						viewDetailsModal = b.modal.new({
							title: data.data.title,
							width:800,							
							html: data.data.html,
							buttons: {
								'Reprocess Items' : function() {
									redirect("/spare_parts/dealer_request/reprocess_items/" + dealer_request_id);
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

		download_modal.init({

			title: "Download Warehouse Requests",
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
							url: "/spare_parts/dealer_request/download_check",
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
														url: "/spare_parts/dealer_request/download_proceed",
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
																																		
																			redirect('/spare_parts/dealer_request/export_xls/'+ start_date +'/' + end_date);
																
																			
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