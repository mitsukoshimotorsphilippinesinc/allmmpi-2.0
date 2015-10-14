<div class='alert alert-info'>
	<h2>P2P Downline Checker</h2>
</div>

<div class="row-fluid form-horizontal">
	<div class="control-group">
		<label class="control-label">Head Account ID</label>
		<div class="controls">
			<input class="input head-account-id" type="text" placeholder="Account ID" />
			<button class="btn btn-primary btn-search-downline-count"><i class="icon-search icon-white"></i></button>
		</div>
	</div>
</div>

<div class="row-fluid downline-view-table">
</div>

<script type="text/template" id="downline-view-table-template">
	<div class="row-fluid">
		<div class="span6">
			<h3>LEFT</h3>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Account ID</th>
						<th>Usage</th>
						<th>Product Name</th>
						<th>Product Price</th>
					</tr>
				</thead>
				<tbody>
					<% for(i in LEFT) { %>
					<tr>
						<td><%= LEFT[i].account_id %></td>
						<td><%= LEFT[i].usage %></td>
						<td><%= LEFT[i].product_data.product_name %></td>
						<td>P<%= LEFT[i].product_data.standard_retail_price %></td>
					</tr>
					<% } %>
				</tbody>
			</table>
		</div>
		<div class="span6">
			<h3>RIGHT</h3>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Account ID</th>
						<th>Usage</th>
						<th>Product Name</th>
						<th>Product Price</th>
					</tr>
				</thead>
				<tbody>
					<% for(i in RIGHT) { %>
					<tr>
						<td><%= RIGHT[i].account_id %></td>
						<td><%= RIGHT[i].usage %></td>
						<td><%= RIGHT[i].product_data.product_name %></td>
						<td>P<%= RIGHT[i].product_data.standard_retail_price %></td>
					</tr>
					<% } %>
				</tbody>
			</table>
		</div>
	</div>
	<%= _.template($('#downline-view-credited-table').html(), {CREDITED: CREDITED}) %>
</script>

<script type="text/template" id="downline-view-credited-table">
	<div class="row-fluid">
		<h3>CREDITED TRANSACTIONS</h3>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Account ID</th>
					<th>Left Account ID</th>
					<th>Right Account ID</th>
					<th>Product Name</th>
					<th>Product Price</th>
					<th>Datetime</th>
				</tr>
			</thead>
			<tbody>
				<% for(i in CREDITED) { %>
				<tr>
					<td><%= CREDITED[i].account_id %></td>
					<td><%= CREDITED[i].left_account_id %></td>
					<td><%= CREDITED[i].right_account_id %></td>
					<td><%= CREDITED[i].product_data.product_name %></td>
					<td>P<%= CREDITED[i].product_data.standard_retail_price %></td>
					<td><%= CREDITED[i].insert_timestamp %></td>
				</tr>
				<% } %>
			</tbody>
		</table>
	</div>
</script>

<script type="text/javascript">
	$(document).ready(function(){
		$('.btn-search-downline-count').click(function(e){
			e.preventDefault();
			var head_account_id = $('.head-account-id').val();
			beyond.request({
				url: '/admin/card_management/check_card_p2p_downlines',
				data: {
					head_account_id: head_account_id
				},
				on_success: function(data){
					$('.downline-view-table').html('');
					if(data.status) {
						console.log(data.data);
						$('.downline-view-table').html(_.template($('#downline-view-table-template').html(), data.data));
					} else {
						var err_modal = beyond.modal.create({
							title: 'P2P Downline Card Check',
							html: data.msg
						});
						err_modal.show();
					}
				}
			});
		});
	});
</script>