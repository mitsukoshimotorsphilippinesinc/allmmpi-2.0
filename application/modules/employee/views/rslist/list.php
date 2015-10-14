<div class="page-header clearfix">
	<h2>My Encoded RS Cards <small></small></h2>
</div>
<form id='frm_filter' class='form-horizontal' method='post' action ='/members/rslist/page'>
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
					<label class="pull-left" for="use_date_range">Account ID:</label>
					<div class="controls">
                        <select id="account_id" name="account_id">
                            <option class="account_options" value="all">ALL</option>
							<?php
								$html_option = "";
								foreach ($member_account_details as $mad) {
									$html_option .= "<option class='account_options' value='{$mad->account_id}'>{$mad->account_id}</option>";
								}
							?>					       	
							<?= $html_option; ?>					
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
		<span class="label label-success">Account ID: <?= $account_id; ?></span>
		<span class="label label-success">Timestamp: <?= $between_timestamps; ?> </span>
	</div>	
	
</form>
<div class="ui-element">
	<div>
		<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
			<thead>
				<tr>
					<th>Card ID</th>
					<th>Account ID</th>	
					<th>Status</th>
					<th>Use Type</th>
					<th>Encoded Timestamp</th>								
				</tr>
			</thead>
			<tbody id="order_tbody_html">
				<?php if(empty($transactions)): ?>
					<tr><td colspan="10" style="text-align:center;"><strong>No Result</strong></td></tr>
				<?php else: ?>
					<?php foreach($transactions as $t): ?>

					<tr> 
						<td><?= $t->card_id ?> </td>
						<td><?= $t->account_id ?></td>
						<td><?= $t->status ?></td>
						<td><?= strtoupper($t->use_type)?></td>
						<td><?= $t->used_timestamp ?></td> 						
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
	$(document).on('ready', function(){
		var account_id = "<?= $account_id; ?>";
		$("#account_id").val(account_id);
	});

	var member_id = "<?=$member_id?>";

	var current_page = 1;
	
	var showTransactions = function(page, filter_by) {
		
	   if (filter_by==null) filter_by = 'BOTH';
        
		b.request({
	        url: '/members/rslist/page',
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
		//showTransactions(1);               
	});			
	

	$(function() {
		
		$("#from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
            timeFormat: 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",			
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datepicker('setDate', '<?= $to_date ?>');
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd') + ' 12:00 am');
			$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd h:M:s tt'));
			$('#frm_filter').submit();
		});
			
	});
//]]>
</script>