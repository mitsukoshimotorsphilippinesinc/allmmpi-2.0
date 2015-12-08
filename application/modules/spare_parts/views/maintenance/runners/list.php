<?php
	$upload_url = $this->config->item("media_url") . "/employees";
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Runners <a href='/spare_parts/maintenance/add_runner' class='btn btn-small'  style='float:right;'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>

<form id='search_details' method='get' action =''>

	<strong>Search By:&nbsp;</strong>
	<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">		
		<option value="complete_name">Complete Name</option>						
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
			<th style="width: 5em;">Image</th>
			<th>Complete Name</th>
			<th style="width: 25em;">Warehouse</th>
			<th style="width: 15em;">Position</th>			
			<th style="">Is Active</th>
			<th style='width: 10em;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($runners)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($runners as $r): ?>
		<tr>
			<?php
				$employment_view_details = $this->human_relations_model->get_employment_information_view_by_id($r->id_number);

				if ((empty($employment_view_details->image_filename)) || ($employment_view_details->image_filename == NULL) || (trim($employment_view_details->image_filename) == "")) {
					$image_display = "ni_". strtolower($employment_view_details->gender) .".png";
				} else {
					$image_display = $employment_view_details->image_filename;
				}	
			
			?>

			<td><img id="" style="width:70px; height:70px;" alt="" src="<?= $upload_url; ?>/<?= $image_display ?>"></td>
			<td><?= $r->complete_name ?></td>
			<?php
				
				$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($r->warehouse_id);

				if (empty($warehouse_details)) {
					echo "<td>N/A</td>";
				} else {
					echo "<td>{$warehouse_details->warehouse_name}</td>";
				}

				$position_details = $this->human_relations_model->get_position_by_id($r->position_id);

				if (empty($position_details)) {
					echo "<td>N/A</td>";
				} else {
					echo "<td>{$position_details->position_name}</td>";
				}
			

				if ($r->is_active == 0) {
					echo "<td style='text-align:center'><span class='label label-important'>NO</span></td>";
				} else {
					echo "<td style='text-align:center'><span class='label label-success'>YES</span></td>";
				}
			?>						
			<td>
				<a href='/spare_parts/maintenance/view_runner/<?= $r->runner_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>				
				<a href='/spare_parts/maintenance/edit_runner/<?= $r->runner_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/spare_parts/maintenance/delete_runner/<?= $r->runner_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>