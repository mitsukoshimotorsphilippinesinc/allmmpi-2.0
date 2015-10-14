<?php
    $current_date = date("Y-m-d");

	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));

	$upload_url = $this->config->item("media_url") . "/package_types";
	$_upload_url = urlencode($upload_url);

?>
<div class="alert alert-info">
<h2>View Package  <a href='/cms/packages' class='btn btn-small' style="float:right;margin-top: 5px; margin-right: -30px;">Back</a></h2>
</div>
<hr/>
<?php if (empty($package)): ?>
	<h3>Package not found.</h3>
<?php else: ?>
<form action='' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='package_id' name='package_id' value='<?= $package->package_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $package->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls">
			<label class='data'><?= $package->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="image_filename">Teaser Image</label>
		<div class="controls">
			<?php if(!empty($package->image_filename) && is_file("{$upload_url}/{$package->image_filename}")): ?>
				<a href="<?= $upload_url; ?>/<?= $package->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$package->image_filename); ?>"><img src="<?= $upload_url; ?>/<?= $package->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$package->image_filename); ?>" style="max-width: 200px;max-height: 200px;"></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('bg_color') ?>">
		<label class="control-label" for="bg_color">Background Color</label>
		<div class="controls">
			<div style="background-color: <?= $package->bg_color?>; height: 50px; width: 70px;"></div>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($package->is_published) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
