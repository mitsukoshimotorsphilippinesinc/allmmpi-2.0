<?php
//var_dump($this->uri->segment_array());
$page = $this->uri->segment(5);
?>

<div class="page-header clearfix">
	<h2 >Funds To Paycard</h2>	
</div>
	
	<div class="">

		<form id='frm_filter' class='form-horizontal' method='post' action ='/members/fundstopaycard/page'>
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
							<label class="pull-left" for="use_date_range">Status:</label>
							<div class="controls">
								<select id="status" name="status">
								<option class="status_options" value="ALL">ALL</option>
								<option class="status_options" value="PENDING">PENDING</option>
								<option class="status_options" value="PROCESSED">PROCESSED</option>
								<option class="status_options" value="CANCELLED">CANCELLED</option>
								<option class="status_options" value="VOID">VOID</option>								
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
				<span class="label label-success">Status:<?= $status; ?></span>
				<span class="label label-success">Timestamp:<?= $between_timestamps; ?></span>
			</div>	
			
		</form>
		<div>
			<button id='request-f2p-btn' class="btn btn-primary" style="float:right; margin-bottom: 10px;">Request Funds To Paycard</button>			
		</div>
		<div class='clearfix'></div>
		<div class="ui-element">
				<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
					<thead>
						<tr>
							<th style='text-align:center;'>Gross</th>
							<th style='text-align:center;'>GCEP</th> 				
							<th style='text-align:center;'>Net Of GCEP</th>
							<th style='text-align:center;'>W. Tax</th>
							<th style='text-align:center;'>Net of Tax</th>
							<th style='text-align:center;'>Balance</th>
							<th style='text-align:center;'>Deduction</th>
							<th style='text-align:center;'>Card Fee</th>
							<th style='text-align:center;'>Net</th>
							<th style='text-align:center;'>Paycard</th>
							<th style='text-align:center;'>Date Requested</th>
							<th style='text-align:center;'>Status</th>
							<th style='text-align:center;'>Payout Period</th>
							<th style='text-align:center;width:50px;'>Action</th>
						</tr>
						
					</thead>
					<tbody id="order_tbody_html">
						<?php if(empty($member_f2p)): ?>
							<tr>
								<tr><td colspan='14' style='text-align:center;'><strong>No Records Found</strong></td></tr>
							</tr>
						<?php else: ?>
						<?php foreach ($member_f2p as $f2p): ?>
							<tr data='<?= $f2p->funds_to_paycard_id ?>'>										
								<td style='text-align:right;'><?= $f2p->gross; ?></td>
								<td style='text-align:right;'><?= $f2p->gcep; ?></td>								
								<td style='text-align:right;'><?= $f2p->net_of_gcep; ?></td>	
								<td style='text-align:right;'><?= $f2p->wtax; ?></td>	
								<td style='text-align:right;'><?= $f2p->net_of_wtax; ?></td>	
								<td style='text-align:right;'><?= $f2p->deduction1; ?></td>	
								<td style='text-align:right;'><?= $f2p->deduction2; ?></td>	
								<td style='text-align:right;'><?= $f2p->fee; ?></td>	
								<td style='text-align:right;'><?= $f2p->final_commission; ?></td>
								<td style='text-align:right;'><?= $f2p->cash_card; ?></td>
								<td><?= $f2p->insert_timestamp; ?></td>							
								<?php
									if  ($f2p->status == 'PENDING')  {
										$status_label =  "<span class='status label label-warning'>{$f2p->status}</span>";
									} else if (($f2p->status == 'CANCELLED') || ($f2p->status == 'VOID')) {
										$status_label =  "<span class='status label label-important'>{$f2p->status}</span>";
									} else {
										$status_label =  "<span class='status label label-success'>{$f2p->status}</span>";
									}
								?>
								
								<?php
									$payout_period = $f2p->start_date . ' to ' . $f2p->end_date; 
								?>
								
								<td><?= $status_label; ?></td>
								<td><?= $payout_period; ?></td>
								<td>
									<?php				
									if ($f2p->status == 'PROCESSED') {
									
										//echo "
										//		<a class='btn btn-small btn-primary transfer-voucher-btn' data='{$f2p->funds_to_paycard_id}' title='View'><i class='icon-info-sign icon-white'></i></a>";
											
									} 
									
									if ($f2p->status == 'PENDING') {
									
										echo "												
												<a class='btn btn-small btn-danger cancel-f2p-btn' data='{$f2p->funds_to_paycard_id}' title='Cancel'><i class='icon-remove icon-white'></i></a>";
									}					
									?>
								</td>
							
							</tr>
						<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
		</div>
		<div>				
			<?= $this->pager->create_links($get_data); ?>
		</div>
	
	</div>

</div>

<script id="test" type="text/javascript">

	$(document).ready(function(){
		<?php 
			$page = $this->uri->segment(4);
		?>
		var _page = <?= ($page) ? $page : 1  ?>;
		
		
		var _hasError = 1;

		$('.cancel-f2p-btn').live("click",function() {
			funds_to_paycard_id = $(this).attr("data");
			
			b.request({
				url : '/members/fundstopaycard/cancel_transfer',
				data : {
						"funds_to_paycard_id" : funds_to_paycard_id
						},
						
				on_success : function(data) {
					if (data.status == "1")	{
						// show add form modal					
						var withPendingCancelModal = b.modal.new({
							title: 'Funds To Paycard :: Cancel',
							width:400,
							disableClose: true,
							html: data.data.html,
							buttons: {								
								'Proceed' : function() {									
									if (data.status == "1")	{
										withPendingCancelModal.hide();	
										b.request({
											url : '/members/fundstopaycard/proceed_cancel_f2p',
											data : {
												'funds_to_paycard_id' : funds_to_paycard_id
												},										
											on_success : function(data) {
												
												if (data.status == "1")	{
												
													var cancelTransferModal = b.modal.new({
														title: 'Funds To Paycard :: Cancelled',
														width: 450,
														disableClose: true,
														html: data.data.html,
														buttons: {																
															'Ok' : function() {
																cancelTransferModal.hide();
																redirect('/members/fundstopaycard');
															}
														}
													});
													cancelTransferModal.show();
													
												
												} else {
													var errorCancelTransferModal = b.modal.new({
														title: 'Funds To Paycard :: Error',
														width: 450,
														disableClose: true,
														html: data.data.html,
														buttons: {																
															'Ok' : function() {
																errorCancelTransferModal.hide();
															}
														}
													});
													errorCancelTransferModal.show();
												}
											}
										})
										
									} else {
										var errorConfirmationModal = b.modal.new({
											title: 'Funds To Paycard :: Error',
											width: 450,
											disableClose: true,
											html: data.data.html,
											buttons: {																				
												'Ok' : function() {
													errorConfirmationModal.hide();
												}
											}
										});
										errorConfirmationModal.show();
									}								
								},
								'Close' : function() {
									withPendingCancelModal.hide();								 							
								}
							}
						});
						
						withPendingCancelModal.show();	
						
					} else {
						// show add form modal					
						withPendingCancelModal = b.modal.new({
							title: 'Funds To Paycard :: Error',
							width:450,
							disableClose: true,
							html: data.data.html,
							buttons: {
								'Close' : function() {
									withPendingCancelModal.hide();								 							
								}
							}
						});
						withPendingCancelModal.show();					
					}
				}
			})	
			
		});
		
		
		$('#request-f2p-btn').live("click",function() {
			//funds_to_paycard_id = $(this).attr("data");
			
			b.request({
				url : '/members/fundstopaycard/request_transfer',
				data : {
						//"funds_to_paycard_id" : funds_to_paycard_id
						},
						
				on_success : function(data) {
					if (data.status == "1")	{
						// show add form modal					
						var withPendingCancelModal = b.modal.new({
							title: 'Funds To Paycard :: Request',
							width:400,
							disableClose: true,
							html: data.data.html,
							buttons: {								
								'Proceed' : function() {									
									if (data.status == "1")	{
									
										// check if requested amount is sufficient to members funds
										var f2p_amount = parseInt($('#f2p_amount').val());
										var minimum_f2p_amount = parseInt(data.data.minimum_f2p_amount);
										var maximum_f2p_amount = parseInt(data.data.maximum_f2p_amount);
									
										if (maximum_f2p_amount == 0) {
											var _amount_to_check_max = parseInt(data.data.member_funds);
										} else {
											var _amount_to_check_max = parseInt(maximum_f2p_amount);
										}
									
										if (_amount_to_check_max <  f2p_amount) {
											$("#f2p_amount_error").html("Request exceeds your available Funds.");
											$("#f2p_amount_error").show();
											return false;
										} 
										
										if (f2p_amount <  minimum_f2p_amount) {
											$("#f2p_amount_error").html("The minimum allowable request is " + minimum_f2p_amount + ".");
											$("#f2p_amount_error").show();
											return false;
										}
										
										
										withPendingCancelModal.hide();	
										b.request({
											url : '/members/fundstopaycard/proceed_request_f2p',
											data : {
												'f2p_amount' : f2p_amount
												},										
											on_success : function(data) {
												
												if (data.status == "1")	{
												
													var cancelTransferModal = b.modal.new({
														title: 'Funds To Paycard :: Cancelled',
														width: 450,
														disableClose: true,
														html: data.data.html,
														buttons: {																
															'Ok' : function() {
																cancelTransferModal.hide();
																redirect('/members/fundstopaycard');
															}
														}
													});
													cancelTransferModal.show();
													
												
												} else {
													var errorCancelTransferModal = b.modal.new({
														title: 'Funds To Paycard :: Error',
														width: 450,
														disableClose: true,
														html: data.data.html,
														buttons: {																
															'Ok' : function() {
																errorCancelTransferModal.hide();
															}
														}
													});
													errorCancelTransferModal.show();
												}
											}
										})
												
									} else {
										var errorConfirmationModal = b.modal.new({
											title: 'Funds To Paycard :: Error',
											width: 450,
											disableClose: true,
											html: data.data.html,
											buttons: {																				
												'Ok' : function() {
													errorConfirmationModal.hide();
												}
											}
										});
										errorConfirmationModal.show();
									}								
								},
								'Close' : function() {
									withPendingCancelModal.hide();								 							
								}
							}
						});
						
						withPendingCancelModal.show();	
						
						$("#f2p_amount").keypress(function (e) {
							if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
								return false;
							}
						});
						
					} else {
						// show add form modal					
						withPendingCancelModal = b.modal.new({
							title: 'Funds To Paycard :: Error',
							width:450,
							disableClose: true,
							html: data.data.html,
							buttons: {
								'Close' : function() {
									withPendingCancelModal.hide();								 							
								}
							}
						});
						withPendingCancelModal.show();					
					}
				}
			})				
		});
		
	});
	
	
	$(function() {
		
		$("#from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'changeMonth' : true
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
            timeFormat: 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,	
			'changeMonth' : true
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
		
		var dl_url = b.uri.path + (b.uri.query.length > 0 ? '?' + b.uri.query + '&export=excel' : '?export=excel');
		$('#btn_download').attr('href', dl_url);

	});

    //end of js for accounts tab	
</script>