<?php
foreach ($details as $d) {
?>
<tr>
	<?php
		$repair_hardware_details = $this->information_technology_model->get_repair_hardware_by_id($d->repair_hardware_id);
	?>
	<td><?= $repair_hardware_details->repair_hardware_name ?></td>
	<td><?= $d->quantity ?></td>
	<td><?= $d->description ?></td>
	<td><?= $d->peripherals ?></td>
	<td data="<?= $d->repair_detail_id ?>"><a class="btn btn-danger rmv_wr_item"><i class="icon-white icon-minus"></i></a></td>
</tr>	
<?php
}
?>