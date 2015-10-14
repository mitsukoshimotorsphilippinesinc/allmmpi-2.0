<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Request List<a href='/spare_parts/warehouse_claim/add' class='btn btn-small btn-default'id="add-btn" style="float:right;margin-right:-30px;margin-top:5px;" title='Add New'><i class='icon-plus'></i>&nbsp;Add New</a>&nbsp;&nbsp;<a class='btn btn-small btn-default'id="download-btn" style="float:right;margin-top:5px;display:none;" title='Download'><i class='icon-download' disabled="disabled"></i>&nbsp;Download Result</a></h2></div>

<br>

<div >
	<form id='search_details' method='get' action =''>

		<strong>Status:&nbsp;</strong>
		<select name="search_status" id="search_status" style="width:250px;margin-left:20px" value="<?= $search_status ?>">
			<option value="ALL">ALL</option>						
			<option value="APPROVED">APPROVED</option>
			<option value="CANCELLED">CANCELLED</option>
			<option value="CANCELLATION-APPROVED">CANCELLATION-APPROVED</option>
			<option value="CANCELLATION-DENIED">CANCELLATION-DENIED</option>
			<option value="CANCELLATION-COMPLETED">CANCELLATION-COMPLETED</option>
			<option value="CANCELLATION-FORWARDED">CANCELLATION-FORWARDED</option>
			<option value="CANCELLATION-FOR APPROVAL">CANCELLATION-FOR APPROVAL</option>
			<option value="COMPLETED">COMPLETED</option>
			<option value="COMPLETED-C">COMPLETED W/ CHARGE</option>
			<option value="COMPLETED-R">COMPLETED W/ RETURN</option>
			<option value="COMPLETED-RC">COMPLETED W/ RETURN AND CHARGE</option>
			<option value="DENIED">DENIED</option>			
			<option value="FORWARDED">FORWARDED</option>
			<option value="FOR APPROVAL">FOR APPROVAL</option>
			<option value="PENDING">PENDING</option>
			<option value="PROCESSING">PROCESSING</option>			
		</select>  
	
		<br/>

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
			<th style=''>Motor Brand/Model</th>
			<th style='width:50px;'>Total Items</th>
			<th style='width:100px;'>Warehouse</th>
			<th style=';'>Approved By (Warehouse)</th>			
			<th style='width:70px;'>Date Created</th>
			<th style='width:118px;'>Action</th>
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

			if (substr($status_class, 0, 12) == "cancellation") {
				$status_class = substr($status_class, 13);
			}

			$status_class = str_replace(" ", "-", $status_class);
		
			echo "<td><span class='label label-" . $status_class . "' >{$t->status}</span></td>";

			// get requestor details
			$id = str_pad($t->id_number, 7, '0', STR_PAD_LEFT);
			$requestor_details = $this->human_relations_model->get_employment_information_by_id($id);

			if (count($requestor_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$requestor_details->complete_name}</td>"; 
			}			

			// brand and model
			$motor_brand_model_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id($t->motorcycle_brand_model_id);				
			if (count($motor_brand_model_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$motor_brand_model_details->brand_name}" . " - " . "{$motor_brand_model_details->model_name}</td>"; 
			}				

			// get number of items
			$where = "warehouse_claim_id = " . $t->warehouse_claim_id . " AND status NOT IN ('CANCELLED', 'DELETED')";
			$warehouse_claim_detail_info = $this->spare_parts_model->get_warehouse_claim_detail($where);

			$total_items = 0;
			foreach ($warehouse_claim_detail_info as $wrdi) {
				$total_items = $total_items + ($wrdi->good_quantity + $wrdi->bad_quantity);
			}
			$total_items = number_format($total_items);

			echo "<td  style='text-align:right;'>{$total_items}</td>";

			// get warehouse detail			
			$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($t->warehouse_id);

			if (count($warehouse_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$warehouse_details->warehouse_name}</td>"; 
			}

			if (($t->warehouse_approved_by == 0) || ($t->warehouse_approved_by == '0')) {
				echo "<td>N/A</td>";
			} else {
				$id = str_pad($t->warehouse_approved_by, 7, '0', STR_PAD_LEFT);
				$warehouse_signatory_details = $this->human_relations_model->get_employment_information_view_by_id($id);
				echo "<td>{$warehouse_signatory_details->complete_name}</td>";
			}

			?>				
			<td><?= $t->insert_timestamp; ?></td>

			<td data1="<?= $t->warehouse_claim_id ?>" data2="<?= $t->request_code ?>">				
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

				if ($t->status == 'CANCELLATION-APPROVED') {
					echo "<a class='btn btn-small btn-success process-btn' data='cancellation-forward to warehouse' title='Forward to Warehouse'><i class='icon-white icon-home'></i></a>";
				}

				if ($t->status == 'COMPLETED') {
					if (($t->mtr_number == 0) || ($t->mtr_number == NULL)) {
						echo "<a class='btn btn-small btn-primary assign-mtr' data='assign mtr' title='Assign MTR Number'><i class='icon-white icon-pencil'></i></a>
								<a class='btn btn-small btn-primary process-btn' data='cancel completed' title='Cancel Override'><i class='icon-white icon-remove'></i></a>";
					} else {
						echo "<a href='/spare_parts/display_mtr/" . $t->request_code . "' target = '_blank' class='btn btn-small btn-success print-mtr' data='print mtr' title='Print MTR' data='<?= $t->request_code ?>'><i class='icon-white icon-print'></i></a>
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

	$(".assign-mtr").click(function(){

		var _request_id = $(this).parent().attr("data1");
		var _request_code = $(this).parent().attr("data2")

		b.request({
			url: "/spare_parts/load_assign_mtr",
			data: {
				'request_id' : _request_id,
				'request_code' : _request_code,				
			},
			on_success: function(data){

				if (data.status == "1")	{
					
					// show add form modal					
					confirmAssignMTRModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Cancel' : function() {
								confirmAssignMTRModal.hide();								
							},
							'Proceed' : function() {
								if ($.trim($("#txt-mtrnumber").val()) == "") {
									$("#error-mtrnumber").show();
									return;
								
								} else {									
									b.request({
										url : '/spare_parts/check_mtr',
										data : {
											'request_id' : _request_id,
											'request_code' : _request_code,																
											'mtr_number' : $("#txt-mtrnumber").val(),
										},
										on_success : function(data) {
											if (data.status == "1")	{													
												b.request({
													url : '/spare_parts/proceed_assign_mtr',
													data : {
														'request_id' : _request_id,
														'request_code' : _request_code,																
														'mtr_number' : $("#txt-mtrnumber").val(),
													},
													on_success : function(data) {
														if (data.status == "1")	{													
															// show add form modal
															confirmAssignMTRModal.hide();					
															proceedAssignMTRModal = b.modal.new({
																title: data.data.title,
																width:450,	
																html: data.data.html,
																buttons: {
																	'Ok' : function() {
																		proceedAssignMTRModal.hide();
																		redirect('spare_parts/warehouse_claim/listing');
																	}
																}
															});
															proceedAssignMTRModal.show();
														} else {
															confirmAssignMTRModal.hide();					
															errorAssignMTRModal = b.modal.new({
																title: data.data.title,
																width:450,	
																html: data.data.html,
															});
															errorAssignMTRModal.show();
														}												
													} 
												})
											} else {
												$("#error-mtrnumber").text(data.data.html);
												$("#error-mtrnumber").show();												
												return;
											}												
										} 
									})

								}
							}
						}
					});
					confirmAssignMTRModal.show();

					
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
		
	});
	



	$(".process-btn").click(function(){
		processButtonAction($(this).parent().attr("data1"), $(this).parent().attr("data2"), $(this).attr("data"));	
	});

	var processButtonAction = function(warehouse_claim_id, warehouse_claim_code, listing_action) {

		b.request({
			url: "/spare_parts/warehouse_claim/for_listing_confirm",
			data: {
				'warehouse_claim_id' : warehouse_claim_id,
				'warehouse_claim_code' : warehouse_claim_code,
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

								$("#error-reasonremarks").hide();


								if (listing_action == 'cancel') {
									
									if ($.trim($("#txt-remarks").val()) == "") {
										$("#error-reasonremarks").show();
										return;
									}
								}	
								$("#error-reasonremarks").hide();
								
								// ajax request
								b.request({
									url : '/spare_parts/warehouse_claim/for_listing_proceed',
									data : {				
										'warehouse_claim_id' : warehouse_claim_id,
										'warehouse_claim_code' : warehouse_claim_code,
										'listing_action' : listing_action,
										'remarks' : $("#txt-remarks").val(),
										//'mtr_number' : $("#txt-mtrnumber").val(),
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
														redirect('spare_parts/warehouse_claim/listing');
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
		var warehouse_claim_id = $(this).parent().attr("data1");
		var warehouse_claim_code = $(this).parent().attr("data2");
		var listing_action = $(this).attr("data");
	
		b.request({
			url: "/spare_parts/warehouse_claim/view_details",
			data: {
				"warehouse_claim_id" : warehouse_claim_id,
				"warehouse_claim_code" : warehouse_claim_code,
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
									processButtonAction(warehouse_claim_id, warehouse_claim_code, 'cancel');
								},
								'For Approval' : function() {
									processButtonAction(warehouse_claim_id, warehouse_claim_code, 'for approval');
								},
								'Edit' : function() {
									//processButtonAction(warehouse_claim_id, warehouse_claim_code, 'edit');
									redirect("/spare_parts/warehouse_claim/edit/" + warehouse_claim_id);
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
									processButtonAction(warehouse_claim_id, warehouse_claim_code, 'forward to warehouse');
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
									redirect("/spare_parts/warehouse_claim/reprocess_items/" + warehouse_claim_id);
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

			title: "Download Warehouse Claims",
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
							url: "/spare_parts/warehouse_claim/download_check",
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
														url: "/spare_parts/warehouse_claim/download_proceed",
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
																																		
																			redirect('/spare_parts/warehouse_claim/export_xls/'+ start_date +'/' + end_date +'/' + _search_status +'/' + _search_by +'/' + _search_text);
																
																			
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