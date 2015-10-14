<?php

//echo css('vitalc.css');
echo css('bootstrap.css');
//echo css('/assets/css/voucher.css');

// check validity
$voucher_validity = "";
if (($voucher_details->end_timestamp == NULL) || ($voucher_details->end_timestamp == "0000-00-00 00:00:00") || (trim($voucher_details->end_timestamp) == "")) {
    $voucher_validity = "N/A";
} else {
    $voucher_validity = date("F j, Y", strtotime($voucher_details->end_timestamp));
}

// purchase date
$voucher_purchased = date("F j, Y", strtotime($voucher_details->insert_timestamp));

?>

<style>
    body{
        background: url(/assets/img/voucher_bg.png) repeat-y;
    }
</style>

<div class="content-area">
    <div class="voucher-area">

        <div id="voucher-header-info row" style="margin-bottom: 16px;">
            <table>
                <tr>
                    <td>
                        <img class="span2" src="/assets/img/lemon_bg.png" alt="lemon">
                    </td>
                    <td><img style="margin-right: 31%;" src="/assets/img/logo_bg.png" class="span2" alt="vital-c"></td>
                    <td><b style="font-size:35px;color:#008000;"><?=$voucher_code?></b></td>
                </tr>



            </table>
        </div>

        <table class="table">
            <tr style="border: none;">
                <td>
                    <strong>Owner Name:</strong>&nbsp;<?= $proper_owner_name ?>
                </td>
                <td>
                    <strong>Date Purchased:</strong>&nbsp;<?= $voucher_purchased ?>
                </td>
            </tr>
            <tr style="border: none;">
                <td>
                    <strong>From Member Name:</strong>&nbsp;<?= $proper_member_name ?>
                </td>
                <td>
                    <strong>Valid Until:</strong>&nbsp;<?= $voucher_validity ?>
                </td>
            </tr>
            <tr style="border: none;">
                <td>
                    <strong>Member Since:</strong>&nbsp;<?= $proper_member_since ?>
                </td>
            </tr>
        </table>


        <div class="">

            <table class='table table-bordered table-striped'>
                <thead>
                <tr>
                    <th align="center" style="width: 30px; border-right: 1px solid #b0c4de;"><strong>QTY</strong></th>
                    <th align="center"><strong>ITEM</strong></th>
                </tr>
                </thead>
                <tbody>
                <?php
				if($voucher_details->voucher_type_id > 0){
					$voucher_items = json_decode($voucher_details->product_text);

	                $item_list = get_object_vars($voucher_items);
	                foreach ($item_list as $key=>$value) {
	                    $remarks_list .= "
		                        <tr>
									<td style='text-align: center; width: 80px; border-right: 1px solid #b0c4de;'>{$key}</td>
									<td>{$value}</td>
								</tr>
		                    ";
	                }
				}else{
					$voucher_products = $this->members_model->get_member_voucher_products(array(
						'voucher_id'=>$voucher_details->voucher_id,
						'parent_product_id' => 0
					));
					foreach($voucher_products as $vp){
						$_product = $this->items_model->get_product_by_id($vp->product_id);
						$sub_products = $this->members_model->get_member_voucher_products(array(
							'voucher_id'=>$voucher_details->voucher_id,
							'parent_product_id' => $vp->product_id
						));
						$sub_products_html = "";
						if(count($sub_products) > 0){
							$sub_products_html .= "<dl>";
							foreach($sub_products as $sp){
								$_sub_product = $this->items_model->get_product_by_id($sp->product_id);
								$_sp_qty = $sp->qty/$vp->qty;
								$sub_products_html .= "<dd>{$_sp_qty} x {$_sub_product->product_name}</dd>";
							}
							$sub_products_html .= "</dl>";
							
						}
						$remarks_list .= "
		                        <tr>
									<td style='text-align: center; width: 80px; border-right: 1px solid #b0c4de;'>{$vp->qty}</td>
									<td>{$_product->product_name}{$sub_products_html}</td>
								</tr>
		                    ";
					}
				}
                
                ?>

                <?=$remarks_list?>

				</tbody>
            </table>
			<?php
			$_html = "";
			if(count($raffle_entries) > 0):
				foreach($raffle_entries as $rf){
					if(!empty($_html)) $_html .= ", ";
					$_html .= $rf->raffle_number;
				}
			?>
			<table class='table table-bordered table-striped'>
                <tbody>
			<tr>
				<td>
					<label>Raffle Entries:</label>
					<?=$_html;?>
					
				</td>
			</tr>
			</tbody>
        </table>
			
			<?php
			endif;
			?>
        </div>

        <h3 style="text-align: center;">TERMS AND CONDITIONS</h3>
        <div>
            <?=$voucher_terms_and_conditions?>
        </div>

    </div>
</div>