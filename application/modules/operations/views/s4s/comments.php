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
			<th>Description</th>
			<th>Department Name</th>
			<th style='width:2em;'>Is Active?</th>			
			<th style='width:1em;'>&nbsp;</th>	
		</tr>
	</thead>
	<tbody>
	<?php if(empty($s4s)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($s4s as $a): ?>
		<tr>						
			<td><?= $a->pp_name; ?></td>
			<td><?= $a->pp_description; ?></td>
			<td></td>
			<td><?= ($a->is_active) ? 'Yes' : 'No'; ?></td>			
			<td>
				<a href='/operations/s4s/view_with_comments/<?= $a->s4s_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-list icon-white"></i></a>				
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>
	