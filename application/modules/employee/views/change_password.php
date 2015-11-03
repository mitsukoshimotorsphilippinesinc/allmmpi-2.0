<div class='content-area'>
	<h2>Change Password</h2>
	<div class='well' >
		<form action="/employee/signin/proceed_password_change" method="post" class="form-inline" >
		<div id="username_control" class="control-group <?= $this->form_validation->error_class('email') ?>">
			<label class="control-label" for="email">Enter your new password and click "Submit Request" button to receive instructions via your email. Follow these instructions to complete the Change Password process.</label>				
				<br/>
				<br/>
				<div id="new_password_control" class="control-group ">
					<label class="control-label" for="new_password">New Password</label>
					<div class="controls">
						<input id="new_password" type="password" name="new_password" placeholder="New Password">
						<p id="new_password_help" class="help-block"></p>
					</div>
				</div>
				<div id="retype_new_password_control" class="control-group ">
					<label class="control-label" for="retype_new_password">Re-type New Password</label>
					<div class="controls">
						<input id="retype_new_password" type="password" value="" name="retype_new_password" placeholder="Retype New Password">
						<p id="retype_new_password_help" class="help-block"></p>
						<input id="id_number" style="display:none;" value="<?= $user_access_details->id_number; ?>" name="id_number" placeholder="ID Number">
					</div>
				</div>									
		</div>

		<div >
			<button id="" type="submit" class="btn btn-success">Submit Request</button>
		</div>
		</form>
	</div>
</div>

<script type="text/javascript">

	$("#submit-request").click(function(){

		var id_number = "<?= $user_access_details->id_number ?>";
		
		beyond.request({
			url: '/employee/signin/proceed_password_change',
			data: {
				"new_password": $("#new_password").val().trim(),
				"id_number": id_number,

			},
			on_success: function(data){
				if(data.status) {					
					$("#result-count").html(data.data.result_count);					
					$("#contents").html(data.data.html);
					$("#pagination").html(data.data.pagination);

					$('.goto_page').click(function(e){
						e.preventDefault();
						var new_page = $(this).attr('page');
						_current_page = new_page;
						loadResults(new_page);																
					});
				} else {
					var err_modal = beyond.modal.create({
						title: 'Error :: Error',
						html: data.msg
					});
					err_modal.show();
				}
			}
		});

	});

</script>