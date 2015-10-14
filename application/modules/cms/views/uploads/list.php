<?php
	$upload_url = $this->config->item("media_url") . "/uploads";
	$_upload_url = urlencode($upload_url);
?>
<h2>View Uploads</h2>
<hr/>
<style>
	#gallery ul {
		list-style-type: none;
		margin: 5px;
	}
	
	#gallery ul li {
		display: inline-block;
		margin: 2px;
		position: relative;
		width: 100px;
	}
</style>
<form action='' method='' class='form-horizontal'>
<fieldset >
<div class="control-group ">
	<label class="control-label" for="">Image Upload</label>
	<div class="controls">
		<label class='data'><div id="image_upload"></div></label>
	</div>
</div>
<?php if (empty($uploads)): ?>
	<h3>Uploads not found.</h3>
<?php else: ?>
	<div class="control-group ">
		<label class="control-label" for="">Gallery</label>
		<div class="controls">
			<label class='data'>
				<div id="gallery" class="well" style="width: 426px;">
					<ul>
					<?php
					
					if(!empty($uploads))
					{
						foreach($uploads as $picture)
						{
							echo "<li ><a href='{$upload_url}/{$picture->image_filename}'><img src='{$upload_url}/thumbnail/{$picture->image_filename}' alt='{$this->config->item('base_url')}{$upload_url}/{$picture->image_filename}' title='URL: {$this->config->item('base_url')}{$upload_url}/{$picture->image_filename}' /></a><p><a data='{$picture->image_id}' class='btn btn-primary remove_picture'>Delete</a></p></li>";
						}
					}
					
					?>
					</ul>
				</div>
			</label>
		</div>
	</div>
	<div>
	<?= $this->pager->create_links();  ?>
	</div>
<?php endif; ?>
</fieldset>
</form>
<script type="text/javascript">

$(document).ready(function(){
	$("#gallery").yoxview({
		popupMargin: "50 100",
		autoHideInfo: false,
		autoHideMenu: false,
		renderInfoPin: false
	});

	// uploader
	$('#image_upload').Uploadrr({
		singleUpload : false,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.gif','.jpg', '.png'],
		target : base_url + '/admin/upload/process?location=<?=$_upload_url?>&type=general&gallery_id=0&ts=<?=time()?>',
		onComplete: function() {
			redirect("/cms/uploads/");
		}
	});
})

	$(".remove_picture").click(function(e){
		var confirm_delete = b.modal.new({});
		
		confirm_delete.init({
			title: "Confirm Picture Deletion",
			html: "Are you sure you want to delete this picture?",
			width: 300,
			buttons: {
				"No": function(){
					confirm_delete.hide();
				},
				"Yes": function(){
					confirm_delete.hide();
					b.request({
						url: '/cms/uploads/update_gallery',
						data: {
							"image_id": $(e.target).attr("data"),
						},
						on_success: function(data) {
							redirect("/cms/uploads");
						},
						on_error: function(data){

						}
					});

				}
			},
			disableClose: true
		});

		confirm_delete.show();
	});

</script>
