<?php
	$gallery_url = $this->config->item('base_url') . "/main/gallery/";
	$upload_url = $this->config->item("media_url") . "/galleries";
	
	foreach($galleries as $key => $g):
		//load users model
		$this->load->model('users_model');

		// get author
		$user = $this->users_model->get_user_by_id($g->user_id);
		$author = $user->username;
		// set pretty date
		$pretty_date = date('F j, Y H:i',strtotime($g->insert_timestamp));
		// tags		
		
		
		// link url
		$link =  $gallery_url . "view/" . $g->gallery_id;

		/*
		
		<?php foreach($g->gallery_pictures as $picture): ?>
			<div class="image-container">
				<img src='<?= $upload_url . '/thumbnail/gallery_' . $picture->gallery_id .'/' . $picture->image_filename ?>' alt='' title='' class="image"/>
			</div>
		<?php endforeach; ?>

		 */
?>
<div id="gallery_<?= $g->gallery_id; ?>" class="gallery-box">
	<h3 class="gallery-title"><a href="<?=$link?>"><?=$g->gallery_title?></a></h3>
	<div class="gallery-meta">
		<span class="author">Author: <?=$author?></span>
		<span class="pubdate">Date: <?= date("M j, Y",strtotime($g->insert_timestamp)); ?></span>
	</div>
	<?php if(!is_null($g->gallery_pictures) && !empty($g->gallery_pictures)):?>
	<div id="gallery_images_<?= $g->gallery_id; ?>" class="gallery-images well clearfix">

		<?php foreach($g->gallery_pictures as $picture): ?>

			<div class='image-container'>
				<a class="image_display" href='<?= $upload_url . '/gallery_' . $picture->gallery_id .'/' . $picture->image_filename ?>' data-gallery-id="<?= $g->gallery_id; ?>"> <img src='<?= $upload_url . '/thumbnail/gallery_' . $picture->gallery_id .'/' . $picture->image_filename ?>' alt='' /></a>
			</div>

		<?php endforeach; ?>

	</div>
	<?php endif; ?>
	<div class="read-more">
		<a href='<?=$link?>'>View Gallery</a>
	</div>
</div>
<?php 
	endforeach;
?>
<br/>
<?php
	if(!is_null($this->pager)) echo $this->pager->create_links();
?>
<script id='gallery-display-template' type='text/template'>
	/*<% console.log("a") %>
	<div>
		<div class="advanced-slider" id="gallery-thumbnails-slider">
	        <ul class="slides">
			<% 
				$.each(images, function(index, image) {
					console.log(image);
			%>
				<li class='slide'>
					<div>
						<%= image %>
					</div>
					<div class='thumbnail'>
						<p class="thumbnail-description">
							<%= image %>
	                    </p>
					</div>
				</li>
			<% });	%>
	        </ul>
	    </div>
	</div>*/
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".gallery-images").each(function() {
			var gallery = $(this).attr("id");
			$("#"+gallery).yoxview({
				popupMargin: "50 100",
				autoHideInfo: false,
				autoHideMenu: false,
				renderInfoPin: false
			});
		});
	});
	/*$(".image_display").click(function(e) {
		e.preventDefault();
		var gallery_id = $(this).data("gallery-id");
		var gallery_title = $("#gallery_"+gallery_id+" .gallery-title a").html();
		var images = [];
		
		$("#gallery_"+gallery_id+" .gallery-images .image-container").each(function() {
			
		});
		
		b.modal.create({
			title: gallery_title,
			width: 875,
			html: _.template($('#gallery-display-template').html(), {"images": images})
		}).show();
		
		$('#gallery-thumbnails-slider').advancedSlider({
			width: 805,
			height: 345,
			skin: 'text-thumbnail-pointer',
			shadow: false,
			effectType: 'swipe',
			overrideTransition: true,
			slideButtons: false,
			thumbnailType: 'scroller',
			thumbnailWidth: 158,
			thumbnailHeight: 120,
			thumbnailArrows: false,
			maximumVisibleThumbnails: 4,
			keyboardNavigation: true,
			slideArrows: false,
			timerToggle: true,
			slideshowDelay: 1000 * 10,
			pauseSlideshowOnHover:true, 
		});
	});*/
</script>