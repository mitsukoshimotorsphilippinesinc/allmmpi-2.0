<div class="featured-content">
    <div class="slides_container">
		<?php 
			$featured_promos = $this->contents_model->get_active_featured_promos();
			foreach($featured_promos as $fp) {

				$base_url = $this->config->item('base_url');
				$img_url = "";
				
				if(!is_null($fp->image_filename) && !empty($fp->image_filename))
				{
					$img_url = $this->config->item('media_url')."/featured/{$fp->image_filename}";
				}
				
				
				if ($fp->url!=NULL && ($fp->url!="#" && $fp->url!="")) $link_url = $base_url ."/main/featured/promos/". $fp->url;
				else $link_url = "#";
				 

				if ($fp->promo_text!="" || $fp->promo_text!=NULL) $caption = "<div class='caption'><p>{$fp->promo_text}</p></div>";
				else $caption = "";

		        echo "<div class='slide'><a href='{$link_url}'><img src='{$img_url}' alt='{$fp->promo_title}'></a>{$caption}</div>";				
			}
		?>
    </div>
</div>