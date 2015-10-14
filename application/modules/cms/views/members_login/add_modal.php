<?php
	$this->load->model('contents_model');
	$achievement_types = $this->contents_model->get_member_achievements();
	$options = array();
	
	$counter_100 = 1;
	
	while($counter_100 <= 3)
	{
		$options[$counter_100] = $counter_100;	
		$counter_100++;
	}
?>

<div class='control-group'>
	<label class='control-label' for='member_name'><strong>Slide Name <em>*</em></strong></label>
	<div class='controls'><input type='text' placeholder='Slide Name Here' class='span6' value='' id='slide_name' name='slide_name' /></div>
	<span id="slide_name_error" class="label label-important" style="display:none">Slide Name is Required</span>
	
	<label class='control-label' for='description'><strong>Description <em>*</em></strong></label>
	<div class='controls'><input type='text' class='span6' placeholder='Short Description Here' name='description' id='description' value=''></div>
	<span id="description_error" class="label label-important" style="display:none">Description is Required</span>
			
	<label class='control-label' for='priority_id'><strong>Priority Number <em>*</em></strong></label>
	<div class='controls'>
		<?php echo form_dropdown('priority_id', $options, "", "name='priority_id' id='priority_id'")?>
	</div>
	<span id="priority_id_error" class="label label-important" style="display:none">Priority ID is Required</span>
	
	
	
</div>

<script type="text/javascript">

</script>