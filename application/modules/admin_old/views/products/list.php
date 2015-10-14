<?php
	$product_type_options = "";
	foreach($product_types_list as $p)
		$product_type_options .= "<option value='{$p->product_type_id}' data-is-package='{$p->is_package}'>{$p->name}</option>";
		
	$product_line_options = "";
	foreach($product_lines as $p)
		$product_line_options .= "<option value='{$p->product_line_id}'>{$p->product_line}</option>";
?>

<div class='alert alert-info'>
    <h2>Products
    <div class="pull-right">
	    <a href="/admin/products/export_excel" class='btn btn-small' id='export_excel'><i class="icon-print"></i><span> Export to Excel</span></a>
	    <a class='btn btn-small' id='btn_add_new'><i class="icon-plus"></i><span> Add New</span></a>
    </div>
</h2></div>

<div id="search_container">
	<form id='search_details' method='get' action ='/admin/products'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:130px;" value="<?= $search_by ?>">
			<option value="item_name">Item Name</option>
			<option value='product_type'>Product Type</option>			
			<option value='product_line'>Product Line</option>
		</select>
		<span id='item_name_select'>
			<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	
		</span>

		<span id='product_types_select' style='display:none;'>
			<select id='product_type' name='product_type'>
				<?= $product_type_options ?>
			</select>
		</span>
		
		<span id='product_lines_select' style='display:none;'>
			<select id='product_line' name='product_line'>
				<?= $product_line_options ?>
			</select>
		</span>
		
		<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
		<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>
		
		<span>
			
		</span>
		
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
			<th>Item</th>
			<th>Product Code</th>
			<th style='width:170px;'>Product Type</th>
			<th style='width:200px;'>Regular Price</th>
			<th style='width:200px;'>GC Price</th>
			<th>Raffle Product?</th>
			<th style='width:200px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($products)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php $product_types_array = json_decode($product_types);?>
	<?php foreach ($products as $product): ?>
		<tr data ='<?= $product->product_id ?>'>
			<td>
			<?php
			if($product->item_id == 0)
				echo $product->product_name;
			elseif($product->item_id > 0)
				echo $product->item_name;
			?>
			<?php if($product->is_product_rebate): ?>
				<span class="label label-info">Product Rebate Purchasable</span>
			<?php endif; ?>
			</td>
			<td><?= $product->product_code?></td>
			<td><?php
				$product_type_id = $product->product_type_id;
				if(isset($product_types_array->$product_type_id))
				{
					$prod_type = $product_types_array->$product_type_id;
					$prod_name = $prod_type->name;
				}
				else
					$prod_name = "Not found.";
				echo $prod_name;
				?>
			</td>
			<td>
				<ul class="unstyled">
					<li><em>Standard Retail Price</em>: <?= number_format($product->standard_retail_price, 2); ?></li>
					<li><em>Member Price</em>: <?= number_format($product->member_price, 2); ?></li>
					<li><em>Employee Price</em>: <?= number_format($product->employee_price, 2); ?></li>
				</ul>
			</td>
			<td>
				<ul class="unstyled">
					<li><em>Standard Retail Price</em>: <?= number_format($product->giftcheque_standard_retail_price, 2); ?></li>
					<li><em>Member Price</em>: <?= number_format($product->giftcheque_member_price, 2); ?></li>
					<li><em>Employee Price</em>: <?= number_format($product->giftcheque_employee_price, 2); ?></li>
				</ul>
			</td>
			<td><?= ($product->is_raffle) ? 'Yes' : 'No' ?></td>
			<td>
				<a class='btn btn-small btn-primary btn_tag_raffle' data="<?= $product->product_type_id?>"><i class="icon-gift icon-white" title='Raffle Product'></i></a>
				<a class='btn btn-small btn-primary btn_show_cards' data="<?= $product->product_type_id; ?>"><i class="icon-th icon-white" title="RS Cards" ></i></a>
				<a class='btn btn-small btn-primary btn_show_gallery' data="<?= $product->product_type_id; ?>"><i class="icon-picture icon-white" title="Gallery" ></i></a>
				<a href="/admin/products/edit/<?= $product->product_id?>" class='btn btn-small btn-primary'><i class="icon-pencil icon-white" title="Edit" ></i></a>
				<a class='btn btn-small btn-danger btn_delete_product' data="<?= $product->product_type_id; ?>"><i class="icon-remove icon-white" title="Delete" ></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>

<?php
	// load the templates
	$this->load->view('template_edit_raffle_product');
?>

<script type="text/javascript">

	$(document).on('ready', function(){
		var search_by = "<?=$search_by?>";
		if(search_by != "")
		{
			$('#search_option option[value="'+search_by+'"]').attr("selected", "selected");
			$("#search_option").trigger('change');
		}
	});
	
	$("#button_search").live("click",function() {
		var _search_string = $.trim($("#search_string").val());
        var _search_option = $("#search_option").val();
		
		if(_search_option == 'item_name')
		{
			if (_search_string == '') {
				$("#search_error").show();
				$("#search_summary").hide();
			} else {
				$("#search_details").submit();
				$("#search_error").hide(); 
				$("#search_summary").show();           
			}
		}
		else
		{
			$("#search_details").submit();
			$("#search_error").hide(); 
			$("#search_summary").show();
		}
		
		return false;
	});
	
	$("#button_refresh").live("click",function() {
		windows.location.href = '/admin/products';
	});
	
	$(document).on("click","#btn_add_new",function(){
		
		var product_types = <?= $product_types ?>;
		var product_types_html = "";
		
		$.each(product_types,function(k,v){
			product_types_html = product_types_html + '<option value="'+k+'" data-is-package="'+v.is_package+'">'+v.name+'</option>';
		});
		
		var add_product = b.modal.new({
			title: "Add New Product",
			html: '\
				<form class="form-inline">\n\
					<div style="margin-bottom: 10px;">\n\
						<label>Type:&nbsp;</label>\n\
						<select id="product_types" name="product_types" style="width:auto;">'+product_types_html+'</select>\n\
					</div>\n\
					<div id="package_selection" style="margin-bottom: 10px; display: none;">\n\
						<label>Package?&nbsp;</label>\n\
						<select id="is_package" name="is_package" style="width:auto;">\n\
							<option value="0">No</option>\n\
							<option value="1">Yes</option>\n\
						</select>\n\
					</div>\n\
				</form>',
			width: 350,
			disableClose: true,
			buttons: {
				"Cancel": function(){
					add_product.hide();
				},
				"Ok": function(){
					var selected_type = $("#product_types").val();
					var is_package = $("#is_package").val();
					if(is_package == 0)
					{
						redirect("/admin/products/add/product/"+selected_type);
					}
					else if(is_package == 1)
					{
						redirect("/admin/products/add/package/"+selected_type);
					}
					
					add_product.hide();
				}
			}
		});
		
		add_product.show();
		
		return false;
	});
	
	$(document).on("change","#product_types",function(){
		console.log($("#product_types option:selected").data('is-package'));
		var is_package = $("#product_types option:selected").data('is-package');
		
		if($(this).val() == 11) {
			$("#package_selection").css("display","none");
			$("#is_package").val(1);
		} else if(is_package == 0) {
			$("#package_selection").css("display","none");
			$("#is_package").val(0);
		} else if (is_package == 1) {
			$("#package_selection").css("display","");
			$("#is_package").val(1);
		}
	});
	
	var confirmAddProduct = function(item_id, product_line_id, standard_retail_price, member_price, employee_price, igpsm_points) {
		beyond.request({
			url : '/admin/products/confirm_add',
			data : {
				'_item_id' : item_id,
				'_product_line_id' : product_line_id,
				'_standard_retail_price' : standard_retail_price,
				'_member_price' : member_price,
				'_employee_price' : employee_price,
				'_igpsm_points' : igpsm_points
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddProductModal = b.modal.new({
						title: 'Add New Item',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddProductModal.hide();
							},
							'Yes' : function() {
								addProduct(item_id, product_line_id, standard_retail_price, member_price, employee_price, igpsm_points);
								confirmAddProductModal.hide();
							}
						}
					});
					confirmAddProductModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var addProduct = function(item_id, product_line_id, standard_retail_price, member_price, employee_price, igpsm_points) {	
		beyond.request({
			url : '/admin/products/add_product',
			data : {
				'_item_id' : item_id,
				'_product_line_id' : product_line_id,
				'_standard_retail_price' : standard_retail_price,
				'_member_price' : member_price,
				'_employee_price' : employee_price,
				'_igpsm_points' : igpsm_points
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addProductModal = b.modal.new({
						title: 'Add New Product : Successful',
						disableClose: true,
						html: 'You have successfully added a new Product',
						buttons: {
							'Ok' : function() {
								addProductModal.hide();
								redirect('/admin/products');
							}
						}
					});
					addProductModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$(document).on("click",".btn_show_gallery",function(){
		var _product_id = $(this).parent().parent().attr("data");
		var _product_type_id = $(this).attr("data");
		b.request({
			url: '/admin/products/gallery',
			data : {
				'_product_id' : _product_id,
				'_product_type_id' : _product_type_id
			},
			on_success : function(data){
				if (data.status == "ok")	{
					
					// show add form modal					
					var editGalleryModal = b.modal.new({
						title: 'Gallery',
						width: 780,
						html: data.data.html
					});
					editGalleryModal.show();					
				}
			}
		});
	});
	
	$('.btn_edit_product').live("click",function() {		
		var _product_id = $(this).parent().parent().attr("data");
			
		beyond.request({
			url : '/admin/products/edit',
			data : {
					'_product_id' : _product_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var editProductModal = b.modal.new({
						title: 'Edit Product',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editProductModal.hide();
							},
							'Update' : function() {
								var _item_id = $('#item_id').val();
								var _product_line_id = $('#product_line_id').val();
								var _standard_retail_price = $('#standard_retail_price').val();
								var _member_price = $('#member_price').val();
								var _employee_price = $('#employee_price').val();
								var _igpsm_points = $('#igpsm_points').val();
								
								checkRequiredFields(_item_id, _product_line_id, _standard_retail_price, _member_price, _employee_price, _igpsm_points, _product_id);
								
								if (hasError == 0) {																	
									confirmEditProduct(_item_id, _product_line_id, _standard_retail_price, _member_price, _employee_price, _igpsm_points, _item_id, _product_id);
									editProductModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editProductModal.show();					
				}
			}
		})
		return false;		
	});
	
	var confirmEditProduct = function(item_id, product_line_id, standard_retail_price, member_price, employee_price, igpsm_points, product_id) {
		
		beyond.request({
			url : '/admin/products/confirm_edit',
			data : {
				'_item_id' : item_id,
				'_product_line_id' : product_line_id,
				'_standard_retail_price' : standard_retail_price,
				'_member_price' : member_price,
				'_employee_price' : employee_price,
				'_igpsm_points' : igpsm_points,
				'_product_id' : product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmEditItemModal = b.modal.new({
						title: 'Edit Product',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditItemModal.hide();
							},
							'Yes' : function() {
								updateProduct(item_id, product_line_id, standard_retail_price, member_price, employee_price, igpsm_points, product_id);
								confirmEditItemModal.hide();
							}
						}
					});
					confirmEditItemModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var updateProduct = function(item_id, product_line_id, standard_retail_price, member_price, employee_price, igpsm_points, product_id) {	
		beyond.request({
			url : '/admin/products/update_product',
			data : {
				'_item_id' : item_id,
				'_product_line_id' : product_line_id,
				'_standard_retail_price' : standard_retail_price,
				'_member_price' : member_price,
				'_employee_price' : employee_price,
				'_igpsm_points' : igpsm_points,
				'_product_id' : product_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var updateProductModal = b.modal.new({
						title: 'Edit Product :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Product',
						buttons: {
							'Ok' : function() {
								updateProductModal.hide();
								redirect('/admin/products');
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
	
	$('.btn_delete_product').live("click",function() {		
		var _product_id = $(this).parent().parent().attr("data");
		var _product_type_id = $(this).attr("data");
		beyond.request({
			url : '/admin/products/delete',
			data : {
					'_product_id' : _product_id,
					'_product_type_id':_product_type_id
					},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					// show add form modal					
					var deleteProductModal = b.modal.new({
						title: data.data.title,
						width: 400,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Cancel' : function() {
								deleteProductModal.hide();
							},
							'Delete' : function() {																															
								confirmDeleteProduct(_product_id,_product_type_id);
								deleteProductModal.hide();																
							}
						}
					});
					deleteProductModal.show();					
				}
			}
		})
		return false;
	});
	
	var confirmDeleteProduct = function(product_id,product_type_id) {
		beyond.request({
			url : '/admin/products/confirm_delete',
			data : {
				'_product_id' : product_id,
				'_product_type_id': product_type_id
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var confirmDeleteProductsModal = b.modal.new({
						title: data.data.title,
						disableClose: true,
						html: data.data.html,
						width: 250,
						buttons: {
							'Cancel' : function() {
								confirmDeleteProductsModal.hide();
							},
							'Yes' : function() {
								deleteProduct(product_id,product_type_id);
								confirmDeleteProductsModal.hide();
							}
						}
					});
					confirmDeleteProductsModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deleteProduct = function(product_id,product_type_id) {
		beyond.request({
			url : '/admin/products/delete_product',
			data : {
				'_product_id' : product_id,
				'_product_type_id': product_type_id
			},
			on_success : function(data) {
				if (data.status == "ok")	{
					
					var deleteProductModal = b.modal.new({
						title: data.data.title+' :: Successful',
						disableClose: true,
						width: 300,
						html: 'You have successfully deleted a Product',
						buttons: {
							'Ok' : function() {
								deleteProductModal.hide();
								redirect('/admin/products');
							}
						}
					});
					deleteProductModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	var checkRequiredFields = function(item_id, product_line_id, standard_retail_price, member_price, employee_price, igpsm_points) {
		hasError = 0;
		$('#item_id_error').hide();
		$('#product_line_id_error').hide();
		$('#standard_retail_price_error').hide();
		$('#member_price_error').hide();
		$('#employee_price_error').hide();
		$('#igpsm_error').hide();
		
		if (item_id == '') {
			$('#item_id_error').show();
			hasError = 1;
		}
		
		if (product_line_id == '') {
			$('#product_line_id_error').show();
			hasError = 1;
		}
		
		if (standard_retail_price == '') {
			$('#standard_retail_price_error').show();
			hasError = 1;
		}
		
		if (member_price == '') {
			$('#member_price_error').show();
			hasError = 1;
		}
		
		if (employee_price == '') {
			$('#employee_price_error').show();
			hasError = 1;
		}
		
		if (igpsm_points == '') {
			$('#igpsm_points_error').show();
			hasError = 1;
		}	
		return hasError;
	};
	
	/*$("body").on('click', '#export_excel', function(){
		var search_by = $("#search_option").val();
		var search_text = $("#search_string").val();
		b.request({
			url: '/admin/products/export_excel',
			data: {
				'search_by': search_by,
				'search_text': search_text
			},
		});
	});*/
	
	$(document).on("click",".btn_show_cards",function(){
		var _product_id = $(this).parent().parent().attr("data");
		redirect("/admin/products/view_cards/"+_product_id);
	});
	
	$("#search_option").on('change', function(){
		var search_option = $('#search_option').val();
		if(search_option == 'item_name')
		{
			$('#item_name_select').show();
			$('#product_types_select').hide();
			$('#product_lines_select').hide();
		}
		else if(search_option == 'product_type')
		{
			$('#item_name_select').hide();
			$('#product_types_select').show();
			$('#product_lines_select').hide();
		}
		else if(search_option == 'product_line')
		{
			$('#item_name_select').hide();
			$('#product_types_select').hide();
			$('#product_lines_select').show();
		}
	});
	
	$(document).on('click', ".btn_tag_raffle", function(){
		var _product_id = $(this).parent().parent().attr("data");
		
		//get raffle data
		b.request({
			url: '/admin/products/get_raffle_products',
			data: {'product_id': _product_id},
			on_success: function(data){
				if(data.status == 'ok') {
					var raffle_modal = b.modal.create({
						title: 'Edit Raffle Product',
						html: _.template($('#edit_raffle_product_template').html(), {"product_id": _product_id, "qty_generated": data.data.qty_generated, "qty_needed": data.data.qty_needed, "is_active": data.data.is_active}),
						width: 350,
						disableClose: true,
						buttons: {
							'Cancel': function(){
								raffle_modal.hide();
							},
							'Save': function(){
								//request
								var is_active = $('#is_active').val();
								var qty_needed = $('#qty_needed').val();
								var qty_generated = $('#qty_generated').val();
								raffle_modal.hide();
								b.request({
									url: '/admin/products/tag_raffle_products',
									data: {
										'product_id': _product_id,
										'is_active': is_active,
										'qty_needed': qty_needed,
										'qty_generated': qty_generated
									},
									on_success: function(data){
										if(data.status == 'ok'){
											var success_modal = b.modal.create({
												title: 'Success',
												html: data.msg,
												width: 350,
												disableClose: true,
												buttons: {
													'Close': function(){
														location.href = location.href;
													}
												}
											});
											success_modal.show();
										}
									}
								});
							}
						}
					});
					raffle_modal.show();
				}else if(data.status == 'error') {
					var error_modal = b.modal.create({
						title: 'Error',
						html: data.msg,
						width: 350
					});
					error_modal.show();
				}
			}	
		});
	});

</script>