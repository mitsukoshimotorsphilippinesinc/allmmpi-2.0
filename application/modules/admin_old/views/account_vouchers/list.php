<div class='alert alert-info'>
    <h2>Voucher Products
    <div class="pull-right">
	    <a class='btn btn-small' id='btn_add_new'><i class="icon-plus"></i><span> Add New</span></a>
    </div>
</h2></div>

<div id="search_container">
	<form id='search_details' method='get' action ='/admin/account_vouchers'>
		<fieldset>
			<div class='row-fluid'>
				<div class='span span6'>
					<div class='control-group'>
						<label class="control-label" for="voucher_type_id_filter" style='display:inline'>Select Voucher Type:</label>
						<select id='voucher_type_id_filter' name='voucher_type_id_filter' style='width:100px;'>
							<option value='all'>ALL</option>
							<option value='1'>FPV</option>
							<option value='2'>MPV</option>
						</select>
						<button class='btn btn-primary' style='margin-bottom:10px;'>&nbsp;&nbsp;Go&nbsp;&nbsp;</button>
					</div>
				</div>
			</div>
		</fieldset>
	</form>
</div>	

<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Voucher Type</th>
			<th>Group Name</th>
			<th>Products</th>
			<th>Is Active?</th>
			<th style='width:200px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if(empty($voucher_products)):?>
			<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
		<?php else: ?>
			<?php foreach ($voucher_products as $vp): ?>
				<tr data-voucher_product_id='<?= $vp->voucher_product_id ?>' data-voucher_product_name='<?= $vp->voucher_product_name ?>' data-voucher_type_id='<?=$vp->voucher_type_id?>'>
					<td><?=$vp->voucher_type?></td>
					<td><?=$vp->voucher_product_name?></td>
					<td>
						<ul class="unstyled">
						<?php foreach($vp->array_items as $key => $array_item):?>
							<li><?= "{$array_item->product} x {$array_item->qty}" ?></li>
						<?php endforeach;?>
						</ul>
					</td>
					<td>
						<?php if($vp->is_active == 1): ?>
							Yes
						<?php elseif($vp->is_active == 0): ?>
							No
						<?php endif; ?>
					</td>
					<td>
						<a href="#" class='btn btn-small btn_edit_product btn-primary' data-json_items='<?=$vp->json_array_items?>'><i class="icon-pencil icon-white" title="Edit" ></i></a>
						<?php if($vp->is_active == 1): ?>
							<a class='btn btn-small btn-danger btn_delete_product' data-voucher_product_id="<?= $vp->voucher_product_id; ?>"><i class="icon-remove icon-white" title="Delete" ></i></a>
						<?php else: ?>
							<a class='btn btn-small btn-success btn_activate_product' data-voucher_product_id="<?= $vp->voucher_product_id; ?>"><i class="icon-ok icon-white" title="Activate" ></i></a>
						<?php endif; ?>
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
	$this->load->view('template_voucher_product');
?>

<script type="text/javascript">
	$(document).on('ready', function() {
		var voucher_type_id = '<?=$voucher_type_id?>';
		$('#voucher_type_id_filter option[value="'+voucher_type_id+'"]').attr("selected", "selected");
	});

	var _voucher_types = <?= json_encode($voucher_types) ?>;
	var _products = <?= json_encode($products) ?>;
	var voucher_products_listing = {};

	$("body").on('click', '#btn_add_new', function(){
		var voucher_modal = b.modal.create({
			title: 'Select Voucher Products',
			html: _.template($('#voucher_products_template').html(), {"voucher_product_id": 0, "voucher_product_name" : "", "voucher_types": _voucher_types, "products" : _products, "voucher_type_id" : 0, "json_items" : ""}),
			disableClose: true,
			buttons: {
				'Close': function() {
					voucher_products_listing = {};
					voucher_modal.hide();
				},
				'Ok' : function() {	
					var voucher_type_id = $('#voucher_type_id').val();
					var voucher_product_name = $('#voucher_product_name').val();
					
					b.request({
						url: '/admin/account_vouchers/add_voucher_product',
						data: {
							'voucher_type_id': voucher_type_id,
							'voucher_products': voucher_products_listing,
							'voucher_product_name': voucher_product_name
						},
						on_success: function(data){
							if(data.status == 'ok') {
								voucher_modal.hide();
								var success_modal = b.modal.create({
									title: 'Success',
									html: data.msg,
									disableClose: true,
									buttons: {
										'Close': function() {
											success_modal.hide();
											window.location.reload();
										}
									}
								});
								success_modal.show();
							}else if(data.status == 'error'){
								var error_modal = b.modal.create({
									title: 'Error Encountered',
									html: data.msg
								});
								error_modal.show();
							}
						}
					});
				}
			}
		});
		voucher_modal.show();
	});
	
	$("body").on('click', '.btn_edit_product', function(){
		var voucher_type_id = $(this).parent().parent().data('voucher_type_id');
		var voucher_product_id = $(this).parent().parent().data('voucher_product_id');
		var json_items = $(this).data('json_items');
		var voucher_product_name = $(this).parent().parent().data('voucher_product_name');
		
		$.each(json_items, function(index, item) {
			var product_id = item.product_id;
			var qty = item.qty;
			
			var product_array = {};
			product_array["product_id"] = product_id;
			product_array["qty"] = qty;
			voucher_products_listing[product_id] = product_array;
		});
		
		var voucher_modal = b.modal.create({
			title: 'Select Voucher Products',
			html: _.template($('#voucher_products_template').html(), {"voucher_product_id": voucher_product_id, "voucher_product_name": voucher_product_name, "voucher_types": _voucher_types, "products" : _products, "voucher_type_id" : voucher_type_id, "json_items" : json_items}),
			disableClose: true,
			buttons: {
				'Close': function() {
					voucher_products_listing = {};
					voucher_modal.hide();
				},
				'Ok' : function() {	
					var voucher_type_id = $('#voucher_type_id').val();
					var voucher_product_name = $('#voucher_product_name').val();
					
					b.request({
						url: '/admin/account_vouchers/edit_voucher_product',
						data: {
							'voucher_product_id': voucher_product_id,
							'voucher_type_id': voucher_type_id,
							'voucher_products': voucher_products_listing,
							'voucher_product_name': voucher_product_name
						},
						on_success: function(data){
							if(data.status == 'ok') {
								voucher_modal.hide();
								var success_modal = b.modal.create({
									title: 'Success',
									html: data.msg,
									disableClose: true,
									buttons: {
										'Close': function() {
											success_modal.hide();
											window.location.reload();
										}
									}
								});
								success_modal.show();
							}else if(data.status == 'error'){
								var error_modal = b.modal.create({
									title: 'Error Encountered',
									html: data.msg
								});
								error_modal.show();
							}
						}
					});
				}
			}
		});
		voucher_modal.show();
	});
	
	$("body").on('click', '.btn_delete_product', function(){
		var voucher_product_id = $(this).data('voucher_product_id');
		
		var confirm_modal = b.modal.create({
			title: 'Confirm Action',
			html: 'Are you sure you want to deactivate this voucher product?',
			disableClose: true,
			buttons: {
				'Close' : function(){
					confirm_modal.hide();
				},
				'Confirm': function(){
					confirm_modal.hide();
					b.request({
						url: '/admin/account_vouchers/deactivate_voucher_product',
						data: {
							'voucher_product_id': voucher_product_id
						},
						on_success: function(data) {
							if(data.status == 'ok') {
								var success_modal = b.modal.create({
									title: 'Success',
									html: data.msg,
									disableClose: true,
									buttons: {
										'Close': function() {
											success_modal.hide();
											window.location.reload();
										}
									}
								});
								success_modal.show();
							}else if(data.status == 'error') {
								var error_modal = b.modal.create({
									title: 'Error Encountered',
									html: data.msg
								});
								error_modal.show();
							}
						}
					});
				}
			}
		});
		confirm_modal.show();
	});
	
	$('body').on('click', '.btn_activate_product', function(){
		var voucher_product_id = $(this).data('voucher_product_id');
		
		var confirm_modal = b.modal.create({
			title: 'Confirm Action',
			html: 'Are you sure you want to re-activate this voucher product?',
			disableClose: true,
			buttons: {
				'Close' : function(){
					confirm_modal.hide();
				},
				'Confirm': function(){
					confirm_modal.hide();
					b.request({
						url: '/admin/account_vouchers/reactivate_voucher_product',
						data: {
							'voucher_product_id': voucher_product_id
						},
						on_success: function(data) {
							if(data.status == 'ok') {
								var success_modal = b.modal.create({
									title: 'Success',
									html: data.msg,
									disableClose: true,
									buttons: {
										'Close': function() {
											success_modal.hide();
											window.location.reload();
										}
									}
								});
								success_modal.show();
							}else if(data.status == 'error') {
								var error_modal = b.modal.create({
									title: 'Error Encountered',
									html: data.msg
								});
								error_modal.show();
							}
						}
					});
				}
			}
		});
		confirm_modal.show();
	});
</script>