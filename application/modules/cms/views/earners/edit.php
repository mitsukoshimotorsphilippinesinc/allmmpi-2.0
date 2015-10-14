<div class='alert alert-info'><h3>Rank <?= $rank ?></h3></div>

<form action='/cms/earners/add' method='post' class='form-inline'>
	<fieldset >
		<input type="hidden" class='span4' placeholder="" name="earner_id" id="earner_id" value="<?= $earner_id ?>" readonly>
		<div class="control-group <?= $this->form_validation->error_class('member_name') ?>">
			<label class="control-label" for="member_name"><strong>Member Name <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="" name="member_name" id="member_name" value="<?= $member_name ?>">
				<p class="help-block"><?= $this->form_validation->error('member_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('group_name') ?>">
			<label class="control-label" for="group_name"><strong>Group Name <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="" name="group_name" id="group_name" value="<?= $group_name; ?>">
				<p class="help-block"><?= $this->form_validation->error('group_name'); ?></p>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	
</script>