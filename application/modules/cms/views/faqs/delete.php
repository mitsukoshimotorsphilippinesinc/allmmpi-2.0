<div class="alert alert-info">
	<h2>Delete FAQ  <a href='/cms/faqs' class='btn btn-small' style="float:right;margin-top:5px;margin-right:-30px;">Back</a></h2>
</div>
<hr/>
<?php if (empty($faq)): ?>
	<h3>Featured FAQ not found.</h3>
<?php else: ?>
<form action='/cms/faqs/delete/<?= $faq->faqs_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='faqs_id' name='faqs_id' value='<?= $faq->faqs_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="question">Question</label>
		<div class="controls">
			<label class='data'><?= $faq->question ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls">
			<label class='data'><?= $faq->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Active?</label>
		<div class="controls">
			<label class='data'><?= ($faq->is_published) ? 'Yes' : 'No'  ?></label>
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
