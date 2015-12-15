<script id='search-branch-template' type='text/template'>
	<h3>Search Branch:</h3>
	<form id='frm_assign_search' class="form-search control-group" onsubmit='return false;'>
		<input type="text" id='txt_branch_search_key' name='txt_branch_search_key' class="input-xlarge search-query assign-input-search" placeholder='Branch Name' value='' style="">
		<button id='btn_branch_search' class="btn"><i class="icon-search"></i>&nbsp;Search</button>
		<p id='txt_branch_search_key_help' class="help-block" style='display:none;'></p>
	</form>
	<div class="alert">
	  <strong>NOTE:</strong> Will only display first 50 results.
	</div>
	<table class="table table-striped table-condensed assign-branch-list">
		<thead>
			<tr>				
				<th style="" class='id_number'>Branch ID</th>
				<th style="">Name</th>
				<th style="">Company</th>				
				<th style="">Is Active</th>
				<th class='action'>&nbsp;</th>
			</tr>
		</thead>
	</table>
	<div id='assign-branch-list'>
		<table class="table table-striped table-condensed assign-branch-list">
			<tbody id='assign-branch-listing'>
			</tbody>
		</table>
	</div>
</script>

<script id='assign-branch-item-template' type='text/template'>

	<%	console.log(branches);
		$.each(branches, function(index, item) { 
		
	%>
			<tr id='bra_<%= item.branch_id %>'>				
				<td class='branch_id' style="width:80px;"><%= item.branch_id %></td>				
				<td style=""><%= item.branch_name  %></td>
				<td style=""><%= item.company_name  %></td>
				<td style=""><%= item.is_active  %></td>
				<td class='action'><button class='btn btn-small btn-info btn-select-branch' data-id='<%= item.branch_id %>' data-branchname='<%= item.branch_name%>'>Select</button></td>
			</tr>
	<%	}); %>
</script>