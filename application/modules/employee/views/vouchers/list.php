<div class="page-header clearfix">
	<h2 >Vouchers <small></small></h2>
</div>
<div class='ui-element'>
	<table class='table table-bordered table-striped'>
		<thead>
			<tr>
				<th>Code</th>
				<th>Description</th> 
				<th>Status</th>
				<th>Valid Until</th>
				<th>Insert Date</th>
				<th style='width:90px;'>&nbsp;</th>
			</tr>
		</thead>
		<tbody style="font-size:12px;">
		<?php if(empty($member_vouchers)): ?>
			<tr>
				<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
			</tr>
		<?php else: ?>
		<?php foreach ($member_vouchers as $voucher): ?>
			<tr data='<?= $voucher->voucher_code ?>'>						
				<td><?= $voucher->voucher_code; ?></td>
				
				<?php
					$product_list = "";
					if (!(($voucher->voucher_type_id == 0) || ($voucher->voucher_type_id == ""))) {
						$voucher_items = json_decode($voucher->product_text);
						
						$item_list = get_object_vars($voucher_items);

						foreach ($item_list as $key=>$value) {
		                    $product_list .= "	                        
									{$key} - {$value}<br/>
		                    ";                
		                }
					} else {
						
						$voucher_products = $this->members_model->get_member_voucher_products(array(
							'voucher_id'=>$voucher->voucher_id,
							'parent_product_id' => 0
						));
						foreach($voucher_products as $vp){
							$_product = $this->items_model->get_product_by_id($vp->product_id);
							$sub_products = $this->members_model->get_member_voucher_products(array(
								'voucher_id'=>$voucher->voucher_id,
								'parent_product_id' => $vp->product_id
							));
							$sub_products_html = "";
							if(count($sub_products) > 0){
								$sub_products_html .= "<dl>";
								foreach($sub_products as $sp){
									$_sub_product = $this->items_model->get_product_by_id($sp->product_id);
									$sub_products_html .= "<dd>{$sp->qty} x {$_sub_product->product_name}</dd>";
								}
								$sub_products_html .= "</dl>";

							}
							if(!empty($product_list)) $product_list .= "<br /><br />";
							$product_list .= "{$vp->qty} - {$_product->product_name}{$sub_products_html}";
						}		
								
					}
					$raffle_entries = "";
					if(isset($voucher_raffle_entries[$voucher->voucher_id])){
						foreach($voucher_raffle_entries[$voucher->voucher_id] as $rf){
							if(!empty($raffle_entries)) $raffle_entries .= ", ";
							$raffle_entries .= $rf->raffle_number;
						}
					}
					if(!empty($raffle_entries)) $raffle_entries = "<br /><br /><label>Raffle Entries</label>".$raffle_entries;
				?>							
				
				<td>
					<?= $product_list; ?>
					<?= $raffle_entries; ?>
				</td>					
				<td><?= $voucher->status; ?></td>
				<td><?= $voucher->end_timestamp; ?></td>
				<td><?= $voucher->insert_timestamp; ?></td>					
				<td>
					<a href="/members/vouchers/display_pdf/<?= $voucher->voucher_code?>" class='btn btn-small btn-primary btn_view_voucher' data='<?=$voucher->voucher_id?>'><i class="icon-gift icon-white"></i> View</a>
				</td>
			
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div>

<div>
<?= $this->pager->create_links();  ?>
</div>

<script type="text/javascript">

   
</script>

