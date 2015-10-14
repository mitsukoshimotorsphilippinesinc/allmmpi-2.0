<?php
	$upload_url = $this->config->item("media_url") . "/galleries";
	$_upload_url = urlencode($upload_url);
?>
<div class="alert alert-info">
	<h2>View Gallery  <a href='/cms/galleries' class='btn btn-small' style="float:right;margin-top:5px;margin-right: -30px;">Back</a></h2>
</div>
<hr/>
<?php if (empty($gallery)): ?>
	<h3>Gallery not found.</h3>
<?php else: ?>
<style>
	#gallery ul {
		list-style-type: none;
		margin: 5px;
	}
	
	#gallery ul li {
		display: inline-block;
		margin: 2px;
		position: relative;
		width: 100px;
	}
</style>
<form action='' method='' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='gallery_id' name='gallery_id' value='<?= $gallery->gallery_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="gallery_title">Gallery Title</label>
		<div class="controls">
			<label class='data'><?= $gallery->gallery_title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="gallery_description">Gallery Description</label>
		<div class="controls">
			<label class='data'><?= $gallery->gallery_description ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($gallery->is_published) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="">Gallery Description</label>
		<div class="controls">
			<label class='data'><div id="image_upload"></div></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="">Gallery</label>
		<div class="controls">
			<label class='data'>
				<div id="gallery" class="well" style="width: 426px;">
					<ul>
					<?php
					
					if(!empty($gallery_pictures))
					{
						foreach($gallery_pictures as $picture)
						{
							echo "<li ><a href='{$upload_url}/gallery_{$picture->gallery_id}/{$picture->image_filename}'><img src='{$upload_url}/thumbnail/gallery_{$picture->gallery_id}/{$picture->image_filename}' alt='".pathinfo($picture->image_filename,PATHINFO_FILENAME)."' title='".pathinfo($picture->image_filename,PATHINFO_FILENAME)."' /></a><p><a data='{$picture->picture_id}' class='btn btn-primary remove_picture'>Delete</a></p></li>";
						}
					}
					
					?>
					</ul>
				</div>
			</label>
		</div>
	</div>
</fieldset>
</form>
<script type="text/javascript">

$(document).ready(function(){
	$("#gallery").yoxview({
		popupMargin: "50 100",
		autoHideInfo: false,
		autoHideMenu: false,
		renderInfoPin: false
	});

	// uploader
	$('#image_upload').Uploadrr({
		singleUpload : false,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.gif','.jpg', '.png'],
		target : base_url + '/admin/upload/process?location=<?=$_upload_url?>&type=gallery&gallery_id=<?= $gallery->gallery_id; ?>&ts=<?=time()?>',
		onComplete: function() {
			redirect("/cms/galleries/view/<?= $gallery->gallery_id; ?>");
		}
	});
})

	$(".remove_picture").click(function(e){
		var confirm_delete = b.modal.new({});
		
		confirm_delete.init({
			title: "Confirm Picture Deletion",
			html: "Are you sure you want to delete this picture?",
			width: 300,
			buttons: {
				"No": function(){
					confirm_delete.hide();
				},
				"Yes": function(){
					confirm_delete.hide();
					b.request({
						url: '/cms/galleries/update_gallery',
						data: {
							"picture_id": $(e.target).attr("data"),
							"gallery_id":<?= $gallery->gallery_id?>
						},
						on_success: function(data) {
							redirect("/cms/galleries/view/<?= $gallery->gallery_id; ?>");
						},
						on_error: function(data){

						}
					});

				}
			},
			disableClose: true
		});

		confirm_delete.show();
	});

</script>
<?php endif; ?>
