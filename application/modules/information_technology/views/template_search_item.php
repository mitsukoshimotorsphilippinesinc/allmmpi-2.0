

<script id='search-item-template' type='text/template'>	
		
	<h3>Type your search key below:</h3>
	<br/>
	<form id='frm_item_search' class="form-search control-group" onsubmit='return false;'>
		<input type="text" id='txt_item_search_key' name='txt_item_search_key' class="input-large search-query assign-input-search" placeholder='Name / Description' value='' style="">
		<button id='btn_item_search' class="btn"><i class="icon-search"></i>&nbsp;Search</button>
	</form>
	<table class="table table-striped table-condensed assign-customer-list">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
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
				<td style="width: 50px;"><%= item.name %></td>
				<td style=""><%= item.description %></td>				
				<td style="width: 80px;"><button class='btn btn-small btn-info btn-select-item' data-id='<%= item.item_id %>'  data-name='<%= item.name %>' data-description='<%= item.description %>'>Select</button></td>
			</tr>
	<%	}); %>
</script>