<div class="alert alert-info">
	<h2>Add New Galleries  <a href='/cms/galleries' class='btn btn-small' style="float:right;margin-top:5px;margin-right: -30px;">Back</a></h2>
</div>
<hr/>
<form action='/cms/galleries/add' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('gallery_title') ?>">
			<label class="control-label" for="gallery_title">Gallery Title <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="gallery_title" id="gallery_title" value="<?= set_value('gallery_title') ?>">
				<p class="help-block"><?= $this->form_validation->error('gallery_title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('gallery_description') ?>">
			<label class="control-label" for="gallery_description">Gallery Description </label>
			<div class="controls">
				<textarea name="gallery_description" id="gallery_description" class="input-xlarge span6" rows="6"><?= set_value('gallery_description') ?></textarea>
				<p class="help-block"><?= $this->form_validation->error('gallery_description'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_published') ?>">
			<label class="control-label" for="is_published">Published? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_published", $options, set_value('is_published'),"id='is_published' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_published'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Add New Gallery</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
</script>