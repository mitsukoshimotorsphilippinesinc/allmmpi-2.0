<div class='alert alert-info'><h2>Product Lines <a id='btn_add_new' class='btn btn-small' style='float:right;margin-right:-30px;margin-top:5px;margin-left:5px;'><i class="icon-plus"></i><span> Add New</span></a>

<?php if (!empty($product_lines)):  ?>	
<a href="/admin/product_lines/pdf_view" style='float:right;margin-left:5px;margin-top:5px;' class="btn btn-primary pdf btn-small"><i class="icon-print icon-white"></i> View As PDF</a>
<a href="/admin/product_lines/excel_view" style='float:right;margin-left:5px;margin-top:5px;' class="btn btn-primary btn-small pdf"><i class="icon-print icon-white"></i> Export As Excel</a>
<?php endif; ?>
</h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/product_lines'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:100px;" value="<?= $search_by ?>">
			<option value="product_line">Product Line</option>
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
			<th style='width:auto;'>Product Line</th>
			<th style='width:357px;'>Visible</th>
			<th style='width:228px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if(empty($product_lines)):?>
		<tr><td colspan='3' style='text-align:center;'><strong>No Records Found</strong></td></tr>
		<?php else: ?>
		<?php foreach ($product_lines as $product_line): ?>
		<tr data='<?=$product_line->product_line_id?>'>
			<td><?= $product_line->product_line; ?></td>
			<td><pre class='prettyprint'><?= ($product_line->is_visible == 1) ? 'Yes' : 'No'; ?></pre></td>
			<td>
				<a class='btn btn-small btn-primary btn_edit_product_line'><i class="icon-pencil icon-white" title="Edit"></i></a>
				<a class='btn btn-small btn-danger btn_delete_product_line'><i class="icon-remove icon-white" title="Delete"></i></a>				
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
			url : '/admin/product_lines/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addProductLineModal = b.modal.new({
						title: 'Add New Product Line',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addProductLineModal.hide();
							},
							'Add' : function() {
								var product_line = $('#product_line').val();
								var is_visible = $('#is_visible').val();
							
								checkRequiredFields(product_line, is_visible);
								
								if (hasError == 0) {																	
									confirmAddItemType(product_line, is_visible);
									addProductLineModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addProductLineModal.show();					
				}
			}
		})				
	});
	
	var confirmAddItemType = function(product_line, is_visible) {
		beyond.request({
			url : '/admin/product_lines/confirm_add',
			data : {
				'product_line' : product_line,
				'is_visible' : is_visible
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddProductLineModal = b.modal.new({
						title: 'Add New Product Line',
						disableClose: true,
						html: data.html,
						buttons: {
							'No' : function() {
								confirmAddProductLineModal.hide();
							},
							'Yes' : function() {
								addProductLine(product_line, is_visible);
								confirmAddProductLineModal.hide();
							}
						}
					});
					confirmAddProductLineModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Add New Product Line : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})
	};
	
	var addProductLine = function(product_line, is_visible) {	
		beyond.request({
			url : '/admin/product_lines/add_product_line',
			data : {
				'product_line' : product_line,
				'is_visible' : is_visible
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var addProductLineModal = b.modal.new({
						title: 'Add New Product Line : Successful',
						width: 350,
						disableClose: true,
						html: 'You have successfully added a new Product Line',
						buttons: {
							'Ok' : function() {
								addProductLineModal.hide();
								redirect('/admin/product_lines');
							}
						}
					});
					addProductLineModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Add New Product Line : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})		
	};
	
	$('.btn_edit_product_line').live("click",function() {		
		var _product_line_id = $(this).parent().parent().attr("data");
			
		beyond.request({
			url : '/admin/product_lines/edit',
			data : {
					'_product_line_id' : _product_line_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var editProductLineModal = b.modal.new({
						title: 'Edit Product Line',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editProductLineModal.hide();
							},
							'Update' : function() {
								var product_line = $('#product_line').val();
								var is_visible = $('#is_visible').val();
								var product_line_id = $('#orig_product_line').val();
								
								checkRequiredFields(product_line, is_visible);
								
								if (hasError == 0) {																	
									confirmEditProductLine(product_line, is_visible ,product_line_id);
									editProductLineModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editProductLineModal.show();					
				}
			}
		})
		
	});
	
	var confirmEditProductLine = function(product_line, is_visible ,product_line_id) {
		
		beyond.request({
			url : '/admin/product_lines/confirm_edit',
			data : {
				'product_line' : product_line,
				'is_visible' : is_visible,
				'product_line_id' : product_line_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmEditProductLineModal = b.modal.new({
						title: 'Edit Item',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditProductLineModal.hide();
							},
							'Yes' : function() {
								updateProductLine(product_line, is_visible ,product_line_id);
								confirmEditProductLineModal.hide();
							}
						}
					});
					confirmEditProductLineModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Edit Product Line : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})
	};
	
	var updateProductLine = function(product_line, is_visible ,product_line_id) {	
	
		beyond.request({
			url : '/admin/product_lines/update_product_line',
			data : {
				'product_line' : product_line,
				'is_visible' : is_visible,
				'product_line_id' : product_line_id
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var updateItemTypeModal = b.modal.new({
						title: 'Edit Item Type :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Product Line',
						width: 350,
						buttons: {
							'Ok' : function() {
								updateItemTypeModal.hide();
								redirect('/admin/product_lines');
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
	
	$('.btn_delete_product_line').live("click",function() {		
		var _product_line_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/product_lines/delete',
			data : {
					'product_line_id' : _product_line_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deleteProductLineModal = b.modal.new({
						title: 'Delete Product Line',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteProductLineModal.hide();
							},
							'Delete' : function() {																															
								confirmDeleteProductLine(_product_line_id);
								deleteProductLineModal.hide();																
							}
						}
					});
					deleteProductLineModal.show();					
				}
			}
		})
		
	});
	
	var confirmDeleteProductLine = function(product_line_id) {
		beyond.request({
			url : '/admin/product_lines/confirm_delete',
			data : {
				'product_line_id' : product_line_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeleteProductLineModal = b.modal.new({
						title: 'Delete Product Line',
						disableClose: true,
						html: data.html,
						width: 300,
						buttons: {
								'No' : function() {
								confirmDeleteProductLineModal.hide();
							},
							'Yes' : function() {
								deleteProductLine(product_line_id);
								confirmDeleteProductLineModal.hide();
							}
						}
					});
					confirmDeleteProductLineModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deleteProductLine = function(product_line_id) {	
		beyond.request({
			url : '/admin/product_lines/delete_product_line',
			data : {
				'product_line_id' : product_line_id
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var deleteProductLineModal = b.modal.new({
						title: 'Delete Product Line :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Product Line.',
						width: 350,
						buttons: {
							'Ok' : function() {
								deleteProductLineModal.hide();
								redirect('/admin/product_lines');
							}
						}
					});
					deleteProductLineModal.show();	
				} else {
					var ErrorModal = b.modal.new({
						title: 'Delete Product Line : Error',
						width: 300,
						html: data.msg
					});
					ErrorModal.show();
				}
			} // end on_success
		})		
	};
	
	var checkRequiredFields = function(product_line, is_visible) {
		hasError = 0;
		$('#product_line_error').hide();
		$('#is_visible_error').hide();
		
		if (product_line == '') {
			$('#product_line_error').show();
			hasError = 1;
		}
		
		if (is_visible == '') {
			$('#is_visible_error').show();
			hasError = 1;
		}
	};
	
	$("#button_refresh").click(function(e){
		e.preventDefault();
		
		redirect('/admin/product_lines');
	});
	
</script>