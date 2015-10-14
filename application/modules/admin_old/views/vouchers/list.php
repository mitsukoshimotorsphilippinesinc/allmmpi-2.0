<div class='alert alert-info'><h2>Vouchers <a class='btn btn-small' id='btn_create_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Create New</span></a></h2></div>

<!--div class='alert alert-info'><h2>Vouchers</h2></div-->

<div id="search_container">
	<form id='search_details' method='get' action ='/admin/vouchers'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">
			<option value="voucher_code">Voucher Code</option>	
			<option value="name">Name</option>	
			<option value="product_text">Product Text</option>		
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
			<th style='width:250px;'>Voucher</th>
			<th style='width:150px;'>Owner</th>		
			<th style='width:100px;'>Status</th>
			<th style='width:100px;'>Expiration Timestamp</th>
			<th style='width:100px;'>Issuance Timestamp</th>
			<th style='width:100px;'>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($vouchers)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($vouchers as $voucher): ?>
		<tr data ='<?= $voucher->voucher_id ?>'>
			<td><b><?= $voucher->voucher_code; ?></b><br/><?= $voucher->product_text; ?></td>
			<?php
				$proper_name = $voucher->last_name . ', ' . $voucher->first_name . ' ' . $voucher->middle_name;
			?>
			<td><?= $proper_name; ?></td>
			<td><?= $voucher->status; ?></td>			
			<td><?= $voucher->update_timestamp; ?></td>
			<td><?= $voucher->insert_timestamp; ?></td>		
			<td>			
				<a class='btn btn-small btn-primary btn_view_details'><i class="icon-remove icon-info-sign icon-white" title="View Details" ></i><span> View Details</span></a>
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
	
	$('#btn_create_new').live("click",function() {		
		beyond.request({
			url : '/admin/vouchers/create_new',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var createNewVoucherModal = b.modal.new({
						title: 'Create New Voucher',
						width: 550,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								createNewVoucherModal.hide();
							},
							'Add' : function() {
								var _voucher_product_id = $('#voucher_product_id').val();								
								var _quantity = $('#quantity').val();
																						
								checkRequiredFields(_voucher_product_id, _quantity);
								
								if (hasError == 0) {																	
									confirmCreateVoucher(_voucher_product_id, _quantity);
									createNewVoucherModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					createNewVoucherModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmCreateVoucher = function(voucher_product_id, quantity) {

		beyond.request({
			url : '/admin/vouchers/confirm_create',
			data : {
				'_voucher_product_id' : voucher_product_id,
				'_quantity' : quantity
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmCreateVoucherModal = b.modal.new({
						title: 'Create New Voucher/s',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmCreateVoucherModal.hide();
							},
							'Yes' : function() {
								createVoucher(voucher_product_id, quantity);
								confirmCreateVoucherModal.hide();
							}
						}
					});
					confirmCreateVoucherModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var createVoucher = function(voucher_product_id, quantity) {	
		beyond.request({
			url : '/admin/vouchers/create_voucher',
			data : {
				'_voucher_product_id' : voucher_product_id,
				'_quantity' : quantity
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var createVoucherModal = b.modal.new({
						title: 'Create New Voucher : Successful',
						disableClose: true,
						html: 'You have successfully created new Voucher/s',
						buttons: {
							'Ok' : function() {
								createVoucherModal.hide();
								redirect('/admin/vouchers');
							}
						}
					});
					createVoucherModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};

	
	$('.btn_view_details').live("click",function() {		
		var _voucher_id = $(this).parent().parent().attr("data");
				
		beyond.request({
			url : '/admin/vouchers/view_details',
			data : {
					'_voucher_id' : _voucher_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var viewVoucherDetailsModal = b.modal.new({
						title: 'View Voucher Details',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Close' : function() {
								viewVoucherDetailsModal.hide();
							}
						}
					});
					viewVoucherDetailsModal.show();					
				}
			}
		})
		return false;		
	});
		
	var checkRequiredFields = function(voucher_product_id, quantity) {
		hasError = 0;
			
		$('#voucher_product_name_error').hide();
		$('#quantity_error').hide();
	
		if ((voucher_product_id == '') || (voucher_product_id == 0)) {
			$('#voucher_product_id_error').show();
			hasError = 1;
		}
	
		if ((quantity == '') || (quantity <= 0) || (quantity >= 9999)) {
			$('#quantity_error').show();
			hasError = 1;
		}
	
		return hasError;
	};

</script>