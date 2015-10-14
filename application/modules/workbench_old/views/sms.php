<div class='content-area'>
	<h2>SMS</h2>
	<div class='well' >
		
		<?php if ($sms_qued) :?>
			SMS sent to <?=set_value('mobile');?>	
		<?php else: ?>
			<?php if (isset($mobile_error)) if ($mobile_error) :?>
				<div class="alert alert-error">
					<h4 class="alert-heading">Error!</h4>
					Email not found.
				</div>
			<?php endif; ?>
			<form action="/workbench/sms" method="post" class="form-inline" >
				<div id="mobile_control" class="control-group <?= $this->form_validation->error_class('mobile'); ?>">
					<label class="control-label" for="mobile">Mobile Number</label>
					<div class="controls">
						<input type="text" class='' placeholder="" name="mobile" id="mobile" value="<?= set_value('mobile') ?>" > 
						<p id="mobile_help" class="help-block"><?= $this->form_validation->error('mobile'); ?></p>
					</div>
				</div>
				<div id="message_control" class="control-group <?= $this->form_validation->error_class('message'); ?>">
					<label class="control-label" for="mobile">Message</label>
					<div class="controls">
						<textarea class='' placeholder="" name="message" id="message" ><?= set_value('message') ?></textarea> 
						<p id="message_help" class="help-block"><?= $this->form_validation->error('message'); ?></p>
					</div>
				</div>
				<div >
					<button type="submit" class="btn btn-success">Send SMS</button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>