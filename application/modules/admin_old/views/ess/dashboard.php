<div class='alert alert-info'>
	<h2>Encoded Sales Summary</h2>
</div>

<div class="row-fluid">
	<div class="span4">
		<div class='alert alert-info'><h3>Generate</h3></div>
		<div><h4>Select Series</h4></div>
		<div class="row-fluid">
			<div class="span3">
				<div><h5>Package</h5></div>
				<div>
					<?php foreach($sorterd_series[1] as $sp) : ?>
					<div><input type="checkbox" class="input series-selection" data-series-type="1" data-series-number="<?= $sp ?>" />&nbsp;<?= $sp ?></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="span3">
				<div><h5>Sales</h5></div>
				<div>
					<?php foreach($sorterd_series[0] as $rs) : ?>
					<div><input type="checkbox" class="input series-selection" data-series-type="0" data-series-number="<?= $rs ?>" />&nbsp;<?= $rs ?></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="span6">
				<div class="container">
					<div class="control-group">
						<label class="control-label">Start Date</label>
						<div class="controls">
							<div class="input-append" >
								<input title="Start Date" class="input-medium generate-start-date" id="start-date" type="text" value="">
								<span id='start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">End Date</label>
						<div class="controls">
							<div class="input-append" >
								<input title="Start Date" class="input-medium generate-end-date" id="end-date" type="text" value="">
								<span id='end-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
							</div>
						</div>
					</div>
				</div>
				<button class="btn btn-primary btn-generate-ess">Generate ESS</button>
			</div>
		</div>
	</div>
	<div class="span8">
		<div class='alert alert-info'>
			<h3>Leaders</h3>
			<button class='btn btn-small btn-add-new-leader pull-right' style="margin-top: -27px; margin-right: -30px;">
				<i class="icon-plus"></i>
				<span> Add New</span>
			</button>
		</div>
		<div class="leaders_table"></div>
	</div>
</div>

<script type="text/template" id="leader_list_template">
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Member ID</th>
				<th>Account ID</th>
				<th>Member Name</th>
				<th>Active</th>
				<th style="width: 40px;"></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in leaders) { %>
			<tr>
				<td><%= leaders[i].member_id %></td>
				<td><%= leaders[i].account_id %></td>
				<td><%= leaders[i].name %></td>
				<td><span class="label label-<%= (leaders[i].is_active)?'success':'important' %>"><%= (leaders[i].is_active == 1)?'YES':'NO' %></span></td>
				<td>
					<% if(leaders[i].is_active == 1) { %>
					<button class="btn btn-danger btn-deactivate" title="Deactivate" data-id="<%= leaders[i].accountid %>"><i class="icon-off icon-white"></i></button>
					<% } else { %>
					<button class="btn btn-success btn-activate" title="Activate" data-id="<%= leaders[i].accountid %>"><i class="icon-ok icon-white"></i></button>
					<% } %>
				</td>
			</tr>
			<% } %>
		</tbody>
	</table>
	<%= pagination %>
</script>

<script type="text/template" id="new_leader_form_template">
	<div class="row-fluid form-horizontal">
		<div class="control-group">
			<label class="control-label">Select Member</label>
			<div class="controls">
				<input type="text" class="input input-large input-search-member" placeholder="Firstname / Lastname / Middlename" />
				<button class="btn btn-primary btn-search-member" title="Search Member"><i class="icon-search icon-white"></i></button>
			</div>
		</div>

		<div class="member-search-list" style="max-height: 400px; overflow: auto;"></div>
	</div>
</script>

<script type="text/template" id="member_search_template">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Member ID</th>
				<th>Member Name</th>
				<th style="width: 210px;"></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in members) { %>
			<tr>
				<td><%= i %></td>
				<td><%= members[i].fullname %></td>
				<td>
					<button class="btn btn-small btn-primary btn-get-accounts" data-member-id="<%= i %>" data-member-name="<%= members[i].fullname %>">Get Accounts</button>
				</td>
			</tr>
			<% } %>
		</tbody>
	</table>
</script>

<script type="text/template" id="account-selection-template">
	<select class="input input-medium account-selection" data-member-id="<%= account_ids[0].member_id %>">
		<% for(i in account_ids) { %>
		<option value="<%= account_ids[i].account_id %>"><%= account_ids[i].account_id %></option>
		<% } %>
	</select>
	<button class="btn btn-primary btn-small btn-select-account">Select</button>
</script>

<script type="text/template" id="new-leader-confirm-template">
	Are you sure you want to add member <b>[<%= member_id %>] <%= member_name %></b> on account <b>[<%= account_id %>]</b> as a leader?
</script>

<script type="text/javascript">

	var loadLeaders = function(page){
		beyond.request({
			url: '/admin/ess/get_leader_list',
			data: {
				page: page
			},
			on_success: function(data){
				if(data.status) {
					$('.leaders_table').html(_.template($('#leader_list_template').html(), data.data));

					$('.btn-deactivate').click(function(e){
						e.preventDefault();
						var id = $(this).data('id');

						var confirm_modal = beyond.modal.create({
							title: 'Deactivate Leader',
							html: 'Are you sure you want to deactivate this leader?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Deactivate': function(){
									confirm_modal.hide();
									beyond.request({
										url: '/admin/ess/leader_activation',
										data: {
											id: id,
											active: 0
										},
										on_success: function(data){
											if(data.status) {
												var success_modal = beyond.modal.create({
													title: 'Deactivate Leader',
													html: 'Leader was deactivated',
													disableClose: true,
													buttons: {
														'Ok': function(){
															window.location = "<?= site_url('admin/ess') ?>";
														}
													}
												});
												success_modal.show();
											} else {
												var err_modal = beyond.modal.create({
													title: 'Deactivate Leader',
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

					$('.btn-activate').click(function(e){
						e.preventDefault();
						var id = $(this).data('id');

						var confirm_modal = beyond.modal.create({
							title: 'Activate Leader',
							html: 'Are you sure you want to activate this leader?',
							disableClose: true,
							buttons: {
								'No': function(){
									confirm_modal.hide();
								},
								'Yes Deactivate': function(){
									confirm_modal.hide();
									beyond.request({
										url: '/admin/ess/leader_activation',
										data: {
											id: id,
											active: 1
										},
										on_success: function(data){
											if(data.status) {
												var success_modal = beyond.modal.create({
													title: 'Activate Leader',
													html: 'Leader was activated',
													disableClose: true,
													buttons: {
														'Ok': function(){
															window.location = "<?= site_url('admin/ess') ?>";
														}
													}
												});
												success_modal.show();
											} else {
												var err_modal = beyond.modal.create({
													title: 'Activate Leader',
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

					$('.goto_page').click(function(e){
						e.preventDefault();
						var new_page = $(this).attr('page');
						loadLeaders(new_page);
					});
				} else {
					var err_modal = beyond.modal.create({
						title: 'ESS :: Leaders',
						html: data.msg
					});
					err_modal.show();
				}
			}
		});
	};

	$(document).ready(function(){

		loadLeaders(1);

		$("#start-date").datepicker({
			'dateFormat' : "yy-mm-dd"
		});
		
		$("#start-date-icon").click(function(e) {
			$("#start-date").datepicker("show");
		});
		
		$("#end-date").datepicker({
			'dateFormat' : "yy-mm-dd"	
		});
		
		$("#end-date-icon").click(function(e) {
			$("#end-date").datepicker("show");
		});

		$('.btn-generate-ess').click(function(e){
			e.preventDefault();

			var start_date = $('.generate-start-date').val();
			var end_date = $('.generate-end-date').val();

			var selected_series = [[],[]];
			$('.series-selection').each(function(){ if($(this).attr('checked') == "checked") { selected_series[$(this).data('series-type')].push($(this).data('series-number')); } });
			
			beyond.request({
				url: '/admin/ess/generate_ess',
				data: {
					selected_series: selected_series,
					start_date: start_date,
					end_date: end_date
				},
				on_success: function(data){
					if(data.status) {
						var success_modal = beyond.modal.create({
							title: 'ESS :: Generate',
							html: 'Generation has started. The report will be emailed to you once it is completed.',
							disableClose: true,
							buttons: {
								'Ok': function(){
									window.location = "<?= site_url('admin/ess') ?>";
								}
							}
						});
						success_modal.show();
					} else {
						var err_modal = beyond.modal.create({
							title: 'ESS :: Generate',
							html: data.msg
						});
						err_modal.show();
					}
				}
			});
		});

		$('.btn-add-new-leader').click(function(e){
			e.preventDefault();

			var add_new_leader_modal = beyond.modal.create({
				title: 'Add New Leader',
				html: _.template($('#new_leader_form_template').html(), {})
			});
			add_new_leader_modal.show();

			$('.btn-search-member').click(function(e){
				e.preventDefault();
				var search_member = $('.input-search-member').val();

				beyond.request({
					url: '/webpoi/get_members',
					data: {
						search_key: search_member
					},
					on_success: function(data){
						$('.member-search-list').html(_.template($('#member_search_template').html(), data.data));

						$('.btn-get-accounts').click(function(e){
							e.preventDefault();
							var member_id = $(this).data('member-id');
							var member_name = $(this).data('member-name');
							var that = this;

							beyond.request({
								url: '/admin/ess/get_accounts',
								data: {
									member_id: member_id
								},
								on_success: function(data){
									$(that).after(_.template($('#account-selection-template').html(), data.data));

									$(that).parent().find('.btn-select-account').click(function(e){
										var account_id = $(this).parent().find('.account-selection').val();

										var confirm_modal = beyond.modal.create({
											title: 'ESS :: Leaders',
											html: _.template($('#new-leader-confirm-template').html(), {
												member_id: member_id,
												member_name: member_name,
												account_id: account_id
											}),
											disableClose: true,
											buttons: {
												'No': function(){
													confirm_modal.hide();
												},
												'Yes Add Leader': function(){
													confirm_modal.hide();

													beyond.request({
														url: '/admin/ess/add_leader',
														data: {
															member_id: member_id,
															account_id: account_id
														},
														on_success: function(data){
															if(data.status) {
																var success_modal = beyond.modal.create({
																	title: 'ESS :: Leaders',
																	html: 'Member was successfully added as leader.',
																	disableClose: true,
																	buttons: {
																		'Ok': function(){
																			window.location = "<?= site_url('admin/ess') ?>";
																		}
																	}
																});
																success_modal.show();
															} else {
																var err_modal = beyond.modal.create({
																	title: 'ESS :: Leaders',
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

									$(that).remove();
								}
							});
						});
					}
				});
			});
		});

	});
</script>