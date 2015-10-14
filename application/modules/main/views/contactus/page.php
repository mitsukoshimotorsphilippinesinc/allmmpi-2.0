<h2><?= $title ?></h2>
<div>
<?= $contact_details->body; ?>
</div>
<br>
<br>
<div>
	<p><strong><em>For your queries, suggestions and comments, please fill out the form below.</em></strong><br /></p>
	<div>
		<form action="/main/contactus/email" method="post">
			<p>Name <em>*</em><br />
				<span><input id="name" class="profile-label-container" type="text" name="name" value="<?= set_value("name"); ?>" size="40" /></span>
				<br>
				<span style="color: red;"><?= $this->form_validation->error('name'); ?></span>
			</p>
			<p>Email  <em>*</em><br />
				<span><input id="email" class="profile-label-container" type="text" name="email" value="<?= set_value("email"); ?>" size="40" /></span>
				<br>
				<span style="color: red;"><?= $this->form_validation->error('email'); ?></span>
			</p>
			<p>Subject <em>*</em><br />
				<span><input id="subject" class="profile-label-container" type="text" name="subject" value="<?= set_value("subject"); ?>" size="40" /></span>
				<br>
				<span style="color: red;"><?= $this->form_validation->error('subject'); ?></span>
			</p>
			<p>Message  <em>*</em><br />
				<span><textarea id="message" class="profile-label-container" name="message" cols="40" rows="10"><?= set_value("message"); ?></textarea></span>
				<br>
				<span style="color: red;"><?= $this->form_validation->error('message'); ?></span>
			</p>
			<br>
			<!--<p><input type="submit" value="Send"></p>-->
			<p><a class="btn btn-primary" id="send_email">Send</a></p>
		</form>
	</div>
	
	
	
</div>

<script>
	$("body").on('click', '#send_email', function() {
		var name = $("#name").val();
		var email = $("#email").val();
		var subject = $("#subject").val();
		var message = $("#message").val();
		
		var confirm_modal = b.modal.new({
			title: 'Confirm Sending',
			width: 450,
			disableClose: true,
			html: "Are you sure you want to send this message with the following details?",
			buttons: {
				'Confirm' : function() {
					//request		
					confirm_modal.hide();
					b.request({
						url: '/main/contactus/email',
						data: {
							'name': name,
							'email': email,
							'subject': subject,
							'message': message
						},
						on_success: function(data) {
							if(data.status == 1) {
								var success_modal = b.modal.new({
									title: 'Message Sending Successful',
									width: 450,
									disableClose: true,
									html: data.msg,
									buttons: {
										'Close' : function() {
											success_modal.hide();
										}
									}
								});
								success_modal.show();
							}else {
								var error_modal = b.modal.new({
									title: 'Error Found',
									width: 450,
									disableClose: true,
									html: "There was an error in your request.",
									buttons: {
										'Close' : function() {
											error_modal.hide();
										}
									}
								});
								error_modal.show();
							}
						}	
					});
				},
				'Cancel' : function() {
					confirm_modal.hide();
				}
			}
		});
		confirm_modal.show();		
	});
</script>