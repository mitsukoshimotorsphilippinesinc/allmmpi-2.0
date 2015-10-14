<!--h2>Delete Package  <a href='/admin/packages' class='btn btn-large' >Back</a></h2>
<hr/-->
<?php if (empty($package)): ?>
	<h3>Package not found.</h3>
<?php else: ?>
	<label>You are about to delete a Package having the following details:</label>
	<br/>
	
	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:140px;'><label><strong>Package Name</strong></label></td>
				<td><label class=''><?= $package->product_name ?></label></td>
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>Type</strong></label></td>
				<td>
					<label class=''>
						<?php
							if(empty($product_type))
							{
								echo "None";
							}
							else
							{
								$product_type = $product_type[$product_type_id];
								echo $product_type->name;
							}

						?>
					</label>
				</td>
			</tr>

			<tr>
				<td style='width:140px;'><label><strong>Standard Retail Price</strong></label></td>
				<td><label class=''>PHP <?= $package->standard_retail_price ?></label></td>
			</tr>
			<tr>
				<td style='width:140px;'><label><strong>IGPSM Points</strong></label></td>
				<td><label class=''><?= $package->igpsm_points ?></label></td>
			</tr>
		</tbody>
	</table>

<?php endif; ?>
