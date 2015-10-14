<div class='alert alert-info'><h2>Conversion Rate</h2></div>
<div style="width: 600px; margin: 0 auto;">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Type</th>
				<th>C Point Value</th>
				<th style="width: 45px;"></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($cpoint_ratios as $ratio) : ?>
			<tr>
				<td><?= str_replace("_", " ", $ratio->from_type) ?></td>
				<td><?= $ratio->cpoint_ratio ?></td>
				<td>
					<button class="btn btn-primary btn-edit-cpoint-ratio" data-id="<?= $ratio->ratio_id ?>" data-type="<?= str_replace("_", " ", $ratio->from_type) ?>" data-value="<?= $ratio->cpoint_ratio ?>" title="Edit"><i class="icon-edit icon-white"></i></button>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<script type="text/template" id="edit-cpoint-ratio-template">
	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label"><%= type %></label>
			<div class="controls">
				<input type="text" class="input input-small cpoint-ratio-new-value" value="<%= value %>" />
			</div>
		</div>
	</div>
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.btn-edit-cpoint-ratio').click(function(e){
			e.preventDefault();
			var ratio_data = $(this).data();
			var edit_cpoint_ratio_modal = beyond.modal.create({
				title: 'Edit C Point Conversion for ' + $(this).data('type'),
				html: _.template($('#edit-cpoint-ratio-template').html(), ratio_data),
				width: 500,
				buttons: {
					'Edit': function(){
						var confirm_modal = beyond.modal.create({
							title: 'Edit C Point Conversion for ' + ratio_data.type,
							html: 'Are you sure you want to edit this conversion?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Edit': function(){
									confirm_modal.hide();

									beyond.request({
										url: '/admin/cpoints/edit_ratio',
										data: {
											id: ratio_data.id,
											new_val: $('.cpoint-ratio-new-value').val()
										},
										on_success: function(data){
											if(data.status) {
												var success_modal = beyond.modal.create({
													title: 'Edit C Point Conversion for ' + ratio_data.type,
													html: 'Edit Success',
													disableClose: true,
													buttons: {
														'Ok': function(){
															window.location = "<?= site_url('admin/cpoints/conversion_rate') ?>";
														}
													}
												});
												success_modal.show();
											} else {
												var error_modal = beyond.modal.create({
													title: 'Edit C Point Conversion for ' + ratio_data.type,
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
			edit_cpoint_ratio_modal.show();
		});
	});
</script>