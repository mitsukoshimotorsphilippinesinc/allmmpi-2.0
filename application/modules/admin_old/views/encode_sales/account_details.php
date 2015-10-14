<label><strong>Name:</strong> <?= $proper_name ?></label>
<label><strong>Email:</strong> <?= $proper_email ?></label>
<hr/>
<label><strong>Points:</strong></label>
<table class='table table-striped table-bordered'>
	<thead>	
		<tr>
			<td><strong>Details</strong></td>
			<td style='text-align:right;'><strong>Points</strong></td>			
		</td>		
	</thead>
	<tbody>
		<?php
		if ($reset == 0) {
		?>
			<tr>		
						<td>Left RS</td>
						<td style='text-align:right;'><?=number_format($account_details->left_rs)?></td>
					</tr>
				
					<tr>		
						<td>Right RS</td>
						<td style='text-align:right;'><?=number_format($account_details->right_rs)?></td>			
					</tr>
				
					<tr>		
						<td>Pairs RS</td>
						<td style='text-align:right;'><?=number_format($account_details->pairs_rs)?></td>			
					</tr>
		<?php
		} else {
		?>
					<tr>		
						<td>Left RS</td>
						<td style='text-align:right;'>0.00</td>
					</tr>
				
					<tr>		
						<td>Right RS</td>
						<td style='text-align:right;'>0.00</td>			
					</tr>
				
					<tr>		
						<td>Pairs RS</td>
						<td style='text-align:right;'>0</td>			
					</tr>
		<?php
		}
		?>
	</tbody>
</table>