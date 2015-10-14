<div class="row-fluid">
	<form class="form-horizontal" method="GET" action="<?= site_url('admin/accounts_position_count/view/') ?>">
		<div class="control-group">
			<label class="control-label">Account ID</label>
			<div class="controls">
				<input type="text" placeholder="Enter Account ID" name="account_id" />
				<button class="btn btn-primary">Search</button>
			</div>
		</div>
	</form>
	<table class="table table-bordered table-condensed">
		<thead>
			<tr>
				<th>Account ID</th>
				<th>Account Name</th>
				<th>Upline</th>
				<th>Pos</th>
				<th>Date Registered</th>
				<th>Type</th>
				<th>L</th>
				<th>R</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($accounts as $account) : ?>
			<tr>
				<td><?= $account->account_id ?></td>
				<td><?= $account->fullname ?></td>
				<td><?= $account->upline_id ?></td>
				<td><?= $account->position ?></td>
				<td><?= $account->insert_timestamp ?></td>
				<td><?= $account->type_name ?></td>
				<td><?= $account->sp_downline_count_left ?></td>
				<td><?= $account->sp_downline_count_right ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div><?= $this->pager->create_links();  ?></div>
</div>