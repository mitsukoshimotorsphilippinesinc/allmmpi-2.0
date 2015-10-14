<div class='alert alert-info'>
	<h2>
		Card Series 
		<a class='btn btn-small btn-add-new' style='float:right;margin-right:-30px;margin-top:5px;'>
			<i class="icon-plus"></i>
			<span> Add New</span>
		</a>

		<a class='btn btn-small btn-view-deleted' style='float:right; margin-right:10px; margin-top:5px;'>
			<i class="icon-trash"></i>
			<span> View Deleted</span>
		</a>

		<a class='btn btn-small btn-export-generation' style='float:right; margin-right:10px; margin-top:5px;'>
			<i class="icon-arrow-down"></i>
			<span> Export Generation</span>
		</a>

		<a class='btn btn-small btn-activate-cards' style='float:right; margin-right:10px; margin-top:5px;'>
			<i class="icon-check"></i>
			<span> Activate Cards</span>
		</a>

		<a class='btn btn-small btn-verify-card' style='float:right; margin-right:10px; margin-top:5px;'>
			<i class="icon-eye-open"></i>
			<span> Verify Card</span>
		</a>
	</h2>
</div>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th style="width: 90px;">Series Number</th>
			<th style="width: 90px;">Count</th>
			<th>Card Types</th>
			<th style="width: 50px;">Type</th>
			<th style="width: 130px;">Date Created</th>
			<th style="width: 120px;"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($card_series as $cards) : ?>
		<tr>
			<td><?= $cards->series_number ?></td>
			<td><?= (isset($card_series_counts[$cards->series_number]))?$card_series_counts[$cards->series_number]:'' ?></td>
			<td class="card-series-type-table-<?= $cards->card_series_id ?>">
				<?php if(isset($card_series_types[$cards->card_series_id])) : ?>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th style="width: 60px;">Code</th>
							<th>Starting Index</th>
							<th>Ending Index</th>
							<th style="width: 60px;">Unused</th>
							<th style="width: 60px;">Used</th>
							<th style="width: 60px;">Total</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($card_series_types[$cards->card_series_id] as $type) : ?>
						<tr>
							<td><?= $type->type_code ?></td>
							<td><?= ($type->qty > 0)?$type->starting_index:'' ?></td>
							<td><?= ($type->qty > 0)?$type->ending_index:'' ?></td>
							<td class="card-series-type-<?= $cards->card_series_id . "-" . $type->card_type_id . "-" . $type->card_series_type_id ?>-active"><?= $type->qty-$type->used; ?></td>
							<td class="card-series-type-<?= $cards->card_series_id . "-" . $type->card_type_id . "-" . $type->card_series_type_id ?>-used"><?= $type->used; ?></td>
							<td><?= $type->qty ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>
			</td>
			<td><?= ((boolean)$cards->is_package)?'Package':'Sales' ?></td>
			<td><?= $cards->insert_timestamp ?></td>
			<td>
				<button class="btn btn-primary btn-card-type-allotment" data-id="<?= $cards->card_series_id ?>" data-code="<?= $cards->series_number ?>" data-package="<?= $cards->is_package ?>" title="Card Type Allotment"><i class="icon-align-left icon-white"></i></button>
				<button class="btn btn-primary btn-series-generation" data-id="<?= $cards->card_series_id ?>" data-code="<?= $cards->series_number ?>" title="Series Generation"><i class="icon-gift icon-white"></i></button>
				<?php if($this->users_model->is_user_allowed_by_privilege_code($this->user->user_id,'SUPER ADMIN')) { ?>
				<button class="btn btn-danger btn-delete-card-series" data-id="<?= $cards->card_series_id ?>" data-code="<?= $cards->series_number ?>" title="Delete Series"><i class="icon-remove icon-white"></i></button>
				<?php } ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script type="text/template" id="new_card_template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Series Number</label>
			<div class="controls">
				<input type="text" class="input input-small new-series-number" placeholder="XX" style="display:block" maxlength="3" />
				<span class='label label-important' id='card-series-number-error' style='display:none;'>Invalid Series Number.</span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Series Type</label>
			<div class="controls">
				<select class="input input-medium new-series-type">
					<option value="1">Package Series</option>
					<option value="0">Sales Series</option>
				</select>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="new_card_type_template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Select Card Type</label>
			<div class="controls">
				<select class="new-card-type">
					<% for(i in items) { %>
					<option value="<%= i %>"><%= items[i] %></option>
					<% } %>
				</select>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="generation_history_template">
	<div class="row-fluid">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Starting</th>
					<th>Ending</th>
					<th>Count</th>
					<th>Date</th>
					<th style="width: 40px;"></th>
				</tr>
			</thead>
			<tbody>
				<% for(i in series_history) { %>
				<tr>
					<td><%= series_history[i].starting_index %></td>
					<td><%= series_history[i].ending_index %></td>
					<td><%= series_history[i].qty %></td>
					<td><%= series_history[i].insert_timestamp %></td>
					<td><button class="btn btn-primary btn-history-export-generation" data-starting="<%= series_history[i].starting_index %>" data-ending="<%= series_history[i].ending_index %>" data-card="<%= series_history[i].card_type %>" title="Export Generation"><i class="icon-arrow-down icon-white"></i></button></td>
				</tr>
				<% } %>
			</tbody>
		</table>
	</div>
</script>

<script type="text/template" id="new_generation_history_template">
	<% var card_types = <?= json_encode($card_types) ?> %>
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Qty</label>
			<div class="controls">
				<input type="text" class="input input-small new-series-generation-qty" placeholder="1000" style="display:block" />
				<span class='label label-important' id='card-series-quantity-error' style='display:none;'>Invalid quantity.</span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Starting Index</label>
			<div class="controls">
				<%= code %>&nbsp;<input type="text" class="input input-small new-series-generation-starting-index" placeholder="XXXXXXXX" data-code="<%= code %>" maxlength="<%= maxlength %>"/><br />
				<span class='label label-important' id='card-series-starting-index-error' style='display:none;'>Invalid starting index.</span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Default Card Type</label>
			<div class="controls">
				<select class="input input-large new-series-generation-card-type">
					<% for(i in card_types) { %>
					<% if(card_types[i].is_package != type) continue; %>
					<option value="<%= card_types[i].card_type_id %>"><%= card_types[i].code %> - <%= card_types[i].name %></option>
					<% } %>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"></label>
			<div class="controls">
				<button class="btn btn-primary btn-generate-card-series">Generate New Card Series</button>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="card_type_allotment_template">
	<h4>Current Card Types</h4>
	<%= $('.card-series-type-table-'+id).html() %>
	<h4>Series Generation History</h4>
	<div class="allotment-generation-history"></div>
	<% get_series_generation_history(id, function(data){
		$('.allotment-generation-history').html(_.template($('#generation_history_template').html(), {
			series_history: data.data.history
		}));
	}) %>
	<h4>New Series Type</h4>
	<table class="table">
		<tr>
			<td>
				<div class="control-group">
					<label class="control-label">Code</label>
					<div class="controls">
						<select class="input input-large new-card-series-type-id" data-id="<%= id %>">
							<?php foreach($card_types as $card_type) : ?>
							<option value="<?= $card_type->card_type_id ?>" class="card-type-allotment-code-selection" data-package="<?= $card_type->is_package ?>"><?= $card_type->code ?> - <?= $card_type->name ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</td>
			<td>
				<div class="control-group">
					<label class="control-label">Starting Index</label>
					<div class="controls">
						<%= code %> <input type="text" class="input input-small new-card-series-type-starting-index" placeholder="XXXXXXXX"  style="margin: 0px"  maxlength="<%= maxlength %>" /><br />
						<span class='label label-important' id='card-series-allotment-starting-index-error' style='display:none;'>Invalid starting index.</span>
			
					</div>
				</div>
			</td>
			<td>
				<div class="control-group">
					<label class="control-label">Ending Index</label>
					<div class="controls">
						<%= code %> <input type="text" class="input input-small new-card-series-type-ending-index" placeholder="XXXXXXXX" style="margin: 0px" maxlength="<%= maxlength %>"  /><br />
						<span class='label label-important' id='card-series-allotment-ending-index-error' style='display:none;'>Invalid ending index.</span>
			
					</div>
				</div>
			</td>
		</tr>
	</table>
</script>

<script type="text/template" id="view-deleted-series">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Series Number</th>
				<th>Series Type</th>
				<th style="width: 40px;"></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in card_series) { %>
				<tr class="restore_card_series_<%= card_series[i].card_series_id %>">
					<td>
						<%= card_series[i].series_number %>
 					</td>
					<td>
						<% if(card_series[i].is_package == 1)  { %> Package Series <% } else { %> Sales Series <% } %>

 					</td>
 					
 					<td>
						<button class="btn btn-restore btn-primary" data-id="<%= card_series[i].card_series_id %>">Restore</button>
 					</td>
				</tr>
			<% } %>
		</tbody>
	</table>
</script>

<script type="text/template" id="export-generation-template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Starting Index</label>
			<div class="controls">
				<input type="text" class="export-generation-starting-index" placeholder="65XXXXXXXX" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Ending Index</label>
			<div class="controls">
				<input type="text" class="export-generation-ending-index" placeholder="65XXXXXXXX" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Card Type</label>
			<div class="controls">
				<select class="export-generation-card-type">
					<option value="sp">SP</option>
					<option value="rs">RS</option>
				</select>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="activate-cards-template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Starting Index</label>
			<div class="controls">
				<input type="text" class="activate-cards-starting-index" placeholder="65XXXXXXXX" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Ending Index</label>
			<div class="controls">
				<input type="text" class="activate-cards-ending-index" placeholder="65XXXXXXXX" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Card Type</label>
			<div class="controls">
				<select class="activate-cards-card-type">
					<option value="sp">SP</option>
					<option value="rs">RS</option>
				</select>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="verify-card-template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Card ID</label>
			<div class="controls">
				<input type="text" class="input input-medium verify-card-id" />
			</div>
		</div>
	</div>
	<div class="verify-card-info"></div>
</script>

<script type="text/template" id="card-data-template">
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td>Card ID</td>
				<td><%= card_data.card_id %></td>
			</tr>
			<tr>
				<td>Card Code</td>
				<td><%= card_data.card_code %></td>
			</tr>
			<tr>
				<td>Status</td>
				<td><%= card_data.status %></td>
			</tr>
			<tr>
				<td>Type</td>
				<td><%= card_data.type %></td>
			</tr>
		</tbody>
	</table>
</script>

<script type="text/javascript">
	var post_load_active_used = [
		<?php foreach($card_series as $cards) : ?>
			<?php if(isset($card_series_types[$cards->card_series_id])) : ?>
				<?php foreach($card_series_types[$cards->card_series_id] as $type) : ?>
		{
			series_id: '<?= $cards->card_series_id ?>',
			type_id: '<?= $type->card_type_id ?>',
			series_type_id: '<?= $type->card_series_type_id ?>',
			total: <?= $type->qty ?>
		},
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	];

	var process_post_load_active_used = function(){
		if(post_load_active_used.length > 0) {
			beyond.request({
				with_overlay: false,
				url: '/admin/card_management/get_series_type_active_count',
				data: post_load_active_used[0],
				on_success: function(data){
					if(data.status) {
						var active = data.data.active;
						var used = post_load_active_used[0].total - active;
						var selector = ".card-series-type-"+post_load_active_used[0].series_id+"-"+post_load_active_used[0].type_id+"-"+post_load_active_used[0].series_type_id+"-";
						$(selector+'active').html(active);
						$(selector+'used').html(used);
						post_load_active_used.shift();
						process_post_load_active_used();
					}
				}
			});
		}
	};

	var get_series_generation_history = function(id, cb) {
		beyond.request({
			url: '/admin/card_management/get_series_generation_history',
			data: {
				id: id
			},
			on_success: function(data){
				if(typeof cb == "function") cb(data);

				$('.btn-history-export-generation').click(function(e){
					e.preventDefault();
					var starting_index = $(this).data('starting');
					var ending_index = $(this).data('ending');
					var card_type = $(this).data('card');

					export_generation(starting_index, ending_index, card_type);
				});
			}
		});
	};

	var export_generation = function(starting_index, ending_index, card_type) {
		beyond.request({
			url: '/admin/card_management/export_generation',
			data: {
				starting_index: starting_index,
				ending_index: ending_index,
				card_type: card_type
			},
			on_success: function(data){
				if(data.status) {
					var fileloc = "<?= site_url('/assets/media/tmp/') ?>/" + data.data.filename;
					var success_modal = beyond.modal.create({
						title: 'Export Generation',
						html: 'Export Success'
					});
					success_modal.show();
					window.location = fileloc;
				} else {
					var error_modal = beyond.modal.create({
						title: 'Export Generation',
						html: data.msg,
						disableClose: true,
						buttons: {
							'Ok': function(){
								error_modal.hide();
								$('.btn-export-generation').click();
							}
						}
					});
					error_modal.show();
				}
			}
		});
	}

	$(document).ready(function(){
		$('.btn-verify-card').click(function(e){
			e.preventDefault();

			var verify_card_modal = beyond.modal.create({
				title: 'Verify Card',
				html: _.template($('#verify-card-template').html(), {}),
				width: 450,
				buttons: {
					'Verify Card': function(){
						var card_id = $('.verify-card-id').val();

						beyond.request({
							url: '/admin/card_management/verify_card',
							data: {
								card_id: card_id
							},
							on_success: function(data){
								if(data.status) {
									$('.verify-card-info').html(_.template($('#card-data-template').html(), { card_data: data.data.card_data }));
								} else {
									var error_modal = beyond.modal.create({
										title: 'Verify Card',
										msg: data.msg
									});
									error_modal.show();
								}
							}
						});
					}
				}
			});
			verify_card_modal.show();
		});

		$('.btn-activate-cards').click(function(e){
			e.preventDefault();

			var activate_cards_modal = beyond.modal.create({
				title: 'Activate Cards',
				html: _.template($('#activate-cards-template').html(), {}),
				buttons: {
					'Activate': function(){
						var starting_index = $('.activate-cards-starting-index').val();
						var ending_index = $('.activate-cards-ending-index').val();
						var card_type = $('.activate-cards-card-type').val();

						var confirm_modal = beyond.modal.create({
							title: 'Activate Cards',
							html: 'Are you sure you want to activate these cards?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Activate': function(){
									confirm_modal.hide();
									activate_cards_modal.hide();

									beyond.request({
										url: '/admin/card_management/activate_cards',
										data: {
											starting_index: starting_index,
											ending_index: ending_index,
											card_type: card_type
										},
										on_success: function(data){
											if(data.status) {
												var success_modal = beyond.modal.create({
													title: 'Activate Cards',
													html: 'Cards was activated successfully'
												});
												success_modal.show();
											} else {
												var error_modal = beyond.modal.create({
													title: 'Activate Cards',
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
			activate_cards_modal.show();
		});

		$('.btn-export-generation').click(function(e){
			e.preventDefault();
			var export_generation_modal = beyond.modal.create({
				title: 'Export Generation',
				html: _.template($('#export-generation-template').html(), {}),
				buttons: {
					'Export': function(){
						var starting_index = $('.export-generation-starting-index').val();
						var ending_index = $('.export-generation-ending-index').val();
						var card_type = $('.export-generation-card-type').val();

						var confirm_modal = beyond.modal.create({
							title: 'Export Generation',
							html: 'Are you sure you want to export this series?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Export': function(){
									confirm_modal.hide();
									export_generation_modal.hide();
									export_generation(starting_index, ending_index, card_type);
								}
							}
						});
						confirm_modal.show();
					}
				}
			});
			export_generation_modal.show();
		});

		$('.btn-card-type-allotment').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var code = $(this).data('code');
			var maxlength = 10 - String(code).length;
			var is_package = $(this).data('package');

			var card_type_allotment_modal = beyond.modal.create({
				title: 'Card Type Allotment for ' + code + ' Card Series',
				html: _.template($('#card_type_allotment_template').html(), {
					id: id,
					code: code,
					maxlength: maxlength
				}),
				buttons: {
					'Add Card Type Allotment': function() {
						var series_id = $('.new-card-series-type-id').data('id');
						var type_id = $('.new-card-series-type-id').val();
						var starting_index = $('.new-card-series-type-starting-index').val();
						var ending_index = $('.new-card-series-type-ending-index').val();

						if(checkAllotmentSeries(starting_index, ending_index, code)) return;

						var confirm_modal = beyond.modal.create({
							title: 'New Card Type',
							html: 'Are you sure you want to add this card type?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Add New Type': function(){
									confirm_modal.hide();

									beyond.request({
										url: '/admin/card_management/add_card_series_type_allotment',
										data: {
											series_id: series_id,
											type_id: type_id,
											starting_index: starting_index,
											ending_index: ending_index
										},
										on_success: function(data){
											if(data.status){
												window.location = "<?= site_url('admin/card_management/card_series') ?>"
											} else {
												var err_series_card_allotment = beyond.modal.create({
													title: 'New Card Type',
													html: "Error: " + data.msg
												});
												err_series_card_allotment.show();
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
			card_type_allotment_modal.show();

			$('.card-type-allotment-code-selection[data-package!="'+is_package+'"]').remove();
		});

		$('.btn-series-generation').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var code = $(this).data('code');
			var maxlength = 10 - String(code).length;

			get_series_generation_history(id, function(data){
				if(data.status) {
					var generation_history_modal = beyond.modal.create({
						title: 'Series ' + data.data.code + ' Generation History',
						html: _.template($('#generation_history_template').html(), {
							series_history: data.data.history
						})+_.template($('#new_generation_history_template').html(), {code: code, maxlength: maxlength, type: data.data.type})
					});
					generation_history_modal.show();

					$('.btn-generate-card-series').click(function(e){
						e.preventDefault();
						var qty = $('.new-series-generation-qty').val();
						var starting_index = $('.new-series-generation-starting-index').val();
						var card_type = $('.new-series-generation-card-type').val();

						if(checkGenerationSeries(qty,starting_index,code))
							return;

						var confirm_modal = beyond.modal.create({
							title: 'Generate Card Series',
							html: 'Are you sure you want to generate this card series?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Generate': function(){
									confirm_modal.hide();

									beyond.request({
										url: '/admin/card_management/generate_card_series',
										data: {
											series_id: id,
											qty: qty,
											starting_index: starting_index,
											card_type: card_type
										},
										on_success: function(data){
											if(data.status) {
												generation_history_modal.hide();
												var card_generation_success_modal = beyond.modal.create({
													title: 'Card Series Generation',
													html: 'The Card Series is now being generated.',
													disableClose: true,
													buttons: {
														'Ok': function(){
															window.location = "<?= site_url('admin/card_management/card_series') ?>";
														}
													}
												});
												card_generation_success_modal.show();
											} else {
												var err_delete_series = beyond.modal.create({
													title: 'Card Series Generation',
													html: "Error: " + data.msg
												});
												err_delete_series.show();
											}
										}
									});
								}
							}
						});
						confirm_modal.show();
					});
				} else {
					var err_series_gen_history = beyond.modal.create({
						title: 'Card Series Generation History',
						html: "Error: " + data.msg
					});
					err_series_gen_history.show();
				}
			});
		});

		$('.btn-delete-card-series').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var code = $(this).data('code');

			var delete_card_series_modal = beyond.modal.create({
				title: 'Delete Card Series',
				html: 'Are you sure you want to delete <b>' + code + '</b> card series?',
				disableClose: true,
				buttons: {
					'No Cancel': function(){
						delete_card_series_modal.hide();
					},
					'Yes Delete': function(){
						beyond.request({
							url: '/admin/card_management/delete_card_series',
							data: {
								id: id
							},
							on_success: function(data) {
								if(data.status) {
									delete_card_series_modal.hide();
									window.location = "<?= site_url('admin/card_management/card_series') ?>"
								} else {
									var err_delete_series = beyond.modal.create({
										title: 'Delete Card Series',
										html: "Error: " + data.msg
									});
									err_delete_series.show();
								}
							}
						});
					}
				}
			});

			delete_card_series_modal.show();
		});

		$('.btn-add-card-type').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');

			beyond.request({
				url: '/admin/card_management/get_card_series_types',
				data: {
					id: id
				},
				on_success: function(data){
					if(data.status) {

						var addnew_card_type_modal = beyond.modal.create({
							title: 'Add Card Type',
							html: _.template($('#new_card_type_template').html(), {
								items: data.data.items
							}),
							buttons: {
								'Add': function() {
									var type_id = $('.new-card-type').val();

									beyond.request({
										url: '/admin/card_management/add_card_series_type',
										data: {
											series_id: id,
											type_id: type_id
										},
										on_success: function(data) {
											if(data.status) {
												addnew_card_type_modal.hide();
												window.location = "<?= site_url('admin/card_management/card_series') ?>"
											} else {
												var err_new_series_type = beyond.modal.create({
													title: 'Create New Series Type',
													html: "Error: " + data.msg
												});
												err_new_series_type.show();
											}
										}
									});
								}
							}
						});
						addnew_card_type_modal.show();

					}
				}
			});
		});

		$('.btn-add-new').click(function(e){
			e.preventDefault();
			var addnew_modal = beyond.modal.create({
				title: 'Create New Card Series',
				html: _.template($('#new_card_template').html(), {}),
				buttons: {
					'Create': function() {
						var new_series_number = $('.new-series-number').val();
						var new_series_type = $('.new-series-type').val();

						if(checkSeries(new_series_number))
							return;

						beyond.request({
							url: '/admin/card_management/add_card_series',
							data: {
								'series_number': new_series_number,
								'series_type': new_series_type
							},
							on_success: function(data) {
								if(data.status) {
									addnew_modal.hide();
									window.location = "<?= site_url('admin/card_management/card_series') ?>"
								} else {
									var err_new_series = beyond.modal.create({
										title: 'Create New Card Series',
										html: "Error: " + data.msg
									});
									err_new_series.show();
								}
							}
						});
					}
				}
			});
			addnew_modal.show();
		});

		$('.btn-view-deleted').click(function(e){
			e.preventDefault();

			beyond.request({
				url: '/admin/card_management/get_deleted_card_series',
				on_success: function(data){
					var deleted_card_type_modal = beyond.modal.create({
						title: "Deleted Card Series",
						html: _.template($('#view-deleted-series').html(),{
							card_series : data.data.deleted
						})
					});
					deleted_card_type_modal.show();
					$('.btn-restore').click(function(e){
						var id = $(this).data('id');
						var confirm_modal = beyond.modal.create({
							title: 'Restore Deleted Card Series',
							html: 'Are you sure you want to restore this card series?',
							buttons: {
								'Restore':function() {
									beyond.request({
										url: '/admin/card_management/restore_card_series',
										data: { id: id },
										on_success:function(data){
											confirm_modal.hide();
											if(data.status)
											{
												window.location = "<?= site_url('admin/card_management/card_series') ?>";
											}
											else
											{
												var err_new_type = beyond.modal.create({
													title: 'Restore Card Series',
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
				}

			});
		});

	});

	var checkAllotmentSeries = function(starting_index, ending_index, code) {
		$('#card-series-allotment-starting-index-error').hide();
		$('#card-series-allotment-ending-index-error').hide();

		if(starting_index == '' || !isNumeric(starting_index)  || ((code+"").length+starting_index.length)!= 10)
		{
			$('#card-series-allotment-starting-index-error').show();
			return true;
		}

		if(ending_index == '' || !isNumeric(ending_index)  || ((code+"").length+ending_index.length) != 10)
		{
			$('#card-series-allotment-ending-index-error').show();
			return true;
		}

		return false;
	}
	
	var checkGenerationSeries  = function(quantity, starting_index, code) {
		$('#card-series-quantity-error').hide();
		$('#card-series-starting-index-error').hide();

		if(quantity == '' || !isNumeric(quantity) ){
			$('#card-series-quantity-error').show();
			return true;
		}

		if(starting_index == '' || !isNumeric(starting_index)  || ((code+"").length+starting_index.length) != 10){
			$('#card-series-starting-index-error').show();
			return true;
		}

		return false;
	}
	
	var checkSeries = function(series_number) {
		$('#card-series-number-error').hide();

		if(!isNumeric(series_number) || series_number.length < 2 || series_number.length > 3)
		{
			$('#card-series-number-error').show();
			return true;
		}

		return false;
	}
	
</script>