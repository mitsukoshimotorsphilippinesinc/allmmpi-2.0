<div class='alert alert-success'><h2>Account Information</h2></div>
<form id="submit_type" action='/members/earnings/get_account_earnings' method='post' class='form-horizontal'>
	<?php
		$delivery_year = date('Y');
		$delivery_month = date('m');
		$delivery_day = date('d');
	?>
	<div class="container">	
		<br/>
		<br/>
		<div class='tabbable'>
			<ul id='po_tabs' class="nav nav-tabs">
				<li class="active earnings"><a href="#tab_earnings" data-toggle="tab">Account Earnings</a></li>
				<li class='points_and_vouchers'><a href="#tab_points" data-toggle="tab">Points and Vouchers</a></li>
				<li class='pairings'><a href="#tab_pairings" data-toggle="tab">Pairings</a></li>
				<li class='transactions'><a href="#tab_transactions" data-toggle="tab">Transactions</a></li>
			</ul>
			<div class='tab-content'>
				<div class="tab-pane active" id="tab_earnings">
					<table class='table table-condensed table-striped table-bordered' style="width:1000px">
						<thead>
							<tr bgcolor="#61B329">
								<th>Account Earnings</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php if(empty($earnings)): ?>
							<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
						<?php else: ?>
								<tr>
									<td>Direct Sales</td>
									<td><?=$earnings['direct_sales'] ?></td>		
								</tr>
								<tr>
									<td>Pairing - Starter Pack Sales</td>
									<td><?=$earnings['starter_pack_sales'] ?></td>
								</tr>
								<tr>
									<td>Pairing - Repeat Pack Sales</td>
									<td><?=$earnings['repeat_pack_sales'] ?></td>
								</tr>
								<tr>
									<td>Pairing - Value Pack Sales</td>
									<td><?= $earnings['value_pack_sales'] ?></td>
								</tr>
								<tr>
									<td>CD Amount</td>
									<td><?= $earnings['cd_amount'] ?></td>
								</tr>
								<tr>
									<td>Net Cash Commissions Generated</td>
									<td><?= $earnings['net_cash_commissions'] ?></td>
								</tr>
								<tr>
									<td>Net GC Commissions Generated</td>
									<td><?= $earnings['net_gc_commissions'] ?></td>
								</tr>
								<tr>
									<td>Total Commissions Generated</td>
									<td><?= $earnings['total_commissions'] ?></td>
								</tr>
								<tr>
									<td>Total Commission Encashments</td>
									<td><?= $earnings['total_commission_encashments'] ?></td>
								</tr>
								<tr>
									<td>Commission Balance</td>
									<td><?= $earnings['commission_balance'] ?></td>
								</tr>
						<?php endif; ?>
						</tbody>
					</table>
				</div>		
				<div class='tab-pane' id="tab_points">
					<table class='table table-condensed table-striped table-bordered'>
						<thead>
							<tr bgcolor="#61B329">
								<th>Points and Vouchers</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>placeholder</td>
								<td>placeholder</td>
							</tr>
							<tr>
								<td>temp</td>
								<td>temp</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="tab-pane" id="tab_pairings">
					<table class='table table-condensed table-striped table-bordered'>
						<thead>
							<tr bgcolor="#61B329">
								<th>Pairing Type</th>
								<th>Left</th>
								<th>Right</th>
								<th>Pairs</th>
							</tr>
						</thead>
						<tbody>
							<?php if(empty($account)): ?>
								<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
							<?php else: ?>
								<tr>
									<td>Starter Pack</td>
									<td><?= $account->left_sp; ?></td>
									<td><?= $account->right_sp; ?></td>
									<td><?= $account->pairs_sp; ?></td>
								</tr>
								<tr>
									<td>Value Pack</td>
									<td><?= $account->left_rs ?></td>
									<td><?= $account->right_rs ?></td>
									<td><?= $account->pairs_rs ?></td>
								</tr>
								<tr>
									<td>Repeat Sales</td>
									<td><?= $account->left_vp ?></td>
									<td><?= $account->right_vp ?></td>
									<td><?= $account->pairs_vp ?></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<div class="tab-pane" id="tab_transactions">
					<table class='table table-condensed table-bordered table-striped'>
						<thead>
							<tr bgcolor="#0FDDAF">
								<td>Remarks</td>
								<td>Amount</td>
								<td>Timestamp</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="width: 700px">Withdraw</td>
								<td style="width:200px">-9001.00</td>
								<td style="width: 200px">2012-07-02 11:11:11</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="account_id">Select Account</label>
			<div class="controls">
				<?php
					$options = array();
					foreach ($accounts as $a)
					{
						$options[$a->account_id] = $a->account_id;
					}
					$fields = array('id' => 'account_id');
					echo form_dropdown('account_id', $options, '', $fields);
				?>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('start_date') ?> ">
			<label class="control-label" for="start_date">Start Date</label>
			<div id="start_date_container" class="controls form-inline wc-date">
				<?= form_dropdown('start_date_month', $months, $delivery_month, 'id="start_date_month" class="wc-date-month"') ?>
				<?= form_dropdown('start_date_day', $days, $delivery_day, 'id="start_date_day" class="wc-date-day"') ?>
				<?= form_dropdown('start_date_year', $years, $delivery_year, 'id="start_date_year" class="wc-date-year"') ?>
				<input type="hidden" id="start_date" name="start_date" value="<?= set_value('start_date'); ?>" />
				<p class="help-block"><?= $this->form_validation->error('start_date'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('end_date') ?> ">
			<label class="control-label" for="end_date">End Date</label>
				<div id="end_date_container" class="controls form-inline wc-date">
				<?= form_dropdown('end_date_month', $months, $delivery_month, 'id="end_date_month" class="wc-date-month"') ?>
				<?= form_dropdown('end_date_day', $days, $delivery_day, 'id="end_date_day" class="wc-date-day"') ?>
				<?= form_dropdown('end_date_year', $years, $delivery_year, 'id="end_date_year" class="wc-date-year"') ?>
				<input type="hidden" id="end_date" name="end_date" value="<?= set_value('end_date'); ?>" />
				<p class="help-block"><?= $this->form_validation->error('end_date'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<a id='submit_dates' class="btn btn-primary"><strong>View</strong></a>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
	$("#submit_dates").click(function(){
		$("#submit_type").submit();
	});
	
	$("#get_transactions").click(function(){
		beyond.request({
			url: '/members/earnings/get_account_transactions',
			data: {
				'start_date': $("#start_date_transactions").val(),
				'end_date':  $("#end_date_transactions").val(),
				'account_id': $("#account_id").val(),
				'member_id': $("#member_id").val(),
			},
			on_success: function(data) {
				if (data.status == 'ok'){
				
				}
			}
		});
	});
	
	$(document).ready(function(){

		$('#start_date_month').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$('#start_date_day').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$('#start_date_year').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});

		$('#start_date_month').trigger('change');
		$('#start_date_day').trigger('change');
		$('#start_date_year').trigger('change');

	});
	
	$(document).ready(function(){

		$('#end_date_month').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$('#end_date_day').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$('#end_date_year').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		$('#end_date_month').trigger('change');
		$('#end_date_day').trigger('change');
		$('#end_date_year').trigger('change');

	});
	
	/*$(document).ready(function(){
		$("#submit_type").submit();
	});*/
</script>

