<div class="container row-fluid">    
	<!-- main column -->
	<div class="main-col span12">
		<div class="alert alert-success"><h2>Encode Raffle Entry</h2> <a id="clear_form" class="btn" style="float:right;margin-top:-45px; margin-right:-15px;">Clear Form</a></div>
		<div class="row-fluid">
			<div class="span6">
				<div>
					<h3 class="alert alert-info">Encode Card</h3>
					<div class="well">
						<div class="control-group">					
				        	<label class="control-label">Control Code: <em>*</em></label>
				        	<input class='input-large' type='text' id='card_id' placeholder="74********" name='card_id'maxlength='10'>
							<label id='card_id_error' class='label label-important error_msg' style='display:none;'>Control Code is required.</label>
							<label id='card_id_success' class='label label-success' style='display:none;'>Valid Control Code Series</label>
				    	</div>     		
					
						<div class="control-group">	
							<label class="control-label" for="card_code">RSRN: <em>*</em></label>
							<input type="text" class='input-large'  placeholder="ABC123DE" name="card_code" id="card_code" value="">
							<label id='card_code_error' class='label label-important error_msg' style='display:none;'>Card Code is required.</label>
						</div>
					</div>
				</div>
			</div>
			<div class="span6">
				<div>
					<h4 class="alert alert-info">Already have an Account ID? <br /> Enter Account ID Here:</h4>
					<div class="well">
						<div class="control-group">
							<label class='control-label'>Account ID</label>
							<input class='input-large' type='text' id='account_id' name='account_id' maxlength='10' placeholder="Account ID"/>
							<label id='account_id_error' class='label label-important error_msg' style='display:none;'>Account ID is required.</label>
							<label id='account_id_found' class='label label-success error_msg' style='display:none;'>Account ID found.</label>
							<label id='account_id_not_found' class='label label-important error_msg' style='display:none;'>Account ID not found.</label>
							<div id='account_information_success' style='display:none;'></div>
							<label id='account_information_error' class='label label-important error_msg' style='display:none;'></label>
						</div>
						<a id='member_submit' class='btn btn-primary btn-large submit_entry' data-type="member">Submit</a>
						<a id='view_raffle_entries' class='btn btn-success btn-large'>View Raffle Entries</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<h3 id="title-container" class="alert alert-info">Enter Details</h3>
			<div id="details-container">
			<?= $html; ?>
	
			</div>	
		</div>

	</div>
	
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#account_id').keyup(function(){
			if($(this).val() != ""){
				$('.details-group').each(function(){
					$(this).attr('disabled','disabled');
				});
				$("#non_member_submit").addClass('disabled');
				$("#non_member_submit").attr('disabled','disabled');
			} else {
				$('.details-group').each(function(){
					$(this).removeAttr('disabled');
				});
				$("#non_member_submit").removeClass('disabled');
				$("#non_member_submit").removeAttr('disabled');
			}
		});

		
		$('.details-group').keyup(function(){
			var allEmpty = true;
			$('.details-group').each(function(){
				if($(this).val() != ""){
					allEmpty = false;
				}
			});
			if(allEmpty){
				$('#account_id').removeAttr('disabled');
				$("#member_submit").removeClass('disabled');
				$("#member_submit").removeAttr('disabled');
			} else {
				$('#account_id').attr('disabled','disabled');
				$("#member_submit").addClass('disabled');
				$("#member_submit").attr('disabled','disabled');
			}
		});
	});

	$('body').on('click', '.submit_entry', function(){

		if($(this).hasClass("disabled")) return;
		
		var card_id = $('#card_id').val();
		var card_code = $('#card_code').val();
		var account_id = $('#account_id').val();
		var last_name = $('#last_name').val();
		var first_name = $('#first_name').val();
		var middle_name = $('#middle_name').val();
		var address = $('#address').val();
		var contact_number = $('#contact_number').val();
		var email = $('#email').val();
		var referror = $('#referror').val();
		var has_error = 0;
		var type = $(this).data("type");
				
		$(".error_msg").hide();
		
		if(card_id == "")
		{
			has_error++;
			$('#card_id_error').show();
			$('#card_id_success').hide();
		}
		if(card_code == "")
		{
			has_error++;
			$('#card_code_error').show();
		}
		if(account_id == "")
		{
			if(type != "non_member")
			{
				has_error++;
				$('#account_id_error').show();
			}
		}
		if(last_name == "")
		{
			if(type != "member")
			{
				has_error++;
				$('#last_name_error').show();
			}
		}
		if(first_name == "")
		{
			if(type != "member")
			{
				has_error++;
				$('#first_name_error').show();
			}
		}
		if(middle_name == "")
		{
			if(type != "member")
			{
				has_error++;
				$('#middle_name_error').show();
			}
		}
		if(address == "")
		{
			if(type != "member")
			{
				has_error++;
				$('#address_error').show();
			}
		}
		if(contact_number == "")
		{
			if(type != "member")
			{
				has_error++;
				$('#contact_number_error').show();
			}
		}
		if(email == "")
		{
			if(type != "member")
			{
				has_error++;
				$('#email_error').show();
			}
		}
		if(referror == "")
		{
			if(type != "member")
			{
				has_error++;
				$('#referror_error').show();
			}
		}
		
		if(has_error > 0)
		{
			return;
		}
		
		//check account id if exists
		
		//continue
		var confirm_modal = b.modal.create({
			title: 'Confirm Entry',
			html: 'Are you sure you want to enter card '+card_id+' for the Raffle Draw?',
			width: 350,
			disableClose: true,
			buttons: {
				'Confirm': function() {
					confirm_modal.hide();
					b.request({
						url: '/main/raffle/encode_rs',
						data: {
							'type': type,
							'card_id': card_id,
							'card_code': card_code,
							'account_id': account_id,
							'last_name': last_name,
							'first_name': first_name,
							'middle_name': middle_name,
							'address': address,
							'email': email,
							'contact_number': contact_number,
							'referror': referror
						},
						on_success: function(data) {
							if(data.status == 'ok')
							{
								var success_modal = b.modal.create({
									title: 'Encoding Success',
									html: data.msg,
									width: 400,
									disableClose: true,
									buttons: {
										'Close': function() {
											redirect('/main/raffle');
										}
									}
								});
								success_modal.show();
							}
							else if(data.status == 'error')
							{
								confirm_modal.hide();
								var error_modal = b.modal.create({
									title: 'Error in Request',
									html: data.msg,
									width: 400
								});
								error_modal.show();
							}
						}
					});
				},
				'Cancel': function(){
					confirm_modal.hide();
				}
			}
		});
		confirm_modal.show();
	});
	
	$("#view_raffle_entries").live("click",function() {
	    
		var account_id = $('#account_id').val();		
		  
		b.request({
			url: '/main/raffle/display_raffle_entries',
			data: {
				'account_id': account_id
			},
			on_success: function(data) {
				if(data.status == 'ok')
				{
					$("#title-container").html("Your Raffle Entries");
					$("#details-container").html(data.data.html);
					
				}
				else if(data.status == 'error')
				{
					var error_modal = b.modal.create({
						title: 'Error in Request',
						html: data.msg,
						width: 400
					});
					error_modal.show();
				}
			}
		});
				
	});
	
	
	$('body').on('focusout', '#account_id', function(){
		var account_id = $('#account_id').val();

		if(account_id != "") {
			$("#account_id_error").hide();			
		}
		
		$('#account_id_found').hide();
		$('#account_id_not_found').hide();
		$('#account_information_success').hide();
		$('#account_information_error').hide();
		if(account_id != "" && account_id.length == 10)
		{
			b.request({
				url: '/main/raffle/check_account_id',
				data: {
					'account_id': account_id,
				},
				on_success: function(data) {
					if(data.status == 'ok') {
						$('#account_id_found').show();
						$('#account_information_success').show();
						$('#account_information_success').html(data.msg);
					}
					else if(data.status == 'error')
					{
						$('#account_information_error').show();
						$('#account_information_error').html(data.msg);	
					}
				}
			});
		}
	});
	
	$('body').on('focusout', '#card_id', function(){
		var card_id = $('#card_id').val();
	
		if(card_id != "") {
			$("#card_id_error").hide();
			$("#card_id_success").hide();
		}
		
		b.request({
			url: '/main/raffle/check_card_id',
			data: {
				'card_id': card_id,
			},
			on_success: function(data) {
				if(data.status == 'ok') {
					$('#card_id_success').html(data.msg);
					$("#card_id_success").show();
					$('#card_id_error').hide();
					
				}
				else if(data.status == 'error')
				{
					$('#card_id_error').html(data.msg);
					$('#card_id_error').show();
					$("#card_id_success").hide();
				}
			}
		});
	});
	
	
	$(document).on("click","#clear_form",function(e){
		e.preventDefault();
		
		b.request({
			url: '/main/raffle/load_details_form',
			data: {},
			on_success: function(data) {
				$("#details-container").html(data.data.html);				
			}
		});
		
		
		$("input").val("");
		$('.details-group').removeAttr('disabled');
		$(".submit_entry").removeAttr('disabled');
		$(".submit_entry").removeClass('disabled');
		$(".error_msg").hide();
		
		$("#title-container").html("Enter Details");
		$("#account_information_success").hide();
		
	});
	
	
	
	
</script>
