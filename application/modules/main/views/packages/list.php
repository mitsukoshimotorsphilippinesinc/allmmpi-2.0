<div class="content-area">
	<div class="row">
		<h2 class="product_header">All Packages</h2>
		<hr/>
		<div class="pagination pagination-centered">
				<?= $this->pager->create_links();  ?>
		</div>
		<ul class="all-products-item">
			
			<!-- ****start of loop**** -->
			<?php 
				
				$prods = array();
				
				foreach($products as $p): 
					
					$image = "";
					$product_images = json_decode($p->image_filename);
					if (empty($product_images))
					{
						//use placeholder
						$image = check_image_path($image);
					}
					else
					{
						foreach($product_images as $i)
						{
							if($i->is_default == TRUE)
							{
								$image = check_image_path($i->url);
								break;
							}
						}
					}
					
					$prods[$p->product_id] = new ArrayClass(array(
						'product_id' => $p->product_id,
						'image_url' => $image,
						'image_filename' => $p->image_filename,
						'product_name' => $p->product_name,
						'product_line' => $p->product_line,
						'product_description' => $p->product_description,
						'standard_retail_price' => floatVal($p->standard_retail_price),
						'member_price' => floatVal($p->member_price),
						'employee_price' => floatVal($p->employee_price),
						
					));
					
			?>
				
				<li class="items">
					<div class="product-image">
						<img class="items-image" src="<?= $image; ?>" alt="<?= $p->product_name; ?>" />
						<div class='quick-view-box'>
							<div class='bg'></div>
							<button class="btn btn-success btn-prod-quick-view" data-id='<?= $p->product_id ?>'>Quick View</button>
						</div>
					</div>
					<span class="product-name"><?= $p->product_name; ?></span>
					<span class="product-group"><?= $p->product_line; ?></span>
				</li>
				
			<?php endforeach; ?>
			<!-- ****end of loop**** -->
			
		</ul>
		<div class="pagination pagination-centered">
			<?= $this->pager->create_links();  ?>
		</div>
	</div>

</div>


<script id='product-dialog-template' type='text/template'>
<div class='product-dialog'>
	<div class='prod_image'>
		<img src="<%= product.image_url %>" alt="<%= product.product_name %>">
	</div>
	<ul>
   		<li>
			<span class='prod-line'><%= product.product_line %></span>
			<div class='prod-description'><%= _.unescapeHTML(product.product_description) %></div>
		</li>	
	</ul>
	<ol>
		<li><div id="member_price"><%= numberFormat(product.member_price, 2) %></div>Member Price</li>
		<li><div id="standard_retail_price"><%= numberFormat(product.standard_retail_price, 2) %></div>Standard Retail Price</li>
	</ol>
	<button id='add_to_cart' class="btn btn-success" data-id='<%= product.product_id %>'>Add to Cart</button>
</div>
</script>

<script type="text/javascript">

	$(function() {
		
		var products = products = <?= json_encode($prods); ?>;

		$('.items').hover(
			function(){
				$(this).find('.quick-view-box').show();
			},
			function(){
				$(this).find('.quick-view-box').hide();
			}
		);
		
		$('.btn-prod-quick-view').click(function(e) {
			e.preventDefault();
			
			var product_id = $(this).data('id');
			
			if (typeof(products[product_id]) == 'undefined') return false;
			
			var modal = b.modal.create({
				title: products[product_id].product_name,
				html: _.template($('#product-dialog-template').html(), {'product' : products[product_id]}),
				width: 700
			});
			modal.show();
			
			$('#add_to_cart').click(function(e) {
				e.preventDefault();
				
				var is_logged_in = "<?= $this->member->member_id ?>";

				if (is_logged_in == 0) {
					
					redirect('/members/signin');
					
				} else {
					
					//call to /cart/add
					beyond.request({
						url : '/cart/cart/add',
						data : {
							'product_id' : product_id,
							'quantity' : quantity,
							//swappable items
						},
						on_success : function(data) {
							if (data.status == "1")	{
								var success_modal = b.modal.new({
									title: "Add To Cart Success",
									width: 350,
									disableClose: true,
									html: data.html,
									buttons: {
										"Close": function(){
											//redirect
											window.location.href = "/cart/index";
										}
									}
								});
								success_modal.show();
							}
						}
					});
				}
				
			});
			
		});
		
	
	});

</script>