<?php
$per_row = 4;
?>
<div class="content-area">
	<div class="row-fluid grandmaster">
		<center>
			<h2>THE GRANDMASTERS CLUB</h2>
			<?php foreach($grandmasters as $k => $v): ?>
				<?php if($k == 0 || $k % $per_row == 0): ?>
					<ul class="span12">
				<?php endif; ?>
				<li class="span3">
					<img src="/assets/media/members/<?= $v->image_filename; ?>" alt="<?= $v->member_name; ?>">
					<h3><?= $v->member_name; ?></h3>
					<p><?= $v->group_name; ?></p>
				</li>
				<?php if(($k != 0 && ($k+1) % $per_row == 0) || count($grandmasters) - 1 == $k): ?>
					</ul>
				<?php endif; ?>
			<?php endforeach; ?>
		</center>
	</div>
</div>
