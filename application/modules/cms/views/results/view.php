<?php
	$upload_url = $this->config->item("media_url") . "/results";
	$_upload_url = urlencode($upload_url);
?>
<h2>View Result  <a href='/cms/results' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($result)): ?>
	<h3>Result not found.</h3>
<?php else: ?>
<form action='' method='' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='result_id' name='result_id' value='<?= $result->result_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="result">Result</label>
		<div class="controls">
			<label class='data'><?= $result->result ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="member_name">Member Name</label>
		<div class="controls">
			<label class='data'><?= $result->member_name ?></label>
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
</fieldset>
</form>
<?php endif; ?>
