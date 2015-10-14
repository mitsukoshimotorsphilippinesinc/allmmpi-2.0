<input checked type=radio name=slider id=slide1 />
<input type=radio name=slider id=slide2 />
<input type=radio name=slider id=slide3 />
<input type=radio name=slider id=slide4 />
<input type=radio name=slider id=slide5 />

<!-- The Slider -->
<div id="slides">
	<div id="overflow">			
		<div class="inner">
			<article><img src="<?=$this->config->item('img_url')?>/rotating-banner1.jpg" /></article>
			<article><img src="<?=$this->config->item('img_url')?>/rotating-banner1.jpg" /></article>
            <article><img src="<?=$this->config->item('img_url')?>/rotating-banner1.jpg" /></article>
            <article><img src="<?=$this->config->item('img_url')?>/rotating-banner1.jpg" /></article>
            <article><img src="<?=$this->config->item('img_url')?>/rotating-banner1.jpg" /></article>
            <article><img src="<?=$this->config->item('img_url')?>/rotating-banner1.jpg" /></article>
            <article><img src="<?=$this->config->item('img_url')?>/rotating-banner1.jpg" /></article>
		</div> <!-- .inner -->
	</div> <!-- #overflow -->
</div>

<!-- Controls and Active Slide Display -->
<div id="active">
	<label for=slide1>1</label>
	<label for=slide2>2</label>
	<label for=slide3>3</label>
	<label for=slide4>4</label>
	<label for=slide5>5</label>
	<label for=slide6>6</label>
	<label for=slide7>7</label>
</div>
<!-- end of rotating images -->
