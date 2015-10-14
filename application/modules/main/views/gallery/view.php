<?php if(empty($gallery) || is_null($gallery)): ?>
<div class="gallery-box">
	<p>
	Gallery does not exist!
	<br>
	<a href="/main/gallery">Back</a>
	</p>
</div>
<?php
	else:
	$upload_url = $this->config->item("media_url") . "/galleries";
	$gallery_url = $this->config->item('base_url') . "/main/gallery/";

	//load users model
	$this->load->model('users_model');

	// get author
	$user = $this->users_model->get_user_by_id($gallery->user_id);
	$author = $user->username;
	// set pretty date
	$pretty_date = date('F j, Y H:i',strtotime($gallery->insert_timestamp));
	
	// link url
	$link =  $gallery_url . "view/" . $gallery->gallery_id;
?>
<div class="gallery-box">
	<h3 class="gallery-title"><a href="<?=$link?>"><?=$gallery->gallery_title?></a></h3>
	<div class="gallery-meta">
		<span class="author">Author: <?=$author?></span>
		<span class="pubdate">Date: <?= date("M j, Y",strtotime($gallery->insert_timestamp)); ?></span>
	</div>
	<?php if(!is_null($gallery_pictures) && !empty($gallery_pictures)):?>
	<div class="gallery-images well clearfix">

		<?php foreach($gallery_pictures as $picture): ?>

			<div class='image-container'>
				<a href='<?= $upload_url . '/gallery_' . $picture->gallery_id .'/' . $picture->image_filename ?>' ><img src='<?= $upload_url . '/thumbnail/gallery_' . $picture->gallery_id .'/' . $picture->image_filename ?>' alt='' /></a>
			</div>

		<?php endforeach; ?>


	</div>
	<?php endif; ?>
</div>
<?php endif; ?>

<script type="text/javascript">

 
$(document).ready(function(){
	$(".gallery-images").yoxview({
		popupMargin: "50 100",
		autoHideInfo: false,
		autoHideMenu: false,
		renderInfoPin: false
	});
});

/*$(".gallery-images a img").click(function(){
	$('body,html').animate({scrollTop: "137px"}, 250);
});*/

</script>