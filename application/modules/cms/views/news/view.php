<?php
    $current_date = date("Y-m-d");

	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));

	$upload_url = $this->config->item("media_url") . "/news";
	$_upload_url = urlencode($upload_url);

?>
<h2>View News  <a href='/cms/news' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($news)): ?>
	<h3>News not found.</h3>
<?php else: ?>
<form action='' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='news_id' name='news_id' value='<?= $news->featured_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $news->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="sub_title">Sub Title</label>
		<div class="controls">
			<label class='data'><?= $news->sub_title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="excerpt">Excerpt</label>
		<div class="controls">
			<label class='data'><?= $news->excerpt ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls">
			<label class='data'><?= $news->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="image_filename">Teaser Image</label>
		<div class="controls">
			<?php if(!empty($news->image_filename)): ?>
				<a href="<?= $upload_url; ?>/<?= $news->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$news->image_filename); ?>"><img src="<?= $upload_url; ?>/<?= $news->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$news->image_filename); ?>" style="max-width: 200px;max-height: 200px;"></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="tags">Tags</label>
		<div class="controls">
			<label class='data'><?= $news->tags ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($news->is_published) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
