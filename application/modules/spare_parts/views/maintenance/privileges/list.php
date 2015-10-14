<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Users and Privileges</h2></div>

<div >
	<form id='search_details' method='get' action ='/spare_parts/maintenance/privileges'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="username">Username</option>
			<option value="email">Email</option>
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
		
			<span class="label label-info">Search Results for:</span>
			<span class="label label-success"><?= $search_by ?></span>
			<span class="label label-success"><?= $search_text ?></span>
		</div>		
	</form>
</div>


<hr/>
<table class='table table-striped'>
	<thead>
		<tr>						
			<th>Id Number</th>
			<th>Username</th>
			<th>E-mail</th>
			<th>Name</th>			
			<th style=''>Status</th>
			<th style='width:100px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($users)): ?>
		<tr><td colspan='6' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($users as $user): ?>
		<tr data='<?= $user->id_number ?>'>			
			<?php
				$user_account_details = $this->user_model->get_user_by_id_number($user->id_number);
			?>
			<td style="text-align:right;"><?= $user->id_number; ?></td>
			<td style="text-align:center;"><?= $user_account_details->username; ?></td>
			<td style="text-align:center;"><?= $user->company_email_address == NULL ? 'N/A' : $user->company_email_address; ?></td>			
			<td style="text-align:center;"><?= ucfirst($user->first_name) .' '. ucfirst($user->middle_name) .' '. ucfirst($user->last_name); ?></td>					
			<td style="text-align:center;">
				<?php
				if ($user_account_details->is_active == 0)				
					echo "<span class='label label-important'>INACTIVE</span>";
				else 
					echo "<span class='label label-success'>ACTIVE</span>";
				?>				
			<td>
				<a href='/spare_parts/maintenance/edit_user/<?= $user_account_details->user_id ?>' class='btn btn-small btn-primary' title='Edit User'><i class="icon-pencil icon-white"></i></a>				
				<a href='/spare_parts/maintenance/edit_privilege/<?= $user_account_details->user_id ?>' class='btn btn-small btn-primary' title='User Privileges'><i class="icon-lock icon-white"></i></a>
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

	$('#btn_add_new').live("click",function() {		
		beyond.request({
			url : '/admin/users/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addUserModal = b.modal.new({
						title: 'Add New User',
						width: 350,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addUserModal.hide();
							},
							'Add' : function() {
								var _username = $('#username').val();
								var _email = $('#email').val();
								var _password = $('#password').val();
								var _first_name = $('#first_name').val();
								var _last_name = $('#last_name').val();
								var _mobile_number = $('#mobile_number').val();
								var _user_facility = $('#user_facility').val();
								var _is_active = $('#is_active').val();
								var _default_page = $('#default_page').val();

								b.request({
									url: "/admin/users/check_username",
									data: {
										"username": _username
									},
									on_success: function(data)
									{

										$('#username_exist_error').hide();
										checkRequiredFields(_username, _email, _password, _first_name, _last_name, _mobile_number, _user_facility, _is_active, _default_page,function(hasError){
											if(data.status == "error")
											{
												$('#username_exist_error').show();
												hasError = 1;
											}

											if (hasError == 0) {
												confirmAddUser(_username, _email, _password, _first_name, _last_name, _mobile_number, _user_facility, _is_active, _default_page);
												addUserModal.hide();
											}
										});
									}
								});	
							}
						}
					});
					addUserModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmAddUser = function(username, email, password, first_name, last_name, mobile_number, user_facility, is_active, default_page) {
		beyond.request({
			url : '/admin/users/confirm_add',
			data : {
				'_username' : username,
				'_email' : email,
				'_password' : password,
				'_first_name' : first_name,
				'_last_name' : last_name,
				'_mobile_number' : mobile_number,
				'_user_facility' : user_facility,
				'_is_active' : is_active,
				'_default_page' : default_page
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmaddUserModal = b.modal.new({
						title: 'Add New User :: Confirm',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmaddUserModal.hide();
							},
							'Yes' : function() {
								adspare_partsoduct(username, email, password, first_name, last_name, mobile_number, user_facility, is_active, default_page);
								confirmaddUserModal.hide();
							}
						}
					});
					confirmaddUserModal.show();	
				} else {
					var errorAddUserModal = b.modal.new({
						title: 'Add New User :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorAddUserModal.hide();
							}
						}
					});
					errorAddUserModal.show();
				}
			} // end on_success
		})
	};
	
	var adspare_partsoduct = function(username, email, password, first_name, last_name, mobile_number, user_facility, is_active, default_page) {	
		beyond.request({
			url : '/admin/users/add_user',
			data : {
					'_username' : username,
					'_email' : email,
					'_password' : password,
					'_first_name' : first_name,
					'_last_name' : last_name,
					'_mobile_number' : mobile_number,
					'_user_facility' : user_facility,
					'_is_active' : is_active,
					'_default_page' : default_page
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addUserModal = b.modal.new({
						title: 'Add New User :: Successful',
						disableClose: true,
						html: 'You have successfully added a new User',
						buttons: {
							'Ok' : function() {
								addUserModal.hide();
								redirect('/admin/users');
							}
						}
					});
					addUserModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	
	$('.btn_delete_user').live("click",function() {		
		var _user_id = $(this).parent().parent().attr("data");

		beyond.request({
			url : '/admin/users/delete',
			data : {
					'_user_id' : _user_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deleteUserModal = b.modal.new({
						title: 'Delete User',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteUserModal.hide();
							},
							'Delete' : function() {																															
								confirmDeleteUser(_user_id);
								deleteUserModal.hide();																
							}
						}
					});
					deleteUserModal.show();					
				}
			}
		})
		return false;
	});
	
	var confirmDeleteUser = function(user_id) {
		beyond.request({
			url : '/admin/users/confirm_delete',
			data : {
				'_user_id' : user_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeleteUserModal = b.modal.new({
						title: 'Delete User',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmDeleteUserModal.hide();
							},
							'Yes' : function() {
								deleteUser(user_id);
								confirmDeleteUserModal.hide();
							}
						}
					});
					confirmDeleteUserModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deleteUser = function(user_id) {	
		beyond.request({
			url : '/admin/users/delete_user',
			data : {
				'_user_id' : user_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var deleteUserModal = b.modal.new({
						title: 'Delete User :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a User',
						buttons: {
							'Ok' : function() {
								deleteUserModal.hide();
								redirect('/admin/users');
							}
						}
					});
					deleteUserModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	var checkRequiredFields = function(username, email, password, first_name, last_name, mobile_number, user_facility, is_active, default_page, cb) {
		hasError = 0;
		$('#username_error').hide();
		$('#email_error').hide();
		$('#password_error').hide();
		$('#first_name_error').hide();
		$('#last_name_error').hide();
		$('#mobile_number_error').hide();
		$('#user_facility_error').hide();
		$('#is_active_error').hide();
		$('#default_page_error').hide();
		
		if ($.trim(username) == '') {
			$('#username_error').show();
			hasError = 1;
		}
		
		if ($.trim(email) == '') {
			$('#email_error').show();
			hasError = 1;
		}
		
		if (password == '') {
			$('#password_error').show();
			hasError = 1;
		}
		
		if ($.trim(first_name) == '') {
			$('#first_name_error').show();
			hasError = 1;
		}
		
		if ($.trim(last_name) == '') {
			$('#last_name_error').show();
			hasError = 1;
		}
		
		if (mobile_number == '') {
			$('#mobile_number_error').show();
			hasError = 1;
		}
		
		if (user_facility == '') {
			$('#user_facility_error').show();
			hasError = 1;
		}	
		
		if (is_active == '') {
			$('#is_active_error').show();
			hasError = 1;
		}
		
		if ($.trim(default_page) == '') {
			$('#default_page_error').show();
			hasError = 1;
		}

		if(_.isFunction(cb)) cb.call(this,hasError);
	};
	
</script>