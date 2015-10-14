
<head>
<h3> <?= $branch_name ?> TR No:<?= $tr_number ?> </h3>
</head>
<br>
<table id = "form_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th style = "width:200px;">Form</th>
			<th style = "width:100px;">Booklet No.</th>
			<th style = "width:80px;">Series From</th>
			<th style = "width:80px;">Series To</th>
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($record_detail as $rd){

				$booklet_id = $rd->booklet_id;
       			$release_detail_id = $rd->release_detail_id;
       			$booklet_inventory = $this->dpr_model->get_booklet_by_id($booklet_id);
       			$form_type_id = substr($booklet_inventory->booklet_code, 0,2);
       			$form_info = $this->dpr_model->get_form_type_by_id($form_type_id);

				echo "<tr>
					<td>{$form_info->name}</td>
					<td style = 'text-align:center;'>{$booklet_inventory->booklet_number}</td>
					<td style = 'text-align:center;'>{$booklet_inventory->series_from}</td>
					<td style = 'text-align:center;'>{$booklet_inventory->series_to}</td>		
					<td>{$rd->remarks}</td>
				</tr>";
			}
		?>
	</tbody>
</table>
