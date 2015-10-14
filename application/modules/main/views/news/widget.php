<div class="widget news">
	<h3><?=$title?></h3>
    <div class="widget-content">
	<ul>		

	<?php	
		foreach($news as $n):
			$link =  $this->config->item('base_url') . "/main/news/view/" . $n->news_id;
	?>
	<li>
    	<h4 class="news-title"><a href="<?=$link?>"><?=$n->title?></a></h4>
        <p><?=$n->short_body?></p>
         <p class="read-more"><a href="<?=$link?>"><i class="icon-more"></i>read more</a></p>
    </li>
	<?php 
		endforeach;
	?>
    </ul>
    </div>
</div>