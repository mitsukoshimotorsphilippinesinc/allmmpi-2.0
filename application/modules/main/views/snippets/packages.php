
	<!--<div class="row-fluid">
		<div id="featured-packages">
			<div class="slides_container" style="width:inherit;background:#00bcf9">
				<div class="slide" style="width: 960px; height: 339px; display:block;">
					<div class="banner-logo span7">VitalC banner</div>
					<div class="banner-message span5">
						<h3>Premium Starter Pack</h3>
						<p>Lorem ipsum dolor sit amet, consectetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt uy labore et dolore magna aliquyam erat, sed diam voluptua.</p>
						<ul>
							<li class="pull-left"><a href="#">Learn more<i class="icon-blue-arrow"></i></a></li>
							<li class="pull-right" style="float:right; list-style-type:none"><a  href="/main/products/view/1">See all packages<i class="icon-blue-arrow"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<ul>
			<li><a href="#"><img src="<?=$this->config->item('img_url')?>/arrow_left_white_bg.png" alt="arrow left"></a></li>
			<li><a href="#"><img src="<?=$this->config->item('img_url')?>/arrow_right_white_bg.png" alt="arrow right"></a></li></li>
		</ul>
	</div>-->


	<div id="featured-packages-flow" style="min-width:1200px;">
	
		<div  id="package1" class="package_slider" style="min-width:1200px;height:360px;background:#00bcf9">
			<div style="width: 960px; margin-left:auto; margin-right:auto;">
				<div class="banner-logo span7"><img src="/assets/media/packages/starter-standard.png"></img></div>
				<div class="banner-message span5">
					<h3>Standard Starter Pack</h3>
					<p>
						<h4>Each Standard Package includes:</h4>
						<ol type="A">
							<li>RF ID</li>
							<li>Metrobank Paycard</li>
							<li>Sales Kit with Catalogue</li>
							<li>
								<p>*Promotional Discount Vouchers</p>
								<ul>
									<li>SET A (4 Vouchers)</li>
								</ul>
							</li>
							<li>*1 Pure Touch Hand Sanitizer 30 ml</li>
						</ol>
						<br />
						<h5>*Any promotional offer & free product/s may change without prior notice.</h5>
					</p>
					<ul>
						<li class="pull-right" style="float:right; list-style-type:none"><a  href="/pages/starter-packages">See all packages<i class="icon-blue-arrow"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="package2" class="package_slider" style="min-width:1200px;height:360px;background:#82c843">
			<div style="width: 960px; margin-left:auto; margin-right:auto;">
				<div class="banner-logo span7"><img src="/assets/media/packages/starter-premium.png"></img></div>
				<div class="banner-message span5">
					<h3>Premium Starter Pack</h3>
					<p>
						<h4>Each Standard Package includes:</h4>
						<ol type="A">
							<li>RF ID</li>
							<li>Metrobank Paycard</li>
							<li>Sales Kit with Catalogue</li>
							<li>
								<p>*Promotional Discount Vouchers</p>
								<ul>
									<li>SET A (4 Vouchers)</li>
									<li>SET B (4 Vouchers)</li>
									<li>SET C (4 Vouchers)</li>
								</ul>
							</li>
							<li>*1 piece of Vital Denta Max FREE!</li>
						</ol>
						<br />
						<h5>Any promotional offer & free product/s may change without prior notice.</h5>
					</p>
					<ul>
						<li style="float:right; list-style-type:none"><a  href="/pages/starter-packages">See all packages<i class="icon-blue-arrow"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="package3" class="package_slider" style="min-width:1200px;height:360px;background:#ffc811">
			<div style="width: 960px; margin-left:auto; margin-right:auto;">
				<div class="banner-logo span7"><img src="/assets/media/packages/starter-ultimate.png"></img></div>
				<div class="banner-message span5 full">
					<h3>Ultimate Starter Pack</h3>
					<p>
						<h4>Each Standard Package includes:</h4>
						<ol type="A">
							<li>RF ID</li>
							<li>Metrobank Paycard</li>
							<li>Sales Kit with Catalogue</li>
							<li>
								<p>*Promotional Discount Vouchers</p>
								<ul class="pull-left">
									<li>SET A (4 Vouchers)</li>
									<li>SET B (4 Vouchers)</li>
									<li>SET C (4 Vouchers)</li>
									<li>SET D (4 Vouchers)</li>
									<li>SET E (4 Vouchers)</li>
									<li>SET F (4 Vouchers)</li>
									<li>SET G (4 Vouchers)</li>
									<li>SET H (4 Vouchers)</li>
									<li>SET I (4 Vouchers)</li>
								</ul>
							</li>
							<li>
								<p>*FREE Products!</p>
								<ul class="pull-left clearfix">
									<li>1 box of Vital C</li>
									<li>1 box of Vital Ipoh White Coffee</li>
									<li>1 box of Vital Denta Max</li>
									<li style="width:100%;">2 pieces of Vital C Soaps 90g (any variant)</li>
								</ul>
							</li>
								
						</ol>
						<br />
						<h5>Any promotional offer & free product/s may change without prior notice.</h5>
					</p>
					<ul>
						<li class="pull-right" style="float:right; list-style-type:none; margin-top:-20px; width:34%;"><a  href="/pages/starter-packages">See all packages<i class="icon-blue-arrow"></i></a></li>
					</ul>
				</div>
			</div>
		</div>

	</div>
<style type="text/css">
	.banner-message ol > li:last-child{
		float: left;
		width: 100%;

	}
	.banner-message ol > li ul{
		
		
	}
</style>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		/*$("#featured-packages").slides({
			generatePagination: false,
			play: 3000,
			pause: 1000,
			hoverPause: true
		});*/
		
		var step = $("#featured-packages-flow").width();
		//alert(step);
		
		$(".package_slider").css('width', step);
		
		$("#featured-packages-flow").FlowSlider({
			detectTouchDevice: false,
			infinite: true,
			marginStart: 0,
			marginEnd: 0,
			startPosition: 0,
			position: 0,
			controllers: ["Timer"],
			controllerOptions: [
				{
					el: $(document),
					eventStart: "ready",
					step: step,
					time: 5000,
					rewind: true
				}
			]
		});
	
		//to center image vertically
		$('.results-image').each(function () {
			$(this).load(function () {
				var height = $(this).height();
				if(height!=0){
				   margin_top = (200 - height)/2;
				   $(this).css('margin-top', margin_top);
			    }
			});
			var height = $(this).height();
			if(height!=0){
			   margin_top = (200 - height)/2;
			   $(this).css('margin-top', margin_top);
			}
		});

	});
	
</script>
