<?php
	$upload_url = $this->config->item("media_url") . "/spare_parts";
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>

<h2>Inventory <a href='/spare_parts/maintenance/add_inventory' class='btn btn-small'  style='float:right;'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>

<form id='search_details' method='get' action =''>

	<strong>Search By:&nbsp;</strong>
	<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">		
		<option value="sku">SKU</option>
		<option value="model_name">Model Name</option>
		<option value="brand_name">Brand Name</option>
		<option value="description">Description</option>
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
			<th>SKU</th>
			<th>Model Name</th>
			<th>Brand Name</th>
			<th>Description</th>
			<th>SRP</th>
			<th>Good Qty</th>
			<th>Bad Qty</th>
			<th>Stock Limit</th>			
			<th style="width:120px;"></th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($items)): ?>
		<tr><td colspan='10' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($items as $r): ?>	
			<?php			
			if (empty($r->image_filename)) {
				$image_display = "ni_spare_part.png";
			} else {
				$image_display = $r->image_filename;
			}

			if ($r->good_quantity <= $r->stock_limit)
				echo "<tr style='color:red;font-size:14px'>";
			else 
				echo "<tr>";
			?>
			
			<td><img id="" style="width:70px; height:70px;" alt="" src="<?= $upload_url; ?>/<?= $image_display ?>"></td>
			<td style="text-align:right;"><?= $r->sku; ?></td>
			<td><?= $r->model_name ?></td>
			<td><?= $r->brand_name ?></td>
			<td><?= $r->description ?></td>
			<td style="text-align:right;"><?= number_format($r->srp, 2) ?></td>
			<td style="text-align:right;"><?= number_format($r->good_quantity, 2) ?></td>
			<td style="text-align:right;"><?= number_format($r->bad_quantity, 2) ?></td>
			<td style="text-align:right;"><?= number_format($r->stock_limit, 2) ?></td>			
			<td>
				<a href='/spare_parts/maintenance/view_part/<?= $r->sku ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>				
				<a href='/spare_parts/maintenance/edit_part/<?= $r->sku ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/spare_parts/maintenance/delete_part/<?= $r->sku ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>