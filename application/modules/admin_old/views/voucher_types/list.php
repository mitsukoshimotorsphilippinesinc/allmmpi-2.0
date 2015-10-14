<div class='alert alert-info'><h2>Voucher Types <a id='btn_add_new_voucher_type' class='btn btn-small' style='float:right;margin-right:-30px;margin-top:5px;margin-left:5px;'><i class="icon-plus"></i><span> Add New</span></a>

<a href="/admin/voucher_types/excel_view" style='float:right;margin-top:5px;margin-left:5px;' class="btn btn-primary btn-small pdf"><i class="icon-print icon-white"></i> Export As Excel</a>
</h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/voucher_types'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="code">Code</option>
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
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>ID</th>
			<th>Code</th>
			<th>Name</th>
			<th>Date Created</th>
			<th style='width:150px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($voucher_types)): ?>
		<tr><td colspan='6' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($voucher_types as $voucher_type): ?>
		<tr data='<?= $voucher_type->voucher_type_id ?>'>
			<td><?= $voucher_type->voucher_type_id; ?></td>
			<td><?= $voucher_type->code; ?></td>
			<td><?= $voucher_type->name; ?></td>
			<td><?= $voucher_type->insert_timestamp; ?></td>			
			<td>			
				<a title='Edit' class='btn btn-small btn-primary btn_edit_voucher_type'><i class="icon-pencil icon-white"></i></a>
				<a title='Delete' class='btn btn-small btn-danger btn_delete_voucher_type'><i class="icon-remove icon-white"></i></a>			
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

	$('#btn_add_new_voucher_type').live("click",function() {
				
		beyond.request({
			url : '/admin/voucher_types/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addVoucherTypeModal = b.modal.new({
						title: 'Add New Voucher Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addVoucherTypeModal.hide();
							},
							'Add' : function() {
								var _voucher_type_code = $('#voucher_type_code').val();
								var _voucher_type_name = $('#voucher_type_name').val();
								var _description = $('#description').val();							
								
								checkRequiredFields(_voucher_type_code, _voucher_type_name, _description);
							
								if (hasError == 0) {																	
									confirmAddVoucherType(_voucher_type_code, _voucher_type_name, _description);
									addVoucherTypeModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addVoucherTypeModal.show();					
				}
			}
		})				
	});

	var confirmAddVoucherType = function(voucher_type_code, voucher_type_name, description) {
		
		beyond.request({
			url : '/admin/voucher_types/confirm_add',
			data : {
				'_voucher_type_code' : voucher_type_code,
				'_voucher_type_name' : voucher_type_name,
				'_description' : description
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmAddVoucherTypeModal = b.modal.new({
						title: 'Add New Voucher Type',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddVoucherTypeModal.hide();
							},
							'Yes' : function() {
								addVoucherType(voucher_type_code, voucher_type_name, description);
								confirmAddVoucherTypeModal.hide();
							}
						}
					});
					confirmAddVoucherTypeModal.show();	
				} else {					
					var errorVoucherTypeModal = b.modal.new({
						title: 'Add New Voucher Type :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorVoucherTypeModal.hide();
							}
						}
					});
					errorVoucherTypeModal.show();
				}
			} // end on_success
		})
	};

	var addVoucherType = function(voucher_type_code, voucher_type_name, description) {	
		beyond.request({
			url : '/admin/voucher_types/add_voucher_type',
			data : {
				'_voucher_type_code' : voucher_type_code,
				'_voucher_type_name' : voucher_type_name,
				'_description' : description
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var addFacilityModal = b.modal.new({
						title: 'Add New Voucher Type : Successful',
						disableClose: true,
						html: 'You have successfully added a new Voucher Type',
						buttons: {
							'Ok' : function() {
								addFacilityModal.hide();
								redirect('/admin/voucher_types');
							}
						}
					});
					addFacilityModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_edit_voucher_type').live("click",function() {		
		var _voucher_type_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/voucher_types/edit',
			data : {
					'_voucher_type_id' : _voucher_type_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
				
					// show add form modal					
					var editVoucherTypeModal = b.modal.new({
						title: 'Edit Voucher Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editVoucherTypeModal.hide();
							},
							'Update' : function() {
								var _voucher_type_code = $('#voucher_type_code').val();
								var _voucher_type_name = $('#voucher_type_name').val();
								var _description = $('#description').val();							

								checkRequiredFields(_voucher_type_code, _voucher_type_name, _description);
							
								if (hasError == 0) {																	
									confirmEditVoucherType(_voucher_type_code, _voucher_type_name, _description, _voucher_type_id);
									editVoucherTypeModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editVoucherTypeModal.show();					
				}
			}
		})	
	});
	
	
	var confirmEditVoucherType = function(voucher_type_code, voucher_type_name, description, voucher_type_id) {
	
		beyond.request({
			url : '/admin/voucher_types/confirm_edit',
			data : {
				'_voucher_type_code' : voucher_type_code,
				'_voucher_type_name' : voucher_type_name,
				'_description' : description,
				'_voucher_type_id' : voucher_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmEditVoucherTypeModal = b.modal.new({
						title: 'Edit Voucher Type',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditVoucherTypeModal.hide();
							},
							'Yes' : function() {
								updateVoucherType(voucher_type_code, voucher_type_name, description, voucher_type_id);
								confirmEditVoucherTypeModal.hide();
							}
						}
					});
					confirmEditVoucherTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};

	var updateVoucherType = function(voucher_type_code, voucher_type_name, description, voucher_type_id) {	

		beyond.request({
			url : '/admin/voucher_types/update_voucher_type',
			data : {
				'_voucher_type_code' : voucher_type_code,
				'_voucher_type_name' : voucher_type_name,
				'_description' : description,
				'_voucher_type_id' : voucher_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var updateVoucherTypeModal = b.modal.new({
						title: 'Edit Voucher Type :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Voucher Type',
						buttons: {
							'Ok' : function() {
								updateVoucherTypeModal.hide();
								redirect('/admin/voucher_types');
							}
						}
					});
					updateVoucherTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_delete_voucher_type').live("click",function() {		
		var _voucher_type_id = $(this).parent().parent().attr("data");

		beyond.request({
			url : '/admin/voucher_types/delete',
			data : {
					'_voucher_type_id' : _voucher_type_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
				
					// show add form modal					
					var deleteVoucherTypeModal = b.modal.new({
						title: 'Delete Voucher Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteVoucherTypeModal.hide();
							},
							'Delete' : function() {																															
								confirmVoucherTypeDelete(_voucher_type_id);
								deleteVoucherTypeModal.hide();																
							}
						}
					});
					deleteVoucherTypeModal.show();					
				}
			}
		})
	
	});

	var confirmVoucherTypeDelete = function(voucher_type_id) {
		beyond.request({
			url : '/admin/voucher_types/confirm_delete',
			data : {
				'_voucher_type_id' : voucher_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmDeleteVoucherTypeModal = b.modal.new({
						title: 'Delete Voucher Type',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmDeleteVoucherTypeModal.hide();
							},
							'Yes' : function() {
								deleteVoucherType(voucher_type_id);
								confirmDeleteVoucherTypeModal.hide();
							}
						}
					});
					confirmDeleteVoucherTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};

	var deleteVoucherType = function(voucher_type_id) {	
		
		beyond.request({
			url : '/admin/voucher_types/delete_voucher_type',
			data : {
				'_voucher_type_id' : voucher_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var deleteVoucherTypeModal = b.modal.new({
						title: 'Delete Voucher Type :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Voucher Type.',
						buttons: {
							'Ok' : function() {
								deleteVoucherTypeModal.hide();
								redirect('/admin/voucher_types');
							}
						}
					});
					deleteVoucherTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	var checkRequiredFields = function(voucher_type_code, voucher_type_name, description) {
		hasError = 0;
		$('#voucher_type_code_error').hide();
		$('#voucher_type_name_error').hide();
		$('#description_error').hide();
	
		if ($.trim(voucher_type_code) == '') {
			$('#voucher_type_code_error').show();
			hasError = 1;
		}
	
		if ($.trim(voucher_type_name) == '') {
			$('#voucher_type_name_error').show();
			hasError = 1;
		}
		
		if ($.trim(description) == '') {
			$('#description_error').show();
			hasError = 1;
		}	
		
		return hasError;		
	};
	
</script>