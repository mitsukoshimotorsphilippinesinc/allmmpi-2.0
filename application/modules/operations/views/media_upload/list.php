<div class="alert alert-danger">
	<h2>Media Upload <a href='/operations/media_upload/add' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;"><i class="icon-plus"></i><span> Add New</span></a></h2>
</div>
<hr/>

<div id="search_container">
	<form id='search_details' method='get' action ='/operations/media_upload' class="form-inline">
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:auto;" value="<?= $search_by ?>">
			<option value="title">Title</option>
			<option value="description">Description</option>
		</select>
		<input title="Search" class="input-large search-query" style="margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">

		<button id="button_search" class='btn btn-primary' style="margin-left:5px;"><span>Search</span></button>
		<a id='button_refresh' class='btn' style="margin-top:0;"><span>Refresh</span></a>

		<br/>
		<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>

		<?php if ($search_text == ""): ?>
			<div id="search_summary" style="display:none;">
		<?php else: ?>
			<br/>
			<div id="search_summary">
		<?php endif; ?>

			<span class="label label-info">Search Results for:</span>
			<span class="label label-success"><?= $search_by ?></span>
			<span class="label label-success"><?= $search_text ?></span>
		</div>
	</form>
</div>


<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>
			<th style='width:64px;'>Display?</th>
			<th style='width:113px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($media_uploads)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($media_uploads as $a): ?>
		<tr>
			<td><?= $a->title; ?></td>
			<td><?= $a->description; ?></td>
			<td><?= ($a->is_display) ? 'Yes' : 'No'; ?></td>
			<td>
				<a href='/operations/media_upload/view/<?= $a->media_upload_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/operations/media_upload/edit/<?= $a->media_upload_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/operations/media_upload/delete/<?= $a->media_upload_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>
	
<script type="text/javascript">
  //<![CDATA[
  $("#button_refresh").live("click",function() {
		redirect('/operations/media_upload');
	});

</script>  	