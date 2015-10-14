<?php
	echo css('slider/base/advanced-slider-base.css'); 
	echo css('slider/text-thumbnail-pointer/text-thumbnail-pointer.css'); 
	echo css('slider/custom.css'); 
	echo js('libs/slider/jquery.touchSwipe.min.js');
	echo js('libs/slider/jquery.advancedSlider.min.js');
?>


<div class="container">
	<div class="row">
		
		<div class="advanced-slider" id="text-thumbnails-slider">
			<ul class="slides">

				<li class="slide">
					<!-- if image will use the full slide area -->
					<img class="image" src="/assets/js/libs/slider/examples/presentation-assets/images/slides/image7.jpg" />
					
					<div class="layer static" data-horizontal="480" data-vertical="20" data-width="450">
						<div class="content-box">
							<p class="title">Full-size slide image</p> 
	                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
							<a class="btn btn-info" href="#">Read More</a>
						</div>
	            	</div>
					<div class="thumbnail">
						<span class="pointer"></span>
						<div class="thumbnail-text">
							<h3 class="thumbnail-title">Full-size slide image</h3>
							<p class="thumbnail-description">exercitation ullamco laboris nisi ut aliquip ex ea</p>
						</div>
	    			</div>
				</li>


				<li class="slide">					
					<!-- if image will just be a thumbnail / simple photo. also not actual image, it's just a proof of concept -->
					<div class="layer static feature-img " data-horizontal="15" data-vertical="15" data-width="460" data-height="200">
						<img class="image-box" src="/assets/js/libs/slider/examples/presentation-assets/images/slides/image2.jpg" />
					</div>
					
					<div class="layer static" data-horizontal="480" data-vertical="20" data-width="450">
						<div class="content-box">
							<p class="title">Featured Image Thumbnail</p> 
	                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
							<a class="btn btn-info" href="#">Read More</a>
						</div>
	            	</div>
	
					<div class="thumbnail">
						<span class="pointer"></span>
						<div class="thumbnail-text">
							<h3 class="thumbnail-title">Featured Image Thumbnail</h3>
							<p class="thumbnail-description">exercitation ullamco laboris nisi ut aliquip ex ea</p>
						</div>
	    			</div>
				</li>


				<li class="slide">
					<!-- no images defined -->
					
					<div class="layer static" data-horizontal="480" data-vertical="20" data-width="450">
						<div class="content-box">
							<p class="title">No Image Available</p> 
	                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
							<a class="btn btn-info" href="#">Read More</a>
						</div>
	            	</div>
	
					<div class="thumbnail">
						<span class="pointer"></span>
						<div class="thumbnail-text">
							<h3 class="thumbnail-title">No Image Available</h3>
							<p class="thumbnail-description">exercitation ullamco laboris nisi ut aliquip ex ea</p>
						</div>
	    			</div>
				</li>


				<li class="slide">
					<!-- product slides will have larger text area to compensate for the smaller images -->
					<div class="layer static feature-img product" data-horizontal="0" data-vertical="15" data-width="320">	
						<img class="image-box" src="/assets/media/products/product_48_135178079796.jpg" alt=""/>
					</div>

					<div class="layer static" data-horizontal="320" data-vertical="20" data-width="610">
						<div class="content-box">
							<p class="title">Inline HTML content</p> 
	                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
							<a  class="btn btn-success" href="#">Add to Cart</a>
						</div>
	            	</div>
					
					<div class="thumbnail">
						<span class="pointer"></span>
						<!-- this is the div to style to change bg color -->
						<div class="thumbnail-text" style="background-color: lime">
							<h3 class="thumbnail-title">Sodium Ascorbate</h3>
							<p class="thumbnail-description">Personal Care Line</p>
						</div>
	    			</div>
				</li>


				<li class="slide">
					<div class="layer static feature-img product" data-horizontal="0" data-vertical="15" data-width="320">	
						<img class="image-box" src="/assets/media/products/product_89_13517814415.jpg" alt=""/>
					</div>

					<div class="layer static" data-horizontal="320" data-vertical="20" data-width="610">
						<div class="content-box">
							<p class="title">Inline HTML content</p> 
	                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
							<a  class="btn btn-success" href="#">Add to Cart</a>
						</div>
	            	</div>
					
					<div class="thumbnail">
						<span class="pointer"></span>
						<div class="thumbnail-text" style="background-color: beige">
							<h3 class="thumbnail-title">VITAL DENTA MAX 130G</h3>
							<p class="thumbnail-description">Personal Care Line</p>
						</div>
	    			</div>
				</li>
				
				<li class="slide">
					<div class="layer static feature-img product" data-horizontal="0" data-vertical="15" data-width="320">	
						<img class="image-box" src="/assets/media/products/product_85_135177930230.jpg" alt=""/>
					</div>

					<div class="layer static" data-horizontal="320" data-vertical="20" data-width="610">
						<div class="content-box">
							<p class="title">Inline HTML content</p> 
	                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
							<a  class="btn btn-success" href="#">Add to Cart</a>
						</div>
	            	</div>
					
					<div class="thumbnail">
						<span class="pointer"></span>
						<div class="thumbnail-text" style="background-color: yellow">
							<h3 class="thumbnail-title">Eea de toilette 55ML Women - HARMONY</h3>
							<p class="thumbnail-description">Personal Care Line</p>
						</div>
	    			</div>
				</li>

			</ul>
		</div>
		
		
	</div>
</div>

<script type="text/javascript">	

	jQuery(document).ready(function($){
		$('#text-thumbnails-slider').advancedSlider({width: 956,
													 height: 345,
													 skin: 'text-thumbnail-pointer',
													 shadow: false,
													 effectType: 'swipe',
													 overrideTransition: true,
													 slideButtons: false,
													 thumbnailType: 'scroller',
													 thumbnailWidth: 158,
													 thumbnailHeight: 120,
													 thumbnailArrows: false,
													 maximumVisibleThumbnails: 6,
													 keyboardNavigation: true,
													 slideArrows: false,
													 timerToggle: true
													 });
	});	

</script>