<h2>Advertisements <a href='/cms/advertisements/add' class='btn btn-small'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Advertisement Title</th>
			<th style='width:108px;'>Active?</th>
			<th style='width:180px;'>Active From</th>
			<th style='width:180px;'>Active Until</th>
			
			<th style='width:108px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($advertisements)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($advertisements as $a): ?>
		<tr>
			<td><?= $a->advertisement_title; ?></td>
			<td><?= ($a->is_active) ? 'Yes' : 'No'; ?></td>
			<td><?= ($a->active_from == "0000-00-00 00:00:00")? "None" : date("F j, Y",strtotime($a->active_from))  ?></td>
			<td><?= ($a->active_until == "0000-00-00 00:00:00")? "None" : date("F j, Y",strtotime($a->active_until))  ?></td>
			<td>
				<a href='/cms/advertisements/view/<?= $a->advertisement_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/cms/advertisements/edit/<?= $a->advertisement_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/cms/advertisements/delete/<?= $a->advertisement_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>