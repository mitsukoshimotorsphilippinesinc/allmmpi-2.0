<div class='alert alert-info'><h2>Employees <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New Employee</span></a></h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/employees'>
		<strong>Search By:&nbsp;</strong>
		<?= form_dropdown("search_option",array("employee_id" => "Employee ID", "name" => "Name", "email" => "Email"),$search_by,'id="search_option_wo" style="width:150px;"')?>
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
			<th>Employee ID</th>
			<th>Name</th>
			<th>E-mail</th>
			<th style='width:150px;'>Employee Type</th>
			<th>Mobile Number</th>
			<th style='width:150px;'>Status</th>
			<th style='width:150px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($employees)): ?>
		<tr><td colspan='6' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($employees as $employee): ?>
		<tr data='<?= $employee->employee_id ?>'>
			<td><?= $employee->employee_code; ?></td>
			<td><?= ucfirst($employee->first_name).' '.ucfirst($employee->last_name); ?></td>
			<td><?= $employee->email; ?></td>
			<td>
				<?php
					$employee_type = $this->employees_model->get_employee_type_by_id($employee->employee_type_id);
					echo $employee_type->name;
				?>
			</td>
			<td><?= $employee->mobile_number; ?></td>
			<td><?= $employee->is_active == 1 ? 'Active' : 'Inactive'; ?></td>
			<td>
				<a class='btn btn-small btn-success btn_view_change_log' title='View Change Log'><i class="icon-list-alt icon-white"></i></a>
				<a class='btn btn-small btn-primary btn_edit_employee' title='Edit Employee'><i class="icon-pencil icon-white"></i></a>
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
			url : '/admin/employees/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addEmployeeModal = b.modal.new({
						title: 'Add New Employee',
						width: 350,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addEmployeeModal.hide();
							},
							'Add' : function() {
								var employee_code = $('#employee_code').val();
								var email = $('#email').val();
								var first_name = $('#first_name').val();
								var last_name = $('#last_name').val();
								var mobile_number = $('#mobile_number').val();
								var is_active = $('#is_active').val();
								var employee_type_id = $('#employee_type_id').val();

								b.request({
									url: "/admin/employees/check_employee_code",
									data: {
										"employee_code": employee_code,
										"orig_employee_code": ""
									},
									on_success: function(data)
									{

										$('#employee_code_exist_error').hide();
										checkRequiredFields(employee_code,email,first_name,last_name,mobile_number,is_active,function(hasError){
											if(data.status == "error")
											{
												$('#employee_code_exist_error').show();
												hasError = 1;
											}

											if (hasError == 0) {
												confirmAddEmployee(employee_code,email,first_name,last_name,mobile_number,is_active,employee_type_id);
												addEmployeeModal.hide();
											}
										});
									}
								});	
							}
						}
					});
					addEmployeeModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmAddEmployee = function(employee_code,email,first_name,last_name,mobile_number,is_active,employee_type_id) {
		beyond.request({
			url : '/admin/employees/confirm_add',
			data : {
				'employee_code' : employee_code,
				'email' : email,
				'first_name' : first_name,
				'last_name' : last_name,
				'mobile_number' : mobile_number,
				'employee_type_id': employee_type_id,
				'is_active' : is_active
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddEmployeeModal = b.modal.new({
						title: 'Add New Employee :: Confirm',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Cancel' : function() {
								confirmAddEmployeeModal.hide();
							},
							'Yes' : function() {
								addEmployee(employee_code,email,first_name,last_name,mobile_number,is_active,employee_type_id);
								confirmAddEmployeeModal.hide();
							}
						}
					});
					confirmAddEmployeeModal.show();	
				} else {
					var errorAddEmployeeModal = b.modal.new({
						title: 'Add New employee :: Error',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Ok' : function() {
								errorAddEmployeeModal.hide();
							}
						}
					});
					errorAddEmployeeModal.show();
				}
			} // end on_success
		})
	};
	
	var addEmployee = function(employee_code,email,first_name,last_name,mobile_number,is_active,employee_type_id) {	
		beyond.request({
			url : '/admin/employees/add_employee',
			data : {
					'employee_code' : employee_code,
					'email' : email,
					'first_name' : first_name,
					'last_name' : last_name,
					'mobile_number' : mobile_number,
					'employee_type_id': employee_type_id,
					'is_active' : is_active
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addEmployeeModal = b.modal.new({
						title: 'Add New Employee :: Successful',
						disableClose: true,
						html: 'You have successfully added a new employee',
						width: 350,
						buttons: {
							'Ok' : function() {
								addEmployeeModal.hide();
								redirect('/admin/employees');
							}
						}
					});
					addEmployeeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$(document).on("click",".btn_edit_employee",function(){
		var employee_id = $(this).parent().parent().attr("data");
		beyond.request({
			url : '/admin/employees/edit',
			data : {
				"employee_id": employee_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addEmployeeModal = b.modal.new({
						title: 'Edit Employee',
						width: 350,
						disableClose: true,
						html: data.msg,
						buttons: {
							'Cancel' : function() {
								addEmployeeModal.hide();
							},
							'Edit' : function() {
								var orig_employee_code = $('#orig_employee_code').val();
								var employee_code = $('#employee_code').val();
								var email = $('#email').val();
								var first_name = $('#first_name').val();
								var last_name = $('#last_name').val();
								var mobile_number = $('#mobile_number').val();
								var employee_type_id = $('#employee_type_id').val();
								var is_active = $('#is_active').val();
								var change_log = $('#change_log').val();
								b.request({
									url: "/admin/employees/check_employee_code",
									data: {
										"employee_code": employee_code,
										"orig_employee_code": orig_employee_code
									},
									on_success: function(data)
									{

										$('#employee_code_exist_error').hide();
										$('#change_log_error').hide();
										checkRequiredFields(employee_code,email,first_name,last_name,mobile_number,is_active,function(hasError){
											if(change_log == "")
											{
												$('#change_log_error').show();
												hasError = 1;
											}
											
											if(data.status == "error")
											{
												$('#employee_code_exist_error').show();
												hasError = 1;
											}

											if (hasError == 0) {
												confirmEditEmployee(employee_id,orig_employee_code,employee_code,email,first_name,last_name,mobile_number,employee_type_id,is_active,change_log);
												addEmployeeModal.hide();
											}
										});
									}
								});	
							}
						}
					});
					addEmployeeModal.show();					
				}
				else
				{
					b.modal.new({
						title : "Error Notification",
						width : 350,
						html : data.msg
					}).show();
				}
			}
		})
		return false;
	});
	
	var confirmEditEmployee = function(employee_id,orig_employee_code,employee_code,email,first_name,last_name,mobile_number,employee_type_id,is_active,change_log) {
		beyond.request({
			url : '/admin/employees/confirm_edit',
			data : {
				'orig_employee_code' : orig_employee_code,
				'employee_code' : employee_code,
				'email' : email,
				'first_name' : first_name,
				'last_name' : last_name,
				'mobile_number' : mobile_number,
				'employee_type_id' : employee_type_id,
				'is_active' : is_active,
				'change_log' : change_log
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddEmployeeModal = b.modal.new({
						title: 'Edit Employee :: Confirm',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Cancel' : function() {
								confirmAddEmployeeModal.hide();
							},
							'Yes' : function() {
								editEmployee(employee_id,employee_code,email,first_name,last_name,mobile_number,employee_type_id,is_active,change_log);
								confirmAddEmployeeModal.hide();
							}
						}
					});
					confirmAddEmployeeModal.show();	
				} else {
					var errorAddEmployeeModal = b.modal.new({
						title: 'Edit Employee :: Error',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Ok' : function() {
								errorAddEmployeeModal.hide();
							}
						}
					});
					errorAddEmployeeModal.show();
				}
			} // end on_success
		})
	};
	
	var editEmployee = function(employee_id,employee_code,email,first_name,last_name,mobile_number,employee_type_id,is_active,change_log) {	
		beyond.request({
			url : '/admin/employees/edit_employee',
			data : {
					'employee_id' : employee_id,
					'employee_code' : employee_code,
					'email' : email,
					'first_name' : first_name,
					'last_name' : last_name,
					'mobile_number' : mobile_number,
					'employee_type_id' : employee_type_id,
					'is_active' : is_active,
					'change_log' : change_log
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addEmployeeModal = b.modal.new({
						title: 'Edit Employee :: Successful',
						disableClose: true,
						html: 'You have successfully edited an employee',
						width: 350,
						buttons: {
							'Ok' : function() {
								addEmployeeModal.hide();
								redirect('/admin/employees');
							}
						}
					});
					addEmployeeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$(document).on("click",".btn_view_change_log",function(){
		var employee_id = $(this).parent().parent().attr("data");
		beyond.request({
			url: '/admin/employees/view_change_log',
			data: {
				"employee_id" : employee_id
			},
			on_success: function(data){
				if(data.status == "ok")
				{
					b.modal.create({
						title: "View Change Log",
						html: data.msg,
						width: 600
					}).show();
				}
				else
				{
					b.modal.create({
						title: "Error Notification",
						html: data.msg,
						width: 350
					}).show();
				}
			}
		});
	});
	
	var checkRequiredFields = function(employee_code,email,first_name,last_name,mobile_number,is_active, cb) {
		hasError = 0;
		$('#employee_code_error').hide();
		$('#email_error').hide();
		$('#first_name_error').hide();
		$('#last_name_error').hide();
		$('#mobile_number_error').hide();
		$('#is_active_error').hide();
		
		if ($.trim(employee_code) == '') {
			$('#employee_code_error').show();
			hasError = 1;
		}
		
		if ($.trim(email) == '') {
			$('#email_error').show();
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
		
		if (is_active == '') {
			$('#is_active_error').show();
			hasError = 1;
		}

		if(_.isFunction(cb)) cb.call(this,hasError);
	};
	
</script>