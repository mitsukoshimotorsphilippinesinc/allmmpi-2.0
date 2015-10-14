<?php	
	$event_url = $this->config->item('base_url') . "/main/event/";

	//load users model
	$this->load->model('users_model');

	// get author
	$user = $this->users_model->get_user_by_id($event->user_id);
	$author = $user->username;
	// set pretty date
	$pretty_date = date('F j, Y H:i',strtotime($event->update_timestamp));
	// tags		
	$tags = explode(',',$event->tags);
	
	$pretty_start_date = "";
	$pretty_end_date = "";
	
	if(!empty($event->start_date))
	{
		$pretty_start_date = date('F j, Y H:i',strtotime($event->start_date));
		$pretty_start_day = date('j',strtotime($event->start_date));
		$pretty_start_month = date('M',strtotime($event->start_date));
	}
	
	if(!empty($event->end_date))
	{
		$pretty_end_date = date('F j, Y H:i',strtotime($event->end_date));
	}
	
	$tags_links = "";
	foreach($tags as $t)
	{	
		$t_l = str_replace(' ','-',trim($t));
		$t_link_url = $event_url . "tags/" . $t_l;
		$t_link = "<a href='{$t_link_url}'>{$t}</a> ";
		$tags_links .= $t_link;
	}
	
	// link url
	$link =  $event_url . "view/" . $event->news_id;
?>
<div class="news-box">
	<div class="event-date"><span class="month"><?=$pretty_start_month?></span><span class="day"><?=$pretty_start_day?></span></div>
	<div class="event-details">
		<h4 class="event-title"><?=$event->title?></h4>
		<p class="event-venue"></p>
		<p class="event-description"><?=$event->body?></p>
	</div>
	
	<span class="category">Tags: <?=$tags_links?></span>
</div>