<div class='alert alert-info'><h2>Card Series <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<div id="search_container">
	<form id='search_details' method='get' action ='/admin/card_series'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">
			<option value="series_number">Series</option>	
			<option value="code">Card Code</option>	
			<option value="name">Card Name</option>		
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
			<th style='width:50px;'>ID</th>
			<th style='width:100px;'>Series Number</th>	
			<th style='width:200px;'>Code / Name</th>					
			<th style='width:100px;'>Date Created</th>
			<th style='width:100px;'>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($card_series)):?>
		<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($card_series as $card_series): ?>
		<tr data ='<?= $card_series->card_series_id ?>'>
			<td><?= $card_series->card_series_id; ?></td>
			<td><?= $card_series->series_number; ?></td>
			
			<?php
				$card_type = $this->cards_model->get_card_type_by_id($card_series->card_type_id);
				
				if (empty($card_type)) {
					$c_type = '';
				} else {
					$c_type = $card_type->code . ' - ' . $card_type->name;
				}
			?>			
			<?php
				if ($c_type == '') {
			?>
				<td><span class='label label-important' id='vp_type_error'>Unknown Card Type</span></td>
			<?php } else { ?>
				<td><?= $c_type; ?></td>
			<?php
				}
			?>	
			<td><?= $card_series->insert_timestamp; ?></td>			
			<td>
				<a class='btn btn-small btn-primary btn_create_card_to_series'><i class="icon-gift icon-white" title="Add Card to Series" ></i></a>
				<a class='btn btn-small btn-primary btn_edit_card_series'><i class="icon-pencil icon-white" title="Edit" ></i></a>				
				<!--?php // DISABLE DELETE ACTION
				<a class='btn btn-small btn-danger btn_delete_card_series'><i class="icon-remove icon-white" title="Delete" ></i></a>
				?-->				
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
		windows.location.href = '/admin/card_series';
	});
	
	$('#btn_add_new').live("click",function() {		
		beyond.request({
			url : '/admin/card_series/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addCardSeriesModal = b.modal.new({
						title: 'Add New Card Series',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addCardSeriesModal.hide();
							},
							'Add' : function() {
								var _series_number = $('#series_number').val();
								var _card_type_id = $('#card_type_id').val();
								var _description = $('#description').val();
								
								checkRequiredFields(_series_number, _card_type_id, _description);
								var _var_data = _series_number + '|' + _card_type_id + '|' + _description;
								checkRequiredFields('add', _var_data);
								
								if (hasError == 0) {																	
									confirmAddCardSeries(_series_number, _card_type_id, _description);
									addCardSeriesModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addCardSeriesModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmAddCardSeries = function(series_number, card_type_id, description) {

		beyond.request({
			url : '/admin/card_series/confirm_add',
			data : {
				'_series_number' : series_number,
				'_card_type_id' : card_type_id,
				'_description' : description
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmaddCardSeriesModal = b.modal.new({
						title: 'Add New Card Series',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmaddCardSeriesModal.hide();
							},
							'Yes' : function() {
								addCardSeries(series_number, card_type_id, description);
								confirmaddCardSeriesModal.hide();
							}
						}
					});
					confirmaddCardSeriesModal.show();	
				} else {
					var errorCardSeriesModal = b.modal.new({
						title: 'Add New Card Series :: Error',
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
	
	var addCardSeries = function(series_number, card_type_id, description) {	
		beyond.request({
			url : '/admin/card_series/add_card_series',
			data : {
				'_series_number' : series_number,
				'_card_type_id' : card_type_id,
				'_description' : description
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addCardSeriesModal = b.modal.new({
						title: 'Add New Card Series : Successful',
						disableClose: true,
						html: 'You have successfully added a new Card Series',
						buttons: {
							'Ok' : function() {
								addCardSeriesModal.hide();
								redirect('/admin/card_series');
							}
						}
					});
					addCardSeriesModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};

	
	$('.btn_edit_card_series').live("click",function() {		
		var _card_series_id = $(this).parent().parent().attr("data");
			
		beyond.request({
			url : '/admin/card_series/edit',
			data : {
					'_card_series_id' : _card_series_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var editCardSeriesModal = b.modal.new({
						title: 'Edit Card Series',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editCardSeriesModal.hide();
							},
							'Update' : function() {
								var _series_number = $('#series_number').val();
								var _card_type_id = $('#card_type_id').val();
								var _description = $('#description').val();
															
								var _var_data = _series_number + '|' + _card_type_id + '|' + _description;
								checkRequiredFields('edit', _var_data);
								
								if (hasError == 0) {																	
									confirmEditCardSeries(_series_number, _card_type_id, _description, _card_series_id);
									
									editCardSeriesModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editCardSeriesModal.show();					
				}
			}
		})
		return false;		
	});
	
	var confirmEditCardSeries = function(series_number, cart_type_id, description, card_series_id) {
		
		beyond.request({
			url : '/admin/card_series/confirm_edit',
			data : {
				'_series_number' : series_number,
				'_card_type_id' : cart_type_id,
				'_description' : description,
				'_card_series_id' : card_series_id				
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmeditCardSeriesModal = b.modal.new({
						title: 'Edit Card Series',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmeditCardSeriesModal.hide();
							},
							'Yes' : function() {
								updateCardSeries(series_number, cart_type_id, description, card_series_id);
								confirmeditCardSeriesModal.hide();
							}
						}
					});
					confirmeditCardSeriesModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var updateCardSeries = function(series_number, cart_type_id, description, card_series_id) {
		
		beyond.request({
			url : '/admin/card_series/update_card_series',
			data : {
				'_series_number' : series_number,
				'_card_type_id' : cart_type_id,
				'_description' : description,
				'_card_series_id' : card_series_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var updateCardSeriesModal = b.modal.new({
						title: 'Edit Card Series :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Card Series',
						buttons: {
							'Ok' : function() {
								updateCardSeriesModal.hide();
								redirect('/admin/card_series');
							}
						}
					});
					updateCardSeriesModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_delete_card_series').live("click",function() {		
		var _card_series_id = $(this).parent().parent().attr("data");

		beyond.request({
			url : '/admin/card_series/delete',
			data : {
					'_card_series_id' : _card_series_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deleteCardSeriesModal = b.modal.new({
						title: 'Delete Card Series',
						width: 450,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteCardSeriesModal.hide();
							},
							'Delete' : function() {																															
								confirmDeleteCardSeries(_card_series_id);
								deleteCardSeriesModal.hide();																
							}
						}
					});
					deleteCardSeriesModal.show();					
				}
			}
		})
		return false;
	});
	
	var confirmDeleteCardSeries = function(card_series_id) {
		beyond.request({
			url : '/admin/card_series/confirm_delete',
			data : {
				'_card_series_id' : card_series_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeleteCardSeriesModal = b.modal.new({
						title: 'Delete Card Series',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmDeleteCardSeriesModal.hide();
							},
							'Yes' : function() {
								deleteCardSeries(card_series_id);
								confirmDeleteCardSeriesModal.hide();
							}
						}
					});
					confirmDeleteCardSeriesModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deleteCardSeries = function(card_series_id) {	
		beyond.request({
			url : '/admin/card_series/delete_card_series',
			data : {
				'_card_series_id' : card_series_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var deleteCardSeriesModal = b.modal.new({
						title: 'Delete Card Series :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Card Series',
						buttons: {
							'Ok' : function() {
								deleteCardSeriesModal.hide();
								redirect('/admin/card_series');
							}
						}
					});
					deleteCardSeriesModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	
	$('.btn_create_card_to_series').live("click",function() {		
		var _card_series_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/card_series/create',
			data : {'_card_series_id' : _card_series_id},
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
								var _last_card_number = $('#last_card_number').val();
								var _quantity = $('#quantity').val();
								var _rn_prefix = $('#rn_prefix').val();
								var _released_to = $('#released_to').val();
								
								var _var_data = _last_card_number + '|' + _quantity + '|' + _released_to + '|' + _rn_prefix;
								
								checkRequiredFields('create', _var_data);
								
								if (hasError == 0) {																	
									confirmCreateCard(_last_card_number, _quantity, _card_series_id, _released_to, _rn_prefix);
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
	
	var confirmCreateCard = function(last_card_number, quantity, card_series_id, released_to, rn_prefix) {

		beyond.request({
			url : '/admin/card_series/confirm_create',
			data : {
				'_last_card_number' : last_card_number,
				'_quantity' : quantity,
				'_card_series_id' : card_series_id,
				'_released_to' : released_to,
				'_rn_prefix' : rn_prefix
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmcreateCardModal = b.modal.new({
						title: 'Create Card :: Confirm',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmcreateCardModal.hide();
							},
							'Yes' : function() {
								createCard(last_card_number, quantity, card_series_id, rn_prefix);
								confirmcreateCardModal.hide();
							}
						}
					});
					confirmcreateCardModal.show();	
				} else {
					var errorcreateCardModal = b.modal.new({
						title: 'Create Card :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorcreateCardModal.hide();
							}
						}
					});
					errorcreateCardModal.show();
				}
			} // end on_success
		})
	};
	
	var createCard = function(last_card_number, quantity, card_series_id, released_to, rn_prefix) {	
		beyond.request({
			url : '/admin/card_series/create_card_series',
			data : {
				'_last_card_number' : last_card_number,			
				'_quantity' : quantity,
				'_card_series_id' : card_series_id,
				'_released_to' : released_to,
				'_rn_prefix' : rn_prefix
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var createCardSeriesModal = b.modal.new({
						title: 'Create Card Series',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								createCardSeriesModal.hide();
								redirect('/admin/card_series');
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
			var last_card_number = data[0];		
			var quantity = data[1];	
			var released_to = data[2];
			var rn_prefix = data[3];			
			
			$('#released_to_error').hide();
			$('#quantity_error').hide();
			$('#rn_prefix_error').hide();
			
			if ((quantity == '') || (quantity <= 0) || (quantity >= 9999)) {
				$('#quantity_error').show();
				hasError = 1;
			}
			
			// TODO : check string length of rn_prefix
			//if ($.strLen(rn_prefix) < 3) {
			//	$('#rn_prefix_error').show();
			//	hasError = 1;
			//}
			
			if ((released_to == '')) {
				$('#released_to_error').show();
				hasError = 1;
			}
												
		} else {
			
			var data = var_data.split('|');
			var series_number = data[0];
			var card_type_id = data[1];
			var description = data[2];
				
			$('#series_number_error').hide();
			$('#card_type_id_error').hide();
			$('#description_error').hide();
		
			if (($.trim(series_number) == '') || (series_number == 0)) {
				$('#series_number_error').show();
				hasError = 1;
			}
		
			if ((card_type_id == '') || (card_type_id == 0)) {
				$('#card_type_id_error').show();
				hasError = 1;
			}
			
			if ($.trim(description) == '') {
				$('#description_error').show();
				hasError = 1;
			}
		}
	
		return hasError;
	};

</script>