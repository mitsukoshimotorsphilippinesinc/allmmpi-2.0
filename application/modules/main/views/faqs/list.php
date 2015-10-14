<h2><?=$page_title?></h2>
<?php foreach($faqs as $f): ?>

<div class="news-box">
	<h4 class="news-title"><?=$f->question?></h4>
	<div class="news-copy">
		<p><?=$f->body?></p>
	</div>
</div>

<?php endforeach; ?>
<br/>
<?php
	if(!is_null($this->pager)) echo $this->pager->create_links();
?>