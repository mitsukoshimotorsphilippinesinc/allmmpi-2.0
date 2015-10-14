<?php
$per_row = 7;
?>
<div class="content-area">
	<div class="row-fluid millionares">
		<center>
			<h2>THE ELITE CLUB</h2>
			<ul>
			<?php foreach($millionares as $k => $v): ?>
				<li>
					<img src="/assets/media/members/<?= $v->image_filename; ?>" alt="<?= $v->member_name; ?>">
					<h4><?= $v->member_name; ?></h4>
					<p><?= $v->group_name; ?></p>
				</li>
			<?php endforeach; ?>
			</ul>
		</center>
	</div>
</div>
