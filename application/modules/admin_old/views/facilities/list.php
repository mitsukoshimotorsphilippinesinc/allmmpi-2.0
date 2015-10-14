<h2>Facilities <a href='/admin/facilities/add' class='btn btn-small'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>
<table class='table table-striped'>
	<thead>
		<tr>
			<th>Facility Name</th>
			<th>Description</th>
			<th>Type</th>
			<th>Address</th>
			<th>Contact Details</th>
			<th style='width:150px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($facilities)): ?>
		<tr><td colspan='6' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($facilities as $facility): ?>
		<tr>
			<td><?= $facility->facility_name; ?></td>
			<td><?= $facility->facility_description; ?></td>
			<td><?= $facility->facility_type_id == 1 ? 'Warehouse' : 'Depot'; ?></td>
			<td><?= $facility->facility_address; ?></td>
			<td><?= $facility->facility_contact_details; ?></td>
			<td>
				<a href='/admin/facilities/edit/<?= $facility->facility_id ?>' class='btn btn-small btn-primary'><i class="icon-pencil icon-white"></i><span> Edit</span></a>
				<a href='/admin/facilities/delete/<?= $facility->facility_id ?>' class='btn btn-small btn-danger'><i class="icon-remove icon-white"></i><span> Delete</span></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>