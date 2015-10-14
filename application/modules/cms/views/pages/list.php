<h2>Pages <a href='/cms/pages/add' class='btn btn-small'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style='width:584px;'>Title</th>
			<th style='width:auto;'>Slug</th>
			<th style='width:85px;'>Status</th>
			<th style='width:162px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($pages)): ?>
		<tr><td colspan='4' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($pages as $page): ?>
		<tr>
			<td><?= $page->title; ?></td>
			<td><?= $page->slug; ?></td>
			<td><?= $page->is_active == 1 ? 'Active' : 'Inactive'; ?></td>
			<td>
				<a href='/cms/pages/view/<?= $page->content_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/cms/pages/edit/<?= $page->content_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/cms/pages/delete/<?= $page->content_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>