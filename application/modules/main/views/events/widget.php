<div class="widget events">
	<h3>Events</h3>
    <div class="widget-content">
		<?php 
			foreach ($events as $e)
			{
				$pretty_day = date('j',strtotime($e->start_date));
				$pretty_month = date('M',strtotime($e->start_date));
		?>
		<ol>
    		<li>
        		<div class="event-date"><span class="month"><?=$pretty_month?></span><span class="day"><?=$pretty_day?></span></div>
                <div class="event-details">
					<h4 class="event-title"><a href="#"><?=$e->title?></a></h4>
					<p class="event-venue"></p>
					<p class="event-description"><?=$e->body?></p>
				</div>
			</li>
		</ol>
		<?php 
			} // END FOREACH 
		?>
		<p class="all-events-link"><a href="/main/event"><i class="icon-more"></i>See all events</a></p>
    </div>
</div>