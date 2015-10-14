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
<!--h2>Edit Content  <a href='/admin/contents' class='btn btn-large'>Back</a></h2>
<hr/-->

<div class='alert alert-info'><h3>Edit Content <a class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Settings Dashboard</a></h3></div>

<?php if (empty($contents)): ?>
<h3>Content not found.</h3>
<?php else: ?>
<form action='/admin/contents/edit/<?= $contents->content_id ?>' method='post' class='form-inline'>
	<fieldset >
		<input type='hidden' id='orig_slug' name='orig_slug' value='<?= $contents->slug ?>' />
		<div class="control-group <?= $this->form_validation->error_class('title') ?>">
			<label class="control-label" for="title"><strong>Title <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="title" id="title" value="<?= set_value('title',$contents->title) ?>">
				<p class="help-block"><?= $this->form_validation->error('title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('content_type') ?>">
			<label class="control-label" for="content_type"><strong>Content Type <em>*</em></strong></label>
			<div class="controls">
					<?php
					$options = array();
					$options[''] = '';
					$options['email_template'] = 'Email Template';
					$options['sms_template'] = 'SMS Template';
					echo form_dropdown('content_type', $options, set_value('content_type',$contents->content_type),'class="span2"');
					?>
				<p class="help-block"><?= $this->form_validation->error('content_type'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('slug') ?>">
			<label class="control-label" for="slug"><strong>Slug <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="slug" id="slug" value="<?= set_value('slug',$contents->slug) ?>">
				<p class="help-block"><?= $this->form_validation->error('slug'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('thumb') ?>">
			<label class="control-label" for="thumb"><strong>Thumbnail URL</strong></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="thumb" id="thumb" value="<?= set_value('thumb',$contents->thumb) ?>">
				<p class="help-block"><?= $this->form_validation->error('thumb'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('body') ?>">
			<label class="control-label" for="body"><strong>Body <em>*</em></strong></label>
			<div class="controls">
				<div class=''>
					<textarea name="body" id="body" class="input-xlarge span10" rows="20"><?= $this->form_validation->set_value('body',$contents->body) ?></textarea>
				</div>
				<br/>
				<a id='btn_view' class='btn switch-tmce'><span>View</span></a>
				<a id='btn_html' class='btn switch-html'><span>HTML</span></a>
				<p class="help-block"><?= $this->form_validation->error('body'); ?></p>
				<p class="help-block"><?= $this->form_validation->error('body'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('excerpt') ?>">
			<label class="control-label" for="excerpt"><strong>Excerpt</strong></label>
			<div class="controls">
				<textarea name="excerpt" id="excerpt" class="input-xlarge span10" rows="5"><?= set_value('excerpt',$contents->excerpt) ?></textarea>
				<p class="help-block"><?= $this->form_validation->error('excerpt'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_active'); ?>">
			<label class="control-label" for="is_current"><strong>Status <em>*</em> </strong></label>
			<div class="controls">
					<?php

					$options = array('' => '','0' => 'Inactive', '1' => 'Active');
					$extra = "id='is_active' class='span2'";
					echo form_dropdown('is_active', $options, set_value('is_active',$contents->is_active), $extra);
					?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		</div>
	</fieldset>
	<hr/>
	<div class="controls" align="right">			
		<button type="submit" class="btn btn-primary">Update Setting</button>
		<a id='' class="btn return-btn">Cancel</a>
	</div>
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
	
	$(".return-btn").click(function(){		
		redirect('/admin/contents');	
		return false;
	});
	
</script>
<?php endif; ?>
