<div class='alert alert-info'><h3>Delete Setting <a class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Settings Dashboard</a></h3></div>

<?php if (empty($setting)): ?>
	<h3>Setting not found.</h3>
<?php else: ?>
<form action='/admin/setting/delete/<?= $setting->slug ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='slug' name='slug' value='<?= $setting->slug ?>' />
	
	<div class="control-group ">
		<label class="control-label" for="setting_name"><strong>Slug</strong></label>
		<div class="controls">
			<label class='data'><?= $setting->slug ?></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="setting_value"><strong>Value</strong></label>
		<div class="controls">
			<label class='data span8'><pre class='prettyprint'><?= $setting->value ?></pre></label>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="setting_value"><strong>Default</strong></label>
		<div class="controls">
			<label class='data span8'><pre class='prettyprint'><?= (empty($setting->default))? "&nbsp;" : $setting->default; ?></pre></label>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="is_current"><strong>System Code </strong></label>
		<div class="controls">
			<label class='data'><?= $setting->system_code ?></label>
		</div>
	</div>	
</fieldset>

<hr/>
<div class="controls" align="right">			
	<button type="submit" class="btn btn-primary">Delete Setting</button>
	<a id='' class="btn return-btn">Cancel</a>
</div>
</form>

<?php endif; ?>

<script type="text/javascript">
	
	$(".return-btn").click(function(){		
		redirect('/admin/setting');	
		return false;
	});

</script>
