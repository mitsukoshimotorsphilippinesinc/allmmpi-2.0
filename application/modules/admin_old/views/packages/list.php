<div class='alert alert-info'><h2>Packages <a href='/admin/packages/add' class='btn btn-small' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<div id="search_container">
	<form id='search_details' method='get' action ='/admin/packages'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:100px;" value="<?= $search_by ?>">
			<option value="package_name">Package Name</option>			
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
<strong>Note:  (*) - Swappable</strong>
<br>
<br>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Package Name</th>
			<th>Package Type</th>
			<th>Items</th>
			<th>Standard Retail Price</th>
			<th>IGPSM Points</th>
			<th style='width:110px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($packages)): ?>
		<tr><td colspan='10' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($packages as $package): ?>
		<tr data='<?= $package->package_id ?>'>
			<td><?= $package->package_name; ?></td>
			<td><?= $package->package_type; ?></td>
			<td class="items_row">
				<div class="items_list" style="height:35px; overflow:hidden;" >
					<ul class="unstyled">
					<?php $swappable = array();
					$product_count = array();
					foreach($package->products as $k => $product):?>
						<?php
							if($product->is_swappable)
							{
								$swappable[$product->product_name] = $product->quantity;
							}
							else
							{
								$product_count[$product->product_name] = $product->quantity;
							}
							?>
					<?php endforeach;?>
					<?php 
						if(!empty($product_count))
						{
							foreach($product_count as $k => $p)
							{
							echo "<li> {$k} x{$p} </li>";
							}
						}
					?>
					<?php
					if(!empty($swappable))
					{
						foreach($swappable as $k => $p)
						{
							echo "<li>{$k} x {$p} (*)</li>";
						}
					}
					?>
					</ul>
				</div>
				<?php if(count($package->products) >=  2): ?>
				<a class="more_items" style='cursor:pointer;'>More...</a>
				<?php endif ?>
			</td>
			<td><?= number_format($package->standard_retail_price, 2) ?></td>
			<td><?= number_format($package->igpsm_points) ?></td>
			<td>	
				<a class='btn btn-small btn-primary btn_show_gallery'><i class="icon-picture icon-white" title="Gallery" ></i></a>
				<a href="/admin/packages/edit/<?= $package->package_id ?>" class='btn btn-small btn-primary' title="Edit" ><i class="icon-pencil icon-white"></i></a>
				<a class='btn btn-small btn-danger btn_delete_package' title="Delete" ><i class="icon-remove icon-white"></i></a>
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
		windows.location.href = '/admin/packages';
		return false;
	});
	
	$(document).on("click",".btn_show_gallery",function(){
		var _package_id = $(this).parent().parent().attr("data");
		
		b.request({
			url: '/admin/packages/gallery',
			data : {
				'_package_id' : _package_id
			},
			on_success : function(data){
				if (data.status == "ok")	{
					
					// show add form modal					
					var editGalleryModal = b.modal.new({
						title: 'Gallery',
						width: 780,
						html: data.data.html
					});
					editGalleryModal.show();					
				}
			}
		});
	});
	
	$('.btn_delete_package').live("click",function() {		
		var _package_id = $(this).parent().parent().attr("data");

		beyond.request({
			url : '/admin/packages/delete',
			data : {
					'_package_id' : _package_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deletePackageModal = b.modal.new({
						title: 'Delete Package',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deletePackageModal.hide();
							},
							'Delete' : function() {																															
								confirmDeletePackage(_package_id);
								deletePackageModal.hide();																
							}
						}
					});
					deletePackageModal.show();					
				}
			}
		})
		return false;
	});
	
	var confirmDeletePackage = function(package_id) {
		beyond.request({
			url : '/admin/packages/confirm_delete',
			data : {
				'_package_id' : package_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeletePackagesModal = b.modal.new({
						title: 'Delete Packages',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmDeletePackagesModal.hide();
							},
							'Yes' : function() {
								deletePackages(package_id);
								confirmDeletePackagesModal.hide();
							}
						}
					});
					confirmDeletePackagesModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deletePackages = function(package_id) {	
		beyond.request({
			url : '/admin/packages/delete_package',
			data : {
				'_package_id' : package_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var deletePackageModal = b.modal.new({
						title: 'Delete Product :: Successful',
						disableClose: true,
						html: 'You have successfully deleted Package',
						buttons: {
							'Ok' : function() {
								deletePackageModal.hide();
								redirect('/admin/packages');
							}
						}
					});
					deletePackageModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};

</script>