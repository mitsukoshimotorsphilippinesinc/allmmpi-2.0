<div class="social-network">
	<h2>MMPI on Twitter</h2>
    <div id="tweets"></div><div><br/>
	<p>Join the Conversation</p>
	<ul>
		<li><a href="#"><img src="<?=$this->config->item('img_url')?>/twitter_logo_bg.png" alt="twitter" id="twitter-logo"></a></li>
		<li><a href="#"><img src="<?=$this->config->item('img_url')?>/fb_logo_bg.png" alt="facebook"></a></li>		
	</ul>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("#tweets").tweet({
		            username: "username",
		            count: 3,
		            loading_text: "loading tweets..."
		        });		
	});
</script>