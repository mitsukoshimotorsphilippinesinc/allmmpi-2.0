<div class='alert alert-info'><h2>Employee Slots <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New Employee Slot</span></a></h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/employee_slots'>
		<strong>Search By:&nbsp;</strong>
		<?= form_dropdown("search_option",array("product_name" => "Product Name"),$search_by,'id="search_option_wo" style="width:150px;"')?>
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
			<th>Product Name</th>
			<th>Quantity</th>
			<th style='width:150px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($employee_slots)): ?>
		<tr><td colspan='6' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($employee_slots as $employee_slot): ?>
		<tr data='<?= $employee_slot->product_id ?>'>
			<td><?= $employee_slot->product_name; ?></td>
			<td><?= $employee_slot->employee_slots; ?></td>
			<td>
				<a class='btn btn-small btn-primary btn_edit_employee_slot' title='Edit Employee Slot'><i class="icon-pencil icon-white"></i></a>
				<a class='btn btn-small btn-danger btn_delete_employee_slot' title='Delete Employee Slot'><i class="icon-remove icon-white"></i></a>
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
	$("#button_refresh").click(function(e) {
		e.preventDefault();
		redirect("/admin/employee_slots");
	});
	
	$("#btn_add_new").click(function(e) {
		e.preventDefault();
		
		beyond.request({
			url : '/admin/employee_slots/add',
			data : {},
			on_success : function(data) {
				if(data.status == "ok") {
					// show add form modal					
					var addEmployeeSlotModal = b.modal.new({
						title: 'Add New Employee Slot',
						width: 450,
						disableClose: true,
						html: data.msg,
						buttons: {
							'Cancel' : function() {
								addEmployeeSlotModal.hide();
							},
							'Add' : function() {
								var product_id = $('#product_id').val();
								var qty = $('#qty').val();
								
								checkRequiredFields(product_id,qty,function(hasError){
									if (hasError == 0) {
										confirmAddEmployeeSlot(product_id,qty);
										addEmployeeSlotModal.hide();
									}
								});
							}
						}
					});
					addEmployeeSlotModal.show();					
				}
				else
				{
					b.modal.create({
						title: "Error Notification",
						html: data.msg,
						width: 300
					}).show();
				}
			}
		})
		return false;
	});
	
	var confirmAddEmployeeSlot = function(product_id,qty) {
		beyond.request({
			url : '/admin/employee_slots/confirm_add',
			data : {
				'product_id' : product_id,
				'qty' : qty,
			},
			on_success : function(data) {
				if (data.status == "1")
				{
					
					var confirmAddEmployeeSlotModal = b.modal.new({
						title: 'Add New Employee Slot :: Confirm',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Cancel' : function() {
								confirmAddEmployeeSlotModal.hide();
							},
							'Yes' : function() {
								addEmployeeSlot(product_id,qty);
								confirmAddEmployeeSlotModal.hide();
							}
						}
					});
					confirmAddEmployeeSlotModal.show();	
				}
				else
				{
					var errorAddEmployeeSlotModal = b.modal.new({
						title: 'Add New Employee Slot :: Error',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Ok' : function() {
								errorAddEmployeeSlotModal.hide();
							}
						}
					});
					errorAddEmployeeSlotModal.show();
				}
			} // end on_success
		})
	};
	
	var addEmployeeSlot = function(product_id,qty) {	
		beyond.request({
			url : '/admin/employee_slots/add_employee_slot',
			data : {
					'action' : "add",
					'product_id' : product_id,
					'qty' : qty
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addEmployeeSlotModal = b.modal.new({
						title: 'Add New Employee Slot:: Successful',
						disableClose: true,
						html: 'You have successfully added a new employee slot',
						width: 350,
						buttons: {
							'Ok' : function() {
								addEmployeeSlotModal.hide();
								redirect('/admin/employee_slots');
							}
						}
					});
					addEmployeeSlotModal.show();	
				} else {
					var errorAddEmployeeSlotModal = b.modal.new({
						title: 'Add New Employee Slot :: Error',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Ok' : function() {
								errorAddEmployeeSlotModal.hide();
							}
						}
					});
					errorAddEmployeeSlotModal.show();
				}
			} // end on_success
		})		
	};
	
	$(document).on("click",".btn_edit_employee_slot",function(){
		var product_id = $(this).parent().parent().attr("data");
		beyond.request({
			url : '/admin/employee_slots/edit',
			data : {
				"product_id": product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var editEmployeeSlotModal = b.modal.new({
						title: 'Edit Employee Slot',
						width: 350,
						disableClose: true,
						html: data.msg,
						buttons: {
							'Cancel' : function() {
								editEmployeeSlotModal.hide();
							},
							'Edit' : function() {
								var orig_product_id = $('#orig_product_id').val();
								var qty = $('#qty').val();
								
								checkRequiredFields(orig_product_id,qty,function(hasError){
									if (hasError == 0) {
										confirmEditEmployeeSlot(orig_product_id,qty);
										editEmployeeSlotModal.hide();
									}
								});
							}
						}
					});
					editEmployeeSlotModal.show();					
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
	
	var confirmEditEmployeeSlot = function(orig_product_id,qty) {
		beyond.request({
			url : '/admin/employee_slots/confirm_edit',
			data : {
				'orig_product_id' : orig_product_id,
				'qty' : qty
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddEmployeeModal = b.modal.new({
						title: 'Edit Employee Slot :: Confirm',
						disableClose: true,
						html: data.msg,
						buttons: {
							'No' : function() {
								confirmAddEmployeeModal.hide();
							},
							'Yes' : function() {
								editEmployeeSlot(orig_product_id,qty);
								confirmAddEmployeeModal.hide();
							}
						}
					});
					confirmAddEmployeeModal.show();	
				} else {
					var errorAddEmployeeModal = b.modal.new({
						title: 'Add New Employee Slot :: Error',
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
	
	var editEmployeeSlot = function(product_id,qty) {	
		beyond.request({
			url : '/admin/employee_slots/add_employee_slot',
			data : {
					'action' : "edit",
					'product_id' : product_id,
					'qty' : qty
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addEmployeeModal = b.modal.new({
						title: 'Edit Employee Slot :: Successful',
						disableClose: true,
						html: 'You have successfully edited an employee slot',
						width: 350,
						buttons: {
							'Ok' : function() {
								addEmployeeModal.hide();
								redirect('/admin/employee_slots');
							}
						}
					});
					addEmployeeModal.show();	
				} else {
					var errorAddEmployeeSlotModal = b.modal.new({
						title: 'Edit Employee Slot :: Error',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Ok' : function() {
								errorAddEmployeeSlotModal.hide();
							}
						}
					});
					errorAddEmployeeSlotModal.show();
				}
			} // end on_success
		})		
	};
	
	$(document).on("click",".btn_delete_employee_slot",function(){
		var product_id = $(this).parent().parent().attr("data");
		beyond.request({
			url : '/admin/employee_slots/delete',
			data : {
				"product_id": product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var deleteEmployeeSlotModal = b.modal.new({
						title: 'Delete Employee Slot',
						width: 350,
						disableClose: true,
						html: data.msg,
						buttons: {
							'Cancel' : function() {
								deleteEmployeeSlotModal.hide();
							},
							'Delete' : function() {
								deleteEmployeeSlotModal.hide();
								deleteEmployeeSlot(product_id);
							}
						}
					});
					deleteEmployeeSlotModal.show();					
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
	
	var deleteEmployeeSlot = function(product_id) {
		beyond.request({
			url : '/admin/employee_slots/delete_employee_slot',
			data : {
					'product_id' : product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var deleteEmployeeSlotModal = b.modal.new({
						title: 'Delete Employee Slot :: Successful',
						disableClose: true,
						html: 'You have successfully deleted an employee slot.',
						width: 350,
						buttons: {
							'Ok' : function() {
								deleteEmployeeSlotModal.hide();
								redirect('/admin/employee_slots');
							}
						}
					});
					deleteEmployeeSlotModal.show();	
				} else {
					var errorAddEmployeeSlotModal = b.modal.new({
						title: 'Delete Employee Slot :: Error',
						disableClose: true,
						html: data.msg,
						buttons: {
							'Ok' : function() {
								errorAddEmployeeSlotModal.hide();
							}
						}
					});
					errorAddEmployeeSlotModal.show();
				}
			} // end on_success
		});
	}
	
	var checkRequiredFields = function(product_id,qty,cb) {
		hasError = 0;
		$('#product_id_error').hide();
		$('#qty_error').hide();
		
		if (product_id == '') {
			$('#product_id_error').show();
			hasError = 1;
		}
		
		if ($.trim(qty) == '' || qty == 0) {
			$('#qty_error').show();
			hasError = 1;
		}

		if(_.isFunction(cb)) cb.call(this,hasError);
	};
</script>