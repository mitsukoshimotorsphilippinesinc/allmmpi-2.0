<div class='alert alert-danger'><h2>Alert Message <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

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
	<?php if(empty($alert_messages)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($alert_messages as $am): ?>
		<?php
		$start = "--";
		$end = "--";
		if($am->start_timestamp !="0000-00-00 00:00:00") $start = date("m-d-Y H:i:s", strtotime($am->start_timestamp));
		if($am->end_timestamp !="0000-00-00 00:00:00") $end = date("m-d-Y H:i:s", strtotime($am->end_timestamp));
		?>
		<tr>
			<td><?= $am->title; ?></td>
			
			<td><?= $am->content; ?></td>
			<td>
				<?= $start; ?> <strong>to</strong> 
				<?= $end; ?>
			</td>
			<td><?= ($am->is_visible) ? 'Yes' : 'No'; ?></td>
			<td>
				<a href='/it/systems/alert_message/view/<?= $a->message_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/it/systems/alert_message/edit/<?= $a->message_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/it/systems/alert_message/delete/<?= $a->message_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>
