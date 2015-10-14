<?php if (empty($card_type)): ?>
	<h3>Voucher Type not found.</h3>
<?php else: ?>
	<label>You are about to delete a Card Type having the following details:</label>
	<br/>

	<table class='table table-striped table-bordered'>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td style='width:120px;'><label><strong>Code</strong></label></td>
				<td><label class=''><?= $card_type->code ?></label></td>		
			</tr>		
			<tr>
				<td style='width:120px;'><label><strong>Name</strong></label></td>
				<td><label class=''><?= $card_type->name ?></label></td>		
			</tr>
			
			<?php
			$card_series = "";

			// get current series of card type
			$where = "card_type_id = '{$card_type->card_type_id}'";
			$card_series_details = $this->cards_model->get_card_series($where,null,"series_number");

			if (count($card_series_details) == 0) {
				$card_series = "NONE";
			} else {	
				$cnt = 1;									
				foreach ($card_series_details as $csd) {
					if ($cnt == 1) {
						$card_series = $csd->series_number; 
					} else {
						$card_series = $card_series . ", " . $csd->series_number;
					}
					$cnt++;
				}
			}
			?>
			
			<tr>
				<td style='width:120px;'><label><strong>Card Series</strong></label></td>
				<td><label class=''><?= $card_series ?></label></td>
			</tr>	
			
			
			<?php
				if ($card_type->is_package == 0) {
					$is_package = "Sales Card";
				} else {
					$is_package = "Package Card";
				}
			?>
			<tr>
				<td style='width:120px;'><label><strong>Type</strong></label></td>
				<td><label class=''><?= $is_package ?></label></td>		
			</tr>
			<tr>
				<td style='width:120px;'><label><strong>Description</strong></label></td>
				<td><label class=''><?= $card_type->description ?></label></td>		
			</tr>			
		</tbody>		
	</table>	
<?php endif; ?>
