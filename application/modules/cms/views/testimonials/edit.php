<?php
	$upload_url = $this->config->item("media_url") . "/testimonials";
	$_upload_url = urlencode($upload_url);
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
<h2>Edit Testimonial  <a href='/cms/testimonials' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($testimonial)): ?>
<h3>Testimonials not found.</h3>
<?php else: ?>
<form action='/cms/testimonials/edit/<?= $testimonial->testimonial_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('member_name') ?>">
			<label class="control-label" for="member_name">Member Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="member_name" id="member_name" value="<?= set_value('member_name',$testimonial->member_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('member_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('member_details') ?>">
			<label class="control-label" for="member_details">Member Details <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="member_details" id="member_details" value="<?= set_value('member_details',$testimonial->member_details) ?>">
				<p class="help-block"><?= $this->form_validation->error('member_details'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('body') ?>">
			<label class="control-label" for="body">Body <em>*</em></label>
			<div class="controls">
				<div class='editor_container'>
					<textarea id='body' class='editor' name="body"><?= $this->form_validation->set_value('body',$testimonial->body) ?></textarea>
				</div>
				<a id='btn_view' class='btn switch-tmce'><span>View</span></a>
				<a id='btn_html' class='btn switch-html'><span>HTML</span></a>
				<p class="help-block"><?= $this->form_validation->error('body'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<div id="image_filename">
					<?php if(!empty($testimonial->image_filename)):?>
						<img id="member_image" style="max-width:100px; max-height:100px;" alt="" src="/assets/media/testimonials/<?= $testimonial->image_filename ?>">
					<?php endif; ?>
					<input type='hidden' value='<?= $testimonial->image_filename ?>'>
				</div>
				<div id="image_upload"></div>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="tags">Approved? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_approved", $options, set_value('is_approved',$testimonial->is_approved),"id='is_approved' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_approved'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update Testimonial</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	var base_url = "<?=$this->config->item('base_url');?>";

	$(document).ready(function(){
		$('textarea#body').tinymce({
			// Location of TinyMCE script
			script_url : '<?= js_path('libs/tinymce/tiny_mce.js')?>',
			mode: "exact",
			elements : 'body',
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

	// uploader
	$('#image_upload').Uploadrr({
		singleUpload : true,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.gif','.jpg', '.png'],
		target : base_url + '/admin/upload/process?filename=testimonial_<?= $testimonial->testimonial_id?>&location=<?=$_upload_url?>&width=200&height=200&ts=<?=time()?>',
		onComplete: function() {
			$("#member_image").attr('src', '<?=$upload_url?>/testimonial_'+<?= $testimonial->testimonial_id?>+'.jpg?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/cms/testimonials/update_image',
		        data: {
					"filename": '<?= "testimonial_" . $testimonial->testimonial_id .".jpg"?>',
					"testimonial_id":<?= $testimonial->testimonial_id?>
				},
		        on_success: function(data) {		
		        }
		    });		
		}
	});
	
</script>
<?php endif; ?>