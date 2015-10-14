<h1><?=$page_title?></h1>

<?php	
	$events_url = $this->config->item('base_url') . "/main/event/";

	foreach($events as $e):
		//load users model
		$this->load->model('users_model');

		// get author
		$user = $this->users_model->get_user_by_id($e->user_id);
		$author = $user->username;
		// set pretty date
		$pretty_date = date('F j, Y H:i',strtotime($e->insert_timestamp));
		$pretty_start_day = date('j',strtotime($e->start_date));
		$pretty_start_month = date('M',strtotime($e->start_date));
		// tags		
		$tags = explode(',',$e->tags);
		
		$tags_links = "";
		foreach($tags as $t)
		{	
			$t_l = str_replace(' ','-',trim($t));
			$t_link_url = $events_url . "tags/" . $t_l;
			$t_link = "<a href='{$t_link_url}'>{$t}</a> ";
			$tags_links .= $t_link;
		}
		
		// link url
		$link =  $events_url . "view/" . $e->news_id;
?>
<div class="news-box">
	<div class="event-date"><span class="month"><?=$pretty_start_month?></span><span class="day"><?=$pretty_start_day?></span></div>
	<div class="event-details">
		<h4 class="event-title"><a href="<?=$link?>"><?=$e->title?></a></h4>
		<p class="event-venue"></p>
		<p class="event-description"><?=$e->body?></p>
	</div>
	
	<span class="category">Tags: <?=$tags_links?></span>
	<div class="read-more">
		<a href='<?=$link?>'>Read More</a>
	</div>
</div>
<?php 
	endforeach;
?>
<br/>
<?php
	if(!is_null($this->pager)) echo $this->pager->create_links();
?>