<div class="alert alert-info">
	<h2>View Media Upload  <a href='/cms/media_uploads' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;">Back</a></h2>
</div>

<hr/>
<?php if (empty($media_upload)): ?>
	<h3>Media Upload not found.</h3>
<?php else: ?>
<form action='' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='media_upload_id' name='media_upload_id' value='<?= $media_upload->media_upload_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $media_upload->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="title">Description</label>
		<div class="controls">
			<label class='data'><?= $media_upload->description ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls"  style="width: 797px;">
			<label class='data'><?= $media_upload->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_display">Display?</label>
		<div class="controls">
			<label class='data'><?= ($media_upload->is_display) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
