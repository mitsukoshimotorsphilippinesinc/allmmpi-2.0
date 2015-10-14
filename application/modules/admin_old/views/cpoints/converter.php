<div class="row-fluid" style="border-bottom: solid 1px #bcbcbc;">
	<div><b>Convertion Rates</b></div>
	<?php foreach($ratios as $ratio) : ?>
	<div>1 <?= ucwords(str_replace("_", " ", $ratio->from_type)) ?> = <?= $ratio->cpoint_ratio ?> C Point</div>
	<?php endforeach; ?>
</div>
<div class="row-fluid" style="border-bottom: solid 1px #bcbcbc; margin-top: 10px;">
	<div class="span2"><b>Current</b></div>
	<div class="span3">GC: <?= $member_details->gift_cheques ?></div>
	<div class="span3">GCEP: <?= $member_details->gcep ?></div>
	<div class="span4">C Points: <?= $member_details->cpoints ?></div>
</div>
<div class="row-fluid form-horizontal" style="margin-top: 10px;">
	<div class="control-group">
		<label class="control-label">Type</label>
		<div class="controls">
			<select class="converter-convert-type input input-small">
				<option value="GIFT_CHEQUES">GC</option>
				<option value="GCEP">GCEP</option>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Amount</label>
		<div class="controls">
			<input type="text" class="input input-small converter-amount" value="0" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Converted</label>
		<div class="controls">
			<input type="text" class="input input-small converted-amount" value="0" disabled="disabled" />
		</div>
	</div>
</div>

<div class="alert alert-error alert-converter-error hide"></div>