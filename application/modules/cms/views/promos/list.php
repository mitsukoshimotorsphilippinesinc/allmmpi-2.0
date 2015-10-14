<h2>Featured Promos <a href='/cms/promos/add' class='btn btn-small'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style="width: 130px;">Title</th>
			<th style="width: 400px;">Text</th>
			<th style="width: 77px;">Image</th>
			<th>URL</th>
			<th style="width: 42px;">Active?</th>
			<th style="width:71px;">Ordering</th>
			<th style='width:108px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($promos)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($promos as $p): ?>
		<tr>
			<td><?= $p->promo_title; ?></td>
			<td>
				<div class="promo_text" style="height:55px; overflow:hidden;" >
					<?= $p->promo_text; ?>
				</div>
				<?php if(!empty($p->promo_text)):?>
					<a href="#" class="more_text">More...</a>
				<?php endif; ?>
			</td>
			<td>
			<?php if(!empty($p->image_filename)): ?>
				<img style="width:75px; height:75px; border: solid 1px #666;" alt="" src="/assets/media/featured/<?= $p->image_filename ?>">
			<?php else: ?>
				&nbsp;
			<?php endif; ?>	
			</td>
			<td><?= ($p->url == '#' || empty($p->url))? '&nbsp;' : anchor("/featured_promos/view/{$p->url}","/main/featured/promos/{$p->url}"); ?></td>
			<td><?= ($p->is_active) ? 'Yes' : 'No'; ?></td>
			<td>
				<?php if($p->ordering > 1): ?>
				<a href='/cms/promos/order/up/<?= $p->promo_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
				<?php endif; ?>
				<?php if($p->ordering < $max_order): ?>
				<a href='/cms/promos/order/down/<?= $p->promo_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
				<?php endif; ?>
			</td>
			<td>
				<a href='/cms/promos/view/<?= $p->promo_id ?>' class='btn btn-small btn-info' title="View"><i class="icon-search icon-white"></i></a>
				<a href='/cms/promos/edit/<?= $p->promo_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>
				<a href='/cms/promos/delete/<?= $p->promo_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links();  ?>
</div>
<script type="text/javascript">
	$(document).on('click', '.more_text', function() {
		var item = $(this);
		//change 'More' to 'Less'
		item.parent().children('.more_text').html('Less...');
		item.parent().children('.more_text').attr('class', 'less_text');
		//display the rest of the items
		item.parent().children('.promo_text').attr('style', 'height:auto; overflow:auto');
	});
	
	$(document).on('click', '.less_text', function() {
		var item = $(this);
		//change 'Less' to 'More'
		item.parent().children('.less_text').html('More...');
		item.parent().children('.less_text').attr('class', 'more_text');
		//display only first two items
		item.parent().children('.promo_text').attr('style', 'height:55px; overflow:hidden');
	});
</script>