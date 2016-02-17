<?php
	
$date_received = date('Y-m-d');

?>
<style type="text/css">	
	.inventory-orders .item {width:150px;}
	.inventory-orders .qty {width:60px;text-align:right;}	
	.inventory-orders .price {width:80px;text-align:right;}	
	.inventory-orders .remarks {width:280px;}
	.inventory-orders .peripherals {width:280px;}

	.inventory-order-items .item {width:150px;}	
	.inventory-order-items .qty {width:112px;}	
	.inventory-order-items .price {width:112px;text-align:right;}	
	.inventory-order-items .remarks {width:284px;}
	.inventory-order-items .peripherals {width:280px;}

	.inventory-order-items select.qty {width:112px;}
	.inventory-order-items select.item {width:150px;}
	.inventory-order-items input.price {width:102px;text-align:right;}
	.inventory-order-items input.remarks {width:280px;}
	.inventory-order-items input.peripherals {width:280px;}

</style>
<?php

if (is_object($repair_summary_details))
{
	$isAdd = false;
	$titlePrefix = "Edit&nbsp;";
	$submitURL = "/information_technology/repairs/edit/" . $repair_summary_details->repair_summary_id;
	$_id = $repair_summary_details->repair_summary_id;
	$show_approval = false;
}
else
{
	$isAdd = true;
	$titlePrefix = "Create New&nbsp;";
	$submitURL = "/information_technology/repairs/add";
	$_id = 0;
	$show_approval = true;
}
?>

<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>

<div class='alert alert-info'><h3><?= $titlePrefix ?>Repair Entry <a class='btn return-btn add-close' style='float:right;margin-right:-30px;' >Back to Repair List</a></h3></div>

<form id="submit_form" action='<?= $submitURL ?>' method='post' class='form-inline'>
	<fieldset >
		<div class="row-fluid">						
			<div class='alert alert-success'><h4>Requester Details
			<?php if(!$isAdd && !empty($repair_summary_details)):?>
				<label class="label label-important request-code-label" id="requester-request-code-label" style="float:right;font-size:16px;"><?= $repair_summary_details->repair_code ?></label>
			<?php elseif($isAdd): ?>
				<label class="label label-important request-code-label" id="requester-request-code-label" style="float:right;font-size:16px;">00-00000</label>
			<?php endif; ?>
			</h4></div>
		</div>
		
		

		<div class="row-fluid">
			<div class="span6">

				<?php
				if(!$isAdd) {
					if ($repair_summary_details->branch_id <> 0) {
				?>					
						<?= form_checkbox('is_branch', 'is_branch', TRUE, 'id="is_branch" disabled="disabled"') ?> <strong>Is Branch Repair?</strong>
					<?php 
					} else { 
					?>
						<?= form_checkbox('is_branch', 'is_branch', FALSE, 'id="is_branch" disabled="disabled"') ?> <strong>Is Branch Repair?</strong>
				<?php 
					}
				} else { 
				?>
					<?= form_checkbox('is_branch', 'is_branch', FALSE, 'id="is_branch"') ?> <strong>Is Branch Repair?</strong>
				<?php 
				} 
				?>

				<br/><br/>

				<label><strong>Requester:</strong></label>
				<br/>
				<?php if(!$isAdd): ?>
				<small class="customer-assign-btn">
					<?php
						if ($repair_summary_details->branch_id <> 0) {
					?>		
						<input id="search_requester" type="text" placeholder="Search Requester" disabled="disabled" readonly="readonly" value="<?= $repair_summary_details->branch_id ?>">
					<?php
						} else {
					?>		
						<input id="search_requester" type="text" placeholder="Search Requester" disabled="disabled" readonly="readonly" value="<?= $repair_summary_details->id_number ?>">			
					<?php
						}
					?>		
					
				</small>
				<?php elseif($isAdd): ?>
					<input id="search_requester" type="text" placeholder="Search Requester" readonly="readonly">
					<a id="clear_requester" class="btn btn-small btn-primary">Clear</a>
				<?php endif; ?>

				<input id="requester_type" type="text" placeholder="Requester Type" readonly="readonly" style="display:none;">
				<br/><br/>
				<div class="control-group">
					<label class="control-label" for="requester_details"><strong>Details:</strong ></label>					
					<div class="controls">
						<?php if(!$isAdd): 
						
							if ($repair_summary_details->branch_id <> 0) {
								$details_content = get_requester_details($repair_summary_details->branch_id, "branch");
							} else {
								$details_content = get_requester_details($repair_summary_details->id_number, "employee");
							}	

						?>
						<textarea class='span10' rows="7" placeholder="" name="requester_details" id="requester_details" readonly><?= $details_content ?>
						</textarea>
						<?php elseif($isAdd): ?>
						<textarea class='span10' rows="7" placeholder="" name="requester_details" id="requester_details" readonly><?= set_value('requester_details') ?></textarea>
						<?php endif; ?>
					</div>
				</div>
				
			</div>
			<div class="span5">

				
				<label style=""><strong>Repair Status:</strong></label>
				<br/>
				<?php
				$repair_status_options = array();
				$repair_status_options = array('' => 'Select Status...');

				$repair_status_details = $this->information_technology_model->get_repair_status();

				foreach ($repair_status_details as $ipd) {
				 	$repair_status_options[$ipd->repair_status_id] = $ipd->repair_status;
				}				
				?>

				<?= form_dropdown('repair_status', $repair_status_options, set_value('repair_status'), 'id="repair_status"') ?>
				
				<br/>
				<br/>
				<div class="row-fluid">					
					<div class="control-group <?= $this->form_validation->error_class('remarks') ?>">
						<label class="control-label" for="remarks"><strong>Concern:</strong></label>
						<div class="controls">
							<?php if(!$isAdd): ?>
							<textarea class='span12' rows="4" placeholder="" name="reported_concern" id="reported_concern" maxlength="255" style="resize:none;"><?= $repair_summary_details->reported_concern ?></textarea>
							<br/><br/>							
							<?php elseif($isAdd): ?>
							<textarea class='span12' rows="4" placeholder="" name="remarks" id="reported_concern" maxlength="255" style="resize:none;"></textarea>
							<p class="help-block"><?= $this->form_validation->error('remarks'); ?></p>
							<?php endif; ?>
						</div>
					</div>					
				</div>
				<br/>
				
				<div id="received_from_branch_container" style="display:none;">
					<label style=""><strong>TR Number (In):</strong></label>
					<br/>

					<?php if(!$isAdd): ?>
					<input name="tr_number_in" id="tr_number_in" class="" placeholder="TR Number (IN)" value="<?= $repair_summary_details->tr_number_in ?>" />
					<?php elseif($isAdd): ?>
					<input name="tr_number_in" id="tr_number_in" class="" placeholder="Enter TR Number (IN)" />
					<?php endif; ?>

					<br/><br/>
				</div>	

				<div class="row-fluid">
					<label class="control-label" for="date_received"><strong>Date:</strong></label>
					<br/>
					<input type="text" class="input-medium" id="date_received" name='date_received' readonly='readonly' style='cursor:pointer;' />
					<span id='date_received_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
				</div>
				
			</div>
		</div>
		
		<br/>
		<div class='alert alert-success'><h4>Items
			<?php if(!$isAdd && !empty($repair_summary_details)):?>
				<label class="label label-important request-code-label" id="item-request-code-label" style="float:right;font-size:16px;"><?= $repair_summary_details->repair_code?></label>
			<?php elseif($isAdd): ?>
				<label class="label label-important request-code-label" id="item-request-code-label" style="float:right;font-size:16px;">00-00000</label>
			<?php endif; ?>
		</h4></div>	

		
		<div class="row-fluid">
			<table class="table inventory-orders">
				<thead id="items_header">
					<tr>
						<th class="item">Item</th>
						<th class="qty">Quantity</th>
						<th class="remarks">Description / Remarks</th>
						<th class="peripherals">Particulars</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody id="inputs">
					<tr>						
						<td>
							<div class="control-group <?= $this->form_validation->error_class('add_item_name') ?>">
								<div class="controls">
									<?= form_input('search_item',NULL,'id="search_item" class="item" placeholder="Item" readonly');?>
									<input type="hidden" name="add_item_id" id="add_item_id" class="add_item_id" readonly>
									<?php if(false): ?>
									<select class="item" name="add_item_name">
										<option selected="selected" value="">Select Item</option>
										<option value="new">Create New Item</option>
										<?php foreach($items as $item):?>
										<option value="<?=$item->item_id?>" data="<?=$item->item_name?>"><?=$item->item_name?></option>
										<?php endforeach;?>
										<?php
										$new_items = set_value('list_new_item_names');
										if(!empty($new_items)):?>
											<?php foreach(array_filter(explode('|',$new_items)) as $new_item):?>
										<option value="add[]" data="<?=$new_item?>">*New* <?=$new_item?></option>
											<?php endforeach; ?>
										<?php endif;?>
									</select>
									<?php endif; ?>
								</div>
							</div>
							<?=form_hidden('list_new_item_names',implode('|',array_unique(explode('|',set_value('list_new_item_names')))));?>
							<?=form_hidden('new_item_name');?>
						</td>
						<td>
							<?php
							$qty_options = array();
							for ($i=1; $i<=20; $i++) {
								array_push($qty_options, $i);	
							}
							
							echo form_dropdown('add_item_qty',$qty_options, NULL,'id="add_item_qty" class="qty"');
							?>
						</td>
						<td>
							<?= form_textarea('add_item_description',NULL,'id="add_item_description" class="remark" placeholder="Description" style="width:280px;height:50px;resize:none;"');?>
						</td>
						<td>
							<?= form_textarea('add_item_periphrals',NULL,'id="add_item_peripherals" class="peripherals" placeholder="Particulars" style="width:280px;height:50px;resize:none;"');?>
						</td>
						<td>
							<a id="add_wr_item" class="btn btn-primary">Add to List</a>							
						</td>
						
					</tr>

				</tbody>
			</table>
			<div class="control-group <?= $this->form_validation->error_class('add_item_name') ?>">
				<div class="controls">
					<p class="help-block"><?= $this->form_validation->error('add_item_name'); ?></p>
				</div>
			</div>
			<div class="control-group error">
				<p id="input_errors" class="help-block">
					
				</p>
			</div>
			<h4 style="margin-bottom:5px;margin-left:10px;">List of Hardware/s</h4>
			<table class="table inventory-order-items table-bordered table-striped table-condensed">
				<thead id="items_header">					
					<tr>						
						<th class="item">Item</th>
						<th class="qty">Quantity</th>
						<th class="remarks">Description / Remarks</th>
						<th class="peripherals">Particulars</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="wr_items">					
				</tbody>
			</table>
		</div>		
		<hr/>
		<div class="controls" align="right">			
			<a class="btn return-btn add-close">Close</a>
		</div>
		
	</fieldset>
</form>
<?php

	$this->load->view('template_search_requester');
	$this->load->view('template_search_item');
	$this->load->view('template_search_branch');


?>
<script type="text/javascript">

	
	$(document).ready(function(){
		
		var _Id = <?= $_id ?>;

		if (_Id == 0) {			
		} else {
			b.request({
				url: "/information_technology/repairs/get_item_details_view",
				data: {				
					"repair_summary_id" : _Id,
				},
				on_success: function(data) {

					$("#wr_items").html(data.data.html_items);				
				} 
			});
		}
	});
	
	var items_array = <?= json_encode($items); ?>;

	$("#date_received").datepicker({
        timeFormat: 'hh:mm tt',
		'dateFormat' : "yy-mm-dd",			
	});
	
	$("#date_received_icon").click(function(e) {
		$("#date_received").datepicker("show");
	});
	
	$("#date_received").datepicker('setDate', '<?= $date_received ?>');
	$("#date_received").datepicker("option", "changeMonth", true);
	$("#date_received").datepicker("option", "changeYear", true);
	


	$("#search_requester").focus(function() {

		if (document.getElementById('is_branch').checked) {
			assignRequester(1);
		} else {
			assignRequester(0);
		}	

	});
	
	var assignRequester = function(is_branch) {

		if (is_branch == 0)	{

			// show add form modal					
			assignRequesterModal = b.modal.new({
				title: "Assign Requester",
				html: _.template($('#search-requester-template').html(), {}),
				width: 800,
			});
			assignRequesterModal.show();


			$(document).on("click",'#btn_requester_search',function(e) {
				e.preventDefault();
				$('#frm_assign_search').removeClass('error');
				$('#txt_requester_search_key_help').html('');
				
				var search_key = $.trim($('#txt_requester_search_key').val());
				
				if (search_key.length == 0) {
					$('#frm_assign_search').addClass('error');
					$('#txt_requester_search_key_help').html('Search key cannot be empty.');
					$('#txt_requester_search_key_help').show();
					return;
				}
				
				searchPersonnel(search_key, function(data) {
					
					if (data.status == 'ok') {
					
						var employees = data.data.employees;
						$('#assign-requester-listing').html(_.template($('#assign-requester-item-template').html(), {'employees' : employees}));
						$.each(data.data.keys, function(index, key_item) {

							//$('#assign-requester-listing td:nth-child(2)').highlight(key_item);
						});
						
						// apply click event on select buttons
						$('#assign-requester-listing .btn-select-member').click(function(e) {
							var id_number = $(this).data('idnumber');
							
							$("#id_number").val(id_number);

						
							// get requester details
							b.request({
								url: "/information_technology/get_requester_details",
								data: {
									"id_number": id_number,
									"requester_type" : "employee",
								},
								on_success: function(data) {
														
									$("#requester_details").text(data.data.html);

								} 
							});
												
							$("#search_requester").val($(this).data('idnumber'));
							$("#requester_type").val("employee");
							
							
							assignRequesterModal.hide();
							
						});
						
					} else {
						$('#assign-requester-listing').html('<tr><td colspan="3">'+data.msg+'</td></tr>');
					}
					
				});
				
			});
		} else {

			// show add form modal					
			assignRequesterModal = b.modal.new({
				title: "Assign Branch",
				html: _.template($('#search-branch-template').html(), {}),
				width: 600,
			});
			assignRequesterModal.show();


			$(document).on("click",'#btn_branch_search',function(e) {
				e.preventDefault();
				$('#frm_assign_search').removeClass('error');
				$('#txt_branch_search_key_help').html('');
				
				var search_key = $.trim($('#txt_branch_search_key').val());
				
				if (search_key.length == 0) {
					$('#frm_assign_search').addClass('error');
					$('#txt_branch_search_key_help').html('Search key cannot be empty.');
					$('#txt_branch_search_key_help').show();
					return;
				}
				
				searchBranch(search_key, function(data) {
					
					if (data.status == 'ok') {
					
						var branches = data.data.branches;
						$('#assign-branch-listing').html(_.template($('#assign-branch-item-template').html(), {'branches' : branches}));
						$.each(data.data.keys, function(index, key_item) {

							//$('#assign-requester-listing td:nth-child(2)').highlight(key_item);
						});
						
						// apply click event on select buttons
						$('#assign-branch-listing .btn-select-branch').click(function(e) {
							var branch_id = $(this).data('id');
							
							$("#branch_id").val(branch_id);

							alert(branch_id);

							// get requester details
							b.request({
								url: "/information_technology/get_requester_details",
								data: {
									"id_number": branch_id,
									"requester_type" : "branch",
								},
								on_success: function(data) {
														


									$("#requester_details").text(data.data.html);

								} 
							});
												
							$("#search_requester").val($(this).data('id'));
							$("#requester_type").val("branch");
							
							assignRequesterModal.hide();
							
						});
						
					} else {
						$('#assign-requester-listing').html('<tr><td colspan="3">'+data.msg+'</td></tr>');
					}
					
				});
				
			});
		}
		
	};
	
	var searchPersonnel = function(search_key, cb, with_overlay) {
		with_overlay = typeof(with_overlay) == 'undefined' ? true : with_overlay;
		b.request({
			'with_overlay' : with_overlay,
			url: '/information_technology/get_requester',
			data: {'search_key' : search_key},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};

	var searchBranch = function(search_key, cb, with_overlay) {
		with_overlay = typeof(with_overlay) == 'undefined' ? true : with_overlay;
		b.request({
			'with_overlay' : with_overlay,
			url: '/information_technology/get_branch',
			data: {'search_key' : search_key},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};
	
	$('#clear_requester').click(function(){

		//alert($("#date_received").val());

		$('#requester_details').html('');
		$('#id_number').val('');
		$('#details_placeholder').val('');
		$('#search_requester').val('');
	});

	$(document).on("click",'#search_item',function(e) {
	//$("#search_item").focus(function() {
		e.preventDefault();
		//if(_.isObject(document.search_item_modal)) return;
		search_item();

	});

	document.search_item_modal = null;

	var search_item = function() {
		document.search_item_modal = b.modal.create({
			title: "Search Hardware",
			width: 500,
			html: _.template($("#search-item-template").html(),{}),
		});
		
		document.search_item_modal.show();
		
		$("#item_type_search").change(function(e) {
			var search_key = $.trim($("#txt_item_search_key").val());
			if(search_key != "") $("#btn_item_search").trigger("click");		
		})
		
		$("#btn_item_search").click(function(e) {
			e.preventDefault();
			
			var search_key = $.trim($("#txt_item_search_key").val());
			var item_type_id = $.trim($("#item_type_search").val());

			var warehouse_option = $("#item_warehouse_option").val();

			if (search_key.length == 0) 
			{
				return;
			}
			
			b.request({
				url: "/information_technology/search_item",
				data: {
					"search_key": search_key,
				},
				on_success: function(data) {

					var items = data.data.items;

					$("#item-listing").html(_.template($("#item-list-template").html(),{"items": items}));
					
					$("#item-listing .btn-select-item").click(function(e) {
						e.preventDefault();
						var item_id = $(this).data("id");
						var item = items_array[item_id];

						$("#search_item").val($(this).data("name"));
						$("#add_item_id").val(item_id);
						
						document.search_item_modal.hide();
						document.search_item_modal = null;
					});
					
				} 
			});
		});
	}

	$("#remarks").bind("keyup keydown",function(){
		if($(this).val().length > 255){
			$(this).val($(this).val().slice(0, 255));
		}
	});


	$("#add_wr_item").click(function(){

		if ($("#requester_details").text() == '') {
			input_errors = "Assign a Requester first. ";
			var item_error_modal = b.modal.create({
				title: "Error :: Requester Not Set",
				width: 450,
				html: "<p>There was an error in your request.</p><p>"+input_errors+"</p>"
			});
			item_error_modal.show();
			return;				
		}

		var _hasError = 0;
		var input_errors = "";
		var item_name = "";

		if($('#search_item').val() == '') {
			_hasError = 1;
			input_errors += "The Item field is required. ";
		}			

		$('#input_errors').html('<p>'+input_errors+'</p>');

		if (_hasError == 1) {
			return false;
		} else {

			var item_name = "";
			var _tr_number_in = "";

			if($('#search_item').val() == 'add[]')
			{
				item_name = $('input[name="new_item_name"]').val();
			}
			else
			{
				item_name = $('#search_item').val();
			}
			$('#input_errors').html('');

			//alert($("#add_item_qty").val());

			if ($("#repair_status").val() == 2) {
				_tr_number_in = $("#tr_number_in").val();
			} 

			// ajax request
			b.request({
				url : '/information_technology/repairs/create_request',
				data : {
					'request_code' : $("#requester-request-code-label").text(),
					'item_id' : $("#add_item_id").val(),
					'quantity' : $("#add_item_qty").val(),
					'description' : $("#add_item_description").val(),
					'peripherals' : $("#add_item_peripherals").val(),
					'repair_status' : $("#repair_status").val(),
					'reported_concern' : $("#reported_concern").val(),
					'requester_id' : $("#search_requester").val(),
					'tr_number_in' : _tr_number_in,
					'requester_type' : $("#requester_type").val(),
					'date_received' : $("#date_received").val(),
				},
				on_success : function(data) {

					if (data.status == "1")	{
						
						createRequestModal = b.modal.new({
							title: data.data.title,
							width:450,
							html: data.data.html,
						});
						createRequestModal.show();	

					
						$("#wr_items").html(data.data.html_items);
						$("#search_item").val('');
						$("#add_item_id").val('');
						$("#add_item_qty").val('0');
						$("#add_item_description").val('');
						$("#add_item_peripherals").val('');

						$("#requester-request-code-label").text(data.data.repair_code);
						$("#item-request-code-label").text(data.data.repair_code);
						
					} else {			

						errorCreateRequestModal = b.modal.new({
							title: data.data.title,
							width:450,							
							html: data.data.html,
						});
						errorCreateRequestModal.show();	
						
					}
				}

			})
		}
	});
		
	$(".rmv_wr_item").live('click',function(){

		var repair_detail_id = $(this).attr("data");
		
		// ajax request
		b.request({
			url : '/information_technology/repairs/remove_item',
			data : {								
				'repair_detail_id' : repair_detail_id,
			},
			on_success : function(data) {
				
				if (data.status == "1")	{					
					
					// show add form modal					
					proceedRemoveItemModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Ok' : function() {
									
								// TODO: refresh item area

								proceedRemoveItemModal.hide();
							}
						}
					});
					proceedRemoveItemModal.show();
					
				} else {
					// show add form modal
					removeItemModal.hide();					
					errorRemoveItemModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {						
							'Close' : function() {
								errorRemoveItemModal.hide();								 							
							}
						}
					});
					errorRemoveItemModal.show();	
				}
			}
		})
		return false;		
	});

	$(".add-close").live('click',function(){
		window.location.href = '/information_technology/repairs/listing';
		return false;
	});

	$("#repair_status").live('click',function(){
		if (($(this).val() == "2") || ($(this).val() == "15")) {
			$("#received_from_branch_container").show();
		} else {
			$("#received_from_branch_container").hide();
		}
	});
	
</script>
