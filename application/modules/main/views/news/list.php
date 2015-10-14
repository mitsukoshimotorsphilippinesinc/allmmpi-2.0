<?php if(empty($news) || is_null($news)): ?>
	<h4>No News or Events</h4>
<?php
	else:
	$news_url = $this->config->item('base_url') . "/main/news/";

	foreach($news as $n):
		//load users model
		$this->load->model('users_model');

		// get author
		$user = $this->users_model->get_user_by_id($n->user_id);
		$author = $user->username;
		// set pretty date
		$pretty_date = date('F j, Y H:i',strtotime($n->update_timestamp));
		// tags		
		$tags = explode(',',$n->tags);
		
		$tags_links = "";
		foreach($tags as $t)
		{	
			$t_l = str_replace(' ','-',trim($t));
			$t_link_url = $news_url . "tags/" . $t_l;
			$t_link = "<a href='{$t_link_url}'>{$t}</a> ";
			$tags_links .= $t_link;
		}
		
		// link url
		$link =  $news_url . "view/" . $n->featured_id;
?>
<div class="news-box">
	<h3 class="news-title"><a href="<?=$link?>"><?=$n->title?></a></h3>
	<div class="news-meta">
		<span class="author">Author: <?=$author?></span>
		<span class="pubdate">Posted On: <?=date("M j, Y g:i a",strtotime($n->insert_timestamp)); ?></span>
	</div>
	<div class="news-meta">
		<span class="pubdate"><strong>Starts</strong>: <?=date("M j, Y g:i a",strtotime($n->start_date)); ?></span>
		<?php if($n->end_date != "0000-00-00 00:00:00"): ?>
			<span class="pubdate"><strong>Ends</strong>: <?=date("M j, Y g:i a",strtotime($n->end_date)); ?></span>
		<?php endif; ?>
	</div>
	<div class="news-copy">
		<p><?=$n->excerpt?></p>
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
<?php endif; ?>