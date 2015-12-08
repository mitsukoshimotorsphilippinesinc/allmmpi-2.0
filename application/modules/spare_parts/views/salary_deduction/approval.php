<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>For Approval<a class='btn btn-small btn-default'id="download-btn" style="float:right;" title='Download'><i class='icon-download'></i>&nbsp;Download</a></h2></div>

<br>

<div >
	<form id='search_details' method='get' action =''>

		<strong>Status:&nbsp;</strong>
		<select name="search_status" id="search_status" style="width:250px;margin-left:20px" value="<?= $search_status ?>">
			<option value="ALL">ALL</option>
			<option value="FOR APPROVAL">FOR APPROVAL</option>
			<option value="APPROVED">APPROVED</option>
			<option value="DENIED">DENIED</option>			
			<option value="CANCELLATION-FOR APPROVAL">CANCELLATION-FOR APPROVAL</option>
			<option value="CANCELLATION-APPROVED">CANCELLATION-APPROVED</option>
			<option value="CANCELLATION-DENIED">CANCELLATION-DENIED</option>			
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
			<th style=''>Request Code</th>
			<th>Status</th>
			<th style='width:100px;'>Requested By</th>
			<th style='width:100px;'>Motor Brand/Model</th>
			<th style='width:100px;'>Total Items</th>
			<th style='width:100px;'>Warehouse</th>
			<th style='width:100px;'>Approved By (Warehouse)</th>			
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
									
			<td style='text-align:center;'><?= $t->request_code; ?></td>
			
			<?php
			$status_class = strtolower(trim($t->status));			
			$status_class = str_replace(" ", "-", $status_class);
		
			echo "<td><span class='label label-" . $status_class . "' >{$t->status}</span></td>";

			// get requestor details
			$id = str_pad($t->id_number, 7, '0', STR_PAD_LEFT);
			$requestor_details = $this->human_relations_model->get_employment_information_view_by_id($id);			

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

			// number of items
			//$where = "salary_deduction_id = " . $t->salary_deduction_id . " AND status IN ('PENDING', 'COMPLETED')";
			//$salary_deduction_detail_info = $this->spare_parts_model->get_salary_deduction_detail($where);
			$where = "request_summary_id = " . $t->request_summary_id . " AND status IN ('PENDING', 'COMPLETED')";
			$salary_deduction_detail_info = $this->spare_parts_model->get_request_detail($where);

			$total_items = 0;
			foreach ($salary_deduction_detail_info as $wrdi) {
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

			//if (($t->warehouse_approved_by == 0) || ($t->warehouse_approved_by == '0')) {
				echo "<td>N/A</td>";
			//} else {
			//	$id = str_pad($t->warehouse_approved_by, 7, '0', STR_PAD_LEFT);
			//	$warehouse_signatory_details = $this->human_relations_model->get_employment_information_view_by_id($id);
			//	echo "<td>{$warehouse_signatory_details->complete_name}</td>";
			//}

			?>	
			
			<td><?= $t->insert_timestamp; ?></td>

			

			<td data1="<?= $t->request_summary_id ?>" data2="<?= $t->request_code ?>">				
				<a class='btn btn-small btn-info view-details' data='info' title="View Details"><i class="icon-white icon-list"></i></a>	
				<?php
				if (($t->status == 'FOR APPROVAL') || ($t->status == 'CANCELLATION-FOR APPROVAL')) {
					echo "<a class='btn btn-small btn-success process-btn' data='yes' title='Yes'><i class='icon-white icon-ok'></i></a>
						<a class='btn btn-small btn-danger process-btn' data='no' title='No'><i class='icon-white icon-remove'></i></a>
						";
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

		var salary_deduction_id = $(this).parent().attr("data1");
		var salary_deduction_code = $(this).parent().attr("data2");
		var is_approved = $(this).attr("data");

		b.request({
			url: "/spare_parts/salary_deduction/for_approval_confirm",
			data: {
				'salary_deduction_id' : salary_deduction_id,
				'salary_deduction_code' : salary_deduction_code,
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
									url : '/spare_parts/salary_deduction/for_approval_proceed',
									data : {				
										'salary_deduction_id' : salary_deduction_id,
										'salary_deduction_code' : salary_deduction_code,
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
														redirect('spare_parts/salary_deduction/approval');
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
		var salary_deduction_id = $(this).parent().attr("data1");
		var salary_deduction_code = $(this).parent().attr("data2");
	
		b.request({
			url: "/spare_parts/salary_deduction/view_details",
			data: {
				"salary_deduction_id" : salary_deduction_id,
				"salary_deduction_code" : salary_deduction_code,
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
																																		
																			redirect('/spare_parts/salary_deduction/export_xls/'+ start_date +'/' + end_date);
																
																			
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