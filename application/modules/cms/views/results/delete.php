<?php
	$upload_url = $this->config->item("media_url") . "/contest_prizes";
	$_upload_url = urlencode($upload_url);
?>
<h2>Delete Result  <a href='/cms/results' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($result)): ?>
	<h3>Result not found.</h3>
<?php else: ?>
<form action='/cms/results/delete/<?= $result->result_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='result_id' name='result_id' value='<?= $result->result_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="contest_name">Contest Name</label>
		<div class="controls">
			<label class='data'><?= $result->contest_name ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="prize">Prize</label>
		<div class="controls">
			<label class='data'><?= $result->prize ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php if($result->image_filename):?>
				<img id="member_image" style="width:260px; height:172px;" alt="" src="<?= $upload_url; ?>/<?= $result->image_filename ?>">
			<?php endif; ?>
			</label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($result->is_published) ? 'Yes' : 'No'  ?></label>
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
