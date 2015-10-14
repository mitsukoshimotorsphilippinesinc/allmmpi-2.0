<?php
    $current_date = date("Y-m-d");

	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));

	$upload_url = $this->config->item("media_url") . "/events";
	$_upload_url = urlencode($upload_url);

?>
<div class="alert alert-info">
	<h2>View Event  <a href='/cms/event' class='btn btn-small' style="float:right;margin-top:5px;margin-right:-30px;">Back</a></h2>
</div>
<hr/>
<?php if (empty($event)): ?>
	<h3>Event not found.</h3>
<?php else: ?>
<form action='' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='featured_id' name='featured_id' value='<?= $event->featured_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $event->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="sub_title">Sub Title</label>
		<div class="controls">
			<label class='data'><?= $event->sub_title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="start_date">Start Date</label>
		<div class="controls">
			<label class='data'><?= $event->start_date ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="end_date">End Date</label>
		<div class="controls">
			<label class='data'><?= $event->end_date ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="excerpt">Excerpt</label>
		<div class="controls">
			<label class='data'><?= $event->excerpt ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls">
			<label class='data'><?= $event->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="image_filename">Teaser Image</label>
		<div class="controls">
			<?php if(!empty($event->image_filename)): ?>
				<a href="<?= $upload_url; ?>/<?= $event->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$event->image_filename); ?>"><img src="<?= $upload_url; ?>/<?= $event->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$event->image_filename); ?>" style="max-width: 200px;max-height: 200px;"></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="tags">Tags</label>
		<div class="controls">
			<label class='data'><?= $event->tags ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($event->is_published) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
