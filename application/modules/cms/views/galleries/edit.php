<?php
	$upload_url = $this->config->item("media_url") . "/galleries";
	$_upload_url = urlencode($upload_url);
?>
<div class="alert alert-info">
	<h2>Edit Gallery  <a href='/cms/galleries' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;">Back</a></h2>
</div>
<hr/>
<?php if (empty($gallery)): ?>
<h3>galleries not found.</h3>
<?php else: ?>
<form action='/cms/galleries/edit/<?= $gallery->gallery_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('gallery_title') ?>">
			<label class="control-label" for="gallery_title">Gallery Title <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="gallery_title" id="gallery_title" value="<?= set_value('gallery_title',$gallery->gallery_title) ?>">
				<p class="help-block"><?= $this->form_validation->error('gallery_title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('gallery_description') ?>">
			<label class="control-label" for="gallery_description">Gallery Description </label>
			<div class="controls">
				<textarea name="gallery_description" id="gallery_description" class="input-xlarge span6" rows="6"><?= set_value('gallery_description',$gallery->gallery_description) ?></textarea>
				<p class="help-block"><?= $this->form_validation->error('gallery_description'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_published') ?>">
			<label class="control-label" for="is_published">Published? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_published", $options, set_value('is_published',$gallery->is_published),"id='is_published' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_published'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<a href="/cms/galleries/view/<?= $gallery->gallery_id; ?>" class="btn btn-primary">View Gallery</a>
				<br>
				<div id="image_upload"></div>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update Gallery</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	var base_url = "<?=$this->config->item('base_url');?>";

	// uploader
	$('#image_upload').Uploadrr({
		singleUpload : false,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.gif','.jpg', '.png'],
		target : base_url + '/admin/upload/process?location=<?=$_upload_url?>&type=gallery&gallery_id=<?= $gallery->gallery_id; ?>&ts=<?=time()?>',
		onComplete: function() {
			/*$("#member_image").attr('src', '<?=$upload_url?>/gallery_'+<?= $gallery->gallery_id?>+'.jpg?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/cms/galleries/update_image',
		        data: {
					"filename": '<?= "gallery_" . $gallery->gallery_id .".jpg"?>',
					"gallery_id":<?= $gallery->gallery_id?>
				},
		        on_success: function(data) {		
		        }
		    });	*/
		}
	});
	
</script>
<?php endif; ?>