<div class='alert alert-info'>
	<h2>Card Reencode</h2>
</div>

<div class="row-fluid">
	<div class="span6">
	</div>
	<div class="span6">
		<h4>RS Card</h4>
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label">Card ID</label>
				<div class="controls">
					<input type="text" class="input-large reencode-card-id" placeholder="7100000001,7100000002,7100000003-7100000008" style="width: 300px;" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">&nbsp;</label>
				<div class="controls">
					<button class="btn btn-primary btn-get-modifiers">Get Modifiers</button>
				</div>
			</div>
		</div>
		<div class="card-modifiers-list form-horizontal">
		</div>
	</div>
</div>

<hr/>

<div class='alert alert-info'>
	<h2>Reencode History</h2>
</div>

<div class="row-fluid">
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Card(s)</th>
				<th>Modifier(s)</th>
				<th>Username</th>
				<th>Datetime</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($reencode_history as $hist) : ?>
			<tr>
				<td><?= $hist->cards ?></td>
				<td><?= $hist->mod_names ?></td>
				<td><?= $hist->username ?></td>
				<td><?= $hist->insert_timestamp ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<script type="text/template" id="card-modifier-list-template">
	<% for(i in modifiers) { %>
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<input type="checkbox" class="input card-modifiers" data-id="<%= modifiers[i].modifier_id %>" />&nbsp;<%= modifiers[i].modifier_name %>
		</div>
	</div>
	<% } %>
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<button class="btn btn-primary btn-run-modifiers">Run Modifiers</button>
		</div>
	</div>
</script>

<script type="text/javascript">
	var parse_card_ids = function(card_id){
		var ids = [];
		var singles = card_id.split(",");
		for(i in singles) {
			var range = singles[i].split("-");
			if(range.length > 1){
				for(j = range[0]; j <= range[1]; j++) {
					ids.push(String(j));
				}
			} else {
				ids.push(singles[i]);
			}
		}
		return ids;
	};

	$(document).ready(function(){

		$('.btn-get-modifiers').click(function(e){
			//var card_id = $('.reencode-card-id').attr('disabled','disabled').val();
			var card_id = $('.reencode-card-id').val();
			var cards = parse_card_ids(card_id);

			beyond.request({
				url: '/admin/card_management/get_rs_card_modifiers',
				data: {
					card_id: cards[0]
				},
				on_success: function(data){
					if(data.status) {
						//$('.btn-get-modifiers').attr('disabled','disabled');
						$('.card-modifiers-list').html(_.template($('#card-modifier-list-template').html(), data.data));

						$('.btn-run-modifiers').click(function(e){
							var mods = [];
							$('.card-modifiers').each(function(){ if($(this).attr('checked') == 'checked') mods.push($(this).data('id')); });

							var confirm_modal = beyond.modal.create({
								title: 'Reencode Card',
								html: 'Are you sure you want to apply the selected modifiers?',
								disableClose: true,
								buttons: {
									'No': function(){
										confirm_modal.hide();
									},
									'Yes Apply Modifiers': function(){
										confirm_modal.hide();

										beyond.request({
											url: '/admin/card_management/run_card_modifiers',
											data: {
												cards: cards,
												mods: mods
											},
											on_success: function(data){
												if(data.status) {

													var success_modal = beyond.modal.create({
														title: 'Reencode Card',
														html: data.msg,
														disableClose: true,
														buttons: {
															'Ok': function(){
																window.location = "<?= site_url('admin/card_management/card_reencode') ?>";
															}
														}
													});
													success_modal.show();
												} else {
													var err_modal = beyond.modal.create({
														title: 'Reencode Card',
														html: data.msg
													});
													err_modal.show();
												}
											}
										});
									}
								}
							});
							confirm_modal.show();
						
						});
					} else {
						var err_modal = beyond.modal.create({
							title: 'Reencode Card',
							html: data.msg
						});
						err_modal.show();
					}
				}
			});
		});

	});
</script>