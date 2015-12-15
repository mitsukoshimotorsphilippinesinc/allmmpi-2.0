<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>List of Repairs<a href='/information_technology/repairs/add' class='btn btn-small btn-default'id="add-btn" style="float:right;margin-right:-30px;margin-top:5px;" title='Add New'><i class='icon-plus'></i>&nbsp;Add New</a>&nbsp;&nbsp;<a class='btn btn-small btn-default'id="download-btn" style="float:right;margin-top:5px;display:none;" title='Download'><i class='icon-download' disabled="disabled"></i>&nbsp;Download Result</a></h2></div>

<br>

<div >
	<form id='search_details' method='get' action =''>

		<strong>Status:&nbsp;</strong>
		<select name="search_status" id="search_status" style="width:250px;margin-left:24px" value="<?= $search_status ?>">
			<option value="ALL">ALL</option>						
			<option value="OPEN">OPEN</option>			
			<option value="COMPLETED">COMPLETED</option>
			<option value="CLOSED">CLOSED</option>			
			
		</select>  
	
		<br/>

		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:250px;" value="<?= $search_by ?>">
			<option value="branch_name">Branch Name</option>
			<option value="complete_name">Employee Name</option>			
		</select>                 

		<input title="Search" class="input-xlarge search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

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
			<th style='width:80px;'>Repair Code</th>
			<th style='width:50px;'>Overall Status</th>
			<th style=''>Requested By</th>
			<th style='width:300px'>Items - Person IN Charge</th>
			<th style=''>Received By</th>
			<th style=''>TR Number (IN)</th>			
			<th style='width:70px;'>Date Created</th>
			<th style='width:80px;'>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($transfers)):?>
		<tr><td colspan='9' style='text-align:center;'><strong>No Record Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($transfers as $t): ?>
		<tr>
									
			<td><?= $t->repair_code; ?></td>
			
			<?php									
			$status_class = strtolower(trim($t->overall_status));			

			//if (substr($status_class, 0, 12) == "cancellation") {
			//	$status_class = substr($status_class, 13);
			//}

			$status_class = str_replace(" ", "-", $status_class);
		
			echo "<td><span class='label label-" . $status_class . "' >{$t->overall_status}</span></td>";

			if ($t->branch_id <> 0) {
				$requestor_details = $this->human_relations_model->get_branch_by_id($t->branch_id);

				if (count($requestor_details) == 0) {
					echo "<td>N/A</td>";
				} else { 
					echo "<td>{$requestor_details->branch_name}</td>"; 
				}			

			} else {

				// get requestor details
				$id = str_pad($t->id_number, 7, '0', STR_PAD_LEFT);
				$requestor_details = $this->human_relations_model->get_employment_information_view_by_id($id);

				if (count($requestor_details) == 0) {
					echo "<td>N/A</td>";
				} else { 
					echo "<td>{$requestor_details->complete_name}</td>"; 
				}			
			}

			// get number of items
			$where = "repair_summary_id = " . $t->repair_summary_id . "";
			$repairs_detail_info = $this->information_technology_model->get_repair_detail($where);
	
			$where = "repair_summary_id = " . $t->repair_summary_id;
			$repairs_details = $this->information_technology_model->get_repair_detail($where);

			$repair_detail_details = $this->information_technology_model->get_repair_detail("repair_summary_id = " . $t->repair_summary_id);				

			$items_html = "<table style='margin-bottom:0px;' class='table table-condensed table-bordered'>
								<thead>
								</thead>
								<tbody>";

			foreach ($repair_detail_details as $rdd) {
				$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($rdd->repair_hardware_id);

				// get person in charge
				$repair_hardware_remark_details = $this->information_technology_model->get_repair_remark("repair_detail_id = '{$rdd->repair_detail_id}'", NULL, "insert_timestamp DESC");

				if (empty($repair_hardware_remark_details)) {
					$repair_summary_details = $this->information_technology_model->get_repair_summary_by_id($rdd->repair_summary_id);

					$pic_details = $this->human_relations_model->get_employment_information_view_by_id($repair_summary_details->received_by);
				} else {
					$pic_details = $this->human_relations_model->get_employment_information_view_by_id($repair_hardware_remark_details[0]->created_by);
				}


				$items_html .= "<tr>
									<td style='width:110px;'>{$repair_hardware_details->repair_hardware_name} x {$rdd->quantity}</td>
									<td>{$pic_details->complete_name}</td>
								</tr>";
						
				//$items_html .= $repair_hardware_details->repair_hardware_name . ' x ' . $rdd->quantity . ' [' . $pic_details->complete_name . ']<br/>';
			}

			$items_html .= "</tbody>
						</table>";

			echo "<td>{$items_html}</td>";

			$id = str_pad($t->received_by, 7, '0', STR_PAD_LEFT);
			$received_by_details = $this->human_relations_model->get_employment_information_view_by_id($id);

			if (count($received_by_details) == 0) {
				echo "<td>N/A</td>";
			} else { 
				echo "<td>{$received_by_details->complete_name}</td>"; 
			}			
			?>

			<td><?= $t->tr_number_in; ?></td>			
			<td><?= $t->insert_timestamp; ?></td>

			<td data1="<?= $t->repair_summary_id ?>" data2="<?= $t->repair_code ?>">				
				<a href='/information_technology/repairs/update/<?= $t->repair_summary_id ?>' class='btn btn-small btn-info' data='info' title="Update"><i class="icon-white icon-list"></i></a>	
				<?php 
				if ($status_class == 'open') {
				?>
				<a href='/information_technology/repairs/edit/<?= $t->repair_summary_id ?>' class='btn btn-small btn-warning' data='edit' title='Edit'><i class='icon-white icon-file'></i></a>
				<!--a href='/information_technology/repairs/delete/<?= $t->repair_summary_id ?>' class='btn btn-small btn-danger' data='delete' title='Delete'><i class='icon-white icon-remove'></i></a-->
				<?php
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
			url: "/information_technology/load_assign_mtr",
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
										url : '/information_technology/check_mtr',
										data : {
											'request_id' : _request_id,
											'request_code' : _request_code,																
											'mtr_number' : $("#txt-mtrnumber").val(),
										},
										on_success : function(data) {
											if (data.status == "1")	{													
												b.request({
													url : '/information_technology/proceed_assign_mtr',
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
																		redirect('information_technology/repairs/listing');
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

	var processButtonAction = function(repairs_id, repairs_code, listing_action) {

		b.request({
			url: "/information_technology/repairs/for_listing_confirm",
			data: {
				'repairs_id' : repairs_id,
				'repairs_code' : repairs_code,
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
									url : '/information_technology/repairs/for_listing_proceed',
									data : {				
										'repairs_id' : repairs_id,
										'repairs_code' : repairs_code,
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
														redirect('information_technology/repairs/listing');
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
		var repairs_id = $(this).parent().attr("data1");
		var repairs_code = $(this).parent().attr("data2");
		var listing_action = $(this).attr("data");
	
		b.request({
			url: "/information_technology/repairs/view_details",
			data: {
				"repairs_id" : repairs_id,
				"repairs_code" : repairs_code,
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
									processButtonAction(repairs_id, repairs_code, 'cancel');
								},
								'For Approval' : function() {
									processButtonAction(repairs_id, repairs_code, 'for approval');
								},
								'Edit' : function() {
									//processButtonAction(repairs_id, repairs_code, 'edit');
									redirect("/information_technology/repairs/edit/" + repairs_id);
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
									processButtonAction(repairs_id, repairs_code, 'forward to warehouse');
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
									redirect("/information_technology/repairs/reprocess_items/" + repairs_id);
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
							url: "/information_technology/repairs/download_check",
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
														url: "/information_technology/repairs/download_proceed",
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
																																		
																			redirect('/information_technology/repairs/export_xls/'+ start_date +'/' + end_date +'/' + _search_status +'/' + _search_by +'/' + _search_text);
																
																			
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