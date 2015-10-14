<?php	
	$breadcrumb_container = assemble_breadcrumb();

?>

<?= $breadcrumb_container; ?>

<div class='alert alert-danger'>
	<h2>Comments</h2>
</div>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Title</th>
			<th style='width:64px;'>Published?</th>			
			<th style='width:120px;'>&nbsp;</th>	
		</tr>
	</thead>
	<tbody>
	<?php if(empty($announcements)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($announcements as $a): ?>
		<tr>
			<td><?= $a->title; ?></td>
			<td><?= ($a->is_published) ? 'Yes' : 'No'; ?></td>			
			<td>
				<a href='/operations/announcement/view_with_comments/<?= $a->announcement_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-list icon-white"></i></a>				
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>
	