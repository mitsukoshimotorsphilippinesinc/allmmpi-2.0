<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/products";
	$_upload_url = urlencode($upload_url);

	$available_products_html = "";

	foreach($available_products as $ap)
	{
		$available_products_html .= "<option value='{$ap->product_id}'>{$ap->product_name}</option>";
	}
?>

<div>
	<div class="alert alert-info">	
		<h2 id="header_text">Products <a id='add_new_product' class='btn btn-small' style="float: right; margin-top: 5px; margin-right: -30px;"><i class="icon-plus"></i><span> Add New</span></a></h2>
	</div>
	<div id="main_page_container" >
		<table class='table table-striped table-bordered'>
			<thead>
				<tr>
					<th style="width:50px;">Position</th>
					<th>Product</th>
					<th style='width:145px;'>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php if(empty($products)): ?>
				<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
			<?php else: ?>
			<?php foreach ($products as $p): ?>
				<?php
					$product_name = $this->items_model->get_product_by_id($p->product_id);
				?>
				<tr>
					<td><?= $p->order_id;?></td>
					<td><?= $product_name->product_name; ?></td>
					<td>
						<?php if($p->order_id > 1): ?>
						<a href='/cms/products/order/up/<?= $p->product_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
						<?php endif; ?>
						<?php if($p->order_id < $max_order): ?>
						<a href='/cms/products/order/down/<?= $p->product_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
						<?php endif; ?>
						<a class='btn btn-small btn-info btn_show_gallery' title="Gallery" data="<?= $p->product_id?>"><i class="icon-picture icon-white"></i></a>
						<a href='/cms/products/delete/<?= $p->product_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>				
	
<script type="text/javascript">
  //<![CDATA[
	var available_products_html = "<?= $available_products_html?>";
	$(document).on("click",".btn_show_gallery",function(){
		var _product_id = $(this).attr("data");
		
		b.request({
			url: '/cms/products/gallery',
			data : {
				'_product_id' : _product_id
			},
			on_success : function(data){
				if (data.status == "ok")	{

					// show add form modal
					var editGalleryModal = b.modal.new({
						title: 'Gallery',
						width: 780,
						html: data.data.html
					});
					editGalleryModal.show();
				}
			}
		});
	});

	$("#add_new_product").click(function(){
		var add_product = b.modal.new({
			title: "Add Featured Product",
			html: "<label style='display:inline;'>Product:</label> <select id='available_products'>"+available_products_html+"</select>",
			width: 400,
			buttons: {
				"Add": function(){
					var product_id = $("#available_products").val();
					var product_name = $("#available_products>option:selected").text();
					var add_confirm = b.modal.new({
						title: "Confirmation",
						html: "Are you sure you want to add <strong>"+product_name+"</strong> as a featured product?",
						width: 350,
						disableClose: true,
						buttons: {
							"No": function(){
								add_confirm.hide();
							},
							"Yes": function(){
								b.request({
									url: "/cms/products/add",
									data: {
										"product_id": product_id
									},
									on_success: function(data){
										if(data.status == "ok")
										{
											b.modal.new({
												title: "Success",
												html: "Add featured product successful",
												width: 300,
												disableClose: true,
												buttons: {
													"Close": function(){
														redirect("/cms/products");
													}
												}
											}).show();
										}
										else
										{

										}
									}
								});
								add_confirm.hide();
							}
						}
					});
					add_confirm.show();
					add_product.hide();


				}
			}
		});

		add_product.show();
	});
//]]>
</script>