<div class="alert alert-info">
	<h2>Alert Messages <a href='/cms/alerts/add' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;"><i class="icon-plus"></i><span> Add New</span></a></h2>
</div>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Title</th>
			<th>Content</th>
			<th>Start - End</th>
			<th>Visible?</th>
			<th style='width:113px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($alerts)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($alerts as $a): ?>
		<?php
		$start = "--";
		$end = "--";
		if($a->start_timestamp !="0000-00-00 00:00:00") $start = date("m-d-Y", strtotime($a->start_timestamp));
		if($a->end_timestamp !="0000-00-00 00:00:00") $end = date("m-d-Y", strtotime($a->end_timestamp));
		?>
		<tr>
			<td><?= $a->title; ?></td>
			
			<td><?= $a->content; ?></td>
			<td>
				<?= $start; ?> to 
				<?= $end; ?>
			</td>
			<td><?= ($a->is_visible) ? 'Yes' : 'No'; ?></td>
			<td>
				<a href='/cms/alerts/view/<?= $a->message_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/cms/alerts/edit/<?= $a->message_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/cms/alerts/delete/<?= $a->message_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>
	