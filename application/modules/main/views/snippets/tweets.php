<div class="social-network">
	<h2>Vital C on Twitter</h2>
    <div id="tweets"></div><div><br/>
	<p>Join the Conversation</p>
	<ul>
		<li><a href="http://twitter.com/vitalc2010"><img src="<?=$this->config->item('img_url')?>/twitter_logo_bg.png" alt="twitter" id="twitter-logo"></a></li>
		<li><a href="http://www.facebook.com/people/Vital-C-Vital/100000988110594"><img src="<?=$this->config->item('img_url')?>/fb_logo_bg.png" alt="facebook"></a></li>
		<li><a href="<?= site_url('main/rss') ?>"><img src="<?=$this->config->item('img_url')?>/rss_logo_bg.png" alt="rss"></a></li>
	</ul>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("#tweets").tweet({
		            username: "vitalc2010",
		            count: 3,
		            loading_text: "loading tweets..."
		        });		
	});
</script>