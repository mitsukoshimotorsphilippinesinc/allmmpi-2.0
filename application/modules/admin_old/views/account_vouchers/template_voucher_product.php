<script id='voucher_products_template' type='text/template'>
	<form id='form_raffle_product' onsubmit='return false;'>
		<div>
			<label>Voucher Type</label>
			<select id='voucher_type_id' name='voucher_type_id'>
				<%	$.each(voucher_types, function(index, item) { %>
					<option value=<%=item.voucher_type_id%> <%=voucher_type_id == item.voucher_type_id ? 'selected':'' %>><%=item.name%></option>
				<%	}); %>
			</select>
		</div>
		<div>
			Group Name: <input type='text' class='input' id='voucher_product_name' value='<%=voucher_product_name%>' />
		</div>
		<div>
			Qty: <input type='text' class='input-mini' placeholder='0' id='voucher_product_qty' value='' />
			<select id='select_voucher_product_id' name='select_voucher_product_id'>
				<% $.each(products, function(index, item) { %>
					<option value=<%=item.product_id%>><%=item.product_name%></option>
				<% }) %>
			</select>
			<a style='margin-bottom:5px;' class='btn btn_add_voucher_product btn-primary' id='btn_add_voucher_product'>Add Product</a>
		</div>
		<div>
			<table class='table table-striped table-condensed table-bordered'>
				<thead>
					<th>Product Name</th>
					<th>Quantity</th>
					<th></th>
				</thead>
				<tbody id='voucher_products_current_list'>
					<% $.each(json_items, function(index, item) { %>
						<tr data-product_id=<%= item.product_id %>><td><%= item.product %></td><td><%= item.qty %></td><td><a class='btn btn-small btn-danger btn_remove_voucher_product'><i class='icon-remove icon-white' title='remove' ></td></tr>
					<% }) %>
				</tbody>
			</table>
		</div>
		<div>
			<p id='qty_error' class='help-block' style='display:none';>Quantity cannot be 0 or empty.</p>
			<p id='product_id_error' class='help-block' style='display:none;'>No product selected.</p>
			<p id='existing_product_error' class='help-block' style='display:none'>Product already selected.</p>
		</div>
	</form>
</script>

<script type='text/javascript'>
	$(function(){		
		$('body').on('click', '#btn_add_voucher_product', function(){
			var product_id = $("#select_voucher_product_id").val();
			var qty = $('#voucher_product_qty').val();
			
			$('#existing_product_error').hide();
			$('#product_id_error').hide();
			$('#qty_error').hide();
			var with_error = 0;
			
			if(product_id == "" || product_id == 0)
			{
				$('#product_id_error').show();
				with_error = 1;
			}
			
			if(qty == "" || qty == 0) 
			{
				$('#qty_error').show();
				with_error = 1;
			}
			
			if(voucher_products_listing[product_id] != undefined)
			{
				$('#existing_product_error').show();
				with_error = 1;
			}
			
			if(with_error > 0) return;
			
			var product_name = $("#select_voucher_product_id option:selected").text();
			
			var table_row = "<tr data-product_id='"+product_id+"'><td>"+product_name+"</td><td>"+qty+"</td><td><a class='btn btn-small btn-danger btn_remove_voucher_product'><i class='icon-remove icon-white' title='remove' ></td></tr>";
			
			$('#voucher_products_current_list').append(table_row);
			var product_array = {};
			product_array["product_id"] = product_id;
			product_array["qty"] = qty;
			voucher_products_listing[product_id] = product_array;
			
		});
		
		$('body').on('click', '.btn_remove_voucher_product', function(){
			var row = $(this).parent().parent();
			var product_id = row.data('product_id');
			row.remove();
			delete voucher_products_listing[product_id];
			
		});
	});
</script>
