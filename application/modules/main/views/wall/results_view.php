<?php
$per_row = 3;
?>
<div class="content-area">
	<div class="row-fluid achievers_club">
		<center>
			<?php if(!empty($featured)):
			$featured = $featured[0];
			?>
				<ul class="span12">
					<li>
						<img src="/assets/media/results/<?= $featured->image_filename ?>" alt="<?= $featured->result; ?>" style="max-width: 960px;">
						<h3><?= $featured->member_name; ?></h3>
						<p><?= $featured->result; ?></p>
					</li>
				</ul>
			<?php endif; ?>
			<h2>RESULTS</h2>
			<?php foreach($results as $k => $v): ?>
				<?php if($k == 0 || $k % $per_row == 0): ?>
					<ul class="span12">
				<?php endif; ?>
				<li class="span4">
					<img src="/assets/media/results/<?= $v->image_filename; ?>" alt="<?= $v->result; ?>" style="max-width: 305px; max-height: 220px;">
					<h3><?= $v->member_name; ?></h3>
					<p><?= $v->result; ?></p>
				</li>
				<?php if(($k != 0 && ($k+1) % $per_row == 0) || count($results) - 1 == $k): ?>
					</ul>
				<?php endif; ?>
			<?php endforeach; ?>
		</center>
	</div>
</div>			