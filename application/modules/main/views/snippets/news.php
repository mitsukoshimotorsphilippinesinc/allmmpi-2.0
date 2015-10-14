<div class="news row-fluid">
	<h2>News & Updates</h2>
	<ul class="news-updates span12">
	<?php
		$limit = array("rows"=>2,"offset"=>0);
		$news = $this->contents_model->get_featured("(type = 'news' OR type = 'event') AND is_published = 1",$limit,"insert_timestamp DESC");

		foreach ($news as $n)
		{
			$pretty_date = date("F j, Y",strtotime($n->insert_timestamp));
			echo "<li class='span6'><h4>{$n->title}</h4><p>{$n->excerpt}</p><span>{$pretty_date}</span></li>";
		}
	?>
	</ul>
	<ul class="news-updates span12">
	<?php
		$limit = array("rows"=>2,"offset"=>2);
		$news = $this->contents_model->get_featured("(type = 'news' OR type = 'event') AND is_published = 1",$limit,"insert_timestamp DESC");

		foreach ($news as $n)
		{
			$pretty_date = date("F j, Y",strtotime($n->insert_timestamp));
			echo "<li class='span6'><h4>{$n->title}</h4><p>{$n->excerpt}</p><span>{$pretty_date}</span></li>";
		}
	?>
	</ul>
	<a href="<?=$this->config->item('base_url')?>/main/news">See more News Updates and Events<i class="icon-blue-arrow">icon-blue-arrow</i></a>
</div>