<div class='alert alert-info'>
    <h2>Account Voucher Types</h2>
</div>

<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style="width:140px;">Account Voucher Code</th>
			<th> Name</th>
			<th style="width:170px;">Allowed for Inactive Account</th>
			<th style='width:30px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if(empty($account_voucher_types)):?>
			<tr><td colspan='8' style='text-align:center;'><strong>No Records Found</strong></td></tr>
		<?php else: ?>
			<?php foreach ($account_voucher_types as $vp): ?>
				<tr data-voucher_product_id='<?= $vp->voucher_type_id ?>' data-voucher_product_name='<?= $vp->code ?>' data-voucher_type_id='<?=$vp->voucher_type_id?>'>
					<td><?=$vp->code?></td>
					<td><?=$vp->name?></td>
					<td>
						<?php if($vp->allow_inactive == 1): ?>
							Yes
						<?php elseif($vp->allow_inactive == 0): ?>
							No
						<?php endif; ?>
					</td>
					<td>
						<?php 
							if($this->users_model->is_user_allowed_by_privilege_code($this->user->user_id,'SUPER ADMIN')) { 
								if($vp->allow_inactive == 1): ?>
								<a class='btn btn-small btn-danger btn-action' data-voucher_type_id='<?=$vp->voucher_type_id?>' data-action="0">Disable</a>
								<?php else: ?>
								<a class='btn btn-small btn-success btn-action' data-voucher_type_id='<?=$vp->voucher_type_id?>' data-action="1">Allow</a>
								<?php endif; 
						 } ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		$('.btn-action').click(function(e){
			e.preventDefault();
			
			var confirm_message = '';
			var account_voucher_type_id = $(this).data('voucher_type_id');
			var action = $(this).data('action');

			if(action)
				confirm_message = 'Are you sure you want to allow inactive members to use this voucher type?';
			else
				confirm_message = 'Are you sure you want to disable inactive members to use this voucher type?';

			var confirm_modal = b.modal.create({
				title: 'Confirm Action',
				html: confirm_message,
				disableClose: true,
				buttons: {
					'Close' : function(){
						confirm_modal.hide();
					},
					'Confirm': function(){
						confirm_modal.hide();
						b.request({
							url: '/admin/account_voucher_types/update_allow_inactive',
							data: {
								'account_voucher_type_id': account_voucher_type_id,
								'action': action
							},
							on_success: function(data) {
								if(data.status == 'ok') {
									var success_modal = b.modal.create({
										title: 'Success',
										html: data.msg,
										disableClose: true,
										buttons: {
											'Close': function() {
												success_modal.hide();
												window.location.reload();
											}
										}
									});
									success_modal.show();
								}else if(data.status == 'error') {
									var error_modal = b.modal.create({
										title: 'Error Encountered',
										html: data.msg
									});
									error_modal.show();
								}
							}
						});
					}
				}
			});
			confirm_modal.show();
		});
	});
</script>
