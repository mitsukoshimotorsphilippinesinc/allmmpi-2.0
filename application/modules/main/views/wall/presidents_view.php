<?php
$per_row = 5;
?>
<div class="content-area">
	<div class="row-fluid presidents">
		<center>
			<h2>THE PRESIDENTS CLUB</h2>
			<?php foreach($presidents as $k => $v): ?>
				<?php if($k == 0 || $k % $per_row == 0): ?>
					<ul class="span12">
				<?php endif; ?>
				<li class="span2">
					<img src="/assets/media/members/<?= $v->image_filename; ?>" alt="<?= $v->member_name; ?>">
					<h3><?= $v->member_name; ?></h3>
					<p><?= $v->group_name; ?></p>
				</li>
				<?php if(($k != 0 && ($k+1) % $per_row == 0) || count($presidents) - 1 == $k): ?>
					</ul>
				<?php endif; ?>
			<?php endforeach; ?>
		</center>
	</div>
</div>
