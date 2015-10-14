<div class='alert alert-info'><h2>Contents <a class='btn btn-small' href='/admin/contents/add' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span>Add New</span></a></h2></div>


<div>
	<form id='search_details' method='get' action ='/admin/contents'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="title">Title</option>			
		</select>                 
	
		<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

		<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
		<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>
	
		<br/>
		<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>	
	
		<?php
		if ($search_text == "") {
		?>	
			<div id="search_summary" style="display:none;">
		<?php
		} else {
		?>	
			<div id="search_summary">
		<?php
		};
		?>		
		
			<span class="label label-info">Search Results for:</span>
			<span class="label label-success"><?= $search_by ?></span>
			<span class="label label-success"><?= $search_text ?></span>
		</div>		
	</form>
</div>

<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style='width:85px;'>Content ID</th>
			<th style='width:704px;'>Title</th>
			<th style='width:85px;'>Status</th>
			<th style='width:162px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($contents)): ?>
		<tr><td colspan='4' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($contents as $content): ?>
		<tr>
			<td><?= $content->content_id; ?></td>
			<td><?= $content->title; ?></td>
			<td><?= $content->is_active == 1 ? 'Active' : 'Inactive'; ?></td>
			<td>
				<a href='/admin/contents/edit/<?= $content->content_id ?>' class='btn btn-small btn-primary'><i class="icon-pencil icon-white"></i><span> Edit</span></a>
				<a href='/admin/contents/delete/<?= $content->content_id ?>' class='btn btn-small btn-danger'><i class="icon-remove icon-white"></i><span> Delete</span></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>

<script type="text/javascript">

	$("#button_search").live("click",function() {
		var _search_string = $.trim($("#search_string").val());
        var _search_option = $("#search_option").val();

		if (_search_string == '') {
			$("#search_error").show();
			$("#search_summary").hide();
		} else {
			$("#search_details").submit();
			$("#search_error").hide(); 
			$("#search_summary").show();           
		}

		return false;
	});

	$("#button_refresh").live("click",function() {
		windows.location.href = '/inventory/item_sub_types';
	});
	
</script>