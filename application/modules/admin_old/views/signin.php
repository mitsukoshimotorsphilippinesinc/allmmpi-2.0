<section id='admin_signin'>
	<div class='row'>
		<div class='offset4 span4'>
			<h2>Vital-C<br/>Administration</h2>
			<div class='well' style='padding: 30px; margin-top:15px;'>
			<?php if (isset($invalid_login)) if ($invalid_login) :?>
				<div class="alert alert-error">
					<h4 class="alert-heading">Error!</h4>
					Invalid Username or Password.
				</div>
			<?php endif?>
				<form action="/admin/signin/authenticate" method="post" class="form-inline">
					<fieldset>
						
						<div id="username_control" class="control-group <?= $this->form_validation->error_class('username') ?>">
							<label class="control-label" for="username">Username</label>
							<div class="controls">
								<input type="text" placeholder="" name="username" id="username" value="<?= set_value('username') ?>"> 
								<p id="username_help" class="help-block"><?= $this->form_validation->error('username'); ?></p>
							</div>
						</div>
						
						<div id="password_control" class="control-group <?= $this->form_validation->error_class('password') ?>">
							<label class="control-label" for="password">Password</label>
							<div class="controls">
								<input type="password" placeholder="" name="password" id="password" value="<?= set_value('password') ?>"> 
								<p id="username_help" class="help-block"><?= $this->form_validation->error('password') ?></p>
							</div>
						</div>
						<div >
							<button type="submit" class="btn btn-primary">Submit</button>
						<div>		
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</section>