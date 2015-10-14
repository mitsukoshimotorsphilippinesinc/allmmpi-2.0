<?php

$rank_30 = array();
$rank_70 = array();

for($i = 1;$i <= 70;$i++) $rank_70[$i] = $i;
for($i = 1;$i <= 30;$i++) $rank_30[$i] = $i;

$rank_30 = json_encode($rank_30);
$rank_70 = json_encode($rank_70);

?>

<div class='alert alert-info'><h3>Add New Earner <a href="/cms/earners" class='btn' style='float:right;margin-right:-30px;'>Back</a></h3></div>

<form action='/cms/earners/add' method='post' class='form-inline'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('member_name') ?>">
			<label class="control-label" for="member_name"><strong>Member Name <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="" name="member_name" id="member_name" value="<?= set_value('member_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('member_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('group_name') ?>">
			<label class="control-label" for="group_name"><strong>Group Name <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span2' placeholder="" name="group_name" id="group_name" value="<?= set_value('group_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('group_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('earner_type_id') ?>">
			<label class="control-label" for="earner_type_id"><strong>Earner Type <em>*</em></strong></label>
			<div class="controls">
				<?php
					$options = array();
					$options[''] = 'Select Earner Type';
					$options['1'] = 'Monthly Top 30';
					$options['2'] = 'Overall Top 70';
					echo form_dropdown('earner_type_id', $options, set_value('earner_type_id'),'id="earner_type_id" class="span2"');
				?>
				<p class="help-block"><?= $this->form_validation->error('earner_type_id'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('position') ?>">
			<label class="control-label" for="position"><strong>Rank <em>*</em></strong></label>
			<div class="controls">
				<?php
					$options = array();
					$options[''] = '';
					echo form_dropdown('position', $options, set_value('position'),'id="position" style="width:auto;"');
				?>
				<p class="help-block"><?= $this->form_validation->error('position'); ?></p>
			</div>
		</div>
	</fieldset>
	<hr/>
	<div class="control-group">				
		<button type="submit" class="btn btn-primary">Add New Content</button>
	</div>
</form>
<script type="text/javascript">
	
	var rank_thirty = <?= $rank_30; ?>;
	var rank_seventy = <?= $rank_70; ?>;
	
	$(document).ready(function(){
		$("#earner_type_id").trigger("change");
	});
	
	$("#earner_type_id").change(function(){
		var options_html = "";
		
		if($("#earner_type_id").val() == 1)
		{
			$.each(rank_thirty,function(k,v){
				options_html = options_html + '<option value="'+v+'">'+v+'</option>';
			});
		}
		else if($("#earner_type_id").val() == 2)
		{
			$.each(rank_seventy,function(k,v){
				options_html = options_html + '<option value="'+v+'">'+v+'</option>';
			});
		}
		else
		{
			options_html = '<option value=""></option>';
		}
		
		$("#position").html(options_html);
	});
	
</script>