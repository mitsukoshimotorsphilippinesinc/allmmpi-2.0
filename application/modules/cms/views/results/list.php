<h2>Results <a href='/cms/results/add' class='btn btn-small'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Result</th>
			<th>Member</th>
			<th style="width: 65px;">Published?</th>
			<th style="width: 60px;">Featured?</th>
			<th style='width: 109px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($results)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($results as $r): ?>
		<tr>
			<td><?= $r->result; ?></td>
			<td><?= $r->member_name; ?></td>
			<td><?= ($r->is_published) ? 'Yes' : 'No'; ?></td>
			<td><?= ($r->is_featured) ? 'Yes' : 'No'; ?></td>
			<td>
				<a href='/cms/results/view/<?= $r->result_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/cms/results/edit/<?= $r->result_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/cms/results/delete/<?= $r->result_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>