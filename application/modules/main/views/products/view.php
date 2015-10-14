<div class="content-area">
	<div class="row">
		<div>
			<h2>Product Lines</h2>
			<select id='product_line' style='width:250px'>
				<option value='all'>ALL PRODUCTS</option>
				<?php foreach($product_lines as $p): ?>
					<option value='<?=$p->product_line_id?>'><?=$p->product_line?></option>
				<?php endforeach; ?>
				<option value='0'>PACKAGES</option>
			</select>
			<div style='float:right' id='search_product'>
				<form id='search_product_name' class='form-horizontal' method='get' action='/main/products/view'>
					<strong>Search Product:</strong>            

					<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

					<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>

					<br/>
					<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>
					
					<?php
					if ($search_string == "") {
					?>	
						<div id="search_summary" style="display:none;">
					<?php
					} else {
					?>	
						<div id="search_summary" style='float:left;'>
					<?php
					};
					?>		

						<span class="label label-info">Search Results for:</span>
						<span class="label label-success"><?= $search_string ?></span>
					</div>
				</form>
			</div>
		</div>
		<hr/>
		<div class="pagination pagination-centered">
				<?= $this->pager->create_links($search_url);  ?>
		</div>
		<ul class="all-products-item row">
			
			<!-- ****start of loop**** -->
			<?php 
				
				$prods = array();
				
				foreach($products as $p): 
					
					// check if product_type is_visible
					$prod_type = $this->items_model->get_product_types("product_type_id = ".$p->product_type_id);
					$pt = $prod_type[0];
					if($pt->is_visible == 0) continue;
					
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
				<!-- items -->
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
			<?= $this->pager->create_links($search_url);  ?>
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
	<button id='add_to_cart' class="btn btn-success btn_add_to_cart" data-id="<%= product.product_id %>" >Add to Cart</button>
</div>
</script>

<script type="text/javascript">

	$(document).on('ready', function() {
		var selected_product_line = "<?=$selected_product_line?>";
		$("#product_line option[value="+selected_product_line+"]").attr("selected", "selected");
	});

	$('#product_line').change(function(){
		var product_line_id = $('#product_line').val();
		if(product_line_id == 'all') {
			redirect('/main/products');
		}else {
			var link = "/main/products/view/" + product_line_id;
			redirect(link);
		}
	});

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
			
			vitalc.cart.product_view_modal = b.modal.create({
				title: products[product_id].product_name,
				html: _.template($('#product-dialog-template').html(), {'product' : products[product_id]}),
				width: 700
			});
			vitalc.cart.product_view_modal.show();
			
			vitalc.cart.initialize();
			
		});
		
	
	});

</script>