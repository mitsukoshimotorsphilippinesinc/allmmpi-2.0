<?php
foreach ($details as $d) {
?>
<tr>
	<?php
		$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($d->repair_hardware_id);
	?>
	<td><?= $repair_hardware_details->repair_hardware_name ?></td>
	<td class='qty'><?= $d->quantity ?></td>
	<td class='qty'><?= $d->amount ?></td>
	<td><?= $d->description ?></td>	
	<td data="<?= $d->expense_detail_id ?>"><a class="btn btn-danger rmv_wr_item"><i class="icon-white icon-minus"></i></a></td>
</tr>	
<?php
}
?>