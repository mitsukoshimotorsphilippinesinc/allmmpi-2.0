<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = "/assets/uploads";
	$_upload_url = urlencode($upload_url);
		
	$image_filename = set_value("image_filename");
		
	if(empty($image_filename)) $time = time()."".rand(10,99)."".rand(10,99);
	else $time = str_replace("temp_image_","",$image_filename);
	
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
<h2>Add New News  <a href='/cms/news' class='btn btn-small' >Back</a></h2>
<hr/>
<form action='/cms/news/add' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('title') ?>">
			<label class="control-label" for="title">Title <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="title" id="title" value="<?= $this->form_validation->set_value('title') ?>">
				<p class="help-block"><?= $this->form_validation->error('title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('sub_title') ?>">
			<label class="control-label" for="sub_title">Sub Title </label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="sub_title" id="sub_title" value="<?= $this->form_validation->set_value('sub_title') ?>">
				<p class="help-block"><?= $this->form_validation->error('sub_title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('excerpt') ?>">
			<label class="control-label" for="excerpt">Excerpt <em>*</em></label>
			<div class="controls">
				<textarea name="excerpt" id="excerpt " class="input-xlarge span10" rows="5"><?= $this->form_validation->set_value('excerpt') ?></textarea>
				<p class="help-block"><?= $this->form_validation->error('excerpt '); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('body') ?>">
			<label class="control-label" for="body">Body <em>*</em></label>
			<div class="controls">
				<div class=''>
					<textarea name="body" id="body" class="input-xlarge span10" rows="20"><?= $this->form_validation->set_value('body') ?></textarea>
				</div>
				<a id='btn_view' class='btn switch-tmce'><span>View</span></a>
				<a id='btn_html' class='btn switch-html'><span>HTML</span></a>
				<p class="help-block"><?= $this->form_validation->error('body'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Teaser Image </label>
			<div class="controls">
				<div id="news_image">
					<?php if(!empty($image_filename)): ?>
						<a href="/assets/uploads/<?= $image_filename ?>.jpg"><img src="/assets/uploads/<?= $image_filename ?>.jpg" style="max-width: 200px;max-height: 200px;"></a>
						<a id="rmv-teaser" class="btn btn-primary"><span>Remove Picture</span></a>
					<?php endif; ?>
				</div>
				<div id="image_upload">
				</div>
				<input type="hidden" placeholder="" name="image_filename" id="image_filename" value="<?= set_value('image_filename') ?>">
				<p class="help-block"><?= $this->form_validation->error('image_filename'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('tags') ?>">
			<label class="control-label" for="tags">Tags <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="tags" id="tags" value="<?= set_value('tags') ?>">
				<p class="help-block"><?= $this->form_validation->error('tags'); ?></p>
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
				<button id="submit_news" type="submit" class="btn btn-primary">Add New News</button>
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
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.gif','.jpg', '.png'],
			target : base_url + '/admin/upload/process?filename=temp_image_<?= $time; ?>&location=<?=$_upload_url?>&width=700&height=700&ts=<?=time()?>',
			onComplete: function() {
				var image_filename = "temp_image_<?= $time; ?>";
				$("#image_filename").val(image_filename);
				$("#news_image").html('<a href="/assets/uploads/'+image_filename+'.jpg"><img src="/assets/uploads/'+image_filename+'.jpg" style="max-width: 200px;max-height: 200px;"></a>&nbsp;<a id="rmv-teaser" class="btn btn-primary"><span>Remove Picture</span></a>');
			}
		});
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
						url: "/cms/news/rmv_temp_img",
						data: {
							image_filename: $("#image_filename").val(),
							action: "add"
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
								$("#news_image").html("");
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