<h2>Delete Navigation  <a href='/admin/navigations' class='btn btn-large' >Back</a></h2>
<hr/>
<?php if (empty($navigation)): ?>
	<h3>Navigation not found.</h3>
<?php else: ?>
<form action='' method='post' class='form-horizontal'>
<fieldset >
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_type">Type</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= ucfirst(strtolower($navigation->type)) ?></pre></label>
		</div>
	</div>
	<div id="navigation_description_control" class="control-group">
		<label class="control-label" for="navigation_system_code">System</label>
		<div class="controls">
			<label class='data span4'>
				<pre class='prettyprint'><?php
					if($navigation->system_code == "inventory")
					{
						echo "Inventory";
					}
					elseif($navigation->system_code == "network")
					{
						echo "MLM Network";
					}
					elseif($navigation->system_code == "webpos")
					{
						echo "Web-POS";
					}
					elseif($navigation->system_code == "system")
					{
						echo "System";
					}
					elseif($navigation->system_code == 'mis')
					{
						echo "MIS Reports";
					}
					?></pre>
			</label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_parent_id">Type</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->section ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_ordering">Ordering</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->ordering ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_code">Navigation Code</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->code ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_title">Name</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->title ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_url">Link</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->url ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_is_active">Active?</label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->is_active ? "Yes" : "No" ?></pre></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
