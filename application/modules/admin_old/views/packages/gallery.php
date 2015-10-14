<?php if (empty($package)): ?>
	<h3>Package not found.</h3>
<?php else: ?>
<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/packages";
	$_upload_url = urlencode($upload_url);
?>
<style>
	#images ul {
		list-style-type: none;
		margin: 5px;
	}
	
	#images ul li {
		display: inline-block;
		margin: 2px;
		margin-right: 4px;
		margin-bottom: 5px;
		position: relative;
		width: 70px;
	}
	
	#images ul li div.thumb {
		height: 70px;
		width: 70px;
		min-width: 70px;
		min-height: 70px;
		background-size: 100%;
	}
	
	#images ul li div.thumb:hover {
		cursor: pointer;
	}
	
	#images ul li div.thumb:hover:after {
		position: absolute;
		content: "Set as Primary";
	}
	
	#images ul li div.primary {
		border: 2px solid #83F52C;
	}
	
	#images ul li div.primary:after {
		position: absolute;
		content: "Primary";
	}
</style>
<div style="height: 350px;">
	<form action='' method='post' class='form-inline'>
		<fieldset >
			<div class="row-fluid">
				<div class="span7">
					<div class="row-fluid">
						<div class='alert alert-success'>
							<h4>Upload</h4>
						</div>
					</div>
					<div class="image_upload"></div>
				</div>
				<div class="span3">
					<div class="row-fluid">
						<div class='alert alert-success'>
							<h4>Images</h4>
						</div>
					</div>
					<div id="images" class="well" style="overflow: hidden; overflow-y: scroll; height:270px;">
						<ul>
							<?php
							$images = json_decode($package->image_filename);
							if(!empty($images))
							{
								foreach($images->images as $image)
								{
									$primary = "";
									if($image->is_main) $primary = "primary";
									echo "<li ><div data='{$image->filename}' class='thumb {$primary}' style='background-image:url(\"{$upload_url}/{$image->filename}\");'>&nbsp;</div></li>";
								}
							}

							?>
						</ul>
					</div>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<?php endif; ?>
<script type="text/javascript">

	$('.image_upload').html("");

	// uploader
	$('.image_upload').Uploadrr({
		singleUpload : false,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.gif','.jpg', '.png'],
		target : base_url + "/admin/upload/process_prod_pack?id=<?=$package->package_id?>&filename=package_<?=$package->package_id?>&location=<?=$_upload_url?>&type=package&width=200&height=200&ts=<?=time()?>",
		onComplete: function() {
			b.request({
				url: '/admin/packages/gallery',
				data : {
					'_package_id' : <?=$package->package_id?>
				},
				on_success : function(data){
					if (data.status == "ok")
					{
						$(".image_upload").parent().parent().parent().parent().html(data.data.html);
					}
				}
			});
			
		}
	});
	
	$(".thumb").click(function(e){
		var filename = $(this).attr("data");
		var _package_id = <?=$package->package_id?>;

		if(!$(this).hasClass("no-clicking"))
		{
			$(".thumb").addClass("no-clicking");
			setPrimaryModal = b.modal.new({
				title : "Confirmation",
				html : "Are you sure you want to set this picture as primary?",
				width: 300,
				disableClose : true,
				buttons : {
					"No" : function(){
						$(".thumb").removeClass("no-clicking");
						setPrimaryModal.hide();
					},
					"Yes" : function(){
						b.request({
							url : "/admin/packages/set_primary",
							data : {
								"package_id" : _package_id,
								"filename" : filename
							},
							on_success : function(data){
								var setPrimarySuccessModal = b.modal.new({
									title : "Success",
									html : "<p><img src=<?=$upload_url?>/"+data.data.filename+"></p><p>"+data.msg+"</p>",
									width : 350
								});
								setPrimarySuccessModal.show();
								$(".thumb").removeClass("no-clicking");
								$(".thumb").removeClass("primary");
								$(e.target).addClass("primary");
								
							},
							on_error : function(){
								var setPrimaryErrorModal = b.modal.new({
									title : "Error Notification",
									html : "<p>There was an error in your request.</p>",
									width : 250
								});
								setPrimaryErrorModal.show();
								$(".thumb").removeClass("no-clicking");
							}
						});
						setPrimaryModal.hide();
					}
				}
			});
			setPrimaryModal.show()
		}
	});
</script>