<div class='content-area'>
	<h2>Password Reset Request</h2>
	<div class='well' >
		<?php if (isset($email_sent)) if ($email_sent) :?>
			Email sent to <?=set_value('email');?>
			<br>
			<a href="/members/signin">Return to Login screen</a>
		<?php else: ?>
			<?php if (isset($user_not_found)) if ($user_not_found) :?>
				<div class="alert alert-error">
					<h4 class="alert-heading">Error!</h4>
					Email not found.
				</div>
			<?php endif; ?>
			<form action="/members/signin/forgot_password" method="post" class="form-inline" >
				<div id="username_control" class="control-group <?= $this->form_validation->error_class('email') ?>">
					<label class="control-label" for="email">Enter your email and click on "Send Request" and follow the instructions sent to the email to reset your password.</label>
					<div class="controls">
						<input type="text" class='' placeholder="" name="email" id="email" value="<?= set_value('email') ?>" > 
						<p id="email_help" class="help-block"><?= $this->form_validation->error('email'); ?></p>
					</div>
				</div>
				<div >
					<button type="submit" class="btn btn-success">Send Request</button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>