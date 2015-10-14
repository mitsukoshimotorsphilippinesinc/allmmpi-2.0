<?php
	// get proper name and username using member_id
	$member_details = $this->members_model->get_member_by_id($member_id);
	$proper_name = $member_details->first_name . ' ' . $member_details->middle_name . ' ' . $member_details->last_name; 
	//$username_display = '[ '. $member_details->username . ' ]';
	$username_display = "";

	$facility_tags = "<select id='facility_id'>";
	foreach($facilities as $f)
	{
		$facility_tags .= "<option value='{$f->facility_id}'>{$f->facility_name}</option>";
	}
	$facility_tags .= "</select>";
	
	$proper_payment_method = strtoupper($payment_method);
	$proper_status = strtoupper($status);
	
?>

<div class="page-header clearfix">
	<h2>My Orders <small></small></h2>
</div>
<form id='frm_filter' class='form-horizontal' method='post' action ='/members/orders/page'>
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
					<label class="pull-left" for="use_date_range">Payment Method:</label>
					<div class="controls">
                        <select id="payment_method" name="payment_method">
                            <option class="payment_options" value="all">ALL</option>
							<option class="payment_options" value="cheque">CHEQUE</option>
							<option class="payment_options" value="funds">FUNDS</option>
							<option class="payment_options" value="giftcheque">GC</option>
							<option class="payment_options" value="onlinegiftcheque">ONLINE GC</option>
							<option class="payment_options" value="otc" selected="selected">OTC</option>
							<option class="payment_options" value="paypal">PAYPAL</option>
                        </select>
                    </div>
				</div>
				<div class="control-group">
					<label class="pull-left" for="use_date_range">Status:</label>
					<div class="controls">
						<select id="status" name="status">
						<option class="status_options" value="all">ALL</option>
						<option class="status_options" value="pending">PENDING</option>
						<option class="status_options" value="completed">COMPLETED</option>
						<option class="status_options" value="released">RELEASED</option>
					</select>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">
			<div class="span12">
				<button class='btn btn-primary' style='margin-right: 10px;'>&nbsp;&nbsp;Go&nbsp;&nbsp;</button>
				<button id='btn_today' class='btn btn-info'>Today</button>
				<a id='btn_download' href='#' target='_blank' class='offset2 btn btn-success'>Download</a>
			</div>
		</div>
	</fieldset>
	
	<br/>
	<div id="search-result-display">
		<span class="label label-info">Results for:</span>
		<span class="label label-success">Payment Method: <?= $proper_payment_method; ?></span>
		<span class="label label-success">Status: <?= $proper_status; ?></span>
		<span class="label label-success">Timestamp: <?= $between_timestamps; ?> </span>
	</div>	
	
</form>
<div class="ui-element">
	<div>
		<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
			<thead>
				<tr>
					<th style="text-align:center;width: 140px;">Transaction Code</th>
					<th style='text-align:center;width:110px;'>Facility</th>
					<th style='text-align:center;width:110px;'>Pickup Location</th>
					<th style='text-align:center;width:110px;'>Products</th>
					<th style='text-align:center;width:50px;'>Payment Method</th>
					<th style='text-align:center;width:50px;'>Amount</th>
					<th style='text-align:center;'>Status</th>
					<th style='text-align:center;'>Date Ordered</th>
					<?php					
					if (($payment_method <> 'all') && (trim($payment_method) <> '')) {
						echo "<th style='text-align:center;'>Payment Details</th>";
					}
					?>
				</tr>
				
			</thead>
			<tbody id="order_tbody_html">
				<?php if(empty($transactions)): ?>
					<tr><td colspan="10" style="text-align:center;"><strong>No Result</strong></td></tr>
				<?php else: ?>
					<?php foreach($transactions as $t): ?>
					<?php
						$facility_name = "NONE";
						if($t->facility_id != 0) 
						{	
							$facility_name = $facilities_array[$t->facility_id];
						}
						$releasing_facility_name = "NONE";
						if($t->releasing_facility_id != 0)
						{
							$releasing_facility_name = $facilities_array[$t->releasing_facility_id];
						}
						if(is_null($t->ar_number))
						{
							if ($t->status != "CANCELLED") {
								$releasing_facility_name .= "<br /><a href='#' class='change_facility' data='{$t->transaction_id}' data-releasing_facility_id='{$t->releasing_facility_id}'>Change Facility</a>";
							}
						}
						
						//set products bought
						$transaction_products = $this->payment_model->get_payment_transaction_products(array('transaction_id' => $t->transaction_id, 'package_product_id' => 0, 'voucher_type_id' => 0));
						$products_div = "<div class='items_list' style='height:35px; overflow:hidden;'><ul class='unstyled'>";
						$package_length = 0;
						foreach($transaction_products as $p)
						{
							$product = $this->items_model->get_product_by_id($p->product_id);
							$products_div .= "<li>{$p->quantity}x {$product->product_name}</li>";
							
							$package = $this->items_model->get_product_product_by_product_id($p->product_id);
							if(!empty($package)) //is a package
							{
								$package_length = 1;
								$product_products = $this->payment_model->get_payment_transaction_products(array('transaction_id' => $t->transaction_id, 'package_product_id' => $p->product_id));
								foreach($product_products as $prod)
								{
									$child = $this->items_model->get_product_by_id($prod->product_id);
									$products_div .= "<li>&nbsp;&nbsp;&nbsp; - {$prod->quantity}x {$child->product_name} </li>";
								}
							}
						}
						//fpv/mpv voucher products
						$voucher_products = $this->payment_model->get_payment_transaction_products("transaction_id = {$t->transaction_id} and package_product_id = 0 and  voucher_type_id != 0");
						$voucher_products_arr = array();
						if(!empty($voucher_products)){
							foreach($voucher_products as $p)
							{
								$product = $this->items_model->get_product_by_id($p->product_id);
								$voucher_products_arr[$p->voucher_code][]= "{$p->quantity}x {$product->product_name}";
							}
						}
						if(!empty($voucher_products_arr)){
							foreach($voucher_products_arr as $voucher_code => $products)
							{
								$products_div .= "<li>{$voucher_code} </li>";
								foreach($products as $prod)
								{
									$products_div .= "<li>&nbsp;&nbsp;&nbsp; - {$prod} </li>";
								}
							}
						}
						$products_div .= "</ul></div>";
						if((count($transaction_products) + count($voucher_products)) >  2 || $package_length == 1)
							$products_div .= "<a href='#' class='more_items'>More...</a>";
						
						if ($t->transaction_type == 'onlinegiftcheque') {
							$proper_payment_method = 'ONLINE GC';
						} else if ($t->transaction_type == 'giftcheque') {
							$proper_payment_method = 'GC';	
						} else {						
							$proper_payment_method = strtoupper($t->transaction_type);	
						}	
					?>
					<tr data='<?= $t->transaction_id ?>' >
						<td style='text-align:center;'><?= $t->transaction_code ?> </td>
						<td style='text-align:center;'><?= $facility_name ?></td>
						<td style='text-align:center;'><?= $releasing_facility_name ?></td>
						<td><?= $products_div ?></td>
						<td style='text-align:center;'><span class='label label-info'><?= $proper_payment_method ?></span></td> 
						<td style='text-align:right;'><?= number_format($t->total_amount,2) ?></td>
						
						<?php
							$label_status = 'status label';
							if ($t->status == 'RELEASED') {
								$label_status = 'status label label-success';
							} else if ($t->status == 'COMPLETED') {
								$label_status = 'status label label-info';
							} else {
								$label_status = 'status label label-important';
							}
						?>
						
						
						<td style='text-align:center;'><span id='status_<?= $t->transaction_id ?>' class='<?= $label_status; ?>'><?= $t->status ?></span></td>
						<td><?= date("Y-m-d h:i:s",strtotime($t->insert_timestamp)) ?></td> 
						
						<?php					
						if (($payment_method <> 'all') && (trim($payment_method) <> '')) {
							
							// check if transaction has many mode of payment
							$where_tpc = "transaction_id = {$t->transaction_id}";
							$transaction_payment_count = $this->payment_model->get_payment_transaction_details_count($where_tpc);
							
							if ($transaction_payment_count > 1) {
								echo "<td><button class='btn btn-small btn-primary btn-view-details' style='margin-left:20px;margin-top:10px;' data='{$t->transaction_id}' data1='{$t->transaction_code}'>View</button></td>";
							} else {
								echo "<td></td>";
							}							
						}
						?>
						
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

  	$(document).ready(function() {
		var _status = "<?= $status; ?>";
		var _paymentMethod = "<?= $payment_method; ?>";
		
		$("#status").val(_status);
		$("#payment_method").val(_paymentMethod);
		
	<?php
		if($transaction_notice)
		{
	?>
		var notice_modal = b.modal.create({
			title: "<?= $transaction_notice['payment_method'] ?> PAYMENT",
			html: "<?= $transaction_notice['message'] ?>",
			width: 400
		});
		notice_modal.show();
	<?php
		}
	?>
	});			
	
	$(document).on('click', '.more_items', function(e) {
		e.preventDefault();
		var item = $(this);
		//change 'More' to 'Less'
		item.parent().children('.more_items').html('Less...');
		item.parent().children('.more_items').attr('class', 'less_items');
		//display the rest of the items
		item.parent().children('.items_list').attr('style', 'height:auto; overflow:auto');
	});

	$(document).on('click', '.less_items', function(e) {
		e.preventDefault();
		var item = $(this);
		//change 'Less' to 'More'
		item.parent().children('.less_items').html('More...');
		item.parent().children('.less_items').attr('class', 'more_items');
		//display only first two items
		item.parent().children('.items_list').attr('style', 'height:35px; overflow:hidden');
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
	
	$("body").on('click', ".change_facility", function(e){
		e.preventDefault();
		var facility_tags = "<?=$facility_tags?>";
		var transaction_id = $(this).attr('data');
		//var releasing_facility_id = $(this).attr('data-releasing_facility_id');
		//$("#facility_id option[value='"+releasing_facility_id+"']").attr('selected', 'selected');
		
		var change_facility_modal = b.modal.create({
			title: 'Change Facility for Pick Up',
			html: "<label>Select Facility:</label> <div>"+facility_tags+"</div>",
			width: 350,
			disableClose: true,
			buttons: {
				'Save': function() {
					var facility_id = $("#facility_id").val();
					change_facility_modal.hide();
					showLoading();
					b.request({
						url: "/members/orders/change_transaction_facility",
						data: {
							'transaction_id': transaction_id,
							'facility_id': facility_id
						},
						on_success: function(data){
							if(data.status == 'ok')
							{
								var transaction_code = data.data.transaction_code;
								var success_modal = b.modal.create({
									title: 'Change Facility Success',
									html: "You have successfully changed facility for transaction "+transaction_code+".",
									width: 400,
									disableClose: true,
									buttons: {
										'Close': function() {
											success_modal.hide();
											redirect("/members/orders");
										}
									}
								});
								success_modal.show();
							}
							else if(data.status == 'error'){
								var error_modal = b.modal.create({
									title: 'Unable to Change Facility',
									html: data.msg + ' ' + data.data.html,
									width: 500
								});
								error_modal.show();
							}
						}
					});
				},
				'Cancel' : function() {
					change_facility_modal.hide();
				}
			}
		});
		change_facility_modal.show();
	});
	
	
	$("body").on('click', ".btn-view-details", function(e){
		e.preventDefault();		
		var transaction_id = $(this).attr('data');
		var transaction_code = $(this).attr('data1');
		
		
		b.request({
			url: "/members/orders/get_transaction_details",
			data: {
				'transaction_id': transaction_id,
				'transaction_code': transaction_code
			},
			on_success: function(data){
				if(data.status == 'ok')
				{					
					var success_modal = b.modal.create({
						title: 'Payment Details :: ' + transaction_code,
						html: data.data.html,
						width: 600,
						disableClose: true,
						buttons: {
							'Ok': function() {
								success_modal.hide();								
							}
						}
					});
					success_modal.show();
				}
				else if(data.status == 'error'){
					var error_modal = b.modal.create({
						title: 'Payment Details :: Error',
						html: data.data.html,
						width: 500
					});
					error_modal.show();
				}
			}
		});	
	});
	
	
//]]>
</script>