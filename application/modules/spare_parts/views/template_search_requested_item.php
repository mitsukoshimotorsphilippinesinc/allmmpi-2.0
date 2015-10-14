

<script id='select-item-template' type='text/template'>	
		
	<h3>Item:</h3>
	<form id='frm_item_search' class="form-search control-group" onsubmit='return false;'>
		<input type="text" id='txt_item_search_key' name='txt_item_search_key' class="input-xlarge search-query assign-input-search" placeholder='Description / SKU' value='' style="width:400px;">
		<button id='btn_item_search' class="btn"><i class="icon-search"></i>&nbsp;Search</button>
	</form>
	<div class="alert">
	  <strong>NOTE:</strong> Will only display first 50 results.
	</div>
	<table class="table table-striped table-condensed assign-customer-list">
		<thead>
			<tr>
				<th style="width: 5em;">SKU</th>
				<th style="width: 15em;">[Brand/Model] Description</th>
				<th style="width: 5em;">SRP</th>
				<th style="width: 7em;">WH</th>
				<th style="width: 5em;">Rack Location</th>
				<th style="width: 5em;">Good Qty</th>
				<th style="width: 5em;">Bad Qty</th>
				<th style="width: 5em;">Discount</th>
				<th style="width: 5em;">Disc. Price</th>				
				<th style="width: 80px;">&nbsp;</th>
			</tr>
		</thead>
	</table>
	<div id='item-list'>
		<table class="table table-striped table-condensed item-list">
			<tbody id='item-listing'>
			</tbody>
		</table>
	</div>
</script>

<script id='item-list-template' type='text/template'>
	<%	
		$.each(items, function(index, item) { 
	%>
			<tr id='item_<%= item.item_id %>'>
				<td style="width: 5em;"><%= item.sku %></td>
				<td style="width: 15em;"><%= item.full_description %></td>
				<td style="width: 5em;text-align:right;"><%= item.srp %></td>
				<td style="width: 7em;"><%= item.warehouse_name %></td>
				<td style="width: 5em;"><%= item.rack_location %></td>
				<td style="width: 5em;text-align:right;"><%= item.remaining_good_quantity %></td>
				<td style="width: 5em;text-align:right;"><%= item.remaining_bad_quantity %></td>
				<td style="width: 5em;text-align:right;"><%= item.discount %></td>
				<td style="width: 5em;text-align:right;"><%= item.discount_amount %></td>				
				<td style="width: 80px;"><button class='btn btn-small btn-info btn-select-item' data-id='<%= item.item_id %>'  data-description='<%= item.description %>' data-srp='<%= item.srp%>' data-good_quantity='<%= item.good_quantity%>' data-bad_quantity='<%= item.bad_quantity%>' data-remaining_good_quantity='<%= item.remaining_good_quantity%>' data-remaining_bad_quantity='<%= item.remaining_bad_quantity%>' data-request_detail_id='<%= item.request_detail_id%>'>Select</button></td>
			</tr>
	<%	}); %>
</script>