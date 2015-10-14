<div class="alert alert-info">
	<h2>View Announcement  <a href='/cms/announcements' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;">Back</a></h2>
</div>

<hr/>
<?php if (empty($announcement)): ?>
	<h3>Announcement not found.</h3>
<?php else: ?>
<form action='' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='announcement_id' name='announcement_id' value='<?= $announcement->announcement_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $announcement->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls"  style="width: 797px;">
			<label class='data'><?= $announcement->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($announcement->is_published) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
