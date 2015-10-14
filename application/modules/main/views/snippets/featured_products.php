<div class="popular-products">
	<h3>Popular Products</h3>
	<ul>
		<?php foreach($popular_products as $p): ?>
		<li>
			<?php
			$image_filename = "http://placehold.it/100x100";
			if(!is_null($p->image_filename)) $image_filename = "/assets/media/products/{$p->image_filename}";
			?>
			<a href="#" class="view-product" data='<?=$p->product_id?>|product|0'><img src="<?=$image_filename?>" alt="<?= $p->item_name ?>" style="width: 100px; height: 100px;"></a>
			<p><a href="#" class="view-product" data='<?=$p->product_id?>|product|0'><?= $p->item_name ?></a></p>
		</li>
		<?php endforeach; ?>
    </ul>
</div><!-- end: div.popular-products -->
<script type='text/javascript'>

	$(document).on("click",".view-product",function() {
		var _data = $(this).attr("data");

		data = _data.split("|");
		var p_id = parseInt(data[0]);
		var p_category = data[1];
		var p_swappable = data[2];


		//ajax request
		b.request({

			url: '/main/products/view',
			data : {
				'p_id': p_id,
				'p_category' : p_category
			},
			on_success: function(data, status) {
				var product_view_modal = b.modal.new({
					title: data.title,
			        width: '650px',
			        html:  "<div><p>"+data.html+"</p></div>",
			        buttons: {
			            'Add Cart' : function() {
							/*redirect to login for now*/
			                //addToCart(p_id, p_category);
							redirect("members/signin")
							product_view_modal.hide();

			            }
			        }
				})
				product_view_modal.show();
		    }

		});

		return;

	});

	var addToCart = function(prodpack_id, prodpack_type, swap_item_id) {
		if (typeof(prodpack_id) == "undefined") return false;;

		if (typeof(swap_item_id) == "undefined") {
			swap_item_id = 0;
		};

		alert("swap_item_id -" + swap_item_id + "-");

		// check if user is logged-in
		//if (user.is_logged_id) {
		if (prodpack_id) {
			// user is currently loggedin, ajaxrequest
			b.request({

				url: '/cart/add',
				data : {
					'prodpack_id': prodpack_id,
					'prodpack_type' : prodpack_type,
					'swap_item_id' : swap_item_id
				},
				on_success: function(data, status) {
					if (data.status == 1) {
							//var qty = parseInt($("#shopping_cart_quantity").text()) + (data.qty * 1);
							//$("#shopping_cart_quantity").text(qty);
				            redirect("cart");
					} else {


					}
			    }
			});

			return false;

		} else {
			// user is not logged in
			var cart_modal = b.modal.new({
				title: "Buying Product :: Notification",
		        width: '650px',
		        html:  "<p>You are not logged in. Please log in first before purchasing anything.</p>"

			})
			cart_modal.show();
		}

	};

</script>