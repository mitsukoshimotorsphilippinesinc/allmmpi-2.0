<script id='search-requester-template' type='text/template'>
	<h3>Dealer:</h3>
	<form id='frm_assign_search' class="form-search control-group" onsubmit='return false;'>
		<input type="text" id='txt_requester_search_key' name='txt_requester_search_key' class="input-xlarge search-query assign-input-search" placeholder='First Name / Last Name / Dealer Code' value='' style="width: 545px;">
		<button id='btn_requester_search' class="btn"><i class="icon-search"></i>&nbsp;Search</button>
		<p id='txt_requester_search_key_help' class="help-block" style='display:none;'></p>
	</form>
	<div class="alert">
	  <strong>NOTE:</strong> Will only display first 50 results.
	</div>
	<table class="table table-striped table-condensed assign-requester-list">
		<thead>
			<tr>
				<th class='id_number' style="width:80px;">Dealer Code</th>
				<th style="width: 220px;">Name</th>
				<th style="width: 110px;">Contact Number</th>
				<th style="width: 110px;">Agent</th>
				<th style="width: 40px;">Is Active</th>
				<th class='action'>&nbsp;</th>
			</tr>
		</thead>
	</table>
	<div id='assign-requester-list'>
		<table class="table table-striped table-condensed assign-requester-list">
			<tbody id='assign-requester-listing'>
			</tbody>
		</table>
	</div>
</script>

<script id='assign-requester-item-template' type='text/template'>
	<%	console.log(dealers);
		$.each(dealers, function(index, item) { 
		
	%>
			<tr id='dlr_<%= item.old_dealer_code %>'>
				<td class='account_number' style="width:80px;"><%= item.old_dealer_code %></td>
				<td style="width: 250px;"><%= item.complete_name %></td>
				<td style="width: 110px;"><%= item.contact_number  %></td>
				<td style="width: 110px;"><%= item.agent_name  %></td>
				<td style="width: 40px;"><%= item.is_active  %></td>
				<td class='action'><button class='btn btn-small btn-info btn-select-member' data-id='<%= item.dealer_id %>' data-olddealercode='<%= item.old_dealer_code %>' data-completename='<%= item.complete_name%>' data-agentname='<%= item.agent_name%>'>Select</button></td>
			</tr>
	<%	}); %>
</script>