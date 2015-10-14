<h2>Delete Gallery  <a href='/cms/galleries' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($gallery)): ?>
	<h3>Gallery not found.</h3>
<?php else: ?>
<form action='/cms/galleries/delete/<?= $gallery->gallery_id ?>' method='post' class='form-horizontal'>
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
		<label class="control-label" for="">Gallery</label>
		<div class="controls">
			<label class='data'><a href="/cms/galleries/view/<?= $gallery->gallery_id; ?>" class="btn btn-primary">View Gallery</a></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($gallery->is_published) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Confirm Deletion</button>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
