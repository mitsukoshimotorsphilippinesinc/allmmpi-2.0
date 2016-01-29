

<script id='search-item-inventory-template' type='text/template'>	
		
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
				<th style="width: 70px;">Image</th>
				<th style="width: 80px;">SKU</th>
				<th style="width: 280px;">Description</th>
				<th style="width: 280px;">Brand/Model</th>
				<th style="width: 100px;">SRP</th>				
				<th style="width: 80px;">&nbsp;</th>
			</tr>
		</thead>
	</table>
	<div id='item-list'>
		<table class="table table-striped table-condensed item-inventory-list">
			<tbody id='item-inventory-listing'>
			</tbody>
		</table>
	</div>
</script>

<script id='item-inventory-list-template' type='text/template'>
	<%	
		$.each(items, function(index, item) { 
	%>
			<tr id='item_<%= item.item_id %>'>
				<td style="width: 70px;"><a target="_blank" href="#"><img id="" src="<%= item.upload_url %>/<%= item.image_filename %>" alt="" style="width:50px; height:50px;"></img></a></td>
				<td style="width: 80px;"><%= item.sku %></td>
				<td style="width: 280px;"><%= item.description %></td>
				<td style="width: 280px;"><%= item.brand_model %></td>
				<td style="width: 100px;"><%= item.srp %></td>				
				<td style="width: 80px;"><button class='btn btn-small btn-info btn-select-item' data-id='<%= item.item_id %>'  data-description='<%= item.description %>' data-srp='<%= item.srp%>' data-sku='<%= item.sku%>' data-spid='<%= item.spare_part_id%>'>Select</button></td>
			</tr>
	<%	}); %>
</script>