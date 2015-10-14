<h2>User Roles <a href='/admin/roles/add' class='btn btn-small'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>
<table class='table table-striped'>
	<thead>
		<tr>
			<th>Role</th>
			<th>Default</th>
			<th style='width:150px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($user_roles)): ?>
		<tr><td colspan='6' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($user_roles as $user_role): ?>
		<tr>
			<td><?= $user_role->user_role; ?></td>
			<td><?= $user_role->default_url; ?></td>
			<td>
				<a href='/admin/roles/edit/<?= $user_role->role_id ?>' class='btn btn-small btn-primary' title='Edit Role'><i class="icon-pencil icon-white"></i></a>
				<a href='/admin/roles/privileges/<?= $user_role->role_id ?>' class='btn btn-small btn-primary' title='User Role Privileges'><i class="icon-lock icon-white"></i></a>
				<a href='/admin/roles/delete/<?= $user_role->role_id ?>' class='btn btn-small btn-danger' title='Delete Role'><i class="icon-trash icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>