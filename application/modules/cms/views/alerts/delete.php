<div class="alert alert-info">
	<h2>Delete Announcement  <a href='/cms/alerts' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;">Back</a></h2>
</div>
<hr/>
<?php if (empty($alert_message)): ?>
	<h3>Alert Message not found.</h3>
<?php else: ?>
<form action='/cms/alerts/delete/<?= $alert_message->message_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='message_id' name='message_id' value='<?= $alert_message->message_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $alert_message->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls">
			<label class='data'><?= $alert_message->content ?></label>
		</div>
	</div>
	<?php if($alert_message->start_timestamp!="0000-00-00 00:00:00"): ?>
	<div class="control-group ">
		<label class="control-label" for="is_published">Dates</label>
		<div class="controls">
			<label class='data'><?= $alert_message->start_timestamp  ?> to <?= $alert_message->end_timestamp  ?></label>
		</div>
	</div>
	<?php endif; ?>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($alert_message->is_visible) ? 'Yes' : 'No'  ?></label>
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
