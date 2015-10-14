<form id="frm_assign_search" class="form-search control-group" onsubmit="return false;">
<label><strong>Member: </strong></label>
<input id="txt_member_search_key" class="input-xlarge search-query assign-input-search" type="text" value="" placeholder="First Name / Last Name" name="txt_member_search_key">
<button id="btn_member_search" class="btn">Search</button>
</form>
<div id="assign-customer-list">
</div>



<script type="text/javascript">
	$("#btn_member_search").click(function() {
		var memberToSearch = $("#txt_member_search_key").val();
		
		beyond.request({
			url : '/members/transfers/search',
			data : {
				"member_to_search" : memberToSearch
			},
			
			on_success : function(data) {
				if (data.status == "1")	{
					$("#assign-customer-list").html(data.data.html);
				
				}
			}
		})
	
	});
	
	
	
</script>