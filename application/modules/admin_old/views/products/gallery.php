<?php if (empty($product)): ?>
	<h3>Product not found.</h3>
<?php else: ?>
<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/products";
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
							$images = json_decode($product->image_filename);
							if(!empty($images))
							{
								foreach($images as $image)
								{
									$primary = "";
									if($image->is_default) $primary = "primary";
									echo "<li ><div data='{$image->url}' class='thumb {$primary}' style='background-image:url(\"{$this->config->item('base_url')}/{$image->url}\");'>&nbsp;</div><a class='btn btn-danger btn-small delete-img' title='Delete'  data='{$image->url}'><i class='icon-remove icon-white'></i></a></li>";
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
		target : base_url + "/admin/upload/process_prod_pack?id=<?=$product->product_id?>&filename=product_<?=$product->product_id?>&location=<?=$_upload_url?>&type=product&width=200&height=200&ts=<?=time()?>",
		onComplete: function() {
			b.request({
				url: '/admin/products/gallery',
				data : {
					'_product_id' : <?=$product->product_id?>
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
		var _product_id = <?=$product->product_id?>;

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
							url : "/admin/products/set_primary",
							data : {
								"product_id" : _product_id,
								"filename" : filename
							},
							on_success : function(data){
								var setPrimarySuccessModal = b.modal.new({
									title : "Success",
									html : "<p><img src=<?= $this->config->item('base_url'); ?>/"+data.data.filename+"></p><p>"+data.msg+"</p>",
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

	$(".delete-img").click(function(){
		var filename = $(this).attr("data");
		var _product_id = <?=$product->product_id?>;
		var me = this;

		if(!$(this).hasClass("no_clicking"))
		{
			$(".delete-img").addClass("no_clicking");

			var remove_image = b.modal.new({
				title : "Confirmation",
				html : "Are you sure you want to delete this picture?",
				width: 300,
				disableClose : true,
				buttons: {
					"No" : function(){
						$(".delete-img").removeClass("no_clicking");
						remove_image.hide();
					},
					"Yes": function(){
						b.request({
							url : "/admin/products/delete_image",
							data : {
								"product_id" : _product_id,
								"filename" : filename
							},
							on_success : function(data){
								var imgDeleteSuccessModal = b.modal.new({
									title : "Success",
									html : "<p>"+data.msg+"</p>",
									width : 350,
									disableClose: true,
									buttons: {
										"Close": function(){
											imgDeleteSuccessModal.hide();
											$(me).parent().parent().parent().parent().parent().parent().parent().parent().parent().html(data.data.html);
										}
									}
								});
								imgDeleteSuccessModal.show();
								$(".delete-img").removeClass("no_clicking");

							},
							on_error : function(){
								var imgDeleteErrorModal = b.modal.new({
									title : "Error Notification",
									html : "<p>There was an error in your request.</p>",
									width : 250
								});
								imgDeleteErrorModal.show();
								$(".delete-img").removeClass("no_clicking");
							}
						});

						remove_image.hide();
					}
				}

			});
			remove_image.show();
		}
	});
</script>