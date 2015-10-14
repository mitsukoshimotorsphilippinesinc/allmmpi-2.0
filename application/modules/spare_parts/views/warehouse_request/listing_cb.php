
<div class='alert alert-danger'><h2>Request List<a class='btn btn-small btn-default'id="download-btn" style="float:right;" title='Download'><i class='icon-download'></i>&nbsp;Download</a></h2></div>

<br>

<div >
	<form id='search_details' method='get' action =''>

		<strong>Status:</strong>
		<br/>

		<table class=''>
			<thead>
			</thead>
			<tbody>
				<tr>
					<td>	
						<input id="cb-all" type="checkbox" name="cb-all" value="ALL" checked="checked"> ALL
						<br/>
						<input id="cb-pending" type="checkbox" name="status" value="PENDING" checked="checked" disabled> PENDING
					</td>
					<td>
						&nbsp;&nbsp;
						<br/>
						&nbsp;&nbsp;
					</td>
					<td>
						<input id="cb-forapproval" type="checkbox" name="status" value="FOR APPROVAL" checked="checked" disabled> FOR APPROVAL
						<br/>
						<input id="cb-approved" type="checkbox" name="status" value="APPROVED" checked="checked" disabled> APPROVED	
					</td>
					<td>
						&nbsp;&nbsp;
						<br/>
						&nbsp;&nbsp;
					</td>
					<td>
						<input id="cb-denied" type="checkbox" name="status" value="DENIED" checked="checked" disabled> DENIED
						<br/>
						<input id="cb-processing" type="checkbox" name="status" value="PROCESSING" checked="checked" disabled> PROCESSING
					</td>
					<td>
						&nbsp;&nbsp;
						<br/>
						&nbsp;&nbsp;
					</td>
					<td>
						<input id="cb-completed" type="checkbox" name="status" value="COMPLETED" checked="checked" disabled> COMPLETED
						<br/>
						<input id="cb-cancelled" type="checkbox" name="status" value="CANCELLED" checked="checked" disabled> CANCELLED
					</td>	
				</tr>
			</tbody>
		</table>

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
			<th style='width:100px;'>Requested By</th>
			<th style='width:100px;'>Motor Brand/Model</th>
			<th style='width:100px;'>Warehouse</th>
			<th style='width:100px;'>Approved By (Warehouse)</th>			
			<th style='width:120px;'>Remarks</th>
			<th style='width:70px;'>Date Created</th>			
			<th style=''>Action</th>
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
			if ($t->status == 'PENDING') {
				echo "<td><span class='label label-important' >{$t->status}</span></td>";
			} else if ($t->status == 'PROCESSING') {
				echo "<td><span class='label label-info' >{$t->status}</span></td>";
			} else if ($t->status == 'FOR APPROVAL') {
				echo "<td><span class='label label-warning' >{$t->status}</span></td>";
			} else {
				echo "<td><span class='label label-success' >{$t->status}</span></td>";
			}			

			// get requestor details
			$requestor_details = $this->human_relations_model->get_employment_information_by_id($t->id_number);

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

			// get warehouse detail			
			$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($t->warehouse_id);

			if (count($warehouse_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$warehouse_details->warehouse_name}</td>"; 
			}
			?>	
			<td></td>
			<td><?= $t->remarks; ?></td>
			<td><?= $t->insert_timestamp; ?></td>

			

			<td data1="<?= $t->warehouse_request_id ?>" data2="<?= $t->request_code ?>">				
				<a class='btn btn-small btn-primary view-details' data='info' title="View Details"><i class="icon-white icon-list"></i></a>	
				<!--?php
				if ($t->status == 'FOR APPROVAL') {
					echo "<a class='btn btn-small btn-primary process-btn' data='yes' title='Yes'><i class='icon-white icon-ok'></i></a>";
				}

				if (($t->status == 'FOR APPROVAL') || ($t->status == 'APPROVED')) {
					echo "<a class='btn btn-small btn-danger process-btn' data='no' title='No'><i class='icon-white icon-remove'></i></a>";
				}				
				?-->
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


	$("#cb-all").click(function(){
		if (document.getElementById('cb-all').checked) {
			document.getElementById('cb-pending').disabled = true;
			document.getElementById('cb-forapproval').disabled = true;
			document.getElementById('cb-approved').disabled = true;
			document.getElementById('cb-denied').disabled = true;
			document.getElementById('cb-processing').disabled = true;
			document.getElementById('cb-completed').disabled = true;
			document.getElementById('cb-cancelled').disabled = true;
		} else {
			
			document.getElementById('cb-pending').disabled = false;
			document.getElementById('cb-forapproval').disabled = false;
			document.getElementById('cb-approved').disabled = false;
			document.getElementById('cb-denied').disabled = false;
			document.getElementById('cb-processing').disabled = false;
			document.getElementById('cb-completed').disabled = false;
			document.getElementById('cb-cancelled').disabled = false;
		}
		
	})


	$(".process-btn").click(function(){

		var warehouse_request_id = $(this).parent().attr("data1");
		var warehouse_request_code = $(this).parent().attr("data2");
		var is_approved = $(this).attr("data");

		b.request({
			url: "/spare_parts/warehouse_request/for_approval_confirm",
			data: {
				'warehouse_request_id' : warehouse_request_id,
				'warehouse_request_code' : warehouse_request_code,
				'is_approved' : is_approved,
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

								if (is_approved == 'no') {
									
									if ($.trim($("#txt-remarks").val()) == "") {
										$("#error-reasonremarks").show();
										return;
									}
								}	

								$("#error-reasonremarks").hide();

								// ajax request
								b.request({
									url : '/spare_parts/warehouse_request/for_approval_proceed',
									data : {				
										'warehouse_request_id' : warehouse_request_id,
										'warehouse_request_code' : warehouse_request_code,
										'is_approved' : is_approved,
										'remarks' : $("#txt-remarks").val(),
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
														redirect('spare_parts/warehouse_request/approval');
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
	});
	
	$(".view-details").click(function(){
		var warehouse_request_id = $(this).parent().attr("data1");
		var warehouse_request_code = $(this).parent().attr("data2");
	
		b.request({
			url: "/spare_parts/warehouse_request/view_details",
			data: {
				"warehouse_request_id" : warehouse_request_id,
				"warehouse_request_code" : warehouse_request_code,
			},
			on_success: function(data){
				if (data.status == "1")	{
				
					// show add form modal					
					viewDetailsModal = b.modal.new({
						title: data.data.title,
						width:800,
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
							url: "/spare_parts/warehouse_request/download_check",
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
														url: "/spare_parts/warehouse_request/download_proceed",
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
																																		
																			redirect('/spare_parts/warehouse_request/export_xls/'+ start_date +'/' + end_date);
																
																			
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