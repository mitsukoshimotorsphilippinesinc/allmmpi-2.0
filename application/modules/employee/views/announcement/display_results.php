<?php
foreach($announcements as $a) {
					
	$proper_date = date("jS F Y - h:i:s a", strtotime($a->insert_timestamp));
	
	echo "<h2 style='float:left;'>{$a->title}</h2><div style='clear:both;'></div><span style='float:left;margin-top:-15px;'><i>{$proper_date}</i></span><div style='clear:both;'></div><br/>";
	echo $a->body;	
	
	$where = "announcement_id = " . $a->announcement_id;
	$announcement_message_details = $this->asset_model->get_announcement_message($where, NULL, "announcement_message_id");

	$this->load->view("display_comments", $data, TRUE);

	echo "<textarea style='width:67.5em;' class='new-comment-{$a->announcement_id}'></textarea>
			<button class='button-post btn btn-primary pull-right' style='margin-bottom:10px;' data='{$a->announcement_id}' title='Post'>Post</button>
			<div class='announcement-comments-{$a->announcement_id}'></div>
			<div style='width: 100%; height: 2px; background: #F87431; overflow: hidden;''></div>";		
}
?>
