<div class='alert alert-info'><h2>Product Types <a id='btn_add_new' class='btn btn-small' style='float:right;margin-right:-30px;margin-top:5px;margin-left:5px;'><i class="icon-plus"></i><span> Add New</span></a>

<?php if (!empty($product_types)):  ?>	
<a href="/admin/product_types/pdf_view" style='float:right;margin-left:5px;margin-top:5px;' class="btn btn-primary pdf btn-small"><i class="icon-print icon-white"></i> View As PDF</a>
<a href="/admin/product_types/excel_view" style='float:right;margin-left:5px;margin-top:5px;' class="btn btn-primary btn-small pdf"><i class="icon-print icon-white"></i> Export As Excel</a>
<?php endif; ?>
</h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/product_types'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:100px;" value="<?= $search_by ?>">
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
			<th style='width:auto;'>Name</th>
			<th style='width:207px;'>Visible</th>
			<th style='width:207px;'>Regular Buyable</th>
			<th style='width:207px;'>GC Buyable</th>
			<th style='width:207px;'>Is Package</th>
			<th>Is C Points</th>
			<th>Is IGPSM</th>
			<th style='width:108px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if(empty($product_types)):?>
		<tr><td colspan='3' style='text-align:center;'><strong>No Records Found</strong></td></tr>
		<?php else: ?>
		<?php foreach ($product_types as $product_type): ?>
		<tr data='<?=$product_type->product_type_id?>'>
			<td><?= $product_type->name; ?></td>
			<td><pre class='prettyprint'><?= ($product_type->is_visible == 1) ? 'Yes' : 'No'; ?></pre></td>
			<td><pre class='prettyprint'><?= ($product_type->is_regular == 1) ? 'Yes' : 'No'; ?></td>
			<td><pre class='prettyprint'><?= ($product_type->is_gc_buyable == 1) ? 'Yes' : 'No'; ?></pre></td>
			<td><pre class='prettyprint'><?= ($product_type->is_package == 1) ? 'Yes' : 'No'; ?></pre></td>
			<td><pre class='prettyprint'><?= ($product_type->is_cpoints == 1) ? 'Yes' : 'No'; ?></pre></td>
			<td><pre class='prettyprint'><?= ($product_type->is_igpsm == 0) ? 'No' : 'Yes'; ?></pre></td>
			<td>
				<a class='btn btn-small btn-primary btn_edit_product_type'><i class="icon-pencil icon-white" title="Edit"></i></a>
				<a class='btn btn-small btn-danger btn_delete_product_type'><i class="icon-remove icon-white" title="Delete"></i></a>				
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<div>
	<?= $this->pager->create_links($search_url) ?>
</div>

<script type="text/javascript">
	$('#btn_add_new').live("click",function() {		
		beyond.request({
			url : '/admin/product_types/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addProductTypeModal = b.modal.new({
						title: 'Add New Product Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addProductTypeModal.hide();
							},
							'Add' : function() {
								var name = $('#name').val();
								var is_visible = $('#is_visible').val();
								var is_regular_buyable = $('#is_regular_buyable').val();
								var is_gc_buyable = $('#is_gc_buyable').val();
								var is_package = $('#is_package').val();
								var is_cpoints = $('#is_cpoints').val();
								var is_igpsm = $('#is_igpsm').val();
							
								checkRequiredFields(name, is_visible, is_regular_buyable, is_gc_buyable, is_package, is_cpoints, is_igpsm);
								
								if (hasError == 0) {																	
									confirmAddItemType(name, is_visible, is_regular_buyable, is_gc_buyable, is_package, is_cpoints, is_igpsm);
									addProductTypeModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addProductTypeModal.show();					
				}
			}
		})				
	});
	
	var confirmAddItemType = function(name, is_visible, is_regular_buyable, is_gc_buyable, is_package, is_cpoints, is_igpsm) {
		beyond.request({
			url : '/admin/product_types/confirm_add',
			data : {
				'name' : name,
				'is_visible' : is_visible,
				'is_regular_buyable' : is_regular_buyable,
				'is_gc_buyable' : is_gc_buyable,
				'is_package': is_package,
				'is_cpoints' : is_cpoints,
				'is_igpsm' : is_igpsm
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddProductTypeModal = b.modal.new({
						title: 'Add New Product Type',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddProductTypeModal.hide();
							},
							'Yes' : function() {
								addProductType(name, is_visible, is_regular_buyable, is_gc_buyable, is_package, is_cpoints, is_igpsm);
								confirmAddProductTypeModal.hide();
							}
						}
					});
					confirmAddProductTypeModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Add New Product Type : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})
	};
	
	var addProductType = function(name, is_visible, is_regular_buyable, is_gc_buyable, is_package, is_cpoints, is_igpsm) {	
		beyond.request({
			url : '/admin/product_types/add_product_type',
			data : {
				'name' : name,
				'is_visible' : is_visible,
				'is_regular_buyable' : is_regular_buyable,
				'is_gc_buyable' : is_gc_buyable,
				'is_package': is_package,
				'is_cpoints' : is_cpoints,
				'is_igpsm' : is_igpsm
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var addProductTypeModal = b.modal.new({
						title: 'Add New Product Type : Successful',
						width: 350,
						disableClose: true,
						html: 'You have successfully added a new Product Type',
						buttons: {
							'Ok' : function() {
								addProductTypeModal.hide();
								redirect('/admin/product_types');
							}
						}
					});
					addProductTypeModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Add New Product Type : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})		
	};
	
	$('.btn_edit_product_type').live("click",function() {		
		var _product_type_id = $(this).parent().parent().attr("data");
			
		beyond.request({
			url : '/admin/product_types/edit',
			data : {
					'_product_type_id' : _product_type_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var editProductTypeModal = b.modal.new({
						title: 'Edit Product Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editProductTypeModal.hide();
							},
							'Update' : function() {
								var name = $('#name').val();
								var is_visible = $('#is_visible').val();
								var is_gc_buyable = $('#is_gc_buyable').val();
								var is_regular_buyable = $('#is_regular_buyable').val();
								var is_package = $('#is_package').val();
								var is_cpoints = $('#is_cpoints').val();
								var is_igpsm = $('#is_igpsm').val();
								var product_type_id = $('#orig_product_type').val();
								
								checkRequiredFields(name, is_visible, is_gc_buyable, is_package);
								
								if (hasError == 0) {																	
									confirmEditProductType(name, is_visible, is_regular_buyable, is_gc_buyable,product_type_id, is_package, is_cpoints, is_igpsm);
									editProductTypeModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editProductTypeModal.show();					
				}
			}
		})
		
	});
	
	var confirmEditProductType = function(name, is_visible, is_regular_buyable, is_gc_buyable, product_type_id, is_package, is_cpoints, is_igpsm) {
		
		beyond.request({
			url : '/admin/product_types/confirm_edit',
			data : {
				'name' : name,
				'is_visible' : is_visible,
				'is_regular_buyable' : is_regular_buyable,
				'is_gc_buyable' : is_gc_buyable,
				'is_package': is_package,
				'is_cpoints' : is_cpoints,
				'is_igpsm' : is_igpsm,
				'product_type_id' : product_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmEditProductTypeModal = b.modal.new({
						title: 'Edit Item',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditProductTypeModal.hide();
							},
							'Yes' : function() {
								updateProductType(name, is_visible, is_regular_buyable, is_gc_buyable, product_type_id, is_package, is_cpoints, is_igpsm);
								confirmEditProductTypeModal.hide();
							}
						}
					});
					confirmEditProductTypeModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Edit Product Type : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})
	};
	
	var updateProductType = function(name, is_visible, is_regular_buyable, is_gc_buyable, product_type_id, is_package, is_cpoints, is_igpsm) {	
	
		beyond.request({
			url : '/admin/product_types/update_product_type',
			data : {
				'name' : name,
				'is_visible' : is_visible,
				'is_regular_buyable' : is_regular_buyable,
				'is_gc_buyable' : is_gc_buyable,
				'product_type_id' : product_type_id,
				'is_package': is_package,
				'is_cpoints' : is_cpoints,
				'is_igpsm' : is_igpsm
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var updateItemTypeModal = b.modal.new({
						title: 'Edit Item Type :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Product Type',
						width: 350,
						buttons: {
							'Ok' : function() {
								updateItemTypeModal.hide();
								redirect('/admin/product_types');
							}
						}
					});
					updateItemTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_delete_product_type').live("click",function() {		
		var _product_type_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/product_types/delete',
			data : {
					'product_type_id' : _product_type_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deleteProductTypeModal = b.modal.new({
						title: 'Delete Product Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteProductTypeModal.hide();
							},
							'Delete' : function() {																															
								confirmDeleteProductType(_product_type_id);
								deleteProductTypeModal.hide();																
							}
						}
					});
					deleteProductTypeModal.show();					
				}
			}
		})
		
	});
	
	var confirmDeleteProductType = function(product_type_id) {
		beyond.request({
			url : '/admin/product_types/confirm_delete',
			data : {
				'product_type_id' : product_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeleteProductTypeModal = b.modal.new({
						title: 'Delete Product Type',
						disableClose: true,
						html: data.html,
						width: 300,
						buttons: {
								'No' : function() {
								confirmDeleteProductTypeModal.hide();
							},
							'Yes' : function() {
								deleteProductType(product_type_id);
								confirmDeleteProductTypeModal.hide();
							}
						}
					});
					confirmDeleteProductTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deleteProductType = function(product_type_id) {	
		beyond.request({
			url : '/admin/product_types/delete_product_type',
			data : {
				'product_type_id' : product_type_id
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var deleteProductTypeModal = b.modal.new({
						title: 'Delete Product Type :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Product Type.',
						width: 350,
						buttons: {
							'Ok' : function() {
								deleteProductTypeModal.hide();
								redirect('/admin/product_types');
							}
						}
					});
					deleteProductTypeModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Delete Product Type : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})		
	};
	
	var checkRequiredFields = function(name, is_visible, is_gc_buyable, is_package, is_cpoints, is_igpsm) {
		hasError = 0;
		$('#name_error').hide();
		$('#is_visible_error').hide();
		$('#is_regular_buyable_error').hide();
		$('#is_gc_buyable_error').hide();
		$('#is_package_error').hide();
		$('#is_cpoints_error').hide();
		$('#is_igpsm_error').hide();
		
		if (name == '') {
			$('#name_error').show();
			hasError = 1;
		}
		
		if (is_visible == '') {
			$('#is_visible_error').show();
			hasError = 1;
		}
		
		if(is_regular_buyable == '')
		{
			$('#is_regular_buyable_error').show();
			hasError = 1;
		}
		
		if (is_gc_buyable == '') {
			$('#is_gc_buyable_error').show();
			hasError = 1;
		}
		
		if (is_package == ''){
			$('#is_package_error').show();
			hasError = 1;
		}
		
		if(is_cpoints == '') {
			$('#is_cpoints_error').show();
			hasError = 1;
		}
		
		if(is_igpsm == '') {
			$('#is_igpsm_error').show();
			hasError = 1;
		}
	};
	
	$("#button_refresh").click(function(e){
		e.preventDefault();
		
		redirect('/admin/product_types');
	});
	
</script>