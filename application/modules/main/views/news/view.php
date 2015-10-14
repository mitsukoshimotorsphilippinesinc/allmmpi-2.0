<?php if(empty($news) || is_null($news)):?>
	<h4>Entry does not exist</h4>
<?php
	else:
	$news_url = $this->config->item('base_url') . "/main/news/";

	//load users model
	$this->load->model('users_model');

	// get author
	$user = $this->users_model->get_user_by_id($news->user_id);
	$author = $user->username;
	// set pretty date
	$pretty_date = date('F j, Y H:i',strtotime($news->update_timestamp));
	// tags		
	$tags = explode(',',$news->tags);
	
	$tags_links = "";
	foreach($tags as $t)
	{	
		$t_l = str_replace(' ','-',trim($t));
		$t_link_url = $news_url . "tags/" . $t_l;
		$t_link = "<a href='{$t_link_url}'>{$t}</a> ";
		$tags_links .= $t_link;
	}
	
	// link url
	$link =  $news_url . "view/" . $news->featured_id;
?>
<div class="news-box">
	<h3 class="news-title"><a href="<?=$link?>"><?=$news->title?></a></h3>
	<div class="news-meta">
		<span class="author">Author: <?=$author?></span>
		<span class="pubdate">Posted On: <?=date("M j, Y g:i a",strtotime($news->insert_timestamp)); ?></span>
	</div>
	<div class="news-copy">
		<p><?=$news->body?></p>
	</div>
	<span class="category">Tags: <?=$tags_links?></span>
</div>
<?php endif; ?>
<div>
	<a class="btn btn-success" href="/main/news"><i class="icon-arrow-left"></i> <span>Back</span></a>
</div>
