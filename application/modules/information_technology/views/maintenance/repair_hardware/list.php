<?php
	$upload_url = $this->config->item("media_url") . "/repair_hardware";
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Repair - Hardwares <a href='/information_technology/maintenance/add_agent' class='btn btn-small'  style='float:right;'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>

<form id='search_details' method='get' action =''>

	<strong>Search By:&nbsp;</strong>
	<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">		
		<option value="repair_hardware_namee">Hardware Name</option>		
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
	
		<span class="label label-important">Search Results for:</span>		
		<span class="label label-default"><?= $search_by ?></span>
		<span class="label label-default"><?= $search_text ?></span>
	</div>		
</form>

<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style="width: 5em;">Name</th>
			<th>Description</th>
			<th style="width: 25em;">Date Created</th>			
			<th style='width: 10em;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($repair_hardware)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($repair_hardware as $r): ?>
		<tr>			
			<td><?= $r->repair_hardware_name; ?></td>
			<td><?= $r->description ?></td>
			<td><?= $r->insert_timestamp ?></td>					
			<td>
				<a href='/information_technology/maintenance/view_agent/<?= $r->repair_hardware_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>				
				<a href='/information_technology/maintenance/edit_agent/<?= $r->repair_hardware_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/information_technology/maintenance/delete_agent/<?= $r->repair_hardware_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>