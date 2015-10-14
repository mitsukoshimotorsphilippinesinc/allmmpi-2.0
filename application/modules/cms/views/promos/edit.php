<?php
	$upload_url = $this->config->item("media_url") . "/featured";
	$_upload_url = urlencode($upload_url);
?>

<h2>Edit Featured Promo  <a href='/cms/promos' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($promo)): ?>
<h3>Featured Promo not found.</h3>
<?php else: ?>
<?php
	
	$start_date = "0000-00-00";
	$end_date = "0000-00-00";

	$active_start = set_value('active_start',$promo->active_start);
	$active_end = set_value('active_end',$promo->active_end);
	if($active_start != "0000-00-00 00:00:00")
	{
		$start_date = date("Y-n-j",strtotime(set_value('active_start',$active_start)));
	}
	
	if($active_end != "0000-00-00 00:00:00")
	{
		$end_date = date("Y-n-j",strtotime(set_value('active_end',$active_end)));
	}
	
?>
<style>
.editor {
	width: 700px;
	height: 600px;
}
.editor_container {
	border-width: 1px;
	border-style: solid;
	-webkit-border-top-right-radius: 3px;
	-webkit-border-top-left-radius: 3px;
	border-top-right-radius: 3px;
	border-top-left-radius: 3px;
	border-color: #CCC #CCC #DFDFDF;
}
</style>
<form action='/cms/promos/edit/<?= $promo->promo_id; ?>' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('promo_title') ?>">
			<label class="control-label" for="promo_title">Promo Title <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="promo_title" id="promo_title" value="<?= set_value('promo_title',$promo->promo_title) ?>">
				<p class="help-block"><?= $this->form_validation->error('promo_title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('url') ?>">
			<label class="control-label" for="url">Slug </label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="url" id="url" value="<?= set_value('url',$promo->url) ?>">
				<p class="help-block"><?= $this->form_validation->error('url'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('promo_description') ?>">
			<label class="control-label" for="promo_description">Promo Description</label>
			<div class="controls">
				<div class=''>
					<textarea name="promo_description" id="promo_description" class="input-xlarge span10" rows="20"><?= $this->form_validation->set_value('promo_description',$promo->promo_description) ?></textarea>
				</div>
				<a id='btn_description_view' class='btn switch-tmce'><span>View</span></a>
				<a id='btn_description_html' class='btn switch-html'><span>HTML</span></a>
				<p class="help-block"><?= $this->form_validation->error('promo_description'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('promo_text') ?>">
			<label class="control-label" for="promo_text">Promo Text</label>
			<div class="controls">
				<div class=''>
					<textarea name="promo_text" id="promo_text" class="input-xlarge span10" rows="8"><?= $this->form_validation->set_value('promo_text',$promo->promo_text) ?></textarea>
				</div>
				<p class="help-block"><?= $this->form_validation->error('promo_text'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<div id="image_filename">
					<?php if(!empty($promo->image_filename)):?>
						<img id="promo_image" style="width:100px; height:100px;" alt="" src="/assets/media/featured/<?= $promo->image_filename ?>">
					<?php endif; ?>
				</div>
				<div id="image_upload"></div>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$promo->is_active),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('with_active_period') ?>">
			<label class="control-label" for="with_active_period">With Active Period? </label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("with_active_period", $options, set_value('with_active_period',$promo->with_active_period),"id='with_active_period' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('with_active_period'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('active_start') ?>">
			<label class="control-label" for="active_start">Start Date</label>
			<div id="active_start_container" class="controls form-inline wc-date">
				<?= form_dropdown('active_start_month', $months, null, 'id="active_start_month" class="wc-date-month"') ?>
				<?= form_dropdown('active_start_day', $days, null, 'id="active_start_day" class="wc-date-day"') ?>
				<?= form_dropdown('active_start_year', $years, null, 'id="active_start_year" class="wc-date-year"') ?>
				<input type="hidden" id="active_start" name="active_start" value="<?= set_value('active_start'); ?>" />
				<p class="help-block"><?= $this->form_validation->error('active_start'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('active_end') ?>">
			<label class="control-label" for="active_end">End Date</label>
			<div id="active_end_container" class="controls form-inline wc-date">
				<?= form_dropdown('active_end_month', $months, null, 'id="active_end_month" class="wc-date-month"') ?>
				<?= form_dropdown('active_end_day', $days, null, 'id="active_end_day" class="wc-date-day"') ?>
				<?= form_dropdown('active_end_year', $years, null, 'id="active_end_year" class="wc-date-year"') ?>
				<input type="hidden" id="active_end" name="active_end" value="<?= set_value('active_end'); ?>" />
				<p class="help-block"><?= $this->form_validation->error('active_end'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button id="submit_promo" type="submit" class="btn btn-primary">Update Promo</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	var start_date = '<?= $start_date; ?>';
	var end_date = '<?= $end_date; ?>';
	

	$(window).ready(function(){
		$('textarea#promo_description').tinymce({
			// Location of TinyMCE script
			script_url : '<?= js_path('libs/tinymce/tiny_mce.js')?>',
			mode: "exact",
			elements : 'promo_description',
			// General options
			theme : "advanced",
			plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : false,

			// Example content CSS (should be your site CSS)
			content_css : "<?= js_path('libs/tinymce/themes/advanced/skins/default/content.css'); ?>",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",
		});
		
		
		/*Error: tinyMCE is not defined*/
		//beyond.renderTinyMCE('editor2',null);
	})
	
	
	
	$('#btn_description_html').click(function() {
		var id = 'promo_description';
		if (tinyMCE.get(id))
			tinyMCE.execCommand('mceRemoveControl', false, id);
	});
	
	$('#btn_description_view').click(function() {
		var id = 'promo_description';
		if (!tinyMCE.get(id))
			tinyMCE.execCommand('mceAddControl', false, id);
	});

	$(document).ready(function(){

		$('#active_start_month').change(function() {
			beyond.webcontrol.updateDateControl('active_start');
		});
		$('#active_start_day').change(function() {
			beyond.webcontrol.updateDateControl('active_start');
		});
		$('#active_start_year').change(function() {
			beyond.webcontrol.updateDateControl('active_start');
		});
		
		
		
		if(!_.isEmpty(start_date))
		{
			start_date = start_date.split("-");

			$('#active_start_year').val(start_date[0]);
			$('#active_start_month').val(start_date[1]);
			$('#active_start_day').val(start_date[2]);
		}

		$('#active_start_month').trigger('change');
		$('#active_start_day').trigger('change');
		$('#active_start_year').trigger('change');
		
		$('#active_end_month').change(function() {
			beyond.webcontrol.updateDateControl('active_end');
		});
		$('#active_end_day').change(function() {
			beyond.webcontrol.updateDateControl('active_end');
		});
		$('#active_end_year').change(function() {
			beyond.webcontrol.updateDateControl('active_end');
		});

		if(!_.isEmpty(end_date))
		{
			end_date = end_date.split("-");

			$('#active_end_year').val(end_date[0]);
			$('#active_end_month').val(end_date[1]);
			$('#active_end_day').val(end_date[2]);
		}

		$('#active_end_month').trigger('change');
		$('#active_end_day').trigger('change');
		$('#active_end_year').trigger('change');
	});
	
	$("#submit_promo").click(function(){
		var active_period_modal = b.modal.new({});
		var error_msg = "";
		
		if($("#with_active_period").val() * 1)
		{
			if(_.isEmpty($("#active_start").val()))
			{
				error_msg = error_msg.concat("<p>The Start Date is not set.</p>");
			}else if(_.isEmpty($("#active_end").val()))
			{
				error_msg = error_msg.concat("<p>The End Date is not set.</p>");
			}else if($("#active_start").val() > $("#active_end").val())
			{
				error_msg = error_msg.concat("<p>Start Date must not exceed End Date.</p>");
			}
		}
		
		if(!_.isEmpty(error_msg))
		{
			active_period_modal.init({
			
				title: "Error Notification: Active Period",
				html: error_msg,
				width: 300
			
			});
			active_period_modal.show();
			return false;
		}
		
	});
	// uploader
	$('#image_upload').Uploadrr({
		singleUpload : true,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.gif','.jpg', '.png'],
		target : base_url + '/admin/upload/process?filename=promo_<?= $promo->promo_id ?>&location=<?=$_upload_url?>&width=891&height=335&ts=<?=time()?>',
		onComplete: function() {
			$("#promo_image").attr('src', '<?=$upload_url?>/promo_'+<?= $promo->promo_id ?>+'.jpg?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/cms/promos/update_image',
		        data: {
					"filename": '<?= "promo_" . $promo->promo_id .".jpg"?>',
					"promo_id":<?= $promo->promo_id?>
				},
		        on_success: function(data) {
		        }
		    });
		}
	});
</script>
<?php endif; ?>