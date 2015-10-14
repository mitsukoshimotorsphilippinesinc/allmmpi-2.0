<?php
	$this->load->model('contents_model');
	$achievement_types = $this->contents_model->get_member_achievements();
	$options = array();
	foreach($achievement_types as $a)
	{
		$options[$a->member_achievement_id] = $a->achievement_name;
	}
?>


<div>
	<fieldset>
		<select name="search_option_modal" id="search_option_modal" style="width:100px;">
			<option value="name">Name</option>			
			<option value="member_id">Member ID</option>
			<option value="account_id">Account ID</option>			
		</select>                 
		
		
		<input title="Search" class="span4 input-medium search-query" style="margin-top:-5px;" type="text" id="search_member_modal" name="search_member_modal" value="" autofocus="">
		
		<button id="button_search" class='btn'><span>Search</span></button>
		<button id='button_refresh' class='btn'><span>Refresh</span></button>
			
	</fieldset>
</div>

<fieldset>
	<table  class='table table-striped table-condensed'>
		<thead>
			<th>Name</th>
			<th>Actions</th>									
		</thead>
		<tbody id="tbody_html">
		</tbody>
	</table>
</fieldset>
<div class="clearfix"></div>

<div class="alert alert-info">
<h3>Member Details</h3>
</div>

<div class='control-group'>
	<label class='control-label' for='member_id'><strong>Member ID <em>*</em></strong></label>
	<div class='controls'><input type='text' placeholder='Member Id' class='span3' value='' id='member_id' name='member_id' disabled='disabled'/></div>
	<span id="member_id_error" class="label label-important" style="display:none">Member ID is Required</span>

	<label class='control-label' for='member_name'><strong>Member Name <em>*</em></strong></label>
	<div class='controls'><input type='text' placeholder='Member Name' class='span6' value='' id='member_name' name='member_name' /></div>
	<span id="member_name_error" class="label label-important" style="display:none">Member Name is Required</span>
	
	<label class='control-label' for='group_name'><strong>Group Name <em>*</em></strong></label>
	<div class='controls'><input type='text' class='span6' placeholder='Group Name' name='group_name' id='group_name' value=''></div>
	<span id="group_name_error" class="label label-important" style="display:none">Group Name is Required</span>
			
	<label class='control-label' for='facility_item_quantity'><strong>Achievement Type <em>*</em></strong></label>
	<div class='controls'>
		<?php echo form_dropdown('achievement_type', $options, "", "id='achievement_type'")?>
	</div>
</div>

<script type="text/javascript">

	// search
	$("#button_search").live("click",function() {
		var _search_member = $.trim($('#search_member_modal').val());
		var _search_option = $("#search_option_modal").val();
		
		//alert(_search_option + '|' + _search_member);
		searchMembers(1, _search_option, _search_member);
	});

	$("#btn-select-member").live("click",function() {
		var _memberId = $(this).attr("data");
		
		b.request({
	        url: '/cms/members/modal_add_details',
	        data: {
				"member_id":_memberId
			},
		
			on_success: function(data, status) {
				$("#member_id").val(data.data.member_id);		
				$("#member_name").val(data.data.member_name);				
				$("#group_name").val(data.data.group_name);				
		    }
		});		

		return;
		
		
	});

	
	var searchMembers = function(page, search_option, search_member) {	
		b.request({
	        url: '/cms/members/modal_search',
	        data: {
				"page":page,
				"search_text" : search_member,
				"search_by":search_option
			},
		
			on_success: function(data, status) {
				$("#tbody_html").html(data.data.html);
				
				//alert(data.data.html);
				
				//$("#total_rows").html(numberFormat(data.total_records));
		    }
		});		

		return;
	}

</script>