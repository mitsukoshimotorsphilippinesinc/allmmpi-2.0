<?php

	echo css('miniColors/jquery.miniColors.css'); 
	echo js('libs/jquery.miniColors.min.js');
	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/package_types";
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
<h2>Edit Package  <a href='/cms/packages' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($package)): ?>
<h3>Package not found.</h3>
<?php else: ?>
<form action='/cms/packages/edit/<?= $package->featured_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('title') ?>">
			<label class="control-label" for="title">Title <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="title" id="title" value="<?= set_value('title',$package->title) ?>">
				<p class="help-block"><?= $this->form_validation->error('title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('body') ?>">
			<label class="control-label" for="body">Body <em>*</em></label>
			<div class="controls">
				<div class=''>
					<textarea id='body' class='span10' name="body" rows="20"><?= $this->form_validation->set_value('body',$package->body) ?></textarea>
				</div>
				<a id='btn_view' class='btn switch-tmce'><span>View</span></a>
				<a id='btn_html' class='btn switch-html'><span>HTML</span></a>
				<p class="help-block"><?= $this->form_validation->error('body'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Teaser Image </label>
			<div class="controls">
				<div id="package_image">
					<?php if(!empty($package->image_filename) && is_file("{$upload_url}/{$package->image_filename}")): ?>
						<a href="<?= $upload_url; ?>/<?= $package->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$package->image_filename); ?>"><img src="<?= $upload_url; ?>/<?= $package->image_filename ?>?v=<?= filemtime(FCPATH.$upload_url."/".$package->image_filename); ?>" style="max-width: 200px;max-height: 200px;"></a>
						<a id="rmv-teaser" class="btn btn-primary"><span>Remove Picture</span></a>
					<?php endif; ?>
				</div>
				<div id="image_upload">
				</div>
				<input type="hidden" placeholder="" name="image_filename" id="image_filename" value="<?= set_value('image_filename',$package->image_filename) ?>">
				<p class="help-block"><?= $this->form_validation->error('image_filename'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('bg_color') ?>">
			<label class="control-label" for="bg_color">Background Color</label>
			<div class="controls">
				<input type="text" placeholder="" name="bg_color" id="bg_color" value="<?= $this->form_validation->set_value('bg_color',$package->bg_color) ?>">
				<p class="help-block"><?= $this->form_validation->error('bg_color'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_published') ?>">
			<label class="control-label" for="tags">Published? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_published", $options, set_value('is_published',$package->is_published),"id='is_published' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_published'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button id="submit_package" type="submit" class="btn btn-primary">Update Package</button>
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
		
		// uploader
		$('#image_upload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path("pr.gif") ?>',
			allowedExtensions: ['.gif','.jpg', '.png'],
			target : base_url + '/admin/upload/process?filename=package_type_<?= $package->featured_id; ?>&location=<?=$_upload_url?>&width=700&height=700&ts=<?=time()?>',
			onComplete: function(files) {
				var image_file = files[0];
				var file_type = image_file.fileName.split('.').pop();
				
				$("#package_image").html('');
				$("#package_image").html('<a href="<?= $upload_url; ?>/package_type_<?= $package->featured_id; ?>.'+file_type+'?v='+Math.floor(Math.random() * 999999)+'"><img src="<?= $upload_url; ?>/package_type_<?= $package->featured_id; ?>.'+file_type+'?v='+Math.floor(Math.random() * 999999)+'" style="max-width: 200px;max-height: 200px;"></a>&nbsp;<a id="rmv-teaser" class="btn btn-primary"><span>Remove Picture</span></a>');
				

				$("#image_filename").val('package_type_<?= $package->featured_id; ?>.'+file_type);
			}
		});
		
		$("#bg_color").miniColors({
			letterCase: 'uppercase'
		});
	});
	
	
	
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
	
	$(document).on("click",'#rmv-teaser',function(){
		var remove_modal = b.modal.new({
			title: "Confirmation",
			html: "Are you sure you want to remove the teaser image?",
			disableClose: true,
			width: 330,
			buttons:{
				"No": function(){
					remove_modal.hide();
				},
				"Yes": function(){
					b.request({
						url: "/cms/packages/rmv_temp_img",
						data: {
							image_filename: '<?= $package->image_filename; ?>',
							action: "edit"
						},
						on_success: function(data){
							var notification_modal = b.modal.new();
							
							if(data.status == "ok")
							{
								notification_modal.init({
									title: "Image Removal Successful",
									html: data.msg,
									width: 300
								});
								$("#image_filename").val("");
								$("#package_image").html("");
							}
							else if(data.status == "error")
							{
								notification_modal.init({
									title: "Error Notification",
									html: data.msg,
									width: 300
								});
							}
							notification_modal.show();
						},
						on_error: function(){
							b.modal.new({
								title: "Error Notification",
								html: "There was an error in you request.",
								width: 340
							}).show();
						}
					});
					
					remove_modal.hide();
				}
			}
		});
		remove_modal.show();
	});
	
</script>
<?php endif; ?>