<?php
	$from_date = date('Y-m-d H:i a',strtotime($account->insert_timestamp));
	$to_date = date('Y-m-d H:i a');
	
	$sponsor_name = '';
	$upline_name = '';
	
	if (!empty($sponsor))
		$sponsor_name = $sponsor->first_name." ".$sponsor->middle_name." ".$sponsor->last_name;
		
	if (!empty($upline))
		$upline_name = $upline->first_name." ".$upline->middle_name." ".$upline->last_name;
		
	$register_date = strtotime($account->insert_timestamp);
	$date_registered = date('F d, Y', $register_date); 
	
	$monthly_maintenance_count = $account->monthly_maintenance_ctr + $account->ms_monthly_maintenance_ctr;
	$annual_maintenance_count = $account->annual_maintenance_ctr + $account->ms_annual_maintenance_ctr;
?>
<div class="container" style="margin-bottom:16px;">
	<div class="row-fluid">
		<div  class="profile-tab-container span6">
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Account Type: </label>
				<label style="display: inline;"><?= $account_type; ?></label>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Date Registered: </label>
				<label style="display: inline;"><?= $date_registered; ?></label>
			</div>
			<br />
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Monthly Maintenance Counter: </label>
				<label style="display: inline;"><?= $monthly_maintenance_count; ?></label>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Yearly Maintenance Counter: </label>
				<label style="display: inline;"><?= $annual_maintenance_count; ?></label>
			</div>
		</div>
		
		
		<div  class="profile-tab-container span6">
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Sponsor ID:</label>
				<label style="display: inline;"><?= $account->sponsor_id; ?></label>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Sponsor Name:</label>
				<label style="display: inline;"><?= $sponsor_name ?></label>
			</div>
			<br />
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Upline ID:</label>
				<label style="display: inline;"><?= $account->upline_id; ?></label>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="display: inline;">Upline Name:</label>
				<label style="display: inline;"><?= $upline_name ?></label>
			</div>
		</div>
	</div>
</div>
<div>
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active" data="earnings"><a href="#earnings" id='account_earnings_button' data-toggle="tab">Earnings</a></li>
			<li data="igpsm_genealogy"><a href="#igpsm_genealogy" id='igpsm_genealogy_button' data-toggle="tab">IGPSM Genealogy</a></li>
			<li data="unilevel_genealogy"><a href="#unilevel_genealogy" id='unilevel_genealogy_button' data-toggle="tab">Unilevel Genealogy</a></li>			
			<?php 			
			if ($no_of_accounts<$this->settings->max_no_of_accounts) 
			{
				echo "<li data='add_account'><a href='#add_account' id='add_account_button' data-toggle='tab'>Add New Account</a></li>";
			} 
			?>
        </ul>
		<div class="tab-content">
			<div class="tab-pane active" id="earnings">
				<div class='section-header'><h3>Account Earnings as of <?= date("F d, Y"); ?></h3></div>
				<div class='span6' style='margin: 5px 0px;'>
					<table class='table table-bordered table-condensed table-striped'>
						<thead>
							<tr><td><h4>IGPSM Earnings This Cut-Off</h4></td></tr>
						</thead>
						<tbody>
							<tr><td style='text-align:center;'><h3 id='weekly_igpsm'>0.00</h4></td></tr>
						</tbody>
					</table>
				</div>
				<div class='span6' style='margin: 5px 20px;'>
					<table class='table table-bordered table-condensed table-striped'>
						<thead>
							<tr><td><h4>Unilevel Earnings This Month</h4></td></tr>
						</thead>
						<tbody>
							<tr><td style='text-align:center;'><h3 id='monthly_unilevel'>0.00</h4></td></tr>
						</tbody>
					</table>
				</div>
				<hr/>
				<div style="margin-bottom:30px;">
					<div class="row">
						<div class="span span6 earnings_summary">
							<table class='table table-condensed table-striped table-bordered'>
								<thead>
									<tr>
										<th colspan="2">IGPSM Account Earnings</th>
									</tr>
									<tr>
										<th style="width:250px;">Type</th>
										<th>Earnings</th>
									</tr>
								</thead>
								<tbody id="igpsm_earnings">
								</tbody>
							</table>
						</div>
						<div class="span span6 earnings_summary">
							<table class='table table-condensed table-striped table-bordered'>
								<thead>
									<tr>
										<th colspan="2">Unilevel Account Earnings</th>
									</tr>
									<tr>
										<th style="width:270px;">Type</th>
										<th>Earnings</th>
									</tr>
								</thead>
								<tbody id="unilevel_earnings">
								</tbody>
							</table>
							<div>
								<table class='table table-condensed table-striped table-bordered'>
									<thead>
										<tr>
											<th colspan="4">Current Points</th>
										</tr>
										<tr>
											<th style="width:170px">Type</th>
											<th>Left</th>
											<th>Right</th>
											<th>Pairs</th>
										</tr>
									</thead>
									<tbody>
										<!--tr style='display:none;'-->

									
									<?php
										// defined order list from client request on mantis 3781
										$predefined_list = array('ERHM','RS','VP','UP1','P2P');

										$defined_reorder_list = array();
										foreach($predefined_list as $v) $defined_reorder_list[$v] = $all_card_type_pairing[$v];

										$other_list = array();

										//var_dump($all_card_type_pairing);	

										foreach($all_card_type_pairing as $k => $v) if(!in_array($k, $predefined_list)) $other_list[$k] = $v;

										$defined_reorder_list = array_merge($defined_reorder_list, $other_list);
										
										foreach($defined_reorder_list as $key => $all) {
	
											$key_details = $this->cards_model->get_card_type_by_code($key);
											
											$key_name = $key_details->name;
											$key_code = $key;
										
											if (empty($all[0])) {
												
												if ($key == 'P2P') {
													$key_name = "(P-P)<sup>3</sup>";
													$key_code = "(P-P)<sup>3</sup>";
												}
												
												echo "
													<tr>
														<td>{$key_name} ({$key_code})</td>
														<td style='text-align: right;'>" . number_format(0) . "</td>	
														<td style='text-align: right;'>" . number_format(0) . "</td>	
														<td style='text-align: right;'>" . number_format(0, 2) . "</td>
													</tr>";	
											} else {
												
												if ($key == 'P2P') {
													$key_name = "(P-P)<sup>3</sup>";
													$key_code = "(P-P)<sup>3</sup>";
												}

												$card_pair_count = 0;
												//if($all[0]) {
													if ($all[0]->pair_count > 10) {
														$point_pos = strpos($all[0]->pair_count, ".");
														if (($point_pos == NULL) || ($point_pos == "")) {
															$point_pos = 0;
														}
														
														$card_pair_count = 10 + (substr($all[0]->pair_count, $point_pos, strlen($all[0]->pair_count) - $point_pos));
													} else {
														$card_pair_count = $all[0]->pair_count;
													}
												//}	
												
												echo "
													<tr>
														<td>{$key_name} ({$key_code})</td>
														<td style='text-align: right;'>" . number_format(($card_pair_count >= $max_pairs)?0:$all[0]->left_count) . "</td>	
														<td style='text-align: right;'>" . number_format(($card_pair_count >= $max_pairs)?0:$all[0]->right_count) . "</td>	
													";
												
												echo "
														<td style='text-align: right;'>" . number_format(($card_pair_count >= $max_pairs)?$max_pairs:$card_pair_count, 2) . "</td>
													</tr>												
												";
												
											}
											
										}									
									?>

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div style="margin-bottom:30px;">
					<div class="row">
						
						<div class="span span3">
							<div class="control-group ">
								<label for="earnings-start-date" class="control-label"><strong>Start Date</strong></label>
								<div class="controls">
									<div class="input-append" >
										<input title="Start Date" class="input-medium" type="text" id="earnings-start-date" name="earnings-start-date" value="" readonly="readonly">
										<span id='earnings-start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
									</div>
								</div>
							</div>
						</div>
						<div class="span span3">
							<div class="control-group ">
								<label for="earnings-end-date" class="control-label"><strong>End Date</strong></label>
								<div class="controls">
									<div class="input-append" >
										<input title="End Date" class="input-medium" type="text" id="earnings-end-date" name="earnings-end-date" value="" readonly="readonly">
										<span id='earnings-end-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
									</div>
								</div>
							</div>
						</div>
											
						<div class="span span3">
							<div class="control-group ">
								<label class="control-label" for="end_date">Earning Type</label>
								<div class="input-append">
									<select id="earning-type">
										<?php
											$_defined_order = array('IGPSM','UNILEVEL','REFERRAL','ERHM','RS','VP','GC','UP1','P2P');
											$_undefined_order = array();

											$where = "display_on_dashboard = 1";
											$card_type_details = $this->cards_model->get_card_types($where, '', 'card_type_id');

											$_lookup = array();

											foreach ($card_type_details as $ctd)
											{
												$_lookup[$ctd->code] = $ctd->name;
												if(!in_array($ctd->code, $_defined_order)) $_undefined_order[] = $ctd->code;
											}

											$_render_list = array_merge($_defined_order,$_undefined_order,array('ALL'));

											foreach($_render_list as $v)
											{
												$detail = ($_lookup[$v] != "")?" - {$_lookup[$v]}":"";
												echo "<option value='{$v}'>{$v}{$detail}</option>";
											}
											
											// $where = "display_on_dashboard = 1";
											// $card_type_details = $this->cards_model->get_card_types($where, '', 'card_type_id');
											
											// foreach ($card_type_details as $ctd) {
											// 	echo "<option value='{$ctd->code}'>{$ctd->code} - {$ctd->name}</option>";
											// }
										?>
									
										<!--option value="SP_PAIRING">SP Pairing</option>
										<option value="VP_PAIRING">VP Pairing</option>
										<option value="RS_PAIRING">RS Pairing</option-->									
										<!-- <option value="GC">GC (Gift Cheque)</option>
										<option value="IGPSM">IGPSM</option>																	
										<option value="UNILEVEL">Unilevel</option>
										<option value="REFERRAL">Referral</option>
										<option value="ALL">All</option> -->
									</select>
								</div>
							</div>
						</div>
						
						<div class="span1">
							<div class="control-group">
								<label class="control-label">&nbsp;</label>
								<div class="controls">
									<a id='get_account_earning_history' class="btn btn-primary" data="<?= $account_id; ?>"><strong>View</strong></a>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='span span12'>
							<table class='table table-condensed table-striped table-bordered'>
								<thead>
									<tr>
										<th colspan="10">Earnings History</th>
									</tr>
									<tr>
										<th>Remarks</th>
										<th style="width:110px;">From Account ID</th>
										<th style="width:100px;">Amount</th>
										<th style="width:50px;">Level</th>
										<th style="width:50px;">Credit As</th>
										<th style="width:50px;">Card Type</th>
										<th style="width: 150px;">Date</th>
									</tr>
								</thead>
								<tbody id="account_history">
									<tr><td colspan="7" style="text-align: center;">No Entries Found</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="igpsm_genealogy">
			</div>
			<div class="tab-pane" id="unilevel_genealogy">
			</div>
			<?php 			
			if ($no_of_accounts<$this->settings->max_no_of_accounts) 
			{
				echo "<div class='tab-pane' id='add_account'>
						<div id='add_account_container'></div>
					</div>";
			} 
			?>			
			
			
		</div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		
		var currDate = new Date();
		var currYear = new Date().getFullYear();
		var yrRange = "2005:" + currYear;
		
		$("#earnings-start-date").datepicker({
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		$("#earnings-start-date").datepicker('setDate', currDate);
		
		$("#earnings-start-date-icon").click(function(e) {
			$("#earnings-start-date").datepicker("show");
		});

		$("#earnings-end-date").datepicker({
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		var _end_date = new Date();
		_end_date.setDate(_end_date.getDate()+6);
		$("#earnings-end-date").datepicker('setDate', _end_date);
		
		$("#earnings-end-date-icon").click(function(e) {
			$("#earnings-end-date").datepicker("show");
		});
		
		
		$('#get_account_earning_history').click(function(e) {
			e.preventDefault();			
			displayHistory($('#earnings-start-date').val(), $('#earnings-end-date').val(), $('#earning-type').val());
		});
		
		
		displayEarnings();		
		//displayHistory($('#earnings-start-date').val(), $('#earnings-end-date').val(), $('#earning-type').val());
		
	});
</script>