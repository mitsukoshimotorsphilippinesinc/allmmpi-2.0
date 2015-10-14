<?php echo css('base.css');?>
<?php echo css('main.css');?>
<?php echo css('google-code-prettify/prettify.css');?>
<?php echo css('switcherMenu.css');?>
<style type="text/css">

</style>
<div class="">
	<table class='table table-condensed'>
		<tr>
			<td align='left'><h3>List of Product Lines</h3></td>
			<!--td align='right'><h3>Date: <!--?= $date ?></h3></td-->
			<td align='right'><h3>As Of: <?= $timestamp ?></h3></td>
		</tr>
	</table>
	<br />
	<table class='table table-bordered table-condensed table-striped'>
		<thead>
			<tr>
				<th style='width:70px'>&nbsp;</th>
				<th style='width:100px;'>Product Line</th>
				<th style='width:190px;'>Visible</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($product_lines as $k => $i): ?>
			<tr>
				<td><?= $k+1 ?></td>
				<td><?= $i->product_line; ?></td>
				<td><?= ($i->is_visible)? 'Yes' : "No"; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>


