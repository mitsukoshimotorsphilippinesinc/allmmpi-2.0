<div class="alert alert-danger">
	<h2>S4S (System For System) <a href='/operations/s4s/add' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;"><i class="icon-plus"></i><span> Add New</span></a></h2>
</div>
<hr/>

<div id="search_container">
	<form id='search_details' method='get' action ='/operations/s4s/page' class="form-inline">
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:auto;" value="<?= $search_by ?>">
			<option value="pp_name">Name</option>
			<option value="pp_description">Description</option>
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
			<th>Is Active</th>
			<th style='width:160px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($s4s_list)): ?>
		<tr><td colspan='4' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($s4s_list as $a): ?>
		<tr>
			<td><?= $a->pp_name; ?></td>
			<td><?= $a->pp_description; ?></td>
			<?php
			if ($a->is_active == 1) {
				echo "<td style='text-align:center;'><span class='label label-success'>YES</span></td>";
			} else {
				echo "<td style='text-align:center;'><span class='label label-important'>NO</span></td>";
			}
			?>
			<td>
				<a href='/operations/s4s/view/<?= $a->s4s_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/operations/s4s/edit/<?= $a->s4s_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='#' class='btn btn-small btn-primary' id='button_privileges' data='<?= $a->s4s_id ?>' title='Privileges'><i class="icon-lock icon-white"></i></a>
				<a href='/operations/s4s/delete/<?= $a->s4s_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
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
  //<![CDATA[
  	$("#button_refresh").live("click",function() {
		redirect('/operations/s4s');
	});

</script>  	