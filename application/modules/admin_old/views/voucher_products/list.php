<div class='alert alert-info'><h2>Voucher Products <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<div id="search_container">
	<form id='search_details' method='get' action ='/admin/voucher_products'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">
			<option value="voucher_product_name">Product Name</option>	
			<option value="voucher_type">Type</option>		
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
			<span class="label label-success"><?= $searchBy ?></span>
			<span class="label label-success"><?= $search_text ?></span>
		</div>		
	</form>
</div>	

<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style='width:250px;'>Product Name</th>
			<th style='width:150px;'>Type</th>		
			<th style='width:100px;'>Date Created</th>
			<th style='width:100px;'>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($voucher_products)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($voucher_products as $voucher_product): ?>
		<tr data ='<?= $voucher_product->voucher_product_id ?>'>
			<td><?= $voucher_product->voucher_product_name; ?></td>
			
			<?php
				$voucher_product_type = $this->vouchers_model->get_is_voucher_type_by_id($voucher_product->voucher_type_id);
				
				if (empty($voucher_product_type)) {
					$product_type = '';
				} else {
					$product_type = $voucher_product_type->code . ' - ' . $voucher_product_type->name;
				}
			?>
			
			<?php
				if ($product_type == '') {
			?>
				<td><span class='label label-important' id='vp_type_error'>Unknown Voucher Type</span></td>
			<?php } else { ?>
				<td><?= $product_type; ?></td>
			<?php
				}
			?>	
			<td><?= $voucher_product->insert_timestamp; ?></td>			
			<td>
				<a class='btn btn-small btn-primary btn_create_voucher_product'><i class="icon-gift icon-white" title="Create Voucher" ></i></a>
				<a class='btn btn-small btn-primary btn_edit_voucher_product'><i class="icon-pencil icon-white" title="Edit" ></i></a>
				<a class='btn btn-small btn-danger btn_delete_voucher_product'><i class="icon-remove icon-white" title="Delete" ></i></a>
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
		windows.location.href = '/admin/voucher_products';
	});
	
	$('#btn_add_new').live("click",function() {		
		beyond.request({
			url : '/admin/voucher_products/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addVoucherProductModal = b.modal.new({
						title: 'Add New Voucher Product',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addVoucherProductModal.hide();
							},
							'Add' : function() {
								var _voucher_product_name = $('#voucher_product_name').val();
								var _voucher_type_id = $('#voucher_type_id').val();
								
								//checkRequiredFields(_voucher_product_name, _voucher_type_id);
								var _var_data = _voucher_product_name + '|' + _voucher_type_id;
								checkRequiredFields('add', _var_data);
								
								if (hasError == 0) {																	
									confirmAddVoucherProduct(_voucher_product_name, _voucher_type_id);
									addVoucherProductModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addVoucherProductModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmAddVoucherProduct = function(voucher_product_name, voucher_type_id) {

		beyond.request({
			url : '/admin/voucher_products/confirm_add',
			data : {
				'_voucher_product_name' : voucher_product_name,
				'_voucher_type_id' : voucher_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddVoucherProductModal = b.modal.new({
						title: 'Add New Voucher Product',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddVoucherProductModal.hide();
							},
							'Yes' : function() {
								addVoucherProduct(voucher_product_name, voucher_type_id);
								confirmAddVoucherProductModal.hide();
							}
						}
					});
					confirmAddVoucherProductModal.show();	
				} else {
					var errorVoucherProductModal = b.modal.new({
						title: 'Add New Voucher Product :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorVoucherProductModal.hide();
							}
						}
					});
					errorVoucherProductModal.show();
				}
			} // end on_success
		})
	};
	
	var addVoucherProduct = function(voucher_product_name, voucher_type_id) {	
		beyond.request({
			url : '/admin/voucher_products/add_voucher_product',
			data : {
				'_voucher_product_name' : voucher_product_name,
				'_voucher_type_id' : voucher_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addVoucherProductModal = b.modal.new({
						title: 'Add New Voucher Product : Successful',
						disableClose: true,
						html: 'You have successfully added a new Voucher Product',
						buttons: {
							'Ok' : function() {
								addVoucherProductModal.hide();
								redirect('/admin/voucher_products');
							}
						}
					});
					addVoucherProductModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};

	
	$('.btn_edit_voucher_product').live("click",function() {		
		var _voucher_product_id = $(this).parent().parent().attr("data");
				
		beyond.request({
			url : '/admin/voucher_products/edit',
			data : {
					'_voucher_product_id' : _voucher_product_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var editVoucherProductModal = b.modal.new({
						title: 'Edit Voucher Product',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editVoucherProductModal.hide();
							},
							'Update' : function() {
								var _voucher_product_name = $('#voucher_product_name').val();
								var _voucher_type_id = $('#voucher_type_id').val();								
							
								//checkRequiredFields(_voucher_product_name, _voucher_type_id);
								var _var_data = _voucher_product_name + '|' + _voucher_type_id;
								checkRequiredFields('edit', _var_data);
								
								if (hasError == 0) {																	
									confirmEditVoucherProduct(_voucher_product_name, _voucher_type_id, _voucher_product_id);
									
									editVoucherProductModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editVoucherProductModal.show();					
				}
			}
		})
		return false;		
	});
	
	var confirmEditVoucherProduct = function(voucher_product_name, voucher_type_id, voucher_product_id) {
		
		beyond.request({
			url : '/admin/voucher_products/confirm_edit',
			data : {
				'_voucher_product_name' : voucher_product_name,
				'_voucher_type_id' : voucher_type_id,
				'_voucher_product_id' : voucher_product_id				
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmEditVoucherProductModal = b.modal.new({
						title: 'Edit Voucher Product',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditVoucherProductModal.hide();
							},
							'Yes' : function() {
								updateVoucherProduct(voucher_product_name, voucher_type_id, voucher_product_id);
								confirmEditVoucherProductModal.hide();
							}
						}
					});
					confirmEditVoucherProductModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var updateVoucherProduct = function(voucher_product_name, voucher_type_id, voucher_product_id) {
							
		beyond.request({
			url : '/admin/voucher_products/update_voucher_product',
			data : {
				'_voucher_product_name' : voucher_product_name,
				'_voucher_type_id' : voucher_type_id,
				'_voucher_product_id' : voucher_product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var updateProductModal = b.modal.new({
						title: 'Edit Product :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Voucher Product',
						buttons: {
							'Ok' : function() {
								updateProductModal.hide();
								redirect('/admin/voucher_products');
							}
						}
					});
					updateProductModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_delete_voucher_product').live("click",function() {		
		var _voucher_product_id = $(this).parent().parent().attr("data");

		beyond.request({
			url : '/admin/voucher_products/delete',
			data : {
					'_voucher_product_id' : _voucher_product_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deleteVoucherProductModal = b.modal.new({
						title: 'Delete Voucher Product',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteVoucherProductModal.hide();
							},
							'Delete' : function() {																															
								confirmDeleteVoucherProduct(_voucher_product_id);
								deleteVoucherProductModal.hide();																
							}
						}
					});
					deleteVoucherProductModal.show();					
				}
			}
		})
		return false;
	});
	
	var confirmDeleteVoucherProduct = function(voucher_product_id) {
		beyond.request({
			url : '/admin/voucher_products/confirm_delete',
			data : {
				'_voucher_product_id' : voucher_product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeleteVoucherProductsModal = b.modal.new({
						title: 'Delete Voucher Products',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmDeleteVoucherProductsModal.hide();
							},
							'Yes' : function() {
								deleteVoucherProduct(voucher_product_id);
								confirmDeleteVoucherProductsModal.hide();
							}
						}
					});
					confirmDeleteVoucherProductsModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deleteVoucherProduct = function(voucher_product_id) {	
		beyond.request({
			url : '/admin/voucher_products/delete_voucher_product',
			data : {
				'_voucher_product_id' : voucher_product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var deleteVoucherProductModal = b.modal.new({
						title: 'Delete Voucher Product :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Voucher Product',
						buttons: {
							'Ok' : function() {
								deleteVoucherProductModal.hide();
								redirect('/admin/voucher_products');
							}
						}
					});
					deleteVoucherProductModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	
	$('.btn_create_voucher_product').live("click",function() {		
		var _voucher_product_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/voucher_products/create',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var createModal = b.modal.new({
						title: 'Create Voucher :: Details',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								createModal.hide();
							},
							'Create' : function() {
								var _last_name = $('#last_name').val();
								var _first_name = $('#first_name').val();
								var _middle_name = $('#middle_name').val();
								var _mobile_number = $('#mobile_number').val();
								var _email = $('#email').val();
								var _quantity = $('#quantity').val();
															
								var _var_data = _last_name + '|' + _first_name + '|' + _middle_name + '|' + _mobile_number + '|' + _email + '|' + _quantity;
								checkRequiredFields('create', _var_data);
								
								if (hasError == 0) {																	
									confirmCreateVoucherProduct(_last_name, _first_name, _middle_name, _mobile_number, _email, _voucher_product_id, _quantity);
									createModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					createModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmCreateVoucherProduct = function(last_name, first_name, middle_name, mobile_number, email, voucher_product_id, quantity) {

		beyond.request({
			url : '/admin/voucher_products/confirm_create',
			data : {
				'_last_name' : last_name,
				'_first_name' : first_name,
				'_middle_name' : middle_name,
				'_mobile_number' : mobile_number,
				'_email' : email,
				'_quantity' : quantity,
				'_voucher_product_id' : voucher_product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmCreateVoucherModal = b.modal.new({
						title: 'Create Voucher :: Confirm',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmCreateVoucherModal.hide();
							},
							'Yes' : function() {
								createVoucher(last_name, first_name, middle_name, mobile_number, email, voucher_product_id, quantity);
								confirmCreateVoucherModal.hide();
							}
						}
					});
					confirmCreateVoucherModal.show();	
				} else {
					var errorCreateVoucherModal = b.modal.new({
						title: 'Create Voucher :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								errorCreateVoucherModal.hide();
							}
						}
					});
					errorCreateVoucherModal.show();
				}
			} // end on_success
		})
	};
	
	var createVoucher = function(last_name, first_name, middle_name, mobile_number, email, voucher_product_id, quantity) {	
		beyond.request({
			url : '/admin/voucher_products/create_voucher',
			data : {
				'_last_name' : last_name,
				'_first_name' : first_name,
				'_middle_name' : middle_name,
				'_mobile_number' : mobile_number,
				'_email' : email,
				'_quantity' : quantity,
				'_voucher_product_id' : voucher_product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var createVoucherModal = b.modal.new({
						title: 'Create Voucher : Successful',
						disableClose: true,
						html: 'You have successfully created a Voucher.',
						buttons: {
							'Ok' : function() {
								createVoucherModal.hide();
								redirect('/admin/voucher_products');
							}
						}
					});
					createVoucherModal.show();	
				} else {
					var errorCreateVoucherModal = b.modal.new({
						title: 'Create Voucher :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								errorCreateVoucherModal.hide();
							}
						}
					});
					errorCreateVoucherModal.show();
				}
			} // end on_success
		})		
	};
	
	
	
	var checkRequiredFields = function(action, var_data) {	
		hasError = 0;
		
		if (action == 'create') {
			var data = var_data.split('|');
			var last_name = data[0];
			var first_name = data[1];
			var middle_name = data[2];
			var mobile_number = data[3];
			var email = data[4];
			var quantity = data[5];	
			
			$('#last_name_error').hide();
			$('#first_name_error').hide();
			$('#middle_name_error').hide();
			$('#mobile_number_error').hide();
			$('#email_error').hide();
					
			/*if ($.trim(last_name) == '') {
				$('#last_name_error').show();
				hasError = 1;
			}
		
			if ($.trim(first_name) == '') {
				$('#first_name_error').show();
				hasError = 1;
			}
			
			if ($.trim(middle_name) == '') {
				$('#middle_name_error').show();
				hasError = 1;
			}
		
			if ($.trim(mobile_number) == '') {
				$('#mobile_number_error').show();
				hasError = 1;
			}
			
			if ($.trim(email) == '') {
				$('#email_error').show();
				hasError = 1;
			}*/
			
			if ((quantity == '') || (quantity <= 0) || (quantity >= 9999)) {
				$('#quantity_error').show();
				hasError = 1;
			}
												
		} else {
			
			var data = var_data.split('|');
			var voucher_product_name = data[0];
			var voucher_type_id = data[1];
				
			$('#voucher_product_name_error').hide();
			$('#voucher_type_id_error').hide();
		
			if ($.trim(voucher_product_name) == '') {
				$('#voucher_product_name_error').show();
				hasError = 1;
			}
		
			if ((voucher_type_id == '') || (voucher_type_id == 0)) {
				$('#voucher_type_id_error').show();
				hasError = 1;
			}
		}
	
		return hasError;
	};

</script>