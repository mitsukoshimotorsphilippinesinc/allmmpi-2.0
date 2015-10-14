<div class="alert alert-info">	
	<h2 id="header_text">Testimonials <a href='/cms/testimonials/add' class='btn btn-small' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2>
</div>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Member Name</th>
			<th>Member Details</th>
			<th style="width: 210px;">Image</th>
			<th style="width: 63px;">Approved?</th>
			<th style='width: 109px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($testimonials)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($testimonials as $t): ?>
		<tr>
			<td><?= $t->member_name; ?></td>
			<td><?= $t->member_details; ?></td>
			<td>
			<?php if(!empty($t->image_filename)): ?>
				<img style="max-width: 100px; max-height: 100px; border: solid 1px #666;" alt="" src="/assets/media/testimonials/<?= $t->image_filename ?>">
			<?php else: ?>	
				&nbsp;
			<?php endif; ?>
			</td>
			<td><?= ($t->is_approved) ? 'Yes' : 'No'; ?></td>
			<td>
				<a href='/cms/testimonials/view/<?= $t->testimonial_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/cms/testimonials/edit/<?= $t->testimonial_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/cms/testimonials/delete/<?= $t->testimonial_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>