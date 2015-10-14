<?php
	//var_dump($counter_detail);
?>
<div class='alert alert-danger'><h2>Walk-In Sales<a id = "btn suspend" class = 'btn' style = "float:right;" href = "add_new_tr">Suspend</a> <a id = "btn suspend" class = 'btn' style = "float:right; margin-right:5px;" href = "add_new_tr">Finalize</a></div>

<span>C.O. No.</span>
<input value = <?=$order_code?> id = "co_number" name = "co_number" placeholder="C.O. Number"  TYPE = "TEXT">
<button id="add_item" name="add_item" class='btn' style="margin-top:-10px;float:right;"><span>Add Item</span></button>
<table id = "parts_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th style = "width:150px;">SKU</th>
			<th style = "width:500px;">Description</th>
			<th style = "width:70px;">Unit</th>
			<th style = "width:100px;">SRP</th>
			<th style = "width:100px;">QTY</th>
			<th style = "width:80px;">Discount</th>
			<th style = "width:200px;">Total Amount</th>
			<th style = "width:200px;">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
			If (empty($counter_detail)){
				echo "<tr><td colspan = 4> No Records Found... </td></tr>";
			}else{
				foreach ($counter_detail as $cd) {
				
				//	$counter_order_detail_id = {$cd->counter_order_detail_id};

					$where = "item_id = '{$cd->item_id}'";
					$spare_parts_inv = $this->spare_parts_model->get_item($where);

					$sku = $spare_parts_inv[0]->sku;

					$where = "sku = '{$sku}'";
					$spare_parts_detail = $this->spare_parts_model->get_spare_part($where);

					$description = $spare_parts_detail[0]->description;
					$unit = $spare_parts_detail[0]->unit;
					$srp = $cd->srp;
					$quantity = $cd->good_quantity;
					$discount_amount = $cd->discount_amount;
					$total_amount = $cd->total_amount;

       				echo "<tr>			
					<td style = 'text-align:center;'>{$sku}</td>
					<td>{$description}</td>
					<td>{$unit}</td>
					<td style = 'text-align:right;'>{$srp}</td>
					<td style = 'text-align:right;'>{$quantity}</td>
					<td style = 'text-align:right;'>{$discount_amount}</td>
					<td style = 'text-align:right;'>{$total_amount}</td>
					<td><a type = 'btn' class = 'btn delete_item' data = '{$cd->counter_order_detail_id}'>Delete</a></td>
					</tr>";
       			};
			}
		?>
	</tbody>
<table>

<SCRIPT TYPE = "text/javascript">
	
	$('#add_item').click(function(){

		b.request({
			url: "/spare_parts/walk_in/add_new_item",
			data:{
				"order_code": $('#co_number').val(),
			},
			on_success: function(data){
				if (data.status == "1") {
					hideLoading();
					proceedAddItemModal = b.modal.new({
					title: 'Add Item',
					width:1000,
					disableClose: false,
					html: data.data.html,
					buttons:{		
				}
			})
			proceedAddItemModal.show();
		}
			}
		});
	});

</SCRIPT>