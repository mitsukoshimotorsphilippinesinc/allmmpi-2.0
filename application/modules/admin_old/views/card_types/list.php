<div class='alert alert-info'><h2>Card Types <a id='btn_add_new_card_type' class='btn btn-small' style='float:right;margin-right:-30px;margin-top:5px;margin-left:5px;'><i class="icon-plus"></i><span> Add New Card Type</span></a>

<a href="/admin/card_types/excel_view" style='float:right;margin-top:5px;margin-left:5px;' class="btn btn-primary btn-small pdf"><i class="icon-print icon-white"></i> Export As Excel</a>
</h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/card_types'>
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
			<th>Card Series</th>
			<th>Type</th>
			<th>Date Created</th>
			<th style='width:150px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($card_types)): ?>
		<tr><td colspan='10' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($card_types as $card_type): ?>
		<tr data='<?= $card_type->card_type_id ?>'>
			<td><?= $card_type->card_type_id; ?></td>
			<td><?= $card_type->code; ?></td>
			<td><?= $card_type->name; ?></td>
			
			<?php
				$card_series = "";
				
				// get current series of card type
				$where = "card_type_id = '{$card_type->card_type_id}'";
				$card_series_details = $this->cards_model->get_card_series($where,null,"series_number");
				
				if (count($card_series_details) == 0) {
					$card_series = "NONE";
				} else {	
					$cnt = 1;									
					foreach ($card_series_details as $csd) {
						if ($cnt == 1) {
							$card_series = $csd->series_number; 
						} else {
							$card_series = $card_series . ", " . $csd->series_number;
						}
						$cnt++;
					}
				}
								
				if ($card_type->is_package == 0) {
					$is_package_value = "SALES CARD"; 
				} else {
					$is_package_value = "PACKAGE CARD"; 
				}
				
			?>
			
			<td><?= $card_series; ?></td>
			<td><?= $is_package_value; ?></td>
			<td><?= $card_type->insert_timestamp; ?></td>			
			<td>			
				<a title='Create Cards' class='btn btn-small btn-primary btn_create_card_to_series'><i class="icon-gift icon-white" ></i></a>
				<a title='Add a Series' class='btn btn-small btn-primary btn_add_card_series'><i class="icon-white icon-plus-sign"></i></a>	
				<a title='Edit Details' class='btn btn-small btn-primary btn_edit_card_type'><i class="icon-pencil icon-white"></i></a>				
				<a title='Delete' class='btn btn-small btn-danger btn_delete_card_type'><i class="icon-remove icon-white"></i></a>			
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

	$('#btn_add_new_card_type').live("click",function() {
				
		beyond.request({
			url : '/admin/card_types/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addCardTypeModal = b.modal.new({
						title: 'Add New Card Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addCardTypeModal.hide();
							},
							'Add' : function() {
								var _card_type_code = $('#card_type_code').val();
								var _card_type_name = $('#card_type_name').val();
								var _series_number = $('#series_number').val();
								var _sales_package_option = $('#sales_package_option').val();
								var _description = $('#description').val();		
																							
								//checkRequiredFields(_card_type_code, _card_type_name, _series_number, _sales_package_option, _description);
								var _var_data = _card_type_code + '|' + _card_type_name + '|' + _series_number + '|' + _sales_package_option + '|' + _description;
								checkRequiredFields('add', _var_data);
							
								if (hasError == 0) {																	
									confirmAddCardType(_card_type_code, _card_type_name, _series_number, _sales_package_option, _description);
									addCardTypeModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addCardTypeModal.show();					
				}
			}
		})				
	});

	var confirmAddCardType = function(card_type_code, card_type_name, series_number, sales_package_option, description) {
		
		beyond.request({
			url : '/admin/card_types/confirm_add',
			data : {
				'_card_type_code' : card_type_code,
				'_card_type_name' : card_type_name,
				'_description' : description,
				'_series_number' : series_number,
				'_sales_package_option' : sales_package_option
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmAddCardTypeModal = b.modal.new({
						title: 'Add New Card Type',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddCardTypeModal.hide();
							},
							'Yes' : function() {
								addCardType(card_type_code, card_type_name, series_number, sales_package_option, description);
								confirmAddCardTypeModal.hide();
							}
						}
					});
					confirmAddCardTypeModal.show();	
				} else {					
					var errorCardTypeModal = b.modal.new({
						title: 'Add New Card Type :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorCardTypeModal.hide();
							}
						}
					});
					errorCardTypeModal.show();
				}
			} // end on_success
		})
	};

	var addCardType = function(card_type_code, card_type_name, series_number, sales_package_option, description) {	
		
		beyond.request({
			url : '/admin/card_types/add_card_type',
			data : {
				'_card_type_code' : card_type_code,
				'_card_type_name' : card_type_name,
				'_description' : description,
				'_series_number' : series_number,
				'_sales_package_option' : sales_package_option
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var addCartTypeModal = b.modal.new({
						title: 'Add New Card Type : Successful',
						disableClose: true,
						html: 'You have successfully added a new Card Type',
						buttons: {
							'Ok' : function() {
								addCartTypeModal.hide();
								redirect('/admin/card_types');
							}
						}
					});
					addCartTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_edit_card_type').live("click",function() {		
		var _card_type_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/card_types/edit',
			data : {
					'_card_type_id' : _card_type_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
				
					// show add form modal					
					var editCardTypeModal = b.modal.new({
						title: 'Edit Card Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editCardTypeModal.hide();
							},
							'Update' : function() {								
								var _card_type_code = $('#card_type_code').val();
								var _card_type_name = $('#card_type_name').val();
								var _series_number = $('#series_number').val();
								var _sales_package_option = $('#sales_package_option').val();
								var _description = $('#description').val();		

								var _var_data = _card_type_code + '|' + _card_type_name + '|' + _series_number + '|' + _sales_package_option + '|' + _description;
								checkRequiredFields('edit', _var_data);
								
								if (hasError == 0) {																	
									confirmEditCardType(_card_type_code, _card_type_name, _description, _card_type_id, _sales_package_option);
									editCardTypeModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editCardTypeModal.show();					
				}
			}
		})	
	});
	
	
	var confirmEditCardType = function(card_type_code, card_type_name, description, card_type_id, sales_package_option) {
	
		beyond.request({
			url : '/admin/card_types/confirm_edit',
			data : {
				'_card_type_code' : card_type_code,
				'_card_type_name' : card_type_name,
				'_description' : description,
				'_card_type_id' : card_type_id,
				'_sales_package_option' : sales_package_option
				
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmEditCardTypeModal = b.modal.new({
						title: 'Edit card Type',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditCardTypeModal.hide();
							},
							'Yes' : function() {
								updateCardType(card_type_code, card_type_name, description, card_type_id, sales_package_option);
								confirmEditCardTypeModal.hide();
							}
						}
					});
					confirmEditCardTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};

	var updateCardType = function(card_type_code, card_type_name, description, card_type_id, sales_package_option) {	

		beyond.request({
			url : '/admin/card_types/update_card_type',
			data : {
				'_card_type_code' : card_type_code,
				'_card_type_name' : card_type_name,
				'_description' : description,
				'_card_type_id' : card_type_id,
				'_sales_package_option' : sales_package_option
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var updateCardTypeModal = b.modal.new({
						title: 'Edit card Type :: Successful',
						disableClose: true,
						html: 'You have successfully updated a card Type',
						buttons: {
							'Ok' : function() {
								updateCardTypeModal.hide();
								redirect('/admin/card_types');
							}
						}
					});
					updateCardTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_delete_card_type').live("click",function() {		
		var _card_type_id = $(this).parent().parent().attr("data");

		beyond.request({
			url : '/admin/card_types/delete',
			data : {
					'_card_type_id' : _card_type_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
				
					// show add form modal					
					var deleteCardTypeModal = b.modal.new({
						title: 'Delete Card Type',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteCardTypeModal.hide();
							},
							'Delete' : function() {																															
								confirmCardTypeDelete(_card_type_id);
								deleteCardTypeModal.hide();																
							}
						}
					});
					deleteCardTypeModal.show();					
				}
			}
		})
	
	});

	var confirmCardTypeDelete = function(card_type_id) {
		beyond.request({
			url : '/admin/card_types/confirm_delete',
			data : {
				'_card_type_id' : card_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmDeleteCardTypeModal = b.modal.new({
						title: 'Delete Card Type',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmDeleteCardTypeModal.hide();
							},
							'Yes' : function() {
								deleteCardType(card_type_id);
								confirmDeleteCardTypeModal.hide();
							}
						}
					});
					confirmDeleteCardTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};

	var deleteCardType = function(card_type_id) {	
		
		beyond.request({
			url : '/admin/card_types/delete_card_type',
			data : {
				'_card_type_id' : card_type_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var deleteCardTypeModal = b.modal.new({
						title: 'Delete Card Type :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Card Type.',
						buttons: {
							'Ok' : function() {
								deleteCardTypeModal.hide();
								redirect('/admin/card_types');
							}
						}
					});
					deleteCardTypeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_create_card_to_series').live("click",function() {		
		var _card_type_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/card_series/create',
			data : {'_card_type_id' : _card_type_id},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var createModal = b.modal.new({
						title: 'Create :: Details',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								createModal.hide();
							},
							'Create' : function() {							
								var _card_series_id = $('#card_series_id').val();
								var _last_card_number = $('#last_card_count').val();
								var _quantity = $('#quantity').val();
								var _released_to = $('#released_to').val();
								var _rn_prefix = $('#rn_prefix').val();
															
								var _var_data = _card_series_id + '|' + _last_card_number + '|' + _quantity + '|' + _released_to + '|' + _rn_prefix;
								checkRequiredFields('create', _var_data);
								
								if (hasError == 0) {																	
									confirmCreateCard(_card_type_id, _last_card_number, _quantity, _card_series_id, _released_to, _rn_prefix);
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
	
	var confirmCreateCard = function(card_type_id, last_card_number, quantity, card_series_id, released_to, rn_prefix) {

		beyond.request({
			url : '/admin/card_series/confirm_create',
			data : {
				'_card_type_id' : card_type_id,
				'_last_card_number' : last_card_number,
				'_quantity' : quantity,
				'_card_series_id' : card_series_id,
				'_released_to' : released_to,
				'_rn_prefix' : rn_prefix
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmCreateCardModal = b.modal.new({
						title: 'Create Voucher :: Confirm',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmCreateCardModal.hide();
							},
							'Yes' : function() {
							
								createCard(card_type_id, last_card_number, quantity, card_series_id, released_to, rn_prefix);
								confirmCreateCardModal.hide();
							}
						}
					});
					confirmCreateCardModal.show();	
				} else {
					var errorCreateCardModal = b.modal.new({
						title: 'Create Card :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorCreateCardModal.hide();
							}
						}
					});
					errorCreateCardModal.show();
				}
			} // end on_success
		})
	};
	
	var createCard = function(card_type_id, last_card_number, quantity, card_series_id, to_facility, rn_prefix) {	
		beyond.request({
			url : '/admin/card_series/create_card_series',
			data : {
					'_card_type_id' : card_type_id,
					'_last_card_number' : last_card_number,
					'_quantity' : quantity,
					'_card_series_id' : card_series_id,
					'_released_to' : to_facility,
					'_rn_prefix' : rn_prefix
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var createCardSeriesModal = b.modal.new({
						title: 'Create Card : Successful',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								createCardSeriesModal.hide();
								redirect('/admin/card_types');
							}
						}
					});
					createCardSeriesModal.show();	
				} else {
					var errorCardSeriesModal = b.modal.new({
						title: 'Create Card Series :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorCardSeriesModal.hide();
							}
						}
					});
					errorCardSeriesModal.show();
				}
			} // end on_success
		})		
	};
	
	var checkRequiredFields = function(action, var_data) {	
		hasError = 0;
		
		if (action == 'create') {
			var data = var_data.split('|');
			var card_series_id = data[0];	
			var last_card_number = data[1];		
			var quantity = data[2];	
			var released_to = data[3];
			var rn_prefix = data[4];
			
			$('#card_series_id_error').hide();
			$('#released_to_error').hide();
			$('#quantity_error').hide();
			$('#rn_prefix_error').hide();
			
			if ((quantity == '') || (quantity <= 0) || (quantity > 20000)) {
				$('#quantity_error').show();
				hasError = 1;
			}
			
			if ((released_to == '')) {
				$('#released_to_error').show();
				hasError = 1;
			}
			
			// TODO : check string length of rn_prefix
			//if ($.strLen(rn_prefix) < 3) {
			//	$('#rn_prefix_error').show();
			//	hasError = 1;
			//}
		
			if ((card_series_id == '') || (card_series_id == 0)) {
				$('#card_series_id_error').show();
				hasError = 1;
			}
		
		} else {
			var data = var_data.split('|');
			var card_type_code = data[0];
			var card_type_name = data[1];
			var series_number = data[2];
			var sales_package_option = data[3];
			var description = data[4];
		
			$('#card_type_code_error').hide();
			$('#card_type_name_error').hide();
			$('#series_number_error').hide();
			$('#sales_package_error').hide();
			$('#description_error').hide();
	
			if ($.trim(card_type_code) == '') {
				$('#card_type_code_error').show();
				hasError = 1;
			}
	
			if ($.trim(card_type_name) == '') {
				$('#card_type_name_error').show();
				hasError = 1;
			}
		
			if ($.trim(description) == '') {
				$('#description_error').show();
				hasError = 1;
			}
		
			if ($.trim(sales_package_option) == '') {
				$('#sales_package_error').show();
				hasError = 1;
			}
			
			if ((series_number == '0') || (series_number = '')) {
				$('#series_number_error').show();
				hasError = 1;
			}
		}
		return hasError;		
	};
	
	$('.btn_add_card_series').live("click",function() {		
		//var _card_series_id = $(this).parent().parent().attr("data");
		var _card_type_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/card_types/add_series_to_type',
			data : {'_card_type_id' : _card_type_id},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var createModal = b.modal.new({
						title: 'Add a Series :: Details',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								createModal.hide();
							},
							'Add' : function() {							
								var _last_card_number = $('#last_card_number').val();
								var _quantity = $('#quantity').val();
								var _released_to = $('#released_to').val();
															
								var _var_data = _last_card_number + '|' + _quantity + '|' + _released_to;
								checkRequiredFields('create', _var_data);
								
								if (hasError == 0) {																	
									confirmCreateCard(_last_card_number, _quantity, _card_series_id, _released_to);
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
	
	
</script>