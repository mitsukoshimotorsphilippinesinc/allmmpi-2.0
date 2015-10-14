<h2>Delete Page  <a href='/cms/pages' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($page)): ?>
	<h3>Page not found.</h3>
<?php else: ?>
<form action='/cms/pages/delete/<?= $page->content_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='content_id' name='content_id' value='<?= $page->content_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $page->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="content_type">Content Type</label>
		<div class="controls">
			<label class='data'><?= $page->content_type ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="slug">Slug</label>
		<div class="controls">
			<label class='data'><?= $page->slug ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="thumb">Thumbnail URL</label>
		<div class="controls">
			<label class='data'><?= $page->thumb ?></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="body">Body</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $page->body ?></pre></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="excerpt">Excerpt</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $page->excerpt ?></pre></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active">Status</label>
		<div class="controls">
			<label class='data'><?= $page->is_active == 1 ? 'Active' : 'Inactive'  ?></label>
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
