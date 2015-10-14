<div class="widget featured-testimonial">
	<h3><?=$title?></h3>
    <div class="widget-content">
    	<div class="customer-details clearfix">
        	<img src="<?=$this->config->item('media_url')?>/testimonials/<?=$testimonial->image_filename?>" class="avatar">
            <p><span class="name"><?=$testimonial->member_name?></span>
            <?=$testimonial->member_details?></p>
        </div>
        <p class="quote">"<?=$testimonial->body?>"</p>
    </div>
</div>