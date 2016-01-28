<?php

	//echo css('inventory.css');
	echo js('apps/spareparts.js');

?>
<style type="text/css">
	.inventory-orders .good_qty {width:80px;}
	.inventory-orders .bad_qty {width:80px;}
	.inventory-orders .unit {width:60px;}
	.inventory-orders .item {width:200px;}
	.inventory-orders .price {width:80px;}
	.inventory-orders .remark {width:100px;}

	.inventory-order-items .qty {width:112px;}
	.inventory-order-items .unit {width:112px;}
	.inventory-order-items .item {width:224px;}
	.inventory-order-items .price {width:112px;}
	.inventory-order-items .remark {width:284px;}

	.inventory-order-items input.qty  {width:102px;}
	.inventory-order-items select.unit {width:112px;}
	.inventory-order-items select.item {width:224px;}
	.inventory-order-items input.price {width:102px;}
	.inventory-order-items input.remark {width:274px;}

</style>
<?php

if (is_object($free_of_charge_details))
{
	$isAdd = false;
	$titlePrefix = "Edit&nbsp;";
	$submitURL = "/spare_parts/" . $department_module_details->segment_name . "/edit/" . $free_of_charge_details->free_of_charge_id;
	$_id = $free_of_charge_details->free_of_charge_id;
	$show_approval = false;
}
else
{
	$isAdd = true;
	$titlePrefix = "Create New&nbsp;";
	$submitURL = "/spare_parts/" . $department_module_details->segment_name . "/add";
	$_id = 0;
	$show_approval = true;
}
?>


<div class='alert alert-info'><h3><?= $titlePrefix ?><?= $department_module_details->module_name ?> <a class='btn return-btn add-close' style='float:right;margin-right:-30px;' >Back to Request List</a></h3></div>

<form id="submit_form" action='<?= $submitURL ?>' method='post' class='form-inline'>
	<fieldset >
		<div class="row-fluid">						
			<div class='alert alert-success'><h4>Requester Details
			<?php if(!$isAdd && !empty($free_of_charge_details)):?>
				<label class="label label-important request-code-label" id="requester-request-code-label" style="float:right;font-size:16px;"><?= $free_of_charge_details->request_code ?></label>
			<?php elseif($isAdd): ?>
				<label class="label label-important request-code-label" id="requester-request-code-label" style="float:right;font-size:16px;"><?= $department_module_details->module_code ?></label>
			<?php endif; ?>
			</h4></div>
		</div>
		
		<div class="row-fluid">
			<div class="span6">
				<label><strong>Requester:</strong></label>
				<br/>
				<?php if(!$isAdd): ?>
				<small class="customer-assign-btn">
					<input id="search_requester" type="text" placeholder="Search Requester" readonly="readonly" value="<?= $free_of_charge_details->id_number ?>">
				</small>
				<?php elseif($isAdd): ?>
					<input id="search_requester" type="text" placeholder="Search Requester" readonly="readonly">
					<a id="clear_requester" class="btn btn-small btn-primary">Clear</a>
				<?php endif; ?>

				<div class="control-group">
					<label class="control-label" for="requester_details"><strong>Details</strong ></label>					
					<div class="controls">
						<?php if(!$isAdd): 
						$is_employed = ($requester_details->is_employed == 1) ? 'Yes' : 'No';
						$email_address = ($requester_details->company_email_address == NULL) ? $requester_details->personal_email_address : $requester_details->company_email_address;
						$contact_number = ($requester_details->mobile_number == NULL) ? $requester_details->phone_number : $requester_details->mobile_number;

						$department_name = "N/A";
						if (!empty($department_details)) {
							$department_name = $department_details->department_name;
						}

						$details_content = "NAME: {$requester_details->complete_name}\nID NUMBER: {$free_of_charge_details->id_number}\nDEPARTMENT: {$department_name}\nPOSITION: {$position_details->position_name}\nIS EMPLOYED: {$is_employed}\nEMAIL: {$email_address}\nCONTACT NUMBER: {$contact_number}\n";

						?>
						<textarea class='span10' rows="7" placeholder="" name="requester_details" id="requester_details" readonly><?= $details_content ?>
						</textarea>
						<?php elseif($isAdd): ?>
						<textarea class='span10' rows="7" placeholder="" name="requester_details" id="requester_details" readonly><?= set_value('requester_details') ?></textarea>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span11">
						<div class="control-group <?= $this->form_validation->error_class('remarks') ?>">
							<label class="control-label" for="remarks"><strong>Remarks</strong></label>
							<div class="controls">
								<?php if(!$isAdd): ?>
								<textarea class='span8' rows="4" placeholder="" name="current_remarks" readonly><?= $free_of_charge_details->remarks ?></textarea>
								<br/><br/>
								<label class="control-label" for="remarks"><strong>Add New Remarks</strong></label>
								<input class="span12" id="remarks" type="text" placeholder="New Remarks">
								<?php elseif($isAdd): ?>
								<textarea class='span8' rows="4" placeholder="" name="remarks" id="remarks" maxlength="255"><?= set_value('remarks',@$po->remarks) ?></textarea>
								<p class="help-block"><?= $this->form_validation->error('remarks'); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				
			</div>
			<div class="span5">
				<label><strong>From Warehouse:</strong></label>
				<br/>
				<?php if(!$isAdd):

					$warehouse_options = array();
					if ($free_of_charge_details->warehouse_id == 0)
						$warehouse_options = array('0' => 'Select a Warehouse...');
					
					foreach ($warehouse_details as $wd) {
					 	$warehouse_options[$wd->warehouse_id] = $wd->warehouse_name;
					}

				?>
				<?= form_dropdown('add_item_warehouse',$warehouse_options, set_value('add_item_warehouse',$free_of_charge_details->warehouse_id),'id="add_item_warehouse"') ?>
				<?php elseif($isAdd): 
					$warehouse_options = array();
					$warehouse_options = array('0' => 'Select a Warehouse...');
					foreach ($warehouse_details as $wd) {
					 	$warehouse_options[$wd->warehouse_id] = $wd->warehouse_name;
					}
				?>
					
				<?= form_dropdown('add_item_warehouse',$warehouse_options, NULL,'id="add_item_warehouse"') ?>
				<?php endif; ?>

				<br/>
				<br/>
				<label><strong>Motorcycle Brand/Model:</strong></label>
				<br/>
				<?php if(!$isAdd):

					$brand_model_options = array();
					if ($free_of_charge_details->motorcycle_brand_model_id == 0)
						$brand_model_options = array('0' => 'Select a Brand/Model...');

					foreach ($motorcycle_brandmodel_details as $mbd) {
						$concat_brandmodel =  $mbd->brand_name . ' ' . $mbd->model_name;
					 	$brand_model_options[$mbd->motorcycle_brand_model_id] = $concat_brandmodel;
					}
					
					echo form_dropdown('add_item_brandmodel',$brand_model_options, set_value('add_item_brandmodel',$free_of_charge_details->motorcycle_brand_model_id),'id="add_item_brandmodel"');
				?>
				<?php elseif($isAdd):
					$brand_model_options = array();
					$brand_model_options = array('0' => 'Select a Brand/Model...');
					foreach ($motorcycle_brandmodel_details as $mbd) {
						$concat_brandmodel =  $mbd->brand_name . ' ' . $mbd->model_name;
					 	$brand_model_options[$mbd->motorcycle_brand_model_id] = $concat_brandmodel;
					}
				?>
				<?= form_dropdown('add_item_brandmodel',$brand_model_options, NULL,'id="add_item_brandmodel"') ?>
				<?php endif; ?>
				<br/>
				<br/>
				<label><strong>Engine:</strong></label>
				<br/>

				<?php if(!$isAdd): ?>
				<input name="engine" id="engine" class="" placeholder="Enter Engine Number..." value="<?= $free_of_charge_details->engine ?>" />
				<?php elseif($isAdd): ?>
				<input name="engine" id="engine" class="" placeholder="Enter Engine Number..." />
				<?php endif; ?>

				<br/>
				<br/>
				<label><strong>Chassis:</strong></label>
				<br/>

				<?php if(!$isAdd): ?>
				<input name="chassis" id="chassis" class="" placeholder="Enter Chassis Number..." value="<?= $free_of_charge_details->chassis ?>" />
				<?php elseif($isAdd): ?>
				<input name="chassis" id="chassis" class="" placeholder="Enter Chassis Number..." />
				<?php endif; ?>

			</div>	
		</div>
		
		<br/>
		<div class='alert alert-success'><h4>Items
			<?php if(!$isAdd && !empty($free_of_charge_details)):?>
				<label class="label label-important request-code-label" id="item-request-code-label" style="float:right;font-size:16px;"><?= $free_of_charge_details->request_code?></label>
			<?php elseif($isAdd): ?>
				<label class="label label-important request-code-label" id="item-request-code-label" style="float:right;font-size:16px;"><?= $department_module_details->module_code ?></label>
			<?php endif; ?>
		</h4></div>	

		
		<div class="row-fluid span8">
			<table class="table inventory-orders">
				<thead id="items_header">
					<tr>
						<th class="item">Item</th>
						<th class="unit">Unit</th>
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
						<td>
							<?php
							$unit_options = array('1' => 'Pcs');

							echo form_dropdown('add_item_unit',$unit_options,NULL,'class="unit" disabled="disabled"');
							?>
						</td>
						<td><?= form_input('add_item_good_qty',NULL,'class="good_qty" placeholder=" Good Qty"');?></td>
						<td><?= form_input('add_item_bad_qty',NULL,'class="bad_qty" placeholder="Bad Qty"');?></td>
						<td>
							<?= form_input('add_item_price',NULL,'id="add_item_price" class="price" placeholder="Unit Price" readonly');?>
						</td>
						<td>
							<?php
							$discount_options = array();
							for ($i=0; $i<=100; $i++) {
								array_push($discount_options, $i);	
							}
							
							echo form_dropdown('add_item_discount',$discount_options, NULL,'id="add_item_discount" class="unit"');
							?>
						</td>
						<td>
							<?= form_input('add_item_discount_price',NULL,'class="price" placeholder="Disc. Price"');?>
						</td>							
						<td>
							<?= form_input('add_item_remarks',NULL,'class="remark" placeholder="Remarks"');?>
						</td>
						<td>
							<a id="add_wr_item" class="btn btn-primary">Add to List</a>
						</td>
						<td></td>
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
						<th class="item">Item</th>
						<th class="unit">Unit</th>
						<th class="qty">Good Qty</th>
						<th class="qty">Bad Qty</th>
						<th class="price">SRP</th>
						<th class="discount">Disc.(%)</th>
						<th class="discount_price">Disc. Price</th>
						<th class="remark">Remarks</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody id="wr_items">
					<?php
					$temp = NULL;
			
					if (!$isAdd) {
						//$temp = array($free_of_charge_detail_details);
						$temp = json_decode($json_items, true);
					}

					//var_dump($temp);

					for($i=0;$i<count($temp);$i++):?>
						<!--?php if(set_value('item_qty['.$i.']') != '' || !$isAdd):?-->
					<tr class="item_row">
								<?php
								
								$itemInfo = $this->spare_parts_model->get_item_view_by_id($temp[$i]['item_id']);
								
								?>
						<td class="item"><?=set_value('temp_item['.$i.']',$itemInfo->description)?></td>
						<td class="item"><?=set_value('temp_item['.$i.']',$itemInfo->unit)?></td>
						<td class="qty"><?=number_format(set_value('item_good_qty['.$i.']',$temp[$i]['good_quantity']))?></td>
						<td class="qty"><?=number_format(set_value('item_bad_qty['.$i.']',$temp[$i]['bad_quantity']))?></td>
						<td class="price"><?=number_format(set_value('item_price['.$i.']',$temp[$i]['srp']),2)?></td>
						<td class="discount"><?=number_format(set_value('discount['.$i.']',$temp[$i]['discount']))?></td>
						<td class="discount_price"><?=number_format(set_value('discount_price['.$i.']',$temp[$i]['discount_amount']),2)?></td>
						<td class="remark"><?=set_value('item_remarks['.$i.']',$temp[$i]['remarks'])?></td>
						<td id="<?= $temp[$i]['free_of_charge_detail_id'] ?>" data="<?= $temp[$i]['free_of_charge_detail_id'] ?>"><a class="btn btn-danger rmv_wr_item"><i class="icon-white icon-minus"></i></a></td>
						<td class="hidden_values">
							<input type="hidden" name="item_good_qty[]" id="item_good_qty[]" value="<?=set_value('item_good_qty['.$i.']',$temp[$i]['good_quantity'])?>">
							<input type="hidden" name="item_bad_qty[]" id="item_bad_qty[]" value="<?=set_value('item_bad_qty['.$i.']',$temp[$i]['bad_quantity'])?>">
							<input type="hidden" name="unit_name[]" id="unit_name[]" value="<?=set_value('unit_name['.$i.']',$itemInfo->description)?>">
							<input type="hidden" name="item_unit[]" id="item_unit[]" value="<?=set_value('item_unit['.$i.']',$itemInfo->unit)?>">
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

		
		
		
		<hr/>
		<div class="controls" align="right">
			<!--a id='submit_order' class="btn btn-primary">Save Warehouse Request</a-->
			<a class="btn return-btn add-close">Close</a>
		</div>
		
	</fieldset>
</form>
<?php

	$this->load->view('template_search_requester');
	$this->load->view('template_search_item');


?>
<script type="text/javascript">

	
	$(document).ready(function(){


	});

	var saving_timeout;
	var items_array = <?= json_encode($items); ?>;

	var item_entry_row = _.template('\
		<td class="item"><%= item_name %></td>\n\
		<td class="unit"><%= item_unit_name %></td>\n\
		<td class="qty"><%= item_good_qty %></td>\n\
		<td class="qty"><%= item_bad_qty %></td>\n\
		<td class="price"><%= item_price %></td>\n\
		<td class="qty"><%= item_discount %></td>\n\
		<td class="qty"><%= item_discount_price %></td>\n\
		<td class="remark"><%= item_remarks %></td>\n\
		<td id="<%= active_free_of_charge_detail_id %>" data="<%= active_free_of_charge_detail_id %>"><a class="btn btn-danger rmv_wr_item"><i class="icon-white icon-minus"></i></a></td>\n\
		<td class="hidden_values">\n\
			<input type="hidden" name="item_good_qty[]" id="item_good_qty[]" value="<%= hidden_item_good_qty %>">\n\
			<input type="hidden" name="item_bad_qty[]" id="item_bad_qty[]" value="<%= hidden_item_bad_qty %>">\n\
			<input type="hidden" name="unit_name[]" id="unit_name[]" value="<%= item_unit_name %>">\n\
			<input type="hidden" name="item_unit[]" id="item_unit[]" value="<%= item_unit_id%>">\n\
			<input type="hidden" name="item_name[]" id="item_name[]" value="<%= item_id%>">\n\
			<input type="hidden" name="temp_item[]" id="temp_item[]" value="<%= item_name %>">\n\
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
						var id_number = $(this).data('id_number');
						
						$("#id_number").val(id_number);
						
						var details = "NAME: " + $(this).data('fullname') + "\nID NUMBER: " + $(this).data('idnumber') + "\nCOMPANY: " + $(this).data('idnumber') + "\nDEPARTMENT: " + $(this).data('idnumber');
						//$("#member_details").append(details);
						$("#requester_details").text(details);
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

	$(document).on("click",'#search_item',function(e) {
	//$("#search_item").focus(function() {
		e.preventDefault();
		//if(_.isObject(document.search_item_modal)) return;
		search_item();

	});

	document.search_item_modal = null;

	var search_item = function() {
		document.search_item_modal = b.modal.create({
			title: "Search Item",
			width: 900,
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
			
			if (search_key.length == 0) 
			{
				return;
			}
			
			b.request({
				url: "/spare_parts/search_item",
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

		if ((($('input[name="add_item_good_qty"]').val() == '') && ($('input[name="add_item_bad_qty"]').val() == '')) ||
			$('select[name="add_item_unit"]').val() == '' ||
			$('input[name="add_item_name"]').val() == '' ||
			$('input[name="add_item_price"]').val() == '')
		{
			var input_errors = "";

			if (($('input[name="add_item_good_qty"]').val() == '') && ($('input[name="add_item_bad_qty"]').val() == ''))
			{
				input_errors += "The Quantity fields are required. ";
			}
			else if(!(_.isNumber($('input[name="add_item_good_qty"]').val() * 1)) || _.isNaN($('input[name="add_item_good_qty"]').val() * 1))
			{
				input_errors += "The Good Quantity field must contain an integer. "
			}
			else if(!(_.isNumber($('input[name="add_item_bad_qty"]').val() * 1)) || _.isNaN($('input[name="add_item_bad_qty"]').val() * 1))
			{
				input_errors += "The Bad Quantity field must contain an integer. "
			}


			if($('select[name="add_item_unit"]').val() == '') input_errors += "The Unit field is required. ";
			if($('input[name="add_item_name"]').val() == '' || $('input[name="add_item_name"]').val() == 'new') input_errors += "The Item field is required. ";
			if($('input[name="add_item_price"]').val() == '')
			{
				input_errors += "The Unit Price field is required. ";
			}
			else if(!(_.isNumber($('input[name="add_item_price"]').val() * 1)) || _.isNaN($('input[name="add_item_price"]').val() * 1))
			{
				input_errors += "The Unit Price field must contain a number. "
			}

			$('#input_errors').html('<p>'+input_errors+'</p>');
		}
		else
		{

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

			var input_errors;
			if((!(_.isNumber(good_qty * 1)) || _.isNaN(good_qty * 1)) && (!(_.isNumber(bad_qty * 1)) || _.isNaN(bad_qty * 1))) 
			{
				input_errors = "The Quantity fields must contain an integer. ";
				var item_error_modal = b.modal.create({
					title: "Error :: Item Request",
					width: 450,
					html: "<p>There was an error in your request.</p><p>"+input_errors+"</p>"
				});
				item_error_modal.show();
				return;
			}
			
			if ((good_qty == '0') && (bad_qty == '0')) 
			{
				input_errors = "One of the Quantity fields must be greater than zero. ";
				var item_error_modal = b.modal.create({
					title: "Error :: Item Request",
					width: 450,
					html: "<p>There was an error in your request.</p><p>"+input_errors+"</p>"
				});
				item_error_modal.show();
				return;				
			}

			$('input[name="add_item_good_qty"]').val(good_qty);
			$('input[name="add_item_bad_qty"]').val(bad_qty);

			// ajax request
			b.request({
				url : '/spare_parts/free_of_charge/create_request',
				data : {				
					'request_code' : $("#requester-request-code-label").text(),
					'item_id' : $('input[name="add_item_name"]').val(),
					'srp' : $('input[name="add_item_price"]').val(),
					'discount' : $('select[name="add_item_discount"]>option:selected').text(),
					'discount_amount' : $('input[name="add_item_discount_price"]').val(),
					'good_quantity' : $('input[name="add_item_good_qty"]').val(),
					'bad_quantity' : $('input[name="add_item_bad_qty"]').val(),
					'remarks' : $('input[name="add_item_remarks"]').val(),
					'engine' : $("#engine").val(),
					'chassis' : $("#chassis").val(),
					'warehouse_id' : $("#add_item_warehouse").val(),
					'brandmodel' : $("#add_item_brandmodel").val(),
					'requester_id' : $("#search_requester").val(),
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

						$("#wr_items").append('<tr class="item_row">'+
							item_entry_row({item_id: $('input[name="add_item_name"]').val(),
							item_name: item_name,
							item_unit_name: $('select[name="add_item_unit"]>option:selected').text(),
							item_unit_id: $('select[name="add_item_unit"]').val(),
							item_price: numberFormat($('input[name="add_item_price"]').val(),2),
							item_good_qty: numberFormat($('input[name="add_item_good_qty"]').val(),2),
							item_bad_qty: numberFormat($('input[name="add_item_bad_qty"]').val(),2),
							item_discount: numberFormat($('select[name="add_item_discount"]>option:selected').text()) + '%',
							item_discount_price: numberFormat($('input[name="add_item_discount_price"]').val(),2),
							item_remarks: $('input[name="add_item_remarks"]').val(),
							active_free_of_charge_detail_id: data.data.active_free_of_charge_detail_id,
							hidden_item_price: $('input[name="add_item_price"]').val(),
							hidden_item_discount: $('input[name="add_item_discount"]').val(),
							hidden_item_discount_price: $('input[name="add_item_discount_price"]').val(),
							hidden_item_bad_qty: $('input[name="add_item_bad_qty"]').val(),
							hidden_item_good_qty: $('input[name="add_item_good_qty"]').val()})
							+'</tr>');
						

						$('input[name="add_item_good_qty"]').val('');
						$('input[name="add_item_bad_qty"]').val('');
						$('select[name="add_item_unit"]').val('');
						$('#search_item').val('');
						$('input[name="add_item_name"]').val("")
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
							//disableClose: true,
							html: data.data.html,
						});
						errorCreateRequestModal.show();	
						
					}
				}

			})
		}
	});
	

	$(".rmv_wr_item").live('click',function(){

		var free_of_charge_detail_id = $(this).parent().attr("data");
		
		b.request({
			url : '/spare_parts/free_of_charge/confirm_remove_item',
			data : {				
				'request_code' : $("#requester-request-code-label").text(),
				'free_of_charge_detail_id' : free_of_charge_detail_id,	
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
									url : '/spare_parts/free_of_charge/proceed_remove_item',
									data : {				
										'free_of_charge_id' : data.data.free_of_charge_id,
										'free_of_charge_detail_id' : free_of_charge_detail_id,	
										'remarks' : $("#txt-remarks").val(),
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
														$("#" + free_of_charge_detail_id + "").parent().remove();
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
		return false;
	});

	$(".add-close").live('click',function(){
		window.location.href = '/spare_parts/free_of_charge/listing';
		return false;
	})
	
</script>
