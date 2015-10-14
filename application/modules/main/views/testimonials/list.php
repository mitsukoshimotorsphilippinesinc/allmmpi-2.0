<h2><?=$page_title?></h2>

<?php	
	$img_url = $this->config->item('media_url');
	$url = $this->config->item('base_url') . "/main/testimonials/";

	foreach($testimonials as $t):
	$img = "{$img_url}/testimonials/{$t->image_filename}";
	$img = check_image_path($img);
?>
	<div class="news-box">
		<img src='<?=$img;?>' class="pull-left" style="padding-right:16px; max-width: 48px; max-height: 48px;">
		<h3 class="news-title"><a><?=$t->member_name?></a></h3>
		<div class="news-meta">
			<span class="author"><?=$t->member_details?></span>
		</div>
		<div class="news-copy clearfix">
			<p><?=$t->body?></p>
		</div>
	</div>

<?php endforeach; ?>
<br/>
<?php
	if(!is_null($this->pager)) echo $this->pager->create_links();
?>