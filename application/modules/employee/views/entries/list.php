<div class="page-header clearfix">
	<h2>My Promo Entries</h2>
</div>

<div class="ui-element">
	<div>
		<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
			<thead>
				<tr>
					<th>Raffle Name</th>
					<th>Encoded Entries</th>
					<th>Entries As Sponsor</th>
					<th>Total Entries</th>
				</tr>
			</thead>
			<tbody id="order_tbody_html">
				<?php if(empty($raffle_promo_details)): ?>
					<tr><td colspan="10" style="text-align:center;"><strong>No Result</strong></td></tr>
				<?php else: ?>
					<?php foreach($raffle_promo_details as $rpd): ?>

					<tr> 
						<td><?= $rpd->name ?> </td>
						<?php
							// get total entries
							$where = array(
								'raffle_id' => $rpd->raffle_id,
								'member_id' => $this->member->member_id 
							);							
							$encoded_entries = $this->raffles_model->get_raffle_entries_count($where);
							
							$where = array(
								'raffle_id' => $rpd->raffle_id,
								'sponsor_member_id' => $this->member->member_id 
							);							
							$entries_as_sponsor = $this->raffles_model->get_raffle_entries_count($where);
							
							$where = "raffle_id = " . $rpd->raffle_id . " AND (member_id = " . $this->member->member_id . " OR sponsor_member_id =" . $this->member->member_id . ")";
							$total_entries = $this->raffles_model->get_raffle_entries_count($where);
							
							//$where = "raffle_id = " . $rpd->raffle_id . " AND member_id = " . $this->member->member_id;							
							//$total_entries_encoded = $this->raffles_model->get_raffle_entries_count($where);
							
							//$where = "raffle_id = " . $rpd->raffle_id . " AND sponsor_member_id = " . $this->member->member_id;
							//$total_entries_sponsor = $this->raffles_model->get_raffle_entries_count($where);
							
							//$total_entries = $total_entries_encoded + $total_entries_sponsor;
						?>								
						<td><?= $encoded_entries ?></td>
						<td><?= $entries_as_sponsor ?></td>
						<td><?= $total_entries ?></td>														
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<form id='frm_filter' class='form-horizontal' method='post' action ='/members/entries/page'>
	<fieldset>
		<div class='clearfix'>
			<div class='span6'>			
				<div class="control-group">
					<label class="pull-left" for="use_date_range">From Date:</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" class="input-medium" id="from_date" name='from_date' readonly='readonly' style='cursor:pointer;' />
							<span id='from_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>					
				<div class="control-group">
					<label class="pull-left" for="use_date_range">To Date:</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" class="input-medium" id="to_date" name='to_date' readonly='readonly' style='cursor:pointer;' />
							<span id='to_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="pull-left" for="use_date_range">Promo Name:</label>
					<div class="controls">
                        <select id="promo_type" name="promo_type">
							<option class="type_options" value="all">ALL</option>
                            <option class="type_options" value="1">Power 8888 Promo</option>
							<option class="type_options" value="2">Repeat Sales Raffle Promo</option>
							<option class="type_options" value="3">Battle Of The Grandmasters</option>	
							<option class="type_options" value="4">Power 8888 Monthly Promo</option>			
                        </select>
                    </div>
				</div>
				
			</div>
		</div>
		<div class="clearfix">
			<div class="span12">
				<button class='btn btn-primary' style='margin-right: 10px;'>&nbsp;&nbsp;Go&nbsp;&nbsp;</button>
				<button id='btn_today' class='btn btn-info'>Today</button>				
			</div>
		</div>
	</fieldset>
	
	<br/>
	<div id="search-result-display">
		<span class="label label-info">Results for:</span>		
		<?php
			if ($promo_type == 0) $promo_name = 'ALL';
			if ($promo_type == 1) $promo_name = 'Power 8888 Promo';
			if ($promo_type == 2) $promo_name = 'Repeat Sales Raffle Promo';
			if ($promo_type == 3) $promo_name = 'Battle Of The Grandmasters';
			if ($promo_type == 4) $promo_name = 'Power 8888 Monthly Promo';
		?>
		<span class="label label-success">Promo Name: <?= $promo_name; ?></span>
		<span class="label label-success">Timestamp: <?= $between_timestamps; ?> </span>		
	</div>	
	
</form>
<div class="ui-element">
	<div>
		<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
			<thead>
				<tr>
					<th>Raffle Number</th>
					<th>Raffle Name</th>						
					<th>Account ID</th>						
					<th>Sponsor Name</th>	
					<th>Date Inserted</th>													
				</tr>
			</thead>
			<tbody id="order_tbody_html">
				<?php if(empty($transactions)): ?>
					<tr><td colspan="10" style="text-align:center;"><strong>No Result</strong></td></tr>
				<?php else: ?>
					<?php foreach($transactions as $t): ?>

					<tr> 
						<td><?= $t->raffle_number ?> </td>
						<?php
							$raffle_details = $this->raffles_model->get_raffle_by_id($t->raffle_id);
						?>								
						<td><?= $raffle_details->name ?></td>								
						<td><?= $t->account_id ?></td>
						
						<?php
							$sponsor_member_details = $this->members_model->get_member_by_id($t->sponsor_member_id);
							
							if (empty($sponsor_member_details)) {
								$sponsor_proper_name = '';
							} else {
							
								$sponsor_proper_name = $sponsor_member_details->last_name  . ", " . $sponsor_member_details->first_name . " " . $sponsor_member_details->middle_name;							
							}
							
							if ($this->member->member_id == $t->sponsor_member_id) {
						?>	
							<td><strong><?= $sponsor_proper_name ?></strong></td>
						<?php	} else { ?>
							<td><?= $sponsor_proper_name ?></td>
						<?php	} ?>
						
						<td><?= $t->insert_timestamp ?></td>									
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>	
<div>
	<?= $this->pager->create_links($get_data); ?>
</div>

<script type="text/javascript">
  //<![CDATA[

	var member_id = "<?=$member_id?>";

	var current_page = 1;
	
	var showTransactions = function(page, filter_by) {
		
	   if (filter_by==null) filter_by = 'BOTH';
        
		b.request({
	        url: '/members/transfers/page',
	        data: {
				"page":page,
				"member_id" : member_id,
				"filter_by":filter_by
			},
		
		
			on_success: function(data, status) {
				if (data.total_records == 0) {
                    $("#order_tbody_html").html('<tr><td colspan="10" style="text-align:center;"><strong> - No Result - </strong></td></tr>');
                } else {
                    $("#order_tbody_html").html(data.html);
                }
				current_page = page;
		    }
		});		
	 }
	
  	$(document).ready(function() {
		showTransactions(1);               
	});			
	

	$(function() {
		
		$("#from_date").datepicker({
            //'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
           // 'timeFormat': 'H:mm:ss',
			'dateFormat' : "yy-mm-dd",			
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datepicker('setDate', '<?= $to_date ?>');
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd'));
			$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd'));
			$('#frm_filter').submit();
		});
			
	});
//]]>
</script>