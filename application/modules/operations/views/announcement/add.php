<style>
.editor {
	width: 797px;
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
<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Add New Announcement  <a href='/operations/announcement' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -10px;">Back</a></h2>
<hr/>
<form action='/operations/announcement/add' method='post' class='form-horizontal'>
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
				<a id='btn_view' class='btn switch-tmce'><span>View</span></a>
				<a id='btn_html' class='btn switch-html'><span>HTML</span></a>
				<p class="help-block"><?= $this->form_validation->error('body'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_published') ?>">
			<label class="control-label" for="is_published">Published? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_published", $options, set_value('is_published'),"id='is_published' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_published'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button id="submit_announcement" type="submit" class="btn btn-primary">Add New Announcement</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	$(window).ready(function(){
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
</script>