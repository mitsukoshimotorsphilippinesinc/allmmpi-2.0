<?php 
	echo js('libs/jquery.highlight.js');
	echo css('genealogy.css'); 
	echo js('apps/genealogy.js'); 
?>
<style>

.test-container {
	position: relative;
	width: 710px;
	margin: 0 auto;
	border: solid 1px #ccc;
	min-height: 400px;
}

</style>

<div id='test-container' class='test-container'>
</div>

<script type="text/javascript">
	$(function() {
		genealogy.render({
			'target_id' : 'test-container', 
			'url' : '/workbench/genealogy/get_network', 
			'search_url' : '/workbench/genealogy/search', 
			'downline_url' : '/workbench/genealogy/downline', 
			'account_id' : '9900004096'});
	});
</script>