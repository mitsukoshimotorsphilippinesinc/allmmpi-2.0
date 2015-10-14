<style>
.editor {
	width: 797px;
	height: 150px;
}
</style>
<div class="alert alert-info">
	<h2>Add New Alert Message  <a href='/cms/alerts' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;">Back</a></h2>
</div>
<hr/>
<form action='/cms/alerts/add' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('title') ?>">
			<label class="control-label" for="title">Title <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="title" id="title" value="<?= $this->form_validation->set_value('title') ?>">
				<p class="help-block"><?= $this->form_validation->error('title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('body') ?>">
			<label class="control-label" for="body">Body <em>*</em></label>
			<div class="controls">
				<div>
					<textarea name="body" id="body"  class='editor editor_container' rows="20"><?= $this->form_validation->set_value('body') ?></textarea>
				</div>
				<p class="help-block"><?= $this->form_validation->error('body'); ?></p>
			</div>
		</div>
		<div id="event_start" class="control-group <?= $this->form_validation->error_class('start_date') ?>">
			<label class="control-label" for="start_date">Start Date </label>
			<div id="start_date_container" class="controls form-inline wc-date">
				<?= form_dropdown('start_date_month', $months, null, 'id="start_date_month" class="wc-date-month"') ?>
				<?= form_dropdown('start_date_day', $days, null, 'id="start_date_day" class="wc-date-day"') ?>
				<?= form_dropdown('start_date_year', $years, null, 'id="start_date_year" class="wc-date-year"') ?>
				<?= form_dropdown('start_time_hour', $hours, null, 'id="start_time_hour" class="start_time_hour" style="width:auto;"') ?>
				<?= form_dropdown('start_time_minute', $minutes, null, 'id="start_time_minute" class="start_time_minute" style="width:auto;"') ?>
				<input type="hidden" id="start_date" name="start_date" value="" />
				<p class="help-block"><?= $this->form_validation->error('start_date'); ?></p>
			</div>
		</div>
		<div id="event_end" class="control-group <?= $this->form_validation->error_class('end_date') ?>">
			<label class="control-label" for="end_date">End Date</label>
			<div id="end_date_container" class="controls form-inline wc-date">
				<?= form_dropdown('end_date_month', $months, null, 'id="end_date_month" class="wc-date-month"') ?>
				<?= form_dropdown('end_date_day', $days, null, 'id="end_date_day" class="wc-date-day"') ?>
				<?= form_dropdown('end_date_year', $years, null, 'id="end_date_year" class="wc-date-year"') ?>
				<?= form_dropdown('end_time_hour', $hours, null, 'id="end_time_hour" class="end_time_hour" style="width:auto;"') ?>
				<?= form_dropdown('end_time_minute', $minutes, null, 'id="end_time_minute" class="end_time_minute" style="width:auto;"') ?>
				<input type="hidden" id="end_date" name="end_date" value="" />
				<p class="help-block"><?= $this->form_validation->error('end_date'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_visible') ?>">
			<label class="control-label" for="is_visible">Visible? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_visible", $options, set_value('is_visible'),"id='is_visible' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_visible'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button id="submit_alerts" type="submit" class="btn btn-primary">Add New Alert Message</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	
	var start_date = '<?=set_value('start_date')?>';
	var end_date = '<?=set_value('end_date')?>';
	var start_time_hour = '<?= set_value('start_time_hour')?>';
	var start_time_minute = '<?= set_value('start_time_minute')?>';
	var end_time_hour = '<?= set_value('end_time_hour')?>';
	var end_time_minute = '<?= set_value('end_time_minute')?>';
	

	
	$('#btn_html').click(function() {
		var id = 'body';
		if (tinyMCE.get(id))
			tinyMCE.execCommand('mceRemoveControl', false, id);
	});
	
	$('#btn_view').click(function() {
		var id = 'body';
		if (!tinyMCE.get(id))
			tinyMCE.execCommand('mceAddControl', false, id);
	});
	
	$("#submit_alerts").click(function(){
		var active_period_modal = b.modal.create({});
		var error_msg = "";

		if(!(_.isEmpty($("#end_date").val()) || _.isEmpty($("#end_time_hour").val()) || _.isEmpty($("#end_time_minute").val())) &&
				(($("#start_date").val()+" "+$("#start_time_hour").val()+":"+$("#start_time_minute").val()) >= $("#end_date").val()+" "+$("#end_time_hour").val()+":"+$("#end_time_minute").val()))
		{
			error_msg = error_msg.concat("<p>Start Date must not exceed or be the same as End Date.</p>");
		}
		
		if(!_.isEmpty(error_msg))
		{
			active_period_modal.init({
			
				title: "Error Notification: Active Period",
				html: error_msg,
				width: 350
			
			});
			active_period_modal.show();
			return false;
		}
	});
	
	$(document).ready(function(){


		$('#start_date_month').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$('#start_date_day').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$('#start_date_year').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		
		
		
		if(!_.isEmpty(start_date))
		{
			start_date = start_date.split("-");

			$('#start_date_year').val(start_date[0]);
			$('#start_date_month').val(start_date[1]);
			$('#start_date_day').val(start_date[2]);
			
			$('#start_time_hour').val(start_time_hour);
			$('#start_time_minute').val(start_time_minute);
			
		}

		$('#start_date_month').trigger('change');
		$('#start_date_day').trigger('change');
		$('#start_date_year').trigger('change');
		
		$('#end_date_month').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$('#end_date_day').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$('#end_date_year').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		if(!_.isEmpty(end_date))
		{
			end_date = end_date.split("-");

			$('#end_date_year').val(end_date[0]);
			$('#end_date_month').val(end_date[1]);
			$('#end_date_day').val(end_date[2]);
			
			$('#end_time_hour').val(end_time_hour);
			$('#end_time_minute').val(end_time_minute);
		}

		$('#end_date_month').trigger('change');
		$('#end_date_day').trigger('change');
		$('#end_date_year').trigger('change');
	});
</script>