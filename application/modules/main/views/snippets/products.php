<style>
    .products-image{
        visibility: hidden;
    }
</style>
<div class="products">
	<?php
		$product_feature_type = 1; //featured'
		$where = array(
			'feature_type_id' => 1,
		);
		$featured_products = $this->contents_model->get_featured_products($where, "", "order_id");
	?>
	<?php if(!empty($featured_products)): ?>
	<div id='slider' class="products-image slider-horizontal">
		<?php 
			$this->load->model('contents_model');
			$this->load->model('items_model');
			$cnt = 0;
			$prods = array();
			foreach($featured_products as $k=> $f)
			{
				$product = $this->items_model->get_product_by_id($f->product_id);

				$image = "";
				$image = json_decode($product->image_filename);
				if (empty($image))
				{
					//use placeholder
					$image = check_image_path($image);
				}
				else
				{
					foreach($image as $i)
					{
						if($i->is_default == TRUE)
						{
							$image = $i->url;
							$image = check_image_path($image) ;
							break;
						}
					}
				}
				
				$prods[$product->product_id] = new ArrayClass(array(
					'product_id' => $product->product_id,
					'image_url' => $image,
					'image_filename' => $product->image_filename,
					'product_name' => $product->product_name,
					'product_line' => $product->product_line,
					'product_description' => $product->product_description,
					'standard_retail_price' => floatVal($product->standard_retail_price),
					'member_price' => floatVal($product->member_price),
					'employee_price' => floatVal($product->employee_price),
					
				));
				
		?>
			<div class="item item-<?=$cnt++;?>">
				<img src="<?=$image;?>" class="items-image"></img>
				<!-- insert product details here-->
				<input type="hidden" class="product_name" name="product_name" value="<?= $product->product_name?>" />
				<input type="hidden" class="product_description" name="product_description" value="<?= $product->product_description?>" />
				<input type="hidden" class="product_line" name="product_line" value="<?= $product->product_line?>" />
				<input type="hidden" class="standard_retail_price" name="standard_retail_price" value="<?= $product->standard_retail_price?>" />
				<input type="hidden" class="member_price" name="member_price" value="<?= $product->member_price?>" />
				<input type="hidden" class="product_id" name="product_id" value="<?= $product->product_id?>" />
				<div style="display:none;" class="quick-view">
					<button class="btn btn-success item-click" data-id="<?=$product->product_id;?>" >Quick View</button>
				</div>
			</div>
		<?php		
			}
		?>
	</div>
	<?php endif; ?>
	<script id='product-dialog-template' type='text/template'>
	<div id='product-quick-view-box' class="product-item">
		<a class="item">
			<img src="<%= product.image_url %>" id="cart-item" class="items-image" alt="<%= product.product_name %>" ></img>
		</a>
		<a href="#">
			<img src="<?=$this->config->item('img_url')?>/exit_icon_bg.png" class="pull-right" id="exit-cart"></img>
		</a>
		<ul>
			<li><h2><span class="product_name"><%= product.product_name %></span></h2></li>
			<li><h4 class="product_line"><%= product.product_line %></h4></li>
			<li>
				<p class="product_description"><%= _.unescapeHTML(product.product_description) %></p>
			</li>
			<li>
				<ul>
					<li><div class="member_price"><%= numberFormat(product.member_price, 2) %></div>Member Price</li>
					<li><div class="standard_retail_price"><%= numberFormat(product.standard_retail_price, 2) %></div>Standard Retail Price</li>
				</ul>
			</li>
			<li>
				<a href="#" class="btn btn-success btn_add_to_cart" data-id='<%= product.product_id %>' >Add to Cart</a>
			</li>
		</ul>
	</div>
	</script>
	<div id="link_all_products" class="row-fluid pager-content">
		<ul class="famer-pager nav">
			<li><a href="/main/products"> See all our products<i class="icon-blue-arrow"></i></a></li>
		</ul>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function (){
		
		var products = <?= json_encode($prods); ?>;
		
        $("#slider").FlowSlider({
			startPosition: 0.5,
			position: 0.5
		});
		
		
		$('.item').hover(
			function(){
				$(this).children('div').css('display','block');
			},
			function(){
				$(this).children('div').css('display','none');
			}
		);
		
		$('.item-click').click(function (){
			
			var product_id = $(this).data('id');
			
			$(_.template($('#product-dialog-template').html(), {'product' : products[product_id]})).insertAfter('#slider');
			$('#slider').hide();
			$('#link_all_products').hide();
			
			$('#exit-cart').click(function (e) {
				e.preventDefault();
				$('#product-quick-view-box').remove();
				$('#slider').show();
				$('#link_all_products').show();
			});
			
			vitalc.cart.initialize();
			
		});

		$('.news-updates li').hover(
			function(){
				$(this).children('p').children('a').css('visibility','visible');
			},
			function(){
				$(this).children('p').children('a').css('visibility','hidden');
			}
		);

			$('.quick-view').click(function () {
				$(this).load(function () {
					var height = $('#cart-item').height();
					if(height<200 && height!=0){
						total_height = (200 - height)/2;
					    $('#cart-item').css('margin-top',total_height);
					}
				});
				var height = $('#cart-item').height();
				if(height<200 && height!=0){
					total_height = (200 - height)/2;
				    $('#cart-item').css('margin-top',total_height);
				}
			});

			$('.items-image').each(function () {
				$(this).load(function () {
					var height = $(this).height();
				    if(height<200 && height!=0){
				 		total_height = (200 - height)/2;
				 		$(this).css('margin-top',total_height);
				 	}
				});
				var height = $(this).height();
				   	if(height<200 && height!=0){
					 	total_height = (200 - height)/2;
						$(this).css('margin-top',total_height);
					}
			});

        var imgs = $(".www_FlowSlider_com-wrap-2 .items-image");
        var count = imgs.length;
        if (count) {

            imgs.each(function(index, elem){

                $(elem).load(function(){
                    count--;
                    console.log("count:" +count);
                    if (count==0) {
                        setTimeout(function(){$(".products-image").css("visibility","visible")},700);
                    }
                });
            });

        }setTimeout(function(){$(".products-image").css("visibility","visible");},700);


    });
</script>