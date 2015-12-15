<section id='admin_signin'>
	<div class='row'>
		<div class='offset4 span4'>
			<div><img src="/assets/media/mitsu_logo_min.png" style="height:140px;max-width:200%;margin-left:-135px;" alt=""></div>			
			<h3 style="font-family:Consolas;margin-left:5px;">Systems Administration Portal</h3>
			<div class='well' style='padding: 30px; margin-top:15px;'>
			<?php if (isset($invalid_login)) if ($invalid_login) :?>
				<div class="alert alert-error">
					<h4 class="alert-heading">Error!</h4>
					<?php if(isset($active_member) && !$active_member) : ?>
						Your Account is Inactive.
					<?php else : ?>
						Invalid Username or Password.
					<?php endif; ?>
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