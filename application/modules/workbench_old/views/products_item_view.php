<div class="content-area">
	<div class="row-fluid">
		<h2>All Products</h2>
		<div class="pagination pagination-centered">
			<ul>
				<li><a href="#"><img src="../img/arrow_left_pagination.png" alt="left"></a></li>
				<li class="active"><a href="#">1</a></li>
				<li><a href="#">2</a></li>
				<li><a href="#">3</a></li>
				<li><a href="#">4</a></li>
				<li><a href="#">5</a></li>
				<li><a href="#">6</a></li>
				<li><a href="#">7</a></li>
				<li><a href="#"><img src="../img/arrow_right_pagination.png" alt="right"></a></li>
			</ul>
		</div>
		<ul class="all-products-item">
			<!-- ****start of loop**** -->
			<?php foreach($products as $p): ?>
				<li class="items">
					<div>
						<button href="#myModal" data-toggle="modal" class="btn btn-success item-click" id="item-1">Quick View</button>
					</div>
					<a href="#"><img src="<?= $p->image_filename?>" alt="product-item"></a>
					<h3><?= $p->product_name; ?></h3>
					<h4><?= $p->product_line; ?></h4>
					<p>
						<?= $p->product_description; ?>
					</p>
				</li>
			<?php endforeach; ?>
			<!-- ****end of loop**** -->
			<!--
			<li class="items">
				<div>
					<button href="#myModal" data-toggle="modal" class="btn btn-success item-click" id="item-1">Quick View</button>
				</div>
				<a href="#"><img src="#" alt="product-item"></a>
				<h3>Product Name</h3>
				<h4>Daily Suppliment Line</h4>
				<p>
					Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam cursus. Morbi ut mi.
				</p>
			</li>-->
		</ul>
	</div>
	<div class="pagination pagination-centered">
		<ul>
			<li><a href="#"><img src="../img/arrow_left_pagination.png" alt="left"></a></li>
			<li class="active"><a href="#">1</a></li>
			<li><a href="#">2</a></li>
			<li><a href="#">3</a></li>
			<li><a href="#">4</a></li>
			<li><a href="#">5</a></li>
			<li><a href="#">6</a></li>
			<li><a href="#">7</a></li>
			<li><a href="#"><img src="../img/arrow_right_pagination.png" alt="right"></a></li>
		</ul>
	</div>
</div>

<script type="text/javascript">
	
		$('.items').hover(
			function(){
				$(this).children('div').css('display','block')
			},
			function(){
				$(this).children('div').css('display','none')
			}
		)
	
</script>