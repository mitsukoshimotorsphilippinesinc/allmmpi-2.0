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
			<th style='width:20em;'>Posted By</th>
			<th style='width:1em;'>Date Posted</th>
			<th style='width:1em;'>Published?</th>			
			<th style='width:1em;'>&nbsp;</th>	
		</tr>
	</thead>
	<tbody>
	<?php if(empty($announcements)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($announcements as $a): ?>
		<tr>
			<td><?= $a->title; ?></td>
			<?php
				$employment_info_view_details = $this->human_relations_model->get_employment_information_view_by_id($a->id_number);
			?>
			<td><?= $employment_info_view_details->complete_name; ?></td>
			<td><?= $a->insert_timestamp; ?></td>
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
	