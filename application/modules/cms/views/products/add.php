<h2>Add New Result  <a href='/cms/results' class='btn btn-small' >Back</a></h2>
<hr/>
<form action='/cms/results/add' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('result') ?>">
			<label class="control-label" for="result">Result <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Result" name="result" id="result" value="<?= set_value('result') ?>">
				<p class="help-block"><?= $this->form_validation->error('result'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('member_name') ?>">
			<label class="control-label" for="member_name">Member Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Member Name" name="member_name" id="member_name" value="<?= set_value('member_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('member_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_featured') ?>">
			<label class="control-label" for="is_featured">Featured? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_featured", $options, set_value('is_featured'),"id='is_featured' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_featured'); ?></p>
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
				<button type="submit" class="btn btn-primary">Add New Result</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript"></script>