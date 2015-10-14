<?php

	//echo css('inventory.css');
	//echo js('apps/spareparts.js');

?>
<style type="text/css">
	.inventory-orders .good_qty {width:70px;text-align:right;}
	.inventory-orders .bad_qty {width:70px;text-align:right;}
	.inventory-orders .unit {width:60px;}
	.inventory-orders .item {width:190px;}
	.inventory-orders .price {width:80px;text-align:right;}
	.inventory-orders .action {width:80px;}
	.inventory-orders .discount {width:45px;text-align:right;}
	.inventory-orders .discount_price {width:60px;text-align:right;}
	.inventory-orders .remark {width:100px;}

	.inventory-order-items .qty {width:112px;text-align:right;}
	.inventory-order-items .unit {width:112px;}
	.inventory-order-items .item {width:224px;}
	.inventory-order-items .price {width:112px;text-align:right;}
	.inventory-order-items .discount {width:50px;text-align:right;}
	.inventory-order-items .discount_price {width:60;text-align:right;}
	.inventory-order-items .remark {width:284px;}

	.inventory-order-items input.qty  {width:102px;text-align:right;}
	.inventory-order-items select.unit {width:112px;}
	.inventory-order-items select.item {width:224px;}
	.inventory-order-items input.price {width:102px;text-align:right;}
	.inventory-order-items input.discount {width:50px;text-align:right;}
	.inventory-order-items input.discount_price {width:60;text-align:right;}
	.inventory-order-items input.remark {width:274px;}

</style>
<?php
$isAdd = false;
$titlePrefix = "Reprocess Items - &nbsp;";
$submitURL = "/spare_parts/" . $department_module_details->segment_name . "/reprocess/" . $service_unit_details->service_unit_id;
$_id = $service_unit_details->service_unit_id;
$show_approval = false;

?>

<?php
	$breadcrumb_container = assemble_breadcrumb();
					
	$details_content = get_requester_details($service_unit_details->id_number, "employee", 1);

	$motor_brand_model_detail = "N/A";
	if ($service_unit_details->motorcycle_brand_model_id > 0) {
		$motor_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id($service_unit_details->motorcycle_brand_model_id);
		$motor_brand_model_detail = $motor_details->brand_name . " / " . $motor_details->model_name;
	}

?>

<?= $breadcrumb_container; ?>

<div class='alert alert-info'><h3><?= $titlePrefix ?><?= $department_module_details->module_name ?> <a class='btn return-btn add-close' style='float:right;margin-right:-30px;' >Back to Request List</a></h3></div>



<div>
	<table  class='table table-bordered'>
		<thead>				
		</thead>
		<tbody>
			<tr>
				<td style="width:130px"><strong>NAME:</strong></td>
				<td style="width:400px"><?= $details_content->complete_name ?></td>
				<td style="width:130px"><strong>WAREHOUSE:</strong></td>
				<td></td>
			</tr>	
			<tr>
				<td><strong>ID NUMBER:</strong></td>
				<td><?= $details_content->id_number ?></td>
				<td><strong>MOTOR BRAND/MODEL:</strong></td>
				<td><?= $motor_brand_model_detail ?></td>
			</tr>	
			<tr>
				<td><strong>DEPARTMENT:</strong></td>
				<td><?= $details_content->department_name ?></td>
				<td><strong>ENGINE:</strong></td>
				<td><?= $service_unit_details->engine ?></td>
			</tr>	
			<tr>
				<td><strong>POSITION:</strong></td>
				<td><?= $details_content->position_name ?></td>
				<td><strong>CHASSIS:</strong></td>
				<td><?= $service_unit_details->chassis ?></td>
			</tr>	
			<tr>
				<td><strong>EMAIL:</strong></td>
				<td><?= $details_content->email_address ?></td>
				<td><strong>STATUS:</strong></td>
				<td><?= $service_unit_details->status ?></td>
			</tr>	
			<tr>
				<td><strong>CONTACT NUMBER:</strong></td>
				<td><?= $details_content->contact_number ?></td>
				<td><strong>REMARKS:</strong></td>
				<?php
					if (strlen(trim($service_unit_details->remarks)) > 0)
						echo "<td><a href='#' id='view-full-remarks' data='{$service_unit_details->remarks}'><u>View Remarks</u></a></td>";
					else
						echo "<td>N/A</td>";
				?>				
			</tr>								
		<tbody>
	</table>	
</div>		


<form id="submit_form" action='<?= $submitURL ?>' method='post' class='form-inline'>
	<fieldset >		
		<div class="row-fluid">
			<div class="span8">													
				<label class="control-label" for="remarks"><strong>Add New Remarks</strong></label>
				<input style="450px;" id="requester_remarks" type="text" placeholder="New Remarks">	
				<a id="add_new_requester_remarks" class="btn btn-primary">Add</a>			
				</div>							
			</div>
			
		</div>
		
		<br/>
		<div class='alert alert-success'><h4>Items
			<?php if(!$isAdd && !empty($service_unit_details)):?>
				<label class="label label-important request-code-label" id="item-request-code-label" style="float:right;font-size:16px;"><?= $service_unit_details->request_code?></label>
			<?php elseif($isAdd): ?>
				<label class="label label-important request-code-label" id="item-request-code-label" style="float:right;font-size:16px;"><?= $department_module_details->module_code ?></label>
			<?php endif; ?>
		</h4></div>	

		
		<div class="row-fluid">
			<table id="test_table" class="table inventory-orders">
				<thead id="items_header">
					<tr>
						<th class="discount_percentage">Action</th>	
						<th class="item">Item</th>						
						<th class="good_qty">Good Quantity</th>
						<th class="bad_qty">Bad Quantity</th>
						<th class="unit_price">Unit Price</th>						
						<th class="discount_percentage">Discount</th>
						<th class="discount_price">Discount Price</th>
						<th class="remark">Remarks</th>
						<th></th>
						<th></th>
					</tr>					
				</thead>
				<tbody id="inputs">
					<tr>
						<td>
							<?php
							$action_options = array(
												'return' => 'RETURN', 
												'charge' => 'CHARGE'
											);

							echo form_dropdown('add_item_action', $action_options, NULL, 'id="action_option" class="action"');
							?>
						</td>
						
						<td>
							<div class="control-group <?= $this->form_validation->error_class('add_item_name') ?>">
								<div class="controls">
									<?= form_input('search_item',NULL,'id="search_item" class="item" placeholder="Item" readonly');?>
									<input type="hidden" name="add_item_name" id="add_item_name" class="add_item_name" readonly>
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
												
						<!--td>
							
								$request_items = json_decode($json_items);

								$item_detail_options = array('0' => 'Please select an item...');
								foreach ($request_items as $ri) {
																		
									// get item_name based on item_id
									$item_view_details = $this->spare_parts_model->get_item_view_by_id($ri->item_id);

									$item_detail_options[$ri->service_unit_detail_id] = $item_view_details->sku . ' - ' . $item_view_details->description;
									
								}

								echo form_dropdown('select_item', $item_detail_options, NULL, 'id='select_item' class='item');

							?>						
						</td-->
											

						<td><?= form_input('add_item_good_qty',NULL,'class="good_qty" placeholder=" Good Qty"');?></td>
						<td><?= form_input('add_item_bad_qty',NULL,'class="bad_qty" placeholder="Bad Qty"');?></td>
						<td>
							<?= form_input('add_item_price',NULL,'id="add_item_price" class="price" placeholder="Unit Price" readonly');?>							
							<?= form_input('add_request_detail_id',NULL,'id="add_request_detail_id" class="price" placeholder="Request Detail ID" style="display:none"');?>
						</td>						
						<td>
							<?php
							$discount_options = array();
							for ($i=100; $i>=0; $i--) {
								$discount_options[$i] = $i;	
							}
							
							echo form_dropdown('add_item_discount',$discount_options, NULL,'id="add_item_discount" class="unit" disabled="disabled"');
							?>
						</td>
						<td>
							<?= form_input('add_item_discount_price',NULL,'id="add_item_discount_price" class="price" placeholder="Disc. Price" disabled="disabled"');?>
						</td>							
						<td>
							<?= form_input('add_item_remarks',NULL,'class="remark" placeholder="Remarks"');?>
						</td>
						<td>
							<a id="add_wr_item" class="btn btn-primary"><span id="add_wr_item_caption">Return Item/s</span></a>
						</td>
						<td></td>
					</tr>
					<tr id="charge_recipient" hidden="hidden">
						<td><h5 style="margin-top:5px;">Charge To:</h5></td>							
						<td><input id="search_recipient" style="width:170px;" class="item" type="text" placeholder="Search Requester Here..." readonly="readonly"></td>
						<td colspan="6"><?= form_input('add_recipient_name',NULL,'id="add_recipient_name" style="width:500px;" placeholder=" Juan dela Cruz" readonly');?></td>
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
			<h4 style="margin-bottom:5px;margin-left:10px;">List of Items</h4>
			<table class="table inventory-order-items table-bordered table-striped">
				<thead id="items_header">					
					<tr>
						<th class="">Action</th>						
						<th class="">Item</th>						
						<th class="">Good Qty</th>
						<th class="">Bad Qty</th>
						<th class="">Total Qty</th>
						<th class="">SRP</th>
						<th class="">Recipient</th>
						<th class="">Charge Discount</th>
						<th class="">Charge Price</th>
						<th class="">Total Amount</th>
						<th class="">Remarks</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody id="wr_items">
					<?php
					$temp = NULL;
			
					$temp = json_decode($json_reprocessed_items, true);

					for($i=0;$i<count($temp);$i++):?>						
					
					<tr class="item_row">
						<?php								
						$itemInfo = $this->spare_parts_model->get_item_view_by_id($temp[$i]['item_id']);

						$requester_name = "";

						if (!(($temp[$i]['id_number']) == NULL)) {
							$requesterInfo = $this->human_relations_model->get_employment_information_view_by_id($temp[$i]['id_number']);
							$requester_name = $requesterInfo->complete_name;
						}

						$remarks = json_decode($temp[$i]['remarks']);

						$row = "";
						
						if(!empty($remarks))
						{				
							foreach($remarks as $r)
							{	
								$row .= "{$r->datetime} - {$r->message}\n";
							}				
						} 

						?>
						<td class="unit"><?=set_value('item_action['.$i.']',$temp[$i]['action'])?></td>						
						<td class="item"><?=set_value('temp_item['.$i.']',$itemInfo->description)?></td>
						<td class="qty"><?=number_format(set_value('item_good_qty['.$i.']',$temp[$i]['good_quantity']),2)?></td>
						<td class="qty"><?=number_format(set_value('item_bad_qty['.$i.']',$temp[$i]['bad_quantity']),2)?></td>
						<td class="qty"><?=number_format((set_value('item_good_qty['.$i.']',$temp[$i]['good_quantity']) + set_value('item_bad_qty['.$i.']',$temp[$i]['bad_quantity'])),2)?></td>
						<td class="price"><?=number_format(set_value('item_price['.$i.']',$temp[$i]['srp']),2)?></td>
						<td class="item"><?=set_value('item_recipient['.$i.']',$requester_name)?></td>
						<td class="discount"><?=number_format(set_value('discount['.$i.']',$temp[$i]['discount']))?></td>
						<td class="discount_price"><?=number_format(set_value('discount_price['.$i.']',$temp[$i]['discount_amount']),2)?></td>
						<td class="price"><?=number_format(set_value('item_total_amount['.$i.']',$temp[$i]['total_amount']),2)?></td>
						<td class="remark"><?=set_value('item_remarks['.$i.']',$row)?></td>						
						<td class ="remove_item" id="<?= $temp[$i]['request_item_id'] ?>" data="<?= $temp[$i]['service_unit_detail_id'] ?>" data-id="<?= $temp[$i]['request_item_id'] ?>"><a class="btn btn-danger rmv_wr_item"><i class="icon-white icon-minus"></i></a></td>						
						<td class="hidden_values">
							<input type="hidden" name="item_good_qty[]" id="item_good_qty[]" value="<?=set_value('item_good_qty['.$i.']',$temp[$i]['good_quantity'])?>">
							<input type="hidden" name="item_bad_qty[]" id="item_bad_qty[]" value="<?=set_value('item_bad_qty['.$i.']',$temp[$i]['bad_quantity'])?>">
							<input type="hidden" name="unit_name[]" id="unit_name[]" value="<?=set_value('unit_name['.$i.']',$itemInfo->description)?>">
							<input type="hidden" name="item_name[]" id="item_name[]" value="<?=set_value('item_name['.$i.']',$temp[$i]['item_id'])?>">
							<input type="hidden" name="temp_item[]" id="temp_item[]" value="<?=set_value('temp_item['.$i.']',$itemInfo->description)?>">
							<input type="hidden" name="item_price[]" id="item_price[]" value="<?=set_value('item_price['.$i.']',$temp[$i]['srp'])?>">
							<input type="hidden" name="item_remarks[]" id="item_remarks[]" value="<?=set_value('item_remarks['.$i.']',$temp[$i]['remarks'])?>">
						</td>
					</tr>
						<!--?php endif;?-->
					<?php endfor;?>
				</tbody>
			</table>
		</div>
		<div style="margin-left:30px;display:none;">
			<?php 
			if (!$isAdd) {
				echo "<h3>Total Amount: <span id='total-amount'>{$request_item_amount_total->total_amount}</span></h3>";
			} else {
				echo "<h3>Total Amount: <span id='total-amount'>0.00</span></h3>";
			}
			?>
		</div>
		<hr/>
		<div class="controls" align="right">
			<a id='forward_to_warehouse' class="btn btn-primary" data="<?= $is_forwarded ?>"><span id="ftw_caption"></span></a>
			<a href='/spare_parts/display_returnslip/<?= $service_unit_details->request_code ?>' target = '_blank' id='print_return_slip' class="btn btn-primary"><span id="prs_caption">Print Return Slip Form</span></a>						
			<a class="btn return-btn add-close">Close</a>
		</div>
		
	</fieldset>
</form>
<?php
	$this->load->view('template_search_requester');	
	$this->load->view('template_search_requested_item');	

?>

<script type="text/javascript">

	var _is_forwarded = <?= $is_forwarded ?>;
	
	$(document).ready(function(){


		$("#forward_to_warehouse").attr("data");
		
		if (_is_forwarded == 1) {
			$("#ftw_caption").text("Cancel Forward To WH");
			document.getElementById('add_wr_item').setAttribute("disabled","disabled");
			$("#test_table *").attr("disabled", "disabled").off('click');
			document.getElementById("forward_to_warehouse").className = "btn btn-danger";
			//$(".remove_item").hide();
			$(".rmv_wr_item").hide();
			$("#forward_to_warehouse").hide();
		} else {
			$("#ftw_caption").text("Forward To WH");
			$("#test_table *").removeAttr("disabled", "disabled").off('click');
			document.getElementById("forward_to_warehouse").className = "btn btn-primary";
			//$(".remove_item").show();	
			$(".rmv_wr_item").show();					
			$("#forward_to_warehouse").show();
		}

	});

	var items_array = <?= json_encode($items); ?>;

	var item_entry_row = _.template('\
		<td class="unit"><%= item_action %></td>\n\
		<td class="item"><%= select_item %></td>\n\
		<td class="qty"><%= item_good_qty %></td>\n\
		<td class="qty"><%= item_bad_qty %></td>\n\
		<td class="qty"><%= item_total_qty %></td>\n\
		<td class="price"><%= item_price %></td>\n\
		<td class="item"><%= item_recipient %></td>\n\
		<td class="qty"><%= item_discount %></td>\n\
		<td class="qty"><%= item_discount_price %></td>\n\
		<td class="price"><%= item_total_amount %></td>\n\
		<td class="remark"><%= item_remarks %></td>\n\
		<td id="<%= active_reprocessed_item_id %>" data="<%= active_service_unit_detail_id %>" data-id="<%= active_reprocessed_item_id %>"><a class="btn btn-danger rmv_wr_item"><i class="icon-white icon-minus"></i></a></td>\n\
		<td class="hidden_values">\n\
			<input type="hidden" name="item_good_qty[]" id="item_good_qty[]" value="<%= hidden_item_good_qty %>">\n\
			<input type="hidden" name="item_bad_qty[]" id="item_bad_qty[]" value="<%= hidden_item_bad_qty %>">\n\
			<input type="hidden" name="item_price[]" id="item_price[]" value="<%= hidden_item_price %>">\n\
			<input type="hidden" name="item_discount[]" id="item_discount[]" value="<%= hidden_item_discount %>">\n\
			<input type="hidden" name="item_discount_price[]" id="item_discount_price[]" value="<%= hidden_item_discount_price %>">\n\
			<input type="hidden" name="item_remarks[]" id="item_remarks[]" value="<%= item_remarks %>">\n\
		</td>');
	

	$("#search_requester").focus(function() {
		assignRequester();
	});
	
	var assignRequester = function() {

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
							url: "/spare_parts/get_requester_details",
							data: {
								"id_number": id_number,
								"requester_type" : "employee",
							},
							on_success: function(data) {
													
								$("#requester_details").text(data.data.html);

							} 
						});
											
						$("#search_requester").val($(this).data('idnumber'));
						
						
						assignRequesterModal.hide();
						
					});
					
				} else {
					$('#assign-requester-listing').html('<tr><td colspan="3">'+data.msg+'</td></tr>');
				}
				
			});
			
		});
		
	};
	
	var searchPersonnel = function(search_key, cb, with_overlay) {
		with_overlay = typeof(with_overlay) == 'undefined' ? true : with_overlay;
		b.request({
			'with_overlay' : with_overlay,
			url: '/spare_parts/get_requester',
			data: {'search_key' : search_key},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};
	
	$('#clear_requester').click(function(){
		$('#requester_details').html('');
		$('#id_number').val('');
		$('#details_placeholder').val('');
		$('#search_requester').val('');
	});

	$(document).on("change",'#action_option',function(e) {
		e.preventDefault();		
		if ($(this).val() == "return") {
			$("#add_wr_item_caption").text("Return Item/s");
			document.getElementById("add_item_discount").disabled = true;
			document.getElementById("add_item_discount_price").disabled = true;
			$("#charge_recipient").hide();
		} else {
			$("#add_wr_item_caption").text("Charge Item/s");
			document.getElementById("add_item_discount").disabled = false;
			document.getElementById("add_item_discount_price").disabled = false;
			$("#charge_recipient").show();
		}

	});


	/*$(document).on("change",'#select_item',function(e) {
		e.preventDefault();		
		select_item();

	});

	var select_item = function() {

		b.request({
			url: "/spare_parts/get_item_details",
			data: {
				"request_detail_id": $("#select_item").val(),
				"segment_name" : '<?= $department_module_details->segment_name ?>',
			},
			on_success: function(data) {

				
				document.getElementsByName('add_item_good_qty')[0].placeholder= data.data.item_details['good_quantity'];
				document.getElementsByName('add_item_bad_qty')[0].placeholder= data.data.item_details['bad_quantity'];
				$("#add_item_price").val(data.data.item_view_details['srp']);				
				document.getElementById('add_item_discount').value= data.data.discount;				
				document.getElementsByName('add_item_discount_price')[0].placeholder= data.data.item_details['discount_amount'];
				
				return;

				$("#item-listing").html(_.template($("#item-list-template").html(),{"items": items}));
				
				$("#item-listing .btn-select-item").click(function(e) {
					e.preventDefault();
					var item_id = $(this).data("id");
					var item = items_array[item_id];

					$("#search_item").val($(this).data("description"));
					$("#add_item_name").val(item_id);
					$("#add_item_price").val($(this).data("srp"));
					
					// always 1 since there is only 1 unit type (pieces)
					$('select[name="add_item_unit"]').val(1);
					document.getElementsByName('add_item_good_qty')[0].placeholder= $(this).data("good_quantity");
					document.getElementsByName('add_item_bad_qty')[0].placeholder= $(this).data("bad_quantity");

					
					document.search_item_modal.hide();
					document.search_item_modal = null;
				});
				
			} 
		});
		
	}*/


	$(document).on("click",'#search_item',function(e) {	
		e.preventDefault();		
		select_item();
	});

	document.search_item_modal = null;

	var select_item = function() {
		document.search_item_modal = b.modal.create({
			title: "Select Item",
			width: 800,
			html: _.template($("#select-item-template").html(),{}),
		});

		document.search_item_modal.show();

		$("#btn_item_search").click(function(e) {
			e.preventDefault();
			
			var search_key = $.trim($("#txt_item_search_key").val());
			var item_type_id = $.trim($("#item_type_search").val());

			var warehouse_option = $("#item_warehouse_option").val();
	
			b.request({
				url: "/spare_parts/get_requested_items",
				data: {
					"search_key": search_key,
					"request_id": <?= $service_unit_details->service_unit_id ?>,
					"segment_name": '<?= $segment_name ?>',
				},
				on_success: function(data) {

					var items = data.data.items;

					$("#item-listing").html(_.template($("#item-list-template").html(),{"items": items}));
					
					$("#item-listing .btn-select-item").click(function(e) {
						e.preventDefault();
						var item_id = $(this).data("id");
						var item = items_array[item_id];

						$("#search_item").val($(this).data("description"));
						$("#add_item_name").val(item_id);
						$("#add_item_price").val($(this).data("srp"));
						$("#add_request_detail_id").val($(this).data("request_detail_id"));
						// always 1 since there is only 1 unit type (pieces)
						$('select[name="add_item_unit"]').val(1);						

						document.getElementsByName('add_item_good_qty')[0].placeholder= $(this).data("remaining_good_quantity");
						document.getElementsByName('add_item_bad_qty')[0].placeholder= $(this).data("remaining_bad_quantity");

						document.search_item_modal.hide();
						document.search_item_modal = null;
					});
					
				} 
			});
		});

		$("#btn_item_search").trigger("click");		
	}

	//$("#add_wr_item").click(function(){
	$("#add_wr_item").live('click',function(){

		var _hasError = 0;
		var input_errors = "";
		var item_name = "";

		if ($("#select_item").val() == 0) {
			_hasError = 1;
			input_errors += "Select an Item first. "
		}

		if (($('input[name="add_item_good_qty"]').val() == '') && ($('input[name="add_item_bad_qty"]').val() == '')) {
			_hasError = 1;
			input_errors += "The Quantity fields are required. "			
		}
		
		if(!(_.isNumber($('input[name="add_item_good_qty"]').val() * 1)) || _.isNaN($('input[name="add_item_good_qty"]').val() * 1)) {
			_hasError = 1;
			input_errors += "The Good Quantity field must contain an integer. "
		}

		if(!(_.isNumber($('input[name="add_item_bad_qty"]').val() * 1)) || _.isNaN($('input[name="add_item_bad_qty"]').val() * 1)) {
			_hasError = 1;
			input_errors += "The Bad Quantity field must contain an integer. "
		}

		if (($('input[name="add_item_good_qty"]').val() <= 0) && ($('input[name="add_item_bad_qty"]').val() <= 0)) {
			_hasError = 1;
			input_errors += "The Quantity fields should be greater than 0. "			
		}

		if ($('input[name="add_item_good_qty"]').val() < 0) {
			_hasError = 1;
			input_errors += "The Good Quantity field should be greater than 0. "			
		}	

		if ($('input[name="add_item_bad_qty"]').val() < 0) {
			_hasError = 1;
			input_errors += "The Bad Quantity field should be greater than 0. "			
		}
		
		if($('input[name="add_item_name"]').val() == '' || $('input[name="add_item_name"]').val() == 'new') {
			_hasError = 1;
			input_errors += "The Item field is required. ";
		}	

		$('#input_errors').html('<p>'+input_errors+'</p>');

		if (_hasError == 1) {
			return false;
		} else {

			var item_name = "";
			if($('#search_item').val() == 'add[]')
			{
				item_name = $('input[name="new_item_name"]').val();
			}
			else
			{
				item_name = $('#search_item').val();
			}
			$('#input_errors').html('');

			var good_qty = $('input[name="add_item_good_qty"]').val();
			var bad_qty = $('input[name="add_item_bad_qty"]').val();

			good_qty = good_qty.replace(new RegExp('[,]', 'gi'), '');
			bad_qty = bad_qty.replace(new RegExp('[,]', 'gi'), '');			

			$('input[name="add_item_good_qty"]').val(good_qty);
			$('input[name="add_item_bad_qty"]').val(bad_qty);

			//var request_detail_id = $("#select_item").val();
			var request_detail_id = $("#add_request_detail_id").val();
			$('#input_errors').html('');


			// ajax request
			b.request({
				url : '/spare_parts/service_unit/proceed_reprocess_item',
				data : {				
					'request_code' : $("#item-request-code-label").text(),
					'request_detail_id' : request_detail_id,
					'srp' : $('input[name="add_item_price"]').val(),
					'charge_discount' : $('select[name="add_item_discount"]>option:selected').text(),
					'charge_discount_amount' : $('input[name="add_item_discount_price"]').val(),
					'good_quantity' : $('input[name="add_item_good_qty"]').val(),
					'bad_quantity' : $('input[name="add_item_bad_qty"]').val(),
					'remarks' : $('input[name="add_item_remarks"]').val(),
					'action_option' : $('#action_option').val(),
					'id_number' : $('#search_recipient').val(),

				},
				on_success : function(data) {

					if (data.status == "1")	{
						
						createRequestModal = b.modal.new({
							title: data.data.title,
							width:450,
							html: data.data.html,
						});
						createRequestModal.show();	

						$("#requester-request-code-label").text(data.data.request_code);
						$("#item-request-code-label").text(data.data.request_code);
						//$("#total-amount").text(data.data.overall_total_amount);



						var item_entry_row = _.template('\
						<td class="unit"><%= item_action %></td>\n\
						<td class="item"><%= select_item %></td>\n\
						<td class="qty"><%= item_good_qty %></td>\n\
						<td class="qty"><%= item_bad_qty %></td>\n\
						<td class="qty"><%= item_total_qty %></td>\n\
						<td class="price"><%= item_price %></td>\n\
						<td class="item"><%= item_recipient %></td>\n\
						<td class="qty"><%= item_discount %></td>\n\
						<td class="qty"><%= item_discount_price %></td>\n\
						<td class="price"><%= item_total_amount %></td>\n\
						<td class="remark"><%= item_remarks %></td>\n\
						<td id="<%= active_reprocessed_item_id %>" data="<%= active_service_unit_detail_id %>" data-id="<%= active_reprocessed_item_id %>"><a class="btn btn-danger rmv_wr_item"><i class="icon-white icon-minus"></i></a></td>\n\
						<td class="hidden_values">\n\
							<input type="hidden" name="item_good_qty[]" id="item_good_qty[]" value="<%= hidden_item_good_qty %>">\n\
							<input type="hidden" name="item_bad_qty[]" id="item_bad_qty[]" value="<%= hidden_item_bad_qty %>">\n\
							<input type="hidden" name="item_price[]" id="item_price[]" value="<%= hidden_item_price %>">\n\
							<input type="hidden" name="item_discount[]" id="item_discount[]" value="<%= hidden_item_discount %>">\n\
							<input type="hidden" name="item_discount_price[]" id="item_discount_price[]" value="<%= hidden_item_discount_price %>">\n\
							<input type="hidden" name="item_remarks[]" id="item_remarks[]" value="<%= item_remarks %>">\n\
						</td>');





						$("#wr_items").append('<tr class="item_row">'+
							//item_entry_row({item_id: $('input[name="add_item_name"]').val(),
							item_entry_row({item_id: $('input[name="select_item"]').val(),								
							item_action: $('select[name="add_item_action"]>option:selected').text(),							
							select_item: data.data.item_details['description'],
							item_unit_name: $('select[name="add_item_unit"]>option:selected').text(),
							item_price: numberFormat($('input[name="add_item_price"]').val(),2),
							item_recipient: data.data.recipient_name,
							item_good_qty: numberFormat($('input[name="add_item_good_qty"]').val(),2),
							item_bad_qty: numberFormat($('input[name="add_item_bad_qty"]').val(),2),
							item_total_qty: numberFormat(($('input[name="add_item_good_qty"]').val() + $('input[name="add_item_bad_qty"]').val()),2),
							item_discount: numberFormat($('select[name="add_item_discount"]>option:selected').text()) + '%',
							item_discount_price: numberFormat($('input[name="add_item_discount_price"]').val(),2),
							item_total_amount: data.data.item_total_amount,
							item_remarks: $('input[name="add_item_remarks"]').val(),
							active_service_unit_detail_id: data.data.active_service_unit_detail_id,
							active_reprocessed_item_id: data.data.active_reprocessed_item_id,
							hidden_item_price: $('input[name="add_item_price"]').val(),
							hidden_item_discount: $('input[name="add_item_discount"]').val(),
							hidden_item_discount_price: $('input[name="add_item_discount_price"]').val(),
							hidden_item_bad_qty: $('input[name="add_item_bad_qty"]').val(),
							hidden_item_good_qty: $('input[name="add_item_good_qty"]').val()})
							+'</tr>');				

						$('input[name="add_item_good_qty"]').val('');
						$('input[name="add_item_bad_qty"]').val('');						
						$('#search_item').val('');
						$('#select_item').val("Please select an item...");
						$('input[name="add_item_price"]').val('');
						$('input[name="add_item_discount"]>option:selected').text('0');
						$('input[name="add_item_discount_price"]').val('');
						$('input[name="add_item_remarks"]').val('');
						$('input[name="add_item_good_qty"]').attr("placeholder", "0");
						$('input[name="add_item_bad_qty"]').attr("placeholder", "0");
						
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

		var service_unit_detail_id = $(this).parent().attr("data");
		var request_item_id = $(this).parent().attr("data-id");

		b.request({
			type: 'POST',
			url : '/spare_parts/service_unit/confirm_remove_item',
			data : {				
				'request_code' : $("#item-request-code-label").text(),
				'service_unit_detail_id' : service_unit_detail_id,				
			},
			on_success: function(data){

				if (data.status == "1")	{
				
					// show add form modal					
					removeItemModal = b.modal.new({
						title: data.data.title,
						width:450,
						disableClose: true,
						html: data.data.html,
						buttons: {
							'Cancel' : function() {
								removeItemModal.hide();								 							
							},
							'Proceed' : function() {
								if ($.trim($("#txt-remarks").val()) == "") {
									$("#error-reasonremarks").show();
									return;
								}
								
								$("#error-reasonremarks").hide();

								// ajax request
								b.request({
									url : '/spare_parts/service_unit/proceed_remove_item',
									data : {				
										'service_unit_id' : data.data.service_unit_id,
										'service_unit_detail_id' : service_unit_detail_id,	
										'remarks' : $("#txt-remarks").val(),
										'is_reprocess_item' : 1,
										'request_item_id' : request_item_id,
									},
									on_success : function(data) {
										
										if (data.status == "1")	{
											removeItemModal.hide();
											
											// show add form modal					
											proceedRemoveItemModal = b.modal.new({
												title: data.data.title,
												width:450,
												disableClose: true,
												html: data.data.html,
												buttons: {
													'Ok' : function() {
														//$("#" + service_unit_detail_id + "").parent().remove();
														$("#" + request_item_id + "").parent().remove();
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
							}									
						}
					});
					removeItemModal.show();
					
				} else {
					// show add form modal					
					var errorRemoveItemModal = b.modal.new({
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
		//return false;
	});

	$(".add-close").live('click',function(){

		window.location.href = '/spare_parts/service_unit/listing';
		return false;
	})

	$("#view-full-remarks").click(function(e){

		var request_code = '<?= $service_unit_details->request_code ?>';
		var segment_name = '<?= $department_module_details->segment_name ?>';	

		b.request({
			url : '/spare_parts/display_request_remarks',
			data : {				
				'remarks' : $(this).attr("data"),
				'request_code' : request_code,
				'segment_name' : segment_name,
			},
			on_success : function(data) {
				
				if (data.status == "1")	{

					// show add form modal					
					proceedApproveRequestModal = b.modal.new({
						title: data.data.title,
						width:600,
						//disableClose: true,
						html: data.data.html,						
					});
					proceedApproveRequestModal.show();

				} else {
					// show add form modal
					approveRequestModal.hide();					
					errorApproveRequestModal = b.modal.new({
						title: data.data.title,
						width:450,	
						html: data.data.html,
					});
					errorApproveRequestModal.show();	

				}
			}

		})	

	});

	$("#add_new_requester_remarks").click(function(e){

		var request_code = '<?= $service_unit_details->request_code ?>';
		var segment_name = '<?= $department_module_details->segment_name ?>';

		b.request({
			url : '/spare_parts/add_new_requester_remarks',
			data : {				
				'segment_name' : segment_name,
				'remarks' : $("#requester_remarks").val(),
				'request_code' : request_code,
			},
			on_success : function(data) {
				
				if (data.status == "1")	{

					// show add form modal					
					addNewRemarksModal = b.modal.new({
						title: data.data.title,
						width:600,
						//disableClose: true,
						html: data.data.html,						
					});
					addNewRemarksModal.show();

				} else {
					// show add form modal
					addNewRemarksModal.hide();					
					errorAddNewRemarksModal = b.modal.new({
						title: data.data.title,
						width:450,	
						html: data.data.html,
					});
					errorAddNewRemarksModal.show();	

				}
			}
		})	
	});
	
	$("#forward_to_warehouse").live('click',function(){

		var request_code = '<?= $service_unit_details->request_code ?>';
		var segment_name = '<?= $department_module_details->segment_name ?>';
		var request_id   = <?= $service_unit_details->service_unit_id ?>;
		var is_forwarded_val = $(this).attr("data");

		b.request({
			url : '/spare_parts/forward_to_warehouse',
			data : {								
				'request_code' : request_code,
				'segment_name' : segment_name,
				'is_forwarded' : is_forwarded_val,
			},
			on_success : function(data) {							

				if (data.status == "1")	{

					// show add form modal					
					proceedApproveRequestModal = b.modal.new({
						title: data.data.title,
						width:600,
						html: data.data.html,						
					});
					proceedApproveRequestModal.show();	

					if (data.data.is_forwarded == 1) {
						$("#ftw_caption").text("Cancel Forward To WH");						
						$("#test_table *").attr("disabled", "disabled").off('click');
						$(".remove_item").hide();
						document.getElementById("forward_to_warehouse").className = "btn btn-danger";
						$("#forward_to_warehouse").hide();
					} else {

						$("#ftw_caption").text("Forward To WH");
						$("#test_table *").removeAttr("disabled", "disabled").off('click');
						document.getElementById("forward_to_warehouse").className = "btn btn-primary";
						$(".remove_item").show();
						$("#forward_to_warehouse").show();
					}		

					$("#forward_to_warehouse").attr('data', data.data.is_forwarded);
				
				} else {
					// show add form modal
					approveRequestModal.hide();					
					errorApproveRequestModal = b.modal.new({
						title: data.data.title,
						width:450,	
						html: data.data.html,
					});
					errorApproveRequestModal.show();	

				}
			}
		})
	});

	$("#search_recipient").focus(function() {
		assignRecipient();		
	});
	
	var assignRecipient = function() {

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
					//$.each(data.data.keys, function(index, key_item) {
					//});
					
					// apply click event on select buttons
					$('#assign-requester-listing .btn-select-member').click(function(e) {
						var id_number = $(this).data('idnumber');
						var full_name = $(this).data('fullname');
						var department_name = $(this).data('departmentname');
						var position_name = $(this).data('positionname');
						
						var fullDetails = full_name + ' / ' + department_name + ' / ' + position_name;
						
						$("#search_recipient").val(id_number);
						$("#add_recipient_name").val(fullDetails);

						assignRequesterModal.hide();
						
					});
					
				} else {
					$('#assign-requester-listing').html('<tr><td colspan="3">'+data.msg+'</td></tr>');
				}
				
			});
			
		});
		
	};
	
	var searchPersonnel = function(search_key, cb, with_overlay) {
		with_overlay = typeof(with_overlay) == 'undefined' ? true : with_overlay;
		b.request({
			'with_overlay' : with_overlay,
			url: '/spare_parts/get_requester',
			data: {'search_key' : search_key},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};
	
	
</script>
