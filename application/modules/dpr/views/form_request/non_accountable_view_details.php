<html>

<head>
<h3><?= $request_code ?> </h3>
</head>

<body>
<br>
<table id = "request_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>Type Of Form</th>
			<th>Last Series No.</th>
			<th>Pcs. Per Booklet</th>
			<th>QTY</th>		
			<th>Printing Press</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($record_detail as $rd){
       				$form_type_id = $rd->form_type_id;
       				$printing_press_id = $rd->printing_press_id;
       				$request_detail_id = $rd->request_detail_id;

       				$last_series = $rd->last_serial_number;
       				$quantity = $rd->quantity;
       				$status = $rd->status;

					$form_info = $this->dpr_model->get_form_by_id($form_type_id);
					$printing_press_info = $this->dpr_model->get_printing_press_by_id($printing_press_id);
				echo "
				<tr>
					<td>{$form_info->name}</td>
					<td>{$last_series}</td>
					<td>{$form_info->pieces_per_booklet}</td>
					<td>{$quantity}</td>
					<td>{$printing_press_info->complete_name}</td>
					<td>{$status}</td>
				</tr>";
		}
		?>
	</tbody>
</table>
</body>

