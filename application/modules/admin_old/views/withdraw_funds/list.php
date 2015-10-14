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

<div class='alert alert-info'><h2>Withdraw Funds<a id="upload" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Upload Processed Requests</span></a><a id="download" class='btn btn-primary' style='float:right;margin-left:5px;margin-top:5px;'><span>Download Pending Requests</span></a></h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/withdraw_funds'>
	
		<div style='float:right;'>
		<strong >Filter By:&nbsp;</strong>
		<select name="status_select" id="status_select" style="width:150px;" value="<?= $search_by ?>">
			<option value="all">All</option>
			<option value="PENDING">Pending</option>
			<option value="PROCESSING">Processing</option>
			<option value="CANCELLED">Cancelled</option>
			<option value="COMPLETED">Completed</option>
		</select>    
		</div>

		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="transaction_id">Transaction Number</option>
			<option value="first_name">First Name</option>
			<option value="last_name">Last Name</option>
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
			<th style='width:62px;'>Transaction Number</th>
			<th style='width:62px;'>Member ID</th>
			<th>Name</th>
			<th style='width:100px;'>Gross Amount</th>
			<th style='width:100px;'>Tax</th>
			<th style='width:100px;'>Net Amount</th>
			<th style='width:100px;'>Status</th>
			<th style='width:100px;'>Type</th>
			<th style='width:140px;'>Date Requested</th>
			<th style='width:100px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($withdraw_details)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($withdraw_details as $wd): ?>
		<tr>
			<td><?= $wd->transaction_id; ?></td>
			<td><?= $wd->member_id; ?></td>
			<td><?= "{$wd->first_name} {$wd->last_name}"; ?></td>
			<td><?= $wd->amount; ?></td>
			<td><?= $wd->tax; ?></td>
			<td><?= $wd->amount_after_tax; ?></td>
			<td><?= $wd->status; ?></td>
			<td><?= $wd->preferred_payout; ?></td>
			<td><?= $wd->insert_timestamp; ?></td>
			<td>
				<a class='btn btn-small btn-primary view_withdraw' data="<?= $wd->transaction_id ?>" title="View"><i class="icon-pencil icon-white"></i></a>
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
	
	$(".view_withdraw").click(function(){
		var transaction_id = $(this).attr("data");
		
		var withdraw_modal = b.modal.new({});
		
		var me = this;
		
		var withdraw_button = {};
		
		if(!$(this).hasClass("no_clicking"))
		{
			$(this).addClass("no_clicking");
			
			b.request({
				url: "/admin/withdraw_funds/view_transaction",
				data: {
					"_transaction_id" : transaction_id
				},
				on_success: function(data){
					
					var title = "";
					var withdraw_html = "";
					if(data.status == "ok")
					{
						title = "Transaction Details";
						if(data.data.status != "COMPLETED")
						{
							withdraw_button = {
								"Process Transcation": function(){
									b.request({
										url: "/admin/withdraw_funds/execute",
										data: {
											"_transaction_id" : transaction_id 
										},
										on_success: function(){
											//window.location.href = "/admin/withdraw_funds";
											redirect("/admin/withdraw_funds");
										},
										on_error: function(){

										}
									});
									withdraw_modal.hide();
								}
							}
						}
					
						withdraw_html ="\
						<div>\n\
							<div>\n\
							<label><strong>Transaction ID:</strong> "+data.data.transaction_id+"</label>\n\
							<br>\n\
							<label><strong>Member ID:</strong> "+data.data.member_id+"</label>\n\
							<br>\n\
							<label><strong>Name:</strong> "+data.data.last_name+", "+data.data.first_name+"</label>\n\
							<br>\n\
							<label><strong>Mobile Number:</strong> "+data.data.mobile_number+"</label>\n\
							<br>\n\
							<label><strong>Email:</strong> "+data.data.email+"</label>\n\
							<br>\n\
							</div>\n\
							<hr>\n\
							<div>\n\
							<label><strong>Gross Amount:</strong> "+data.data.gross_amount+"</label>\n\
							<br>\n\
							<label><strong>Tax:</strong> "+data.data.tax+"</label>\n\
							<br>\n\
							<label><strong>Net Amount:</strong> "+data.data.net_amount+"</label>\n\
							<br>\n\
							</div>\n\
							<hr>\n\
							<div align='center'>\n\
								<strong>"+data.data.status+"</strong>\n\
							</div>\n\
						</div>"
					}
					else if(data.status == "error")
					{
						title = "Error Notification: Invalid Transcation ID";
						withdraw_html = data.msg;
					}
					
					withdraw_modal.init({
						title: title,
						width: 400,
						html: withdraw_html,
						buttons: withdraw_button
					});
					$(me).removeClass("no_clicking");
					withdraw_modal.show();
				},
				on_error: function(){
					$(me).removeClass("no_clicking");
				}
			});
		}
		
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
							url: "/admin/withdraw_funds/download_check",
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
														url: "/admin/withdraw_funds/download_proceed",
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
																			updateStatusToUserAction(start_date,end_date,'cancel');
																		},
																		"Download": function(){
																			download_xls_modal.hide();
																			updateStatusToUserAction(start_date,end_date,'download',function(status){
																				if(status === "ok")
																				{																				
																				redirect('/admin/withdraw_funds/export_xls/'+ start_date +'/' + end_date);
																				
																				}
																			});
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
	
	$(document).ready(function(){
	
		
		$(document).on("change","#start_date_month",function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$(document).on("change","#start_date_day",function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$(document).on("change","#start_date_year",function() {
			beyond.webcontrol.updateDateControl('start_date');
		});

		$('#start_date_month').trigger('change');
		$('#start_date_day').trigger('change');
		$('#start_date_year').trigger('change');
		
		$(document).on("change","#end_date_month",function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$(document).on("change","#end_date_day",function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$(document).on("change","#end_date_year",function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		$('#end_date_month').trigger('change');
		$('#end_date_day').trigger('change');
		$('#end_date_year').trigger('change');

		
		 $("#status_select").change(function(){
            alert(1);
        });
		
	});

	var updateStatusToUserAction = function(start_date,end_date,user_action,cb) {
		b.request({
			url: "/admin/withdraw_funds/download_update_user_action",
			data: {
				'start_date':start_date,
				'end_date':end_date,
				'user_action':user_action
			},
			on_success: function (data) {
				var update_modal = b.modal.new({});

				if(data.status == "error")
				{
					update_modal.init({
						title: "Error Notification",
						html: "<p>"+data.msg+"</p>",
						width: 250
					});
				}
				else if(data.status == "ok")
				{
					update_modal.init({
						title: "Download Successful",
						html: "<p>"+data.msg+"</p>",
						disableClose: true,
						width: 250,
						buttons: {
							"Ok": function(){
								update_modal.hide();
								//window.location.href = document.URL;								
								redirect('/admin/withdraw_funds');
							}
						}
					});
				}

				update_modal.show();
				
				if(_.isFunction(cb)) cb.call(this,data.status);
			},
			on_error: function (data) {
			}
		});
	}

	function upload_processed_xls() {
		var upload_modal = b.modal.new({});
		var upload_html = "<div id='response_upload' style='display:none;text-align:center;font-weight:16px;font-weight:bold;margin-bottom:25px;'>Excel file has been successfully processed.</div><div id='fileUpload' class='uploadBox_fu'></div>";
		upload_modal.init({
			title: "Upload",
			width: 500,
			html: upload_html
		});

		upload_modal.show();

  		// uploader
		$("#"+$(upload_modal).attr("id")+' #fileUpload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.xls','.xlsx'],
			target : base_url+'/admin/withdraw_funds/upload',
			onComplete: function() {
				$("#"+$(upload_modal).attr("id")+" #response_upload").show();
			}
		});
		
	}


	$("#upload").click(function(){
		upload_processed_xls()
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
			$("#search_summary").show();           
		}
		
		return false;
	});
	
	$("#button_refresh").live("click",function() {
		redirect('/admin/withdraw_funds');
	});
	
</script>