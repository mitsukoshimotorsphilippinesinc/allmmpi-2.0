<script id='search-requester-template' type='text/template'>
	<h3>Member:</h3>
	<form id='frm_assign_search' class="form-search control-group" onsubmit='return false;'>
		<input type="text" id='txt_requester_search_key' name='txt_requester_search_key' class="input-xlarge search-query assign-input-search" placeholder='First Name / Last Name / ID Number' value='' style="width: 545px;">
		<button id='btn_requester_search' class="btn"><i class="icon-search"></i>&nbsp;Search</button>
		<p id='txt_requester_search_key_help' class="help-block" style='display:none;'></p>
	</form>
	<div class="alert">
	  <strong>NOTE:</strong> Will only display first 50 results.
	</div>
	<table class="table table-striped table-condensed assign-requester-list">
		<thead>
			<tr>
				<th class='id_number' style="width:80px;">IDNO</th>
				<th style="width: 220px;">Name</th>
				<th style="width: 110px;">Department</th>
				<th style="width: 110px;">Position</th>
				<th style="width: 40px;">Is Employed</th>
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
	<%	console.log(employees);
		$.each(employees, function(index, item) { 
		
	%>
			<tr id='emp_<%= item.id_number %>'>
				<td class='account_number' style="width:80px;"><%= item.id_number %></td>
				<td style="width: 250px;"><%= item.complete_name %></td>
				<td style="width: 110px;"><%= item.department_name  %></td>
				<td style="width: 110px;"><%= item.position  %></td>
				<td style="width: 40px;"><%= item.is_employed  %></td>
				<td class='action'><button class='btn btn-small btn-info btn-select-member' data-id='<%= item.employment_information_id %>' data-idnumber='<%= item.id_number %>' data-fullname='<%= item.complete_name%>' data-details='<%= item.details%>' data-positionname='<%= item.position%>' data-departmentname='<%= item.department_name%>'>Select</button></td>
			</tr>
	<%	}); %>
</script>