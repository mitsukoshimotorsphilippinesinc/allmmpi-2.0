<!--h2>Delete Content  <a href='/admin/contents' class='btn btn-large' >Back</a></h2>
<hr/-->

<div class='alert alert-info'><h3>Delete Content <a class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Settings Dashboard</a></h3></div>

<?php if (empty($contents)): ?>
	<h3>Content not found.</h3>
<?php else: ?>
<form action='/admin/contents/delete/<?= $contents->content_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='content_id' name='content_id' value='<?= $contents->content_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title"><strong>Title</strong></label>
		<div class="controls">
			<label class='data'><?= $contents->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="content_type"><strong>Content Type:</strong></label>
		<div class="controls">
			<label class='data'><?= $contents->content_type ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="slug"><strong>Slug:</strong></label>
		<div class="controls">
			<label class='data'><?= $contents->slug ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="thumb"><strong>Thumbnail URL:</strong></label>
		<div class="controls">
			<label class='data'><?= $contents->thumb ?></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="body"><strong>Body:</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $contents->body ?></pre></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="excerpt"><strong>Excerpt:</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $contents->excerpt ?></pre></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active"><strong>Status:</strong></label>
		<div class="controls">
			<label class='data'><?= $contents->is_active == 1 ? 'Active' : 'Inactive'  ?></label>
		</div>
	</div>
	<!--div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Confirm Deletion</button>
		</div>
	</div-->
</fieldset>
<hr/>
<div class="controls" align="right">			
	<button type="submit" class="btn btn-primary">Delete Content</button>
	<a id='' class="btn return-btn">Cancel</a>
</div>

</form>
<?php endif; ?>

<script type="text/javascript">
	
	$(".return-btn").click(function(){		
		redirect('/admin/contents');	
		return false;
	});

</script>

