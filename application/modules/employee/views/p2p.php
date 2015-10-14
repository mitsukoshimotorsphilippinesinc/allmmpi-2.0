<div class="page-header">
  <h2>(P-P)<sup>3</sup> <small></small></h2>
</div>
<div>
	<div class="tabbable">
		<ul class="nav nav-tabs">
            <li class="active" data="cards"><a href="#cards" data-toggle="tab">Cards</a></li>
			<li data="inventory"><a href="#inventory" data-toggle="tab">Inventory</a></li>
			<li data="transactions"><a href="#transactions" data-toggle="tab">Transactions</a></li>
        </ul>
		<div class="tab-content">
			<div class="tab-pane active" id="cards">
				<table class='table table-striped table-bordered'>
					<thead>
						<tr>
							<th>Card ID</th>
							<th>Card Code</th>
							<th>Card Status</th>
							<th>Transaction Code</th>
							<th>Assigned Products</th>
							<th style="width: 40px;"></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($cards as $card) : ?>
						<tr>
							<td><?= $card->card_id ?></td>
							<td><?= $card->card_code ?></td>
							<td><?= $card->status ?></td>
							<td><?= $card->transaction_code ?> <br /> Date Ordered: <?= $card->date_ordered?></td>
							<td>
								<?php if(sizeof($card->products) == 0) : ?>
								<span><i>none selected</i></span>
								<?php else : ?>
								<table class='table table-bordered'>
									<tbody>
										<?php foreach($card->products as $prods) : ?>
										<tr>
											<td><?= $prods->name ?></td>
											<td style="width: 30px;">x<?= $prods->qty ?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
								<?php endif; ?>
							</td>
							<td>
								<?php if(sizeof($card->products) == 0) : ?>
								<button class="btn btn-primary btn-select-products" data-cid="<?= $card->card_id ?>"><i class="icon-edit icon-white"></i></button>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			
			<div class="tab-pane" id="inventory">
				<table class='table table-striped table-bordered'>
					<thead>
						<tr>
							<th>Product</th>
							<th style="width: 50px;">Qty</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($products as $product) : ?>
						<tr>
							<td><?= $product->name ?></td>
							<td><?= $product->quantity ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			
			<div class='tab-pane' id='transactions'>
				<table class='table table-striped table-bordered'>
					<thead>
						<tr>
							<th>Date Ordered</th>
							<th>Transaction Code</th>
							<th>Released Cards</th>
							<th>(P-P)3 Package Count</th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($p2p_transactions)): ?>
						<?php foreach($p2p_transactions as $t): ?>
							<tr>
								<td><?= date('M d, Y g:i:s A', strtotime($t->insert_timestamp)) ?></td>
								<td><?= $t->transaction_code ?></td>
								<td><?= $t->p2p_cards_released ?></td>
								<td><?= $t->p2p_products_count ?></td>
							</tr>
						<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan='0'><center>No records found.</center></td></tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/template" id="card_product_selection_template">
	<h5>Please select a total of exactly 2 quantity of any items below.</h5>
	<table class='table table-striped table-bordered'>
		<thead>
			<tr>
				<th>Product</th>
				<th>Available Qty</th>
				<th>Selected Qty</th>
			</tr>
		</thead>
		<tbody>
			<% $.each(products, function(i, v) { %>
			<tr>
				<td>
					<div><%= v.name %></div>
					<div>SRP <%= v.srp %></div>
				</td>
				<td><%= v.quantity %></td>
				<td><input type="text" class="input input-small selected-product-qty" data-pid="<%= v.product_id %>" data-maxqty="<%= v.quantity %>" value="0" /></td>
			</tr>
			<% }); %>
		</tbody>
	</table>
	<div class="alert alert-error hide selection-error">
		<span class="msg"></span>
	</div>
</script>
<script type="text/javascript">
	$('document').ready(function(){

		$('.btn-select-products').click(function(e){
			e.preventDefault();
			var card_id = $(this).data('cid');

			beyond.request({
				url: '/members/p2p/get_card_selection_products',
				on_success: function(data){
					var selection_modal = beyond.modal.create({
						title: 'Product Selection for '+card_id,
						html: _.template($('#card_product_selection_template').html(), data.data),
						buttons: {
							'Assign to Card': function(){
								var total_qty = 0;
								$('.selected-product-qty').each(function(){
									total_qty += ($(this).val()*1);
								});
								if(total_qty == 0) {
									$('.selection-error').show().children('.msg').text("Total selected quantity may not be 0");
									return;
								}

								if(total_qty != 2) {
									$('.selection-error').show().children('.msg').text("Total selected quantity must be 2");
									return;
								}

								var confirm_modal = beyond.modal.create({
									title: 'Product Selection for '+card_id,
									html: 'Are you sure you want to assigned these products to this card?',
									disableClose: true,
									buttons: {
										'Yes': function(){
											confirm_modal.hide();

											var products = [];
											$('.selected-product-qty').each(function(){
												if(($(this).val()*1) > 0) {
													products.push({
														product_id: $(this).data('pid'),
														qty: ($(this).val()*1)
													});
												}
											});
											selection_modal.hide();

											beyond.request({
												url: '/members/p2p/assign_products',
												data: {
													card_id: card_id,
													products: products
												},
												on_success: function(data){
													if(data.status)
													{ 
														var success_modal = beyond.modal.create({
															title: 'Product Selection for '+card_id,
															html: 'Product assignment successful!',
															disableClose: true,
															buttons: {
																'Ok': function(){
																	window.location = "<?= site_url('members/p2p') ?>";
																}
															}
														});
													}
													success_modal.show();
												}
											});
										},
										'No': function(){
											confirm_modal.hide();
										}
									}
								});
								confirm_modal.show();								
							}
						}
					});
					selection_modal.show();

					$('.selected-product-qty').change(function(){
						$('.selection-error').hide();
						var product_id = $(this).data("pid");
						var max_qty = $(this).data("maxqty");
						$(this).val($(this).val()*1);

						if($(this).val() > max_qty) {
							$(this).val(0);
							$('.selection-error').show().children('.msg').text("Selected quantity may not be greater than the available quantity");
						}

						var total_qty = 0;
						$('.selected-product-qty').each(function(){
							total_qty += ($(this).val()*1);
						});
						if(total_qty > 2)
						{
							//$(this).val(0);
							$('.selection-error').show().children('.msg').text("Total selected quantity may not exceed 2");
						}
					});
				}
			});
		});

	});
</script>