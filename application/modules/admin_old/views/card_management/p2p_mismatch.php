<div class='alert alert-info'>
	<h2>P2P Mismatch Log</h2>
</div>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Log ID</th>
			<th>Card ID</th>
			<th>Account ID</th>
			<th>Missing Position</th>
			<th>Waiting Account ID</th>
			<th>Waiting Product ID</th>
			<th>Status</th>
			<th>Date</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($mismatches as $mismatch) : ?>
		<tr>
			<td><?= $mismatch->log_id ?></td>
			<td><?= $mismatch->log_data->trigger_card_id ?></td>
			<td><?= $mismatch->log_data->trigger_account_id ?></td>
			<td><?= $mismatch->log_data->missing_position ?></td>
			<td><?= $mismatch->log_data->waiting_account_id ?></td>
			<td><?= $mismatch->log_data->waiting_product_id ?></td>
			<td><?= $mismatch->status ?></td>
			<td><?= $mismatch->insert_timestamp ?></td>
			<td>
				<?php if($mismatch->status == "PENDING") : ?>
				<button class="btn btn-primary btn-mismatch-resolve" title="Fix Credited Items" data-id="<?= $mismatch->log_id ?>"><i class="icon-cog icon-white"></i></button>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		$('.btn-mismatch-resolve').click(function(e){
			e.preventDefault();
			var log_id = $(this).data('id');

			var resolve_modal = beyond.modal.create({
				title: 'Resolve Mismatch ' + log_id,
				html: _.template($('#resolve-template').html(), {}),
				buttons: {
					'Apply Fix': function(){

						var confirm_modal = beyond.modal.create({
							title: 'Resolve Mismatch ' + log_id,
							html: 'Are you sure you want to apply this fix?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Apply Fix': function(){
									confirm_modal.hide();

									var missing_account_id = $('.resolve-missing-account-id').val();
									var missing_product_id = $('.resolve-missing-product-id').val();

									beyond.request({
										url: '/admin/card_management/fix_mismatch',
										data: {
											missing_account_id: missing_account_id,
											missing_product_id: missing_product_id,
											log_id: log_id
										},
										on_success: function(data){
											if(data.status) {
												resolve_modal.hide();
												var success_modal = beyond.modal.create({
													title: 'Resolve Mismatch ' + log_id,
													html: 'Mismatch Fixed',
													disableClose: true,
													buttons: {
														'Ok': function(){
															success_modal.hide();
															window.location = "<?= site_url('admin/card_management/p2p_mismatch') ?>"
														}
													}
												});
												success_modal.show();
											} else {
												var error_modal = beyond.modal.create({
													title: 'Resolve Mismatch ' + log_id,
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

					}
				}
			});
			resolve_modal.show();
		});
	});
</script>

<script type="text/template" id="resolve-template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Missing Account ID</label>
			<div class="controls">
				<input type="text" class="input input-medium resolve-missing-account-id" placeholder="Account ID" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Missing Product ID</label>
			<div class="controls">
				<input type="text" class="input input-medium resolve-missing-product-id" placeholder="Product ID" />
			</div>
		</div>
	</div>
</script>