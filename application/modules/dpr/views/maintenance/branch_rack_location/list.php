<?php	
	$breadcrumb_container = assemble_breadcrumb();

?>

<?= $breadcrumb_container; ?>

<div class='alert alert-danger'><h2>Branch Rack Location</h2></div>
<hr/>

<form id='search_details' method='get' action =''>

	<strong>Search By:&nbsp;</strong>
	<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">		
		<option value="branch_name">Branch Name</option>
		<option value="rack_location">Rack Location</option>		
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
			<th>Branch Name</th>
			<th style="">Tin Number</th>
			<th style="">Rack Location</th>
			<th style="">Active Branch</th>
			<th style="">Active Rack Location</th>
			<th style='width: 10em;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($branch_rack_locations)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($branch_rack_locations as $brl): ?>
		<tr>			
			<td><?= $brl->branch_name; ?></td>
			<td><?= $brl->tin ?></td>
			<td><?= $brl->rack_location ?></td>
			<?php
				echo "<td style='text-align:center;'>";
				if ($brl->is_active_branch == 1) 
					echo "<circle class='btn-success'><a class='icon-white icon-ok'></circle>";

				if ($brl->is_active_branch == 0) 
					echo "<circle class='btn-danger'><a class='icon-white icon-remove'></circle>";

				if (($brl->is_active_branch == NULL)  || ($brl->is_active_branch == ''))
					echo "<circle>N/A</circle>";

				echo "</td>";
			?>			
			<?php
				echo "<td style='text-align:center;'>";
				if ($brl->is_active_rack_location == 1) 
					echo "<circle class='btn-success'><a class='icon-white icon-ok'></circle>";

				if ($brl->is_active_rack_location == 0) {
					echo "<circle class='btn-danger'><a class='icon-white icon-remove'></circle>";
				} else {
				if (($brl->is_active_rack_location == NULL)  || ($brl->is_active_rack_location == ''))
					echo "<circle>N/A</circle>";
				}
				echo "</td>";
			?>		
			<td>
				<a href='/dpr/maintenance/view_branch_rack_location/<?= $brl->branch_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>				
				<a href='/dpr/maintenance/edit_branch_rack_location/<?= $brl->branch_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<?php
				if (!(trim($brl->rack_location) == '')) {
					echo "<a href='/dpr/maintenance/delete_branch_rack_location/<?= $brl->branch_id ?>' class='btn btn-small btn-danger' title='Delete'><i class='icon-remove icon-white'></i></a>";	
				}
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>
