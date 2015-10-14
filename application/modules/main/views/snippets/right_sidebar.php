<div class="sidebar-col2">
	<div class="widget announcements">
    	<h3>Announcements</h3>
        <div class="widget-content"><img src="<?=$this->config->item('img_url')?>/announcement.png"></div>
    </div>
    
	<!-- Tweets Widget -->
    <?php echo Modules::run('main/tweets'); ?>
    
	<!-- Events Widget -->
    <?php echo Modules::run('main/event/snippets'); ?>
    
	<!-- News Widget -->
    <?php echo Modules::run('main/news/snippets'); ?>

    <!-- Featured Testimonials -->
	<?php echo Modules::run('main/testimonials/featured'); ?>
    
    <div class="widget results">
    	<h3>Results</h3>
		<div id="results" class="widget-content">
			<div class="slides_container" style="width:inherit;">
				<?php if(!is_null($results) && !empty($results) ):?>
				<?php foreach($results as $result): ?>
				<div class="slide"  style="width:262px;height:245px;">					
					<?php if(!is_null($result->image_filename) && !empty($result->image_filename)):?>					
					<img src="<?=$this->config->item('media_url')?>/results/<?= $result->image_filename; ?>">
					<?php else: ?>
						<img src="http://placehold.it/260x172">
					<?php endif; ?>
					<p class="member-name"><?= $result->member_name ?></p>
					<p><?= $result->result; ?></p>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
    </div>
</div><!-- end: div.sidebar-col2 -->
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("#results").slides({
			generatePagination: false,
			play: 3000,
			pause: 1000,
			hoverPause: true
		});
	});
</script>