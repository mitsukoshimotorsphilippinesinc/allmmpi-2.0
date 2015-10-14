<div class='alert alert-info'>
	<h2>Card Types 
		<a class='btn btn-small btn-add-new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a>
		<a class='btn btn-small btn-view-deleted' style='float:right; margin-right:10px; margin-top:5px;'><span> View Deleted</span></a>
		<a class='btn btn-small btn-upgrade-card-mapping' style='float:right; margin-right:10px; margin-top:5px;'><span> Upgrade Cards Mapping</span></a>
	</h2>
</div>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th style="width: 90px;">Code</th>
			<th>Name</th>
			<th style="width: 100px;">Types</th>
			<th style="width: 80px;">Points to Pair</th>
			<th style="width: 80px;">Points</th>			
			<th>Upgradeable To</th>
			<th style="width: 50px;">Color</th>
			<th>Point Type</th>
			<th>Description</th>
			<th style="width: 130px;">Date Created</th>
			<th style="width: 160px;"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($card_types as $card_type) : ?>
		<tr class="card_type_<?= $card_type->card_type_id ?>">
			<td class="card_code"><?= $card_type->code ?></td>
			<td class="card_name"><?= $card_type->name ?></td>
			<td class="card_type" data-type="<?= $card_type->is_package ?>"><?= ($card_type->is_package)?'Package Card':'Sales Card' ?></td>
			<td class="card_points_to_pair"><?= number_format($card_type->points_to_pair, 2) ?></td>
			<td class="card_points"><?= number_format($card_type->points, 2) ?></td>
			<td><?= implode(", ", $card_type->mapping) ?></td>
			<td class="card_color" data-color="<?= $card_type->color ?>"><div style="width:38px; height:26px; background:<?= $card_type->color ?>; border-radius:4px; margin:auto;	"></div></td>
			<td class="card_point_type"><?= $card_type->point_type_name ?></td>
			<td class="card_desc"><?= $card_type->description ?></td>
			<td><?= $card_type->insert_timestamp ?></td>
			<td>
				<?php if(!(boolean)$card_type->is_locked) : ?>
				<button class="btn btn-primary btn-edit-card-type" data-id="<?= $card_type->card_type_id ?>" title="Edit"><i class="icon-white icon-edit"></i></button>
				<?php endif; ?>
				<button class="btn btn-primary btn-view-commissions" data-id="<?= $card_type->card_type_id ?>" data-code="<?= $card_type->code ?>" title="View Commissions"><i class="icon-white icon-list"></i></button>
				<button class="btn btn-primary btn-view-modifiers" data-id="<?= $card_type->card_type_id ?>" title="Modifiers"><i class="icon-white icon-th"></i></button>
				<?php if(!(boolean)$card_type->is_locked && $this->users_model->is_user_allowed_by_privilege_code($this->user->user_id,'SUPER ADMIN')) : ?>
				<button class="btn btn-danger btn-delete-card-type" data-id="<?= $card_type->card_type_id ?>" title="Delete"><i class="icon-white icon-remove"></i></button>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script type="text/template" id="new-card-type-template">
	<% var _card_types = <?= json_encode($card_types) ?> %>
	<div class="row-fluid form-horizontal ">
		
		<div class="control-group">
			<label class="control-label">Code</label>
			<div class="controls">
				<input type="text" class="input input-small new-card-type-code" placeholder="XX" value="<%= code %>" style="display:block"  <%= disabled %> />
				<span class='label label-important' id='card-type-code-error' style='display:none;'>Invalid Card Type Code.</span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Name</label>
			<div class="controls">
				<input type="text" class="input input-medium new-card-type-name" placeholder="Card Type Name" value="<%= name %>"  style="display:block" />
				<span class='label label-important' id='card-type-name-error' style='display:none;'>Invalid Card Type Name.</span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Type</label>
			<div class="controls">
				<select class="input input-medium new-card-type-type">
					<option value="1" <%= (type=='1')?"selected='selected'":"" %>>Package Card</option>
					<option value="0" <%= (type=='0')?"selected='selected'":"" %>>Sales Card</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Points to Pair</label>
			<div class="controls">
				<input type="text" class="input input-medium new-card-type-points-to-pair" placeholder="700" value="<%= points_to_pair %>"  style="display:block" />
				<span class='label label-important' id='card-type-points-to-pair-error' style='display:none;'>Invalid Points to Pair amount.</span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Points</label>
			<div class="controls">
				<input type="text" class="input input-medium new-card-type-points" placeholder="700" value="<%= points %>"  style="display:block" />
				<span class='label label-important' id='card-type-points-error' style='display:none;'>Invalid Points amount.</span>
			</div>
		</div>

		<% if(code) { %>
		<div class="control-group">
			<label class="control-label">Point Type</label>
			<div class="controls">
				<select class="input input-medium new-card-point-type">
					<% for(i in _card_types) { %>
					<option value="<%= _card_types[i].card_type_id %>" <%= (_card_types[i].code == point_type)?'selected="selected"':'' %>><%= _card_types[i].code %></option>
					<% } %>
				</select>
			</div>
		</div>
		<% } %>

		<div class="control-group">
			<label class="control-label">Color</label>
			<div class="controls">
				<input type="text" class="input input-medium new-card-type-color" placeholder="Gray" value="<% if(color) { %> <%=color %> <% } else { %>Gray<% } %>" style="float:left;"   /> <div class="color-picker" style="background-color:<% if(color) { %> <%=color %> <% } else { %>Gray<% } %>"></div> <div style="clear:both"></div>
				<span class='label label-important' id='card-type-color-error' style='display:none;'>Invalid color.</span>
				<?php echo $colors; ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Description</label>
			<div class="controls">
				<textarea class="input new-card-type-desc"><%= desc %></textarea>
			</div>
		</div>
		
	</div>
</script>

<script type="text/template" id="view-commission-table-template">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Commission Type</th>
				<th>Bonus Type</th>
				<th>Qty / Amount</th>
				<th>Date Created</th>
				<th style="width: 80px;"></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in bonuses) { %>
			<% if(typeof bonuses[i] != 'object') continue; %>
			<tr class="card_type_commission_<%= bonuses[i].type_bonus_id %>">
				<td class="commission_type"><%= bonuses[i].commission_type %></td>
				<td class="bonus_type"><%= bonuses[i].bonus_type %></td>
				<td class="qty_amount"><%= bonuses[i].qty_amount %></td>
				<td><%= bonuses[i].insert_timestamp %></td>
				<td>
					<button class="btn btn-primary btn-edit-card-type-commission" data-id="<%= bonuses[i].type_bonus_id %>" data-type="<%= bonuses[i].card_type_id %>" title="Edit"><i class="icon-edit icon-white"></i></button>
					<button class="btn btn-danger btn-delete-card-type-commission" data-id="<%= bonuses[i].type_bonus_id %>" title="Delete"><i class="icon-remove icon-white"></i></button>
				</td>
			</tr>
			<% } %>
		</tbody>
	</table>
	<div>
		<h4 style="margin-bottom: 10px;" class="type-commission-form-header">New Card Type Commission</h4>
		<div class="row-fluid form-horizontal">
			<div class="control-group">
				<label class="control-label">Commission Type</label>
				<div class="controls">
					<select class="input input-medium new-type-commission-commission-type">
						<option value="REFERRAL">REFERRAL</option>
						<option value="PAIRING">PAIRING</option>
						<option value="CASCADE">CASCADE</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Bonus Type</label>
				<div class="controls">
					<select class="input input-medium new-type-commission-bonus-type">
						<option value="FUNDS">FUNDS</option>
						<option value="FPV">FPV</option>
						<option value="MPV">MPV</option>
						<option value="PRODUCT">PRODUCT</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Qty / Amount</label>
				<div class="controls">
					<input type="text" class="input input-medium new-type-commission-qty-amount" style="display:block" />
					<span class='label label-important' id='new-type-commission-qty-amount-error' style='display:none;'>Invalid amount.</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"></label>
				<div class="controls">
					<button class="btn btn-primary btn-card-type-commission-form-button" data-mode="add" data-type="<%= bonuses.id %>">Create New Commission</button>
					<button class="btn btn-danger btn-card-type-commission-form-cancel hide">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="card_modifier_template">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Modifier</th>
				<th>Condition</th>
				<th>Date Created</th>
				<th style="width: 40px;"></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in card_modifiers) { %>
				<% modifiers = $.grep(modifiers, function(e){ return e.modifier_id == card_modifiers[i].modifier_id; }, true) %>
			<tr>
				<td><%= card_modifiers[i].name %></td>
				<td><%= card_modifiers[i].condition %></td>
				<td><%= card_modifiers[i].insert_timestamp %></td>
				<td>
					<button class="btn btn-danger btn-delete-modifier" data-modifier="<%= card_modifiers[i].modifier_id %>" title="Delete Modifier"><i class="icon-white icon-remove"></i></button>
				</td>
			</tr>
			<% } %>
		</tbody>
	</table>
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Modifier</label>
			<div class="controls">
				<select class="input input-large new_card_modifier">
					<% for(i in modifiers) { %>
					<option value="<%= modifiers[i].modifier_id %>"><%= modifiers[i].modifier_name %></option>
					<% } %>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Condition</label>
			<div class="controls">
				<select class="input input-large new_card_condition">
					<option value="NONE">None</option>
					<option value="IGPSM">IGPSM</option>
					<option value="UNILEVEL">UNILEVEL</option>
					<option value="RAFFLE">RAFFLE</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"></label>
			<div class="controls">
				<button class="btn btn-primary btn-add-modifier">Add Modifier</button>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="view-deleted-cards">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Code</th>
				<th>Name</th>
				<th>Points to Pair</th>
				<th>Points</th>
				<th style="width: 40px;"></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in card_types) { %>
				<tr class="restore_card_type_<%= card_types[i].card_type_id %>">
					<td>
						<%= card_types[i].code %>
 					</td>
					<td>
						<%= card_types[i].name %>
 					</td>
 					<td>
						<%= card_types[i].points_to_pair %>
 					</td>
 					<td>
						<%= card_types[i].points %>
 					</td>
 					<td>
						<button class="btn btn-restore btn-primary" data-id="<%= card_types[i].card_type_id %>">Restore</button>
 					</td>
				</tr>
			<% } %>
		</tbody>
	</table>
</script>

<script type="text/template" id="upgrade-card-mapping-template">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th style="width: 200px;">Base Card</th>
				<th style="width: 200px;">Upgrade Card</th>
				<th style="width: 40px;"></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in mapping) { %>
			<tr>
				<td><%= mapping[i].base_card_type_code %></td>
				<td><%= mapping[i].upgrade_card_type_code %></td>
				<td>
					<button class="btn btn-danger btn-remove-upgrade-mapping" data-id="<%= mapping[i].map_id %>"><i class="icon-remove icon-white"></i></button>
				</td>
			</tr>
			<% } %>
		</tbody>
	</table>
	<hr/>
	<div class="row-fluid">
		<div class="span5">
			<div class="control-group">
				<label class="control-label">Base Card</label>
				<div class="controls">
					<select class="input input-medium new-mapping-base-card-id">
						<% for(i in base_cards) { %>
						<option value="<%= i %>"><%= base_cards[i] %></option>
						<% } %>
					</select>
				</div>
			</div>
		</div>
		<div class="span5">
			<div class="control-group">
				<label class="control-label">Upgrade Card</label>
				<div class="controls">
					<select class="input input-medium new-mapping-upgrade-card-id">
						<% for(i in upgrade_cards) { %>
						<option value="<%= i %>"><%= upgrade_cards[i] %></option>
						<% } %>
					</select>
				</div>
			</div>
		</div>
		<div class="span2">
			<div class="control-group">
				<label class="control-label">&nbsp;</label>
				<div class="controls">
					<button class="btn btn-success btn-new-mapping-add">Add</button>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/javascript">

	$(document).ready(function(){

		$('.btn-upgrade-card-mapping').click(function(e){
			e.preventDefault();
			beyond.request({
				url: '/admin/card_management/get_upgrade_mappings',
				on_success: function(data){
					if(data.status) {
						var mapping_modal = beyond.modal.create({
							title: 'Upgrade Card Mapping',
							html: _.template($('#upgrade-card-mapping-template').html(), {
								mapping: data.data.upgrade_mappings,
								base_cards: data.data.base_cards,
								upgrade_cards: data.data.upgrade_cards
							})
						});
						mapping_modal.show();

						$('.btn-remove-upgrade-mapping').click(function(e){
							e.preventDefault();
							var map_id = $(this).data('id');

							var confirm_modal = beyond.modal.create({
								title: 'Upgrade Card Mapping',
								html: 'Are you sure you want to remove this mapping?',
								disableClose: true,
								buttons: {
									'No': function(){
										confirm_modal.hide();
									},
									'Yes Remove': function(){
										confirm_modal.hide();

										beyond.request({
											url: '/admin/card_management/remove_upgrade_mapping',
											data: {
												map_id: map_id
											},
											on_success: function(data){
												if(data.status) {
													mapping_modal.hide();
													window.location = "<?= site_url('admin/card_management/card_types') ?>";
													var success_new_card_mapping = beyond.modal.create({
														title: 'Upgrade Card Mapping',
														html: 'Mapping was removed'
													});
													success_new_card_mapping.show();
												} else {
													var err_new_card_mapping = beyond.modal.create({
														title: 'Upgrade Card Mapping',
														html: "Error: " + data.msg
													});
													err_new_card_mapping.show();
												}
											}
										});
									}
								}
							});
							confirm_modal.show();
						});

						$('.btn-new-mapping-add').click(function(e){
							e.preventDefault();
							var base_card_type_id = $('.new-mapping-base-card-id').val();
							var upgrade_card_type_id = $('.new-mapping-upgrade-card-id').val();

							var confirm_modal = beyond.modal.create({
								title: 'Upgrade Card Mapping',
								html: 'Are you sure you want to add this upgrade map?',
								disableClose: true,
								buttons: {
									'No': function(){
										confirm_modal.hide();
									},
									'Yes Add': function(){
										confirm_modal.hide();

										beyond.request({
											url: '/admin/card_management/add_upgrade_mapping',
											data: {
												base_card_type_id: base_card_type_id,
												upgrade_card_type_id: upgrade_card_type_id
											},
											on_success: function(data){
												if(data.status) {
													mapping_modal.hide();
													window.location = "<?= site_url('admin/card_management/card_types') ?>";
													var success_new_card_mapping = beyond.modal.create({
														title: 'Upgrade Card Mapping',
														html: 'New Mapping was Added'
													});
													success_new_card_mapping.show();
												} else {
													var err_new_card_mapping = beyond.modal.create({
														title: 'Upgrade Card Mapping',
														html: "Error: " + data.msg
													});
													err_new_card_mapping.show();
												}
											}
										});
									}
								}
							});
							confirm_modal.show();
						});
					}
				}
			});
		});

		$('.btn-view-modifiers').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');
			beyond.request({
				url: '/admin/card_management/get_type_modifiers',
				data: {
					id: id
				},
				on_success: function(data){
					if(data.status) {

						var card_type_modifiers_modal = beyond.modal.create({
							title: 'Card Type Modifiers',
							html: _.template($('#card_modifier_template').html(), {card_modifiers : data.data.card_modifiers, modifiers: <?= json_encode($modifiers) ?>})
						});
						console.log(data.data)
						card_type_modifiers_modal.show();

						$('.btn-delete-modifier').click(function(e){
							e.preventDefault();
							var modifier_id = $(this).data('modifier');

							var delete_modifier_confirm_modal = beyond.modal.create({
								title: 'Card Type Modifier',
								html: 'Are you sure you want to delete this modifier?',
								disableClose: true,
								buttons: {
									'No': function(){
										delete_modifier_confirm_modal.hide();
									},
									'Yes Delete': function(){
										delete_modifier_confirm_modal.hide();

										beyond.request({
											url: '/admin/card_management/delete_card_type_modifier',
											data: {
												modifier_id: modifier_id,
												type_id: id
											},
											on_success: function(data){
												if(data.status) {
													card_type_modifiers_modal.hide();
													$('.btn-view-modifiers[data-id="'+id+'"]').click();
												} else {
													var err_new_type_modifier = beyond.modal.create({
														title: 'Card Type Modifier',
														html: "Error: " + data.msg
													});
													err_new_type_modifier.show();
												}
											}
										});
									}
								}
							});
							delete_modifier_confirm_modal.show();

						});

						$('.btn-add-modifier').click(function(e){
							e.preventDefault();

							var new_modifier_id = $('.new_card_modifier').val();
							var new_modifier_condition = $('.new_card_condition').val();

							var confirm_new_modifier_modal = beyond.modal.create({
								title: 'New Card Type Modifier',
								html: 'Are you sure you want to create this modifier?',
								disableClose: true,
								buttons: {
									'No': function(){
										confirm_new_modifier_modal.hide();
									},
									'Yes Create': function(){
										confirm_new_modifier_modal.hide();

										beyond.request({
											url: '/admin/card_management/add_card_type_modifier',
											data: {
												type_id: id,
												modifier_id: new_modifier_id,
												modifier_condition: new_modifier_condition
											},
											on_success: function(data){
												if(data.status) {
													card_type_modifiers_modal.hide();
													$('.btn-view-modifiers[data-id="'+id+'"]').click();
												} else {
													var err_new_type_modifier = beyond.modal.create({
														title: 'Card Type Modifier',
														html: "Error: " + data.msg
													});
													err_new_type_modifier.show();
												}
											}
										});
									}
								}
							});
							confirm_new_modifier_modal.show();
						});

					} else {
						var err_get_type_modifier = beyond.modal.create({
							title: 'Card Type Modifier',
							html: "Error: " + data.msg
						});
						err_get_type_modifier.show();
					}
				}
			});
		});

		$('.btn-view-commissions').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var code = $(this).data('code');

			beyond.request({
				url: '/admin/card_management/get_type_commission_bonuses',
				data: {
					id: id
				},
				on_success: function(data){
					if(data.status) {
						var commission_table_modal = beyond.modal.create({
							title: code + ' Card Type Commissions',
							html: _.template($('#view-commission-table-template').html(), { bonuses: _.extend(data.data.bonuses, {id: id}) })
						});
						commission_table_modal.show();

						$('.btn-card-type-commission-form-cancel').click(function(e){
							e.preventDefault();

							$('.btn-card-type-commission-form-button').html('Create New Commission').data('mode','add');
							$('.type-commission-form-header').html('New Card Type Commission');
							$('.btn-card-type-commission-form-cancel').hide();

							$('.new-type-commission-commission-type option').each(function(){ $(this).removeAttr('selected'); });
							$('.new-type-commission-bonus-type option').each(function(){ $(this).removeAttr('selected'); });
							$('.new-type-commission-qty-amount').val('');
						});

						$('.btn-card-type-commission-form-button').click(function(e){
							e.preventDefault();
							var type_id = $(this).data('type');
							var new_commission_type = $('.new-type-commission-commission-type').val();
							var new_bonus_type = $('.new-type-commission-bonus-type').val();
							var new_qty_amount = $('.new-type-commission-qty-amount').val();

							if(checkCommission(new_qty_amount))
								return;	

							if($(this).data('mode') == 'add') {
								var confirm_new_commission_type = beyond.modal.create({
									title: 'New Card Type Commission',
									html: 'Are you sure you want to add this new card type commission?',
									disableClose: true,
									buttons: {
										'No': function(){
											confirm_new_commission_type.hide();
										},
										'Yes Add': function(){
											confirm_new_commission_type.hide();

											beyond.request({
												url: '/admin/card_management/add_card_type_commission',
												data: {
													id: type_id,
													commission_type: new_commission_type,
													bonus_type: new_bonus_type,
													qty_amount: new_qty_amount
												},
												on_success: function(data){
													if(data.status) {
														commission_table_modal.hide();
														$('.btn-view-commissions[data-id="'+type_id+'"]').click();
													} else {
														var err_new_type_commission = beyond.modal.create({
															title: 'New Card Type Commission',
															html: "Error: " + data.msg
														});
														err_new_type_commission.show();
													}
												}
											});

										}
									}
								});
								confirm_new_commission_type.show();
							} else {
								var id = $(this).data('id');

								var confirm_edit_commission_type = beyond.modal.create({
									title: 'Edit Card Type Commission',
									html: 'Are you sure you want to edit this card type commission?',
									disableClose: true,
									buttons: {
										'No': function(){
											confirm_edit_commission_type.hide();
										},
										'Yes Edit': function(){
											confirm_edit_commission_type.hide();

											beyond.request({
												url: '/admin/card_management/edit_card_type_commission',
												data: {
													id: id,
													commission_type: new_commission_type,
													bonus_type: new_bonus_type,
													qty_amount: new_qty_amount
												},
												on_success: function(data){
													if(data.status) {
														commission_table_modal.hide();
														$('.btn-view-commissions[data-id="'+type_id+'"]').click();
													} else {
														var err_edit_type_commission = beyond.modal.create({
															title: 'Edit Card Type Commission',
															html: "Error: " + data.msg
														});
														err_edit_type_commission.show();
													}
												}
											});
										}
									}
								});
								confirm_edit_commission_type.show();
							}
						});

						$('.btn-edit-card-type-commission').click(function(e){
							e.preventDefault();
							var id = $(this).data('id');
							var type_id = $(this).data('type');

							$('.btn-card-type-commission-form-button').html('Edit Commission').data('mode','edit').data('id',id).data('type',type_id);
							$('.type-commission-form-header').html('Edit Commission');
							$('.btn-card-type-commission-form-cancel').show();

							var commission_type = $('.card_type_commission_'+id+' .commission_type').html();
							var bonus_type = $('.card_type_commission_'+id+' .bonus_type').html();
							var qty_amount = $('.card_type_commission_'+id+' .qty_amount').html();

							$('.new-type-commission-commission-type option[value="'+commission_type+'"]').attr('selected','selected');
							$('.new-type-commission-bonus-type option[value="'+bonus_type+'"]').attr('selected','selected');
							$('.new-type-commission-qty-amount').val(qty_amount);
						});

						$('.btn-delete-card-type-commission').click(function(e){
							e.preventDefault();
							var card_type_commission_id = $(this).data('id');

							var confirm_delete_card_type_commission = beyond.modal.create({
								title: 'Delete Card Type Commission',
								html: 'Are you sure you want to delete this card type commission?',
								disableClose: true,
								buttons: {
									'No': function(){
										confirm_delete_card_type_commission.hide();
									},
									'Yes Delete': function(){
										confirm_delete_card_type_commission.hide();

										beyond.request({
											url: '/admin/card_management/delete_card_type_commission',
											data: {
												id: card_type_commission_id
											},
											on_success: function(data){
												if(data.status) {
													$('.card_type_commission_'+card_type_commission_id).remove();
												} else {
													var err_delete_type_commission = beyond.modal.create({
														title: 'Delete Card Type Commission',
														html: "Error: " + data.msg
													});
													err_delete_type_commission.show();
												}
											}
										});

									}
								}
							});
							confirm_delete_card_type_commission.show();
						}); 
					} else {
						var err_new_type = beyond.modal.create({
							title: code + ' Card Type Commissions',
							html: "Error: " + data.msg
						});
						err_new_type.show();
					}
				}
			});
		});

		$('.btn-edit-card-type').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');

			var code = $('.card_type_'+id+' .card_code').html();
			var name = $('.card_type_'+id+' .card_name').html();
			var type = $('.card_type_'+id+' .card_type').data('type');
			var points_to_pair = $('.card_type_'+id+' .card_points_to_pair').html();
			var points = $('.card_type_'+id+' .card_points').html();
			var color = $('.card_type_'+id+' .card_color').data('color');
			var desc = $('.card_type_'+id+' .card_desc').html();
			var point_type = $('.card_type_'+id+' .card_point_type').html();

			var edit_card_type_modal = beyond.modal.create({
				title: 'Edit Card Type',
				html: _.template($('#new-card-type-template').html(), {
					code: code,
					name: name,
					type: type,
					points_to_pair: points_to_pair,
					points: points,
					color:color,
					desc: desc,
					disabled:' disabled="disabled" ',
					point_type: point_type
				}),
				buttons: {
					'Edit Card': function(){

						var edit_code = $('.new-card-type-code').val();
						var edit_name = $('.new-card-type-name').val();
						var edit_type = $('.new-card-type-type').val();
						var edit_points_to_pair = $('.new-card-type-points-to-pair').val();
						var edit_points = $('.new-card-type-points').val();
						var edit_color = $('.new-card-type-color').val();
						var edit_point_type = $('.new-card-point-type').val();
						var edit_desc = $('.new-card-type-desc').val();

						if(checkCardType(edit_code,edit_name,edit_points_to_pair,edit_points,edit_color))
							return;

						var confirm_modal = beyond.modal.create({
							title: 'Edit Card Type',
							html: 'Are you sure you want to edit this card type?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Edit': function(){
									confirm_modal.hide();

									beyond.request({
										url: '/admin/card_management/edit_card_type',
										data: {
											id: id,
											code: edit_code,
											name: edit_name,
											type: edit_type,
											points_to_pair: edit_points_to_pair,
											points: edit_points,
											color: edit_color,
											point_type: edit_point_type,
											desc: edit_desc
										},
										on_success: function(data){
											if(data.status) {
												window.location = "<?= site_url('admin/card_management/card_types') ?>";
											} else {
												var err_new_type = beyond.modal.create({
													title: 'Edit Card Type',
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
			edit_card_type_modal.show();
		});

		$('.btn-delete-card-type').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');

			var confirm_modal = beyond.modal.create({
				title: 'Delete Card Type',
				html: 'Are you sure you want to delete this card type?',
				disableClose: true,
				buttons: {
					'No': function(){
						confirm_modal.hide();
					},
					'Yes Delete': function(){
						confirm_modal.hide();

						beyond.request({
							url: '/admin/card_management/delete_card_type',
							data: {
								id: id
							},
							on_success: function(data){
								if(data.status) {
									window.location = "<?= site_url('admin/card_management/card_types') ?>";
								} else {
									var err_new_type = beyond.modal.create({
										title: 'Delete Card Type',
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

			var new_card_type_modal = beyond.modal.create({
				title: 'New Card Type',
				html: _.template($('#new-card-type-template').html(), {
					code: '',
					name: '',
					type: '',
					points_to_pair: '',
					points:'',
					color: '',
					desc: '',
					disabled: '',
					point_type: ''
				}),
				buttons: {
					'Create': function(){

						var new_card_code = $('.new-card-type-code').val();
						var new_card_name = $('.new-card-type-name').val();
						var new_card_type = $('.new-card-type-type').val();
						var new_card_points_to_pair = $('.new-card-type-points-to-pair').val();
						var new_card_points = $('.new-card-type-points').val();
						var new_card_color = $('.new-card-type-color').val();
						var new_card_desc = $('.new-card-type-desc').val();
						
						if(checkCardType(new_card_code,new_card_name,new_card_points_to_pair,new_card_points,new_card_color))
							return;

						var confirm_modal = beyond.modal.create({
							title: 'Create Card Type',
							html: 'Are you sure you want to add this new card type?',

							buttons: {
								'Yes Add': function() {
									confirm_modal.hide();
									
									beyond.request({
										url: '/admin/card_management/add_new_card_type',
										data: {
											code: new_card_code,
											name: new_card_name,
											type: new_card_type,
											points_to_pair: new_card_points_to_pair,
											points: new_card_points,
											color: new_card_color,
											desc: new_card_desc
										},
										on_success: function(data){
											if(data.status) {
												window.location = "<?= site_url('admin/card_management/card_types') ?>";
											} else {
												var err_new_type = beyond.modal.create({
													title: 'Create Card Type',
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

			new_card_type_modal.show();
		});
		
		$('.btn-edit-card-modifier').click(function(e){
			e.preventDefault();
			



			var edit_card_modifier_modal = beyond.modal.create({
				title:"Select modifiers for this card",
				html:_.template($('#select-card-modifier').html(),
					{
						
					}),
				buttons: {
					
				}
				
			});

			edit_card_modifier_modal.show();
		});

		$('.btn-view-deleted').click(function(e){
			e.preventDefault();

			beyond.request({
				url: '/admin/card_management/get_deleted_card_types',
				on_success: function(data){
					var deleted_card_type_modal = beyond.modal.create({
						title: "Deleted Card Types",
						html: _.template($('#view-deleted-cards').html(),{
							card_types : data.data.deleted
						})
					});
					deleted_card_type_modal.show();
					$('.btn-restore').click(function(e){
						var id = $(this).data('id');
						var confirm_modal = beyond.modal.create({
							title: 'Restore Deleted Card Types',
							html: 'Are you sure you want to restore this card type?',
							buttons: {
								'Restore':function() {
									beyond.request({
										url: '/admin/card_management/restore_card_type',
										data: { id: id },
										on_success:function(data){
											confirm_modal.hide();
											if(data.status)
											{
												window.location = "<?= site_url('admin/card_management/card_types') ?>";
											}
											else
											{
												var err_new_type = beyond.modal.create({
													title: 'Restore Card Type',
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

		$('.color-picker').live('click',function(){
			var pos = $(this).position();
			$('.card-color-picker-holder').css({
				'top':pos.top + 28,
				'left':pos.left - 110 + 23 
			});
			$('.card-color-picker-holder').show();
		});

		$('.card-color-picker').live('click',function(){
			var color = $(this).data('color');
			$('.card-color-picker-holder').hide();
			$('.new-card-type-color').val(color);
			$('.color-picker').css({'background-color':color});
		}); 

		$('.new-card-type-color').live('keyup',function(){
			$('.color-picker').css({'background-color':$(this).val()});
		});

	});


	var checkCardType = function(new_card_code,new_card_name,new_card_points_to_pair,new_card_points,new_card_color) {
			hasError = 0;
				
			$('#card-type-code-error').hide();	
			$('#card-type-name-error').hide();
			$('#card-type-points-to-pair-error').hide();
			$('#card-type-color-error').hide();
	
			$('#card-type-points-error').hide();

			
			if(new_card_code == '' ){	
				$('#card-type-code-error').show();
				hasError = 1;
			}

			if(new_card_name == ''){	
				$('#card-type-name-error').show();
				hasError = 1;
			}

			if(new_card_points_to_pair == '' || isNaN(parseInt(new_card_points_to_pair))){	
				$('#card-type-points-to-pair-error').show();
				hasError = 1;
			}
			
			if(new_card_points == '' || isNaN(parseInt(new_card_points))){	
				$('#card-type-points-error').show();
				hasError = 1;
			}
		
			return hasError;
	}

	var checkCommission = function(new_qty_amount){
		hasError = 0;

		$('#new-type-commission-qty-amount-error').hide();

		if(new_qty_amount == '' || isNaN(parseInt(new_qty_amount)))
		{
			$('#new-type-commission-qty-amount-error').show();
			hasError = 1;
		}

		return hasError;
	}

</script>

