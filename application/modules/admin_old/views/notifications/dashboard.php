<?php
	$sending_type = 'Email';
	$current_date = date("Y-m-d");	
	
	$mass_notification_cost_sms = $this->settings->mass_notification_cost_sms;
	$mass_notification_cost_email = $this->settings->mass_notification_cost_email;
	
?>
<MainContent>
<style>
td { vertical-align:middle;}
</style>

<style type="text/css">
        /* body { background: #ccc;} */
        div.jHtmlArea .ToolBar ul li a.custom_disk_button 
        {
            background: url(images/disk.png) no-repeat;
            background-position: 0 0;
        }
        
        div.jHtmlArea { border: solid 1px #ccc; background-color: #E7EDEF; }

		div.jHtmlArea iframe { background-color: #f7f7f7; margin-left: 2px; }

		#ShowHide_HTML_Source_View { display: inline-block;}	
</style>


<div class='alert alert-info'><h2>Notification Tools</h2></div>
		
		<div class="">			
			<div class="" style='margin-bottom: 15px;'>
				<div id='content_search_category'>
																							
					<div id='search_main_container' style=''>
						<fieldset class='inline-form' >
							<label>Search Recipient By:</label>
							<input class='input-large search-query' type='text' id='txt_search' name='txt_search' value='' style='width:250px;margin-top:-10px;'> 
						
							<select name="search_option" id="search_option">
								<!--option value="primary_member_code">Member ID</option-->
								<option value="last_name">Member Last Name</option>
								<option value="first_name">Member First Name</option>
								<option value="member_id">Member ID</option>
							</select>                 
						
							<button class='btn btn-info' id='btn_search'><span>Search</span></button>
							<button class='btn btn-info' id='btn_reset' class='black'><span>Reset Search</span></button>		
							<button class='btn btn-info' id='btn_clear' style='float:right;'><span>Clear All</span></button>
							
							<div style='clear:both;'></div>
							
							<span id="search_error_message" class="label label-important" style='margin-left:125px;display:none;'>Search String must be at least three (3) characters.</span>								
						</fieldset>
						
						<fieldset class='inline-form'>
							<label>Add Member Detail To:</label>
							<select name="send_via_option" id="send_via_option">
								<option value="email">E-MAIL</option>
								<option value="sms">SMS</option>
								<option value="email_sms">BOTH</option>
								<!--option value="email_sms">Both Email and SMS</option-->
							</select>             		
						</fieldset>
						
						<fieldset class='inline-form'>
							<label>Send To:</label>
							<select name="send_to_option" id="send_to_option">
								<option value="selected">Selected Members Only</option>
								<option value="all">All Members</option>
								<option value="sms_notification_1">SMS Subscribers</option>
								<option value="sms_notification_0">Non-SMS Subscribers</option>
							</select>   							
							<div style='clear:both;'></div>
						</fieldset>
						
						<fieldset class='inline-form'>
							<label>Chaged?:</label>
							<select name="is_charged_option" id="is_charged_option">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>   							
							<div style='clear:both;'></div>
						</fieldset>
						
						<div class='clearfix'>&nbsp;</div>
						<div>
							<table class='table table-striped table-bordered' id='search_result_container' style='display:none;'>
								<thead>
									<th>Member ID</th>
									<th>Name</th>
									<th>Email</th>
									<th>Mobile Number</th>
									<th>Actions</th>									
								</thead>
								<tbody id='tbody_html_result'>
								</tbody>
							</table>
						</div>
						<div id='pagination_container' align='center' style='margin-top:10px;'>
						
							<div class='clearfix'></div>
						</div>	
					
					</div>
				</div>
			</div>
		</div>
		<hr/>
	  
		<div class="">			            
			<div class='alert-info'><h2>Email</h2></div>
			<br/>
            <div class="" style='margin-bottom: 15px;'>					
				
				<!--input title='Email Address' class='large' type='text' id='email' name='email' value=''  /-->
				<label>To:</label>				
				<span id='to_note_email' style='color:#990000;margin-top:5px;display:none;'><strong>All Members</strong></span>
				<textarea id='recipient_list_email' disabled='disabled' style='height:120px;width:100%;margin-top:10px;'></textarea>
				<div style='clear:both;'></div>
				<label>Message:</label>
				<textarea title='Sender Message Email' placeholder='Test Message' type='text' style='width:90%;height:400px;' id='sender_message_email' name='sender_message_email'></textarea>
				<div style='clear:both;'></div>
				<hr/>					
				<button type='button' style='float:right' id='btn_send_email_message'><span>Send Message</span></button>	
				<br style='clear:both;'/>			
				
			</div>			            
        </div>
		<br/>
		<div class="">			
            <div class='alert-info'><h2>SMS</h2></div>
			<br/>
            <div class="" style='margin-bottom: 15px;'>
				<label>To:</label>				
				<span id='to_note_sms' style='color:#990000;margin-top:5px;display:none;'><strong>All Members</strong></span>
				<textarea id='recipient_list_sms' disabled='disabled' style='height:120px;width:100%;margin-top:10px;'></textarea>							
				<div style='clear:both;'></div>
				<label>Message:</label>
				<span id='body_note' style='color:#990000;margin-top:5px;'></span>
				<textarea id="sender_message_sms" type='text' placeholder='Enter your message here...' style="height:100px;width:95%;margin-top:5px;"></textarea>								
				<div style='clear:both;'></div>
				<hr/>					
				<button type='button' style='float:right' id='btn_send_sms_message'><span>Send Message</span></button>	
				<br style='clear:both;'/>
				
			</div>			            
        </div>
	
	</div>
		
<script type="text/javascript">
  //<![CDATA[
$(document).on('ready', function() {
	
	//$('#sender_message_email').htmlarea();
	
	//$('#mobile_country_code').numeric();
	//$('#mobile_area_code').numeric();
	//$('#mobile_number').numeric();
	
	$('.page_button').live("click", function() {
		var data = $(this).attr('data');
		var search_by = $('#search_option').val();
		var search_text = $.trim($('#txt_search').val());
		showMembers(data, search_by, search_text);
		return false;
	});
	
	$('#btn_search').live("click", function() {
		//var recipient_list = $.trim($('#recipient_list').val());
		var _send_via_option = $("#send_via_option").val();
		var search_by = $('#search_option').val();
		var search_text = $.trim($('#txt_search').val());
		var recipient_list_email = $.trim($('#recipient_list_email').val());
		var recipient_list_sms = $.trim($('#recipient_list_sms').val());
		
		$('#search_error_message').html('Search string must be at least three(3) characters long.');			
		if (search_text.length <= 2) {				
			$('#search_error_message').show();
			return false;
		};
					
		showMembers(1, search_by, search_text, recipient_list_email, recipient_list_sms, _send_via_option);
		return false;
	});
	
	$('#btn_reset').live("click", function() {
		$('#txt_search').val("");
		$('#search_error_message').hide();
		$('#search_result_container').hide();
		$('#tbody_html_result').hide();
		$('#pagination_container').hide();			
		return false;
	});
	
	$('#btn_clear').live("click", function() {
		$('#txt_search').val("");
		$('#search_error_message').hide();
		$('#search_result_container').hide();
		$('#tbody_html_result').hide();
		$('#pagination_container').hide();
		$('#recipient_list_email').val("");
		$('#recipient_list_sms').val("");
		$('#sender_message_email').val("");			
		$('#sender_message_sms').val("");
		return false;
	});

	$("#send_to_option").change(function(){			
		var _option = $("#send_to_option").val();
		var _via_option = $("#send_via_option").val();
		
		if (_option == 'all') {
			if (_via_option == 'email_sms') {
				$("#recipient_list_email").hide();
				$("#recipient_list_sms").hide();
				$("#to_note_email").html('ALL MEMBERS');
				$("#to_note_sms").html('ALL MEMBERS');
				$("#to_note_email").show();				
				$("#to_note_sms").show();                
			} else {
				$("#recipient_list_" + _via_option).hide();
				$("#to_note_" + _via_option).html('ALL MEMBERS');
				$("#to_note_" + _via_option).show();                
			}
		} else if (_option == 'sms_notification_1') {
			if (_via_option == 'email_sms') {
				$("#recipient_list_email").hide();
				$("#recipient_list_sms").hide();
				$("#to_note_email").html('SMS SUBSCRIBERS');
				$("#to_note_sms").html('SMS SUBSCRIBERS');
				$("#to_note_email").show();
				$("#to_note_sms").show();
			} else {
				$("#recipient_list_" + _via_option).hide();
				$("#to_note_" + _via_option).html('SMS SUBSCRIBERS');
				$("#to_note_" + _via_option).show();   
			}			
		} else if (_option == 'sms_notification_0') {
			if (_via_option == 'email_sms') {
				$("#recipient_list_email").hide();
				$("#recipient_list_sms").hide();
				$("#to_note_email").html('NON-SMS SUBSCRIBERS');
				$("#to_note_sms").html('NON-SMS SUBSCRIBERS');
				$("#to_note_email").show();
				$("#to_note_sms").show();
			} else {
				$("#recipient_list_" + _via_option).hide();
				$("#to_note_" + _via_option).html('NON-SMS SUBSCRIBERS');
				$("#to_note_" + _via_option).show();   
			}			
		} else {
			if (_via_option == 'email_sms') {
				$("#recipient_list_email").show();
				$("#recipient_list_sms").show();
				$("#to_note_email").hide();
				$("#to_note_sms").hide();                
			} else {				
				$("#recipient_list_" + _via_option).show();
				$("#to_note_" + _via_option).hide();                
			}
		}
	});
	
	$("#send_via_option").change(function(){			
		var _option = $("#send_via_option").val();
		var search_by = $('#search_option').val();
		var search_text = $.trim($('#txt_search').val());
		var _to_option = $("#send_to_option").val();
		var _recipient_email = $.trim($('#recipient_list_email').val());
		var _recipient_sms = $.trim($('#recipient_list_sms').val());
			

		if (_option == 'email') {										
			if (_to_option == 'all') {					
				$('#recipient_list_' + _option).hide();
				$("#to_note_" + _option).html('ALL MEMBERS');
                $("#to_note_" + _option).show();                
			} else if (_to_option == 'sms_notification_0') {
				$('#recipient_list_' + _option).hide();
				$("#to_note_" + _option).html('NON-SMS SUBSCRIBERS');
                $("#to_note_" + _option).show();                
			} else if (_to_option == 'sms_notification_1') {
				$('#recipient_list_' + _option).hide();
				$("#to_note_" + _option).html('SMS SUBSCRIBERS');
                $("#to_note_" + _option).show();                
			} else {
				$("#recipient_list_" + _option).show();	
                $("#to_note_" + _option).hide();                
			}
		} else if (_option == 'sms') {							
			if (_to_option == 'all') {
				$("#recipient_list_" + _option).hide();					
				$("#to_note_" + _option).html('ALL MEMBERS');
                $("#to_note_" + _option).show();
			} else if (_to_option == 'sms_notification_0') {
				$('#recipient_list_' + _option).hide();
				$("#to_note_" + _option).html('NON-SMS SUBSCRIBERS');
                $("#to_note_" + _option).show();                
			} else if (_to_option == 'sms_notification_1') {
				$('#recipient_list_' + _option).hide();
				$("#to_note_" + _option).html('SMS SUBSCRIBERS');
                $("#to_note_" + _option).show();                			
			} else {				
				$("#recipient_list_" + _option).show();
                $("#to_note_" + _option).hide();                
			}
		} else {
			// for both sms and email
			if (_to_option == 'all') {					
				$('#recipient_list_email').hide();  
				$('#recipient_list_sms').hide();
				$("#to_note_email").html('ALL MEMBERS').show();
				$("#to_note_sms").html('ALL MEMBERS').show();				
			} else {
				$('#recipient_list_email').show();
				$('#recipient_list_sms').show();                
			}
		}
		
		if (!(search_text == '')) {
			showMembers(1, search_by, search_text, _recipient_email, _recipient_sms, _option);
		}
	});
	
	$('#btn_send_email_message').live("click", function() {								
			var send_to_option = $("#send_to_option").val();
			var recipient_list = $.trim($('#recipient_list_email').val());
			var sender_message = $.trim($('#sender_message_email').val());
			var is_charged_option = $.trim($('#is_charged_option').val());
			
			var _cost_sms = '<?= $mass_notification_cost_sms ?>';
			var _cost_email = '<?= $mass_notification_cost_email ?>';
			var _extra_html = "";
			
		
			if (send_to_option == 'selected') {
				if ((recipient_list == '') && (sender_message == '')) {	
					
					sendNotificationErr = b.modal.new({
						title: "Send Notification :: Error",
						//width:450,
						disableClose: true,
						html: "Empty Recipient/s and Message",
						buttons: {						
							'Close' : function() {
								sendNotificationErr.hide();								 							
							}
						}
					});
					sendNotificationErr.show();			

				}
				
				if ((recipient_list == '') && (sender_message != '')) {
					
					sendNotificationErr = b.modal.new({
						title: "Send Notification :: Error",
						//width:450,
						disableClose: true,
						html: "Empty Recipient/s",
						buttons: {						
							'Close' : function() {
								sendNotificationErr.hide();								 							
							}
						}
					});
					sendNotificationErr.show();			

				}
				
				if ((recipient_list != '') && (sender_message == '')) {
					//alert(recipient_list + '|' + sender_message);

					sendNotificationErr = b.modal.new({
						title: "Send Notification :: Error",
						//width:450,
						disableClose: true,
						html: "Empty Message",
						buttons: {						
							'Close' : function() {
								sendNotificationErr.hide();								 							
							}
						}
					});
					sendNotificationErr.show();			

				}
			} else {
				// ALL
				if (sender_message == '') {
					sendNotificationErr = b.modal.new({
						title: "Send Notification :: Error",
						//width:450,
						disableClose: true,
						html: "Empty Message",
						buttons: {						
							'Close' : function() {
								sendNotificationErr.hide();								 							
							}
						}
					});
					sendNotificationErr.show();			

				}
			}
			
			if (is_charged_option == 1) {	
				_extra_html = "Distributor/s will be charged <strong>Php " + _cost_email + "</strong> for every received email message.<br/>";
			}
			


			confirmModal = b.modal.new({
				title: "Send Notification :: Confirm",
				html: _extra_html + "Are you sure you want to send your message via Email?",
				//width: 300,
				disableClose: true,
				buttons: {
					"Cancel": function() {
						confirmModal.hide();
					},
					"Yes": function(){
						confirmModal.hide();
						
						// ajax
						b.request({
							url: "/admin/notifications/send_email_message",
							data: {
								'send_to_option' : send_to_option,
							 	'recipient_list' : recipient_list,
							 	'sender_message' : sender_message,
							 	'is_charged_option' : is_charged_option,
								'cost_sms' : _cost_sms,
								'cost_email' : _cost_email
							},
							on_success: function(data){
								//alert(data.status);
							
								if(data.status == "1") {
									
									successfulModal = b.modal.new({
										title: 'Send Notification : Email',
										html: "<div><p>Message was sent via email to the following: " + data.email_recipients + "</p></div>",
										//width: 300,
										disableClose: true,
										buttons: {
											"Ok": function(){
												successfulModal.hide();
											}
										}
									});
									successfulModal.show();
									
								} else {
									errorModal = b.modal.new({
										title: "Send Notification :: Error",
										html: data.message,
										//width: 300,
										disableClose: true,
										buttons: {
											"Ok": function(){
												errorModal.hide();
											}
										}
									});
									errorModal.show();
								}						
							},
							on_error: function(){
								errorModal = b.modal.new({
									title: "Send Notification :: Error",
									html: "Opps! There is something wrong with the Notification Facility.",
									//width: 300,
									disableClose: true,
									buttons: {
										"Ok": function(){
											errorModal.hide();
										}
									}
								});
								errorModal.show();
							}
						});	
						
					}
				}
			});
			confirmModal.show();
		});	
		
		
		$('#btn_send_sms_message').live("click", function() {								
			var send_to_option = $("#send_to_option").val();
			var recipient_list = $.trim($('#recipient_list_sms').val());
			var sender_message = $.trim($('#sender_message_sms').val());
			var is_charged_option = $.trim($('#is_charged_option').val());
			
			var _cost_sms = '<?= $mass_notification_cost_sms ?>';
			var _cost_email = '<?= $mass_notification_cost_email ?>';
			var _extra_html = "";
			
			if (send_to_option == 'selected') {
				if ((recipient_list == '') && (sender_message == '')) {	
					print_modal = b.modal.new({
						title: "Send Notification :: Error",
						html: 'Empty Recipient/s and Message',
						//width: 300,
						disableClose: true,
						buttons: {
							"Ok": function(){
								print_modal.hide();
							}
						}
					});
					print_modal.show();
				
					return false;
				}
				
				if ((recipient_list == '') && (sender_message != '')) {
					print_modal = b.modal.new({
						title: "Send Notification :: Error",
						html: 'Empty Recipient/s',
						width: 300,
						disableClose: true,
						buttons: {
							"Ok": function(){
								print_modal.hide();
							}
						}
					});
					print_modal.show();
					return false;
				}
				
				if ((recipient_list != '') && (sender_message == '')) {
					print_modal = b.modal.new({
						title: "Send Notification :: Error",
						html: 'Empty Message.',
						width: 300,
						disableClose: true,
						buttons: {
							"Ok": function(){
								print_modal.hide();
							}
						}
					});
					print_modal.show();
					return false;
				}
			} else {
				// ALL
				if (sender_message == '') {	
					print_modal = b.modal.new({
						title: "Send Notification :: Error",
						html: 'Empty Message.',
						width: 300,
						disableClose: true,
						buttons: {
							"Ok": function(){
								print_modal.hide();
							}
						}
					});
					print_modal.show();
					return false;
				}
			}
			
			if (is_charged_option == 1) {	
				_extra_html = "Distributor/s will be charged <strong>Php " + _cost_sms + "</strong> for every received sms message.<br/>";
			}
			
			print_modal = b.modal.new({
				title: "Send Notification :: Confirm",
				html: _extra_html + 'Are you sure you want to send your message via Sms?',
				width: 300,
				disableClose: true,
				buttons: {
					"Cancel": function() {
						print_modal.hide();
					},
					"Yes": function(){
						print_modal.hide();
						
						// ajax
						b.request({
							url: "/admin/notifications/send_sms_message",
							data: {
								'send_to_option' : send_to_option,
								'recipient_list' : recipient_list,
								'sender_message' : sender_message,
								'is_charged_option' : is_charged_option,
								'cost_sms' : _cost_sms,
								'cost_email' : _cost_email
							},
							on_success: function(data){
								console.log(data);
							
								if(data.status == "1") {
									
									print_modal = b.modal.new({
										title: "Send SMS Notification :: Successful",
										html: "<div><p>Message was sent via sms to the following: " + data.sms_recipients + "</p></div>",
										width: 300,
										disableClose: true,
										buttons: {
											"Ok": function(){
												print_modal.hide();
											}
										}
									});
									print_modal.show();
									
								} else {
									print_modal = b.modal.new({
										title: "Send SMS Notification :: Error",
										html: data.message,
										width: 300,
										disableClose: true,
										buttons: {
											"Ok": function(){
												print_modal.hide();
											}
										}
									});
									print_modal.show();
								}						
							},
							on_error: function(){
								print_modal = b.modal.new({
									title: "Send SMS Notification :: Error",
									html: "Opps! There is something wrong with the Notification Facility.",
									width: 300,
									disableClose: true,
									buttons: {
										"Ok": function(){
											print_modal.hide();
										}
									}
								});
								print_modal.show();
							}
						});	
						
					}
				}
			});
			print_modal.show();
			
			
			
			
			//// proceed with sending
			//showDialog({				
			//	title: 'Send Notification :: Confirm',
			//	html: "Are you sure you want to send your message via Sms?",
			//	buttons : {
			//		'Yes': function() {
			//			
			//			ajaxRequest(
			//				"/admin/tools/send_sms_message",								
			//				{'send_to_option' : send_to_option,
			//				 'recipient_list' : recipient_list,
			//				 'sender_message' : sender_message},
			//				function(data) {                                 
			//					if (data.status == 1) {									
			//						//$('#tbl_recipient_list_container').html(data.html);
			//						hideDialog();
			//						
			//						showDialog({
			//							title: 'Send Notification : Sms',
			//							html: "<div><p>Message was sent via sms to the following: " + data.sms_recipients + "</p></div>",
			//							buttons : {
			//								'Ok': function() { 
			//									hideDialog();
			//								}
			//							}
			//						});
            //                        return false;    
			//						
			//					} else {
			//						//alert('Error');
			//						// error
			//					}
			//					hideDialog();
			//				},
			//				function(data) {
			//					hideDialog();
			//				},
			//				false
			//			);
			//			
			//		},
			//		'Cancel': function() {
			//			hideDialog();
			//		}
			//	}
			//});			
		
			return false;
		});	
		
		
		
		
	
	$('.btn_member_add').live("click", function() {
			var _send_via_option = $("#send_via_option").val();			
			var data = $(this).attr('data');
			data = data.split('|');			
			var _member_id = data[0];
			var _page = data[1];
			var _email = data[2];
			var _mobile_number = data[3];									
			var _recipient_list_email = $.trim($('#recipient_list_email').val());
			var _recipient_list_sms = $.trim($('#recipient_list_sms').val());
						
			if ($('.list_add_' + _member_id).html() == 'Add to list') {
		
				b.request({
					url: "/admin/notifications/add_to_list",
					data: {
						'_recipient_list_email':_recipient_list_email,
						'_recipient_list_sms':_recipient_list_sms,
						'_send_via_option':_send_via_option,
						'_member_id':_member_id,
						'_email': _email,
						'_page': _page,						
						'_mobile_number':_mobile_number	
					},
					on_success: function(data){
						if(data.status == "1") {
							$('.list_add_' + _member_id).html('Remove');
							$('.icon_' + _member_id).removeClass('icon-ok');
							$('.icon_' + _member_id).addClass('icon-remove');
							$('.btn_' + _member_id).removeClass('btn-success');
							$('.btn_' + _member_id).addClass('btn-danger');
							$('#recipient_list_email').val(data.recipient_list_email);
							$('#recipient_list_sms').val(data.recipient_list_sms);
						} else {
							print_modal = b.modal.new({
								title: "Add to Recipient List :: Error",
								html: data.message,
								width: 300,
								disableClose: true,
								buttons: {
									"Ok": function(){
										print_modal.hide();
									}
								}
							});
							print_modal.show();
						}						
					},
					on_error: function(){
						// TODO:
					}
				});	
		
		
				//ajaxRequest(
				//	base_url + "admin/tools/add_to_list",
				//	{
				//		'_recipient_list_email':_recipient_list_email,
				//		'_recipient_list_sms':_recipient_list_sms,
				//		'_send_via_option':_send_via_option,
				//		'_member_id':_member_id,
				//		'_email': _email,
				//		'_page': _page,						
				//		'_mobile_number':_mobile_number					
				//	},
				//	function (data) {
				//		if (data.status==1) {
				//			$('.list_add_' + _member_id).html('Remove');															
				//			$('#recipient_list_email').val(data.recipient_list_email);
				//			$('#recipient_list_sms').val(data.recipient_list_sms);
				//			
				//		} else {
				//			// ERROR
				//			showDialog({
				//			title: 'Add to Recipient List :: Error',							
				//			html: data.message,
				//			
				//			buttons : {
				//				'Ok': function() {		
				//					hideDialog();
				//				}
				//			}
				//		});
				//		}
				//	},
				//	function (data) {
				//	},
				//	false
				//);	
			
			} else {			
			
				b.request({
					url: "/admin/notifications/remove_from_list",
					data: {
						'_recipient_list_email':_recipient_list_email,
						'_recipient_list_sms':_recipient_list_sms,
						'_send_via_option':_send_via_option,
						'_member_id':_member_id,
						'_email': _email,
						'_page': _page,						
						'_mobile_number':_mobile_number			
					},
					on_success: function(data){
						if(data.status == "1") {							
							$('.list_add_' + _member_id).html('Add to list');
							$('.icon_' + _member_id).removeClass('icon-remove');
							$('.icon_' + _member_id).addClass('icon-ok');
							$('.btn_' + _member_id).removeClass('btn-danger');
							$('.btn_' + _member_id).addClass('btn-success');								
							$('#recipient_list_email').val(data.recipient_list_email);
							$('#recipient_list_sms').val(data.recipient_list_sms);
						} else {
							print_modal = b.modal.new({
								title: "Remove to Recipient List :: Error",
								html: data.message,
								width: 300,
								disableClose: true,
								buttons: {
									"Ok": function(){
										print_modal.hide();
									}
								}
							});
							print_modal.show();
						}						
					},
					on_error: function(){
						// TODO:
					}
				});	
			
				//ajaxRequest(
				//	base_url + "admin/tools/remove_from_list",
				//	{
				//		'_recipient_list_email':_recipient_list_email,
				//		'_recipient_list_sms':_recipient_list_sms,
				//		'_send_via_option':_send_via_option,
				//		'_member_id':_member_id,
				//		'_email': _email,
				//		'_page': _page,						
				//		'_mobile_number':_mobile_number														
				//	},
				//	function (data) {
				//		if (data.status==1) {
				//			$('.list_add_' + _member_id).html('Add to list');	
				//			$('#recipient_list_email').val(data.recipient_list_email);
				//			$('#recipient_list_sms').val(data.recipient_list_sms);
				//		} else {
				//			// ERROR
				//			showDialog({
				//			title: 'Re',							
				//			html: data.message,
				//			
				//			buttons : {
				//				'Ok': function() {		
				//					hideDialog();
				//				}
				//			}
				//		});
				//		}
				//	},
				//	function (data) {
				//	},
				//	false
				//);			
			
			}
			
		});        
	});
	
	var showMembers = function(page, search_by, search_text, r_list_email, r_list_sms, send_via) {
		
		b.request({
			url: "/admin/notifications/listing",
			data: {
				'page': page,
				'search_text' : search_text,
				'search_by': search_by,
				'recipient_list_email': r_list_email,
				'recipient_list_sms': r_list_sms,
				'send_via_option': send_via
			},
			on_success: function(data){
				if(data.status == "1") {
					$('#search_error_message').hide();
					$("#search_result_container").show();
					$("#tbody_html_result").show();
                    $("#tbody_html_result").html(data.html);
					//$("#pagination_container").html(pagination_create(data.current_page, data.total_records, data.records_per_page, data.pager_adjacents));
					current_page = data.current_page;
				} else {
					// TODO:
				}
				//$(this_button).removeClass("no_clicking")
			},
			on_error: function(){
				//$(this_button).removeClass("no_clicking")
			}
		});
		
		
		//showLoading();
		//ajaxRequest(
		//	base_url + "admin/tools/listing",
		//	{'page':page, 'search_text' : search_text,'search_by':search_by, 'recipient_list_email':r_list_email, 'recipient_list_sms':r_list_sms, 'send_via_option': send_via},
		//	function (data) {
        //        if (data.status == 1) {						
		//			hideLoading();
		//			$('#search_error_message').hide();
		//			$("#search_result_container").show();
		//			$("#tbody_html_result").show();
        //            $("#tbody_html_result").html(data.html);
		//			$("#pagination_container").html(pagination_create(data.current_page, data.total_records, data.records_per_page, data.pager_adjacents));
		//			current_page = data.current_page;
        //        } else {
		//			// display error
		//			hideLoading();
        //        }
		//	},
		//	function (data) {
		//		hideLoading();
		//	},
		//	false
		//);
    }	

//]]>
</script>

</MainContent>
