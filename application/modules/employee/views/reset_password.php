<div class='content-area'>
	<h2>Reset Password</h2>
	<div class='well' >
		<?php if (isset($error_found)) if ($error_found) :?>
			<div class="alert alert-error">
				<h4 class="alert-heading">Error!</h4>
				<?=$error_message;?>
			</div>
		<?php else: ?>
			<div class="alert alert-success">
				<h4 class="alert-heading">Success!</h4>
				Password was successfully reset. Your new password was sent thru your company email <?=$email;?>.
			</div>
		<?php endif; ?>
	</div>
</div>