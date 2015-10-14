<div class='alert alert-info'><h2>Card Modifiers <a class='btn btn-small btn-add-new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th style="width: 130px;">Date Created</th>
			<th style="width: 120px;"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($card_modifiers as $card_modifier) : ?>
		<tr class="card_modifier_<?= $card_modifier->modifier_id ?>">
			<td class="card_name"><?= $card_modifier->modifier_name ?></td><td class="card_desc"><?= $card_modifier->description ?></td>
			<td><?= $card_modifier->insert_timestamp ?></td>
			<td>
				<button class="btn btn-primary btn-edit-card-modifier" data-id="<?= $card_modifier->modifier_id ?>" title="Edit"><i class="icon-white icon-edit"></i></button>
				<button class="btn btn-danger btn-delete-card-modifier" data-id="<?= $card_modifier->modifier_id ?>" title="Delete"><i class="icon-white icon-remove"></i></button>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script type="text/template" id="new-card-modifier-template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Name</label>
			<div class="controls">
				<input type="text" class="input input-medium new-card-modifier-name" placeholder="Card Modifier Name" value="<%= name %>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Description</label>
			<div class="controls">
				<textarea class="input new-card-modifier-desc"><%= desc %></textarea>
			</div>
		</div>
	</div>
</script>

<script type="text/javascript">
	$(document).ready(function(){

		$('.btn-edit-card-modifier').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var name = $('.card_modifier_'+id+' .card_name').html();
			var desc = $('.card_modifier_'+id+' .card_desc').html();

			var edit_card_modifier_modal = beyond.modal.create({
				title: 'Edit Card Modifier',
				html: _.template($('#new-card-modifier-template').html(), {
					name: name,
					desc: desc
				}),
				buttons: {
					'Edit Card': function(){

						var confirm_modal = beyond.modal.create({
							title: 'Edit Card Modifier',
							html: 'Are you sure you want to edit this card modifier?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Edit': function(){
									confirm_modal.hide();

									var name = $('.new-card-modifier-name').val();
									var desc = $('.new-card-modifier-desc').val();

									beyond.request({
										url: '/admin/card_management/update_card_modifier',
										data:{
											id: id,
											name: name,
											desc: desc
										},
										on_success:function(data){
											if(data.status){
												window.location = "<?= site_url('admin/card_management/modifiers') ?>";
											} else {
												var err_new_type = beyond.modal.create({
													title: 'Update Card Modifier',
													html: "Error: " + data.msg
												});
												err_new_type.show();
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
			edit_card_modifier_modal.show();
		});

		$('.btn-delete-card-modifier').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');

			var confirm_modal = beyond.modal.create({
				title: 'Delete Card Modifier',
				html: 'Are you sure you want to delete this card modifier?',
				disableClose: true,
				buttons: {
					'No': function(){
						confirm_modal.hide();
					},
					'Yes Delete': function(){
						confirm_modal.hide();

						beyond.request({
							url: '/admin/card_management/delete_card_modifier',
							data: {
								id: id
							},
							on_success: function(data){
								if(data.status) {
									window.location = "<?= site_url('admin/card_management/modifiers') ?>";
								} else {
									var err_new_type = beyond.modal.create({
										title: 'Delete Card Modifier',
										html: "Error: " + data.msg
									});
									err_new_type.show();
								}
							}
						});
					}
				}
			});
			confirm_modal.show();
		});

		$('.btn-add-new').click(function(e){
			e.preventDefault();

			var new_card_modifier_modal = beyond.modal.create({
				title: 'New Card Modifier',
				html: _.template($('#new-card-modifier-template').html(), {
					name: '',
					desc: ''
				}),
				buttons: {
					'Create': function(){

						var confirm_modal = beyond.modal.create({
							title: 'Create Card Modifier',
							html: 'Are you sure you want to add this new card modifier?',
							buttons: {
								'Yes Add': function() {
									confirm_modal.hide();

									var new_card_name = $('.new-card-modifier-name').val();
									var new_card_desc = $('.new-card-modifier-desc').val();
									new_card_modifier_modal.hide();

									beyond.request({
										url: '/admin/card_management/add_new_card_modifier',
										data: {
											name: new_card_name,
											desc: new_card_desc
										},
										on_success: function(data){
											if(data.status) {
												window.location = "<?= site_url('admin/card_management/modifiers') ?>";
											} else {
												var err_new_type = beyond.modal.create({
													title: 'Create Card Modifier',
													html: "Error: " + data.msg
												});
												err_new_type.show();
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

			new_card_modifier_modal.show();
		});

	});
</script>