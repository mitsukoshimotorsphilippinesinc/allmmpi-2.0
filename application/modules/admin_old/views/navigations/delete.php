<!--h2>Delete Navigation  <a href='/admin/navigations' class='btn btn-large' >Back</a></h2>
<hr/-->
<?php if (empty($navigation)): ?>
	<h3>Navigation not found.</h3>
<?php else: ?>
	
	<label>You are about to delete a Navigation having the following details:</label>
	<br/>
	
	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:120px;'><label><strong>Type</strong></label></td>
				<td><label class=''><?= ucfirst(strtolower($navigation->type)) ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>System</strong></label></td>
				<td><div class="controls">
					<label class='data span3'>
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
							?></pre>
					</label>
				</div></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Section</strong></label></td>
				<td><label class=''><?= $navigation->section ?></label></td>		
			</tr>	
			<tr>
				<td style='width:120px;'><label><strong>Ordering</strong></label></td>
				<td><label class=''><?= $navigation->ordering ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Navigation Code</strong></label></td>
				<td><label class=''><?= $navigation->code ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Name</strong></label></td>
				<td><label class=''><?= $navigation->title ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Link</strong></label></td>
				<td><label class=''><?= $navigation->url ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Is Active</strong></label></td>
				<td><label class=''><?= $navigation->is_active ? "Yes" : "No" ?></label></td>		
			</tr>								
		</tbody>
	</table>
	
	
<!--form action='/admin/navigations/delete/<?= $navigation->navigation_id ?>' method='post' class='form-inline'>
<fieldset >
	<input type='hidden' id='navigation_id' name='navigation_id' value='<?= $navigation->navigation_id ?>' />
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_type"><strong>Type</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= ucfirst(strtolower($navigation->type)) ?></pre></label>
		</div>
	</div>
	<div id="navigation_description_control" class="control-group">
		<label class="control-label" for="navigation_system_code"><strong>System</strong></label>
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
					?></pre>
			</label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_parent_id"><strong>Type</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->section ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_ordering"><strong>Ordering</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->ordering ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_code"><strong>Navigation Code</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->code ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_title"><strong>Name</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->title ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_url"><strong>Link</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->url ?></pre></label>
		</div>
	</div>
	<div id="navigation_name_control" class="control-group">
		<label class="control-label" for="navigation_is_active"><strong>Active?</strong></label>
		<div class="controls">
			<label class='data span4'><pre class='prettyprint'><?= $navigation->is_active ? "Yes" : "No" ?></pre></label>
		</div>
	</div>

</fieldset>
</form-->
<?php endif; ?>
