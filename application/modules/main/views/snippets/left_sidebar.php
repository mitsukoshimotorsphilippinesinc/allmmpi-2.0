<style>
	
</style>
<div class="sidebar-col1">
	<div class="adspace">
		<h3>New Products</h3>
        <a href="#"><img src="<?=$this->config->item('img_url')?>/ad_sidebar1_ad1.jpg" alt="Ad" class="ad"></a>    			</div>

	<?php foreach($members as $k=>$member):?>

	<div class="featured-members" style="margin-bottom:10px;">
    	<h3><?= $k ?></h3>
		<div id="<?= str_replace(" ", "-", $k) ?>" class="featured-member-details">
			<div class="slides_container" style="width:inherit;">
				<?php if(!is_null($member) && !empty($member) ):?>
				<?php foreach($member as $m):?>
				<div class="slide"  style="width:128px;height:194px;">
					<?php if(!is_null($m->image_filename) && !empty($m->image_filename)):?>					
					<img src="<?=$this->config->item('media_url')?>/members/<?= $m->image_filename; ?>" style="width: 111px;height: 123px;margin-left:auto; margin-right:auto;">
					<?php else: ?>
						<?php if($m->sex == "M"):?>
						<img src="<?=$this->config->item('media_url')?>/members/male.jpg" style="width: 111px;height: 123px;margin-left:auto; margin-right:auto;">
						<?php elseif($m->sex == "F"):?>
						<img src="<?=$this->config->item('media_url')?>/members/female.jpg" style="width: 111px;height: 123px;margin-left:auto; margin-right:auto;">
						<?php endif;?>
					<?php endif; ?>
					<p class="member-name"><?= "{$m->first_name} {$m->middle_name} {$m->last_name}"?></p>
					<p>Mindanao: Davao Eagles</p>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
    </div>

	<?php endforeach;?>
    
    <div class="adspace">
        <a href="#"><img src="<?=$this->config->item('img_url')?>/ad_sidebar1_placeholder.jpg" alt="Ad" class="ad"></a>    			</div>
	

</div>
<!-- end: div.sidebar-col1 -->
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$(".featured-member-details").each(function(index){
			var id = $(this).attr("id");
			$("#"+id).slides({
				generatePagination: false,
				play: 3000,
				pause: 1000,
				hoverPause: true
			});
		});
	});
</script>