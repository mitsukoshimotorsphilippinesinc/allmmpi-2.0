<?php
echo css('slider/base/advanced-slider-base.css');
echo css('slider/text-thumbnail-pointer/text-thumbnail-pointer.css');
echo css('slider/custom.css');
echo js('libs/slider/jquery.touchSwipe.min.js');
echo js('libs/slider/jquery.advancedSlider.min.js');
?>


<div id="news-slider">
	<div class="advanced-slider" id="text-thumbnails-slider">
        <ul class="slides">

        <?php
            $limit = array("rows" => 3,"offset" => 0);
            $news = $this->contents_model->get_featured("(type = 'news' OR type = 'event') AND is_published = 1",$limit,"insert_timestamp DESC");

            foreach ($news as $n) :

                $title = $n->title;
                $pretty_date = date("F j, Y",strtotime($n->insert_timestamp));
                $news_url = $n->featured_id;
        ?>

        <li class="slide">
                <div class="layer static" data-horizontal="480" data-vertical="20" data-width="450">
                    <div class="content-box">
                        <p class="title"><a href="/main/news/view/<?=$news_url?>"><?= $title ?></a></p>
                        <p><?= $n->excerpt ?></p>
                        <a class="btn btn-info" href="/main/news">See more News Updates and Events</a>
                    </div>
                </div>
                <div class="thumbnail">
                    <span class="pointer"></span>
                    <div class="thumbnail-text">
                        <h3 class="thumbnail-title"><?= $title ?></h3>
                        <p class="thumbnail-description">

                        </p>
                    </div>
                </div>
            </li>

        <?php
            endforeach;
        ?>


        <?php
        $packages = $this->contents_model->get_featured(array('type' => 'package','is_published' => 1));

        foreach($packages as $p) {
			$title = $p->title;
			$image_filename = check_image_path($this->config->item("media_url") . "/package_types/". $p->image_filename);
			error_log($this->config->item("media_url") . "/package_types/". $p->image_filename);
			$bg_color = $p->bg_color;
			echo "<li class='slide' style='background-color:{$bg_color}'>
						<div class='layer static feature-img product' data-horizontal='0' data-vertical='0' data-width='470'>
							<img class='image-box' src='{$image_filename}'  />
						</div>".$p->body."
						<div class='thumbnail'>
							<span class='pointer'></span>
							<div class='thumbnail-text'>
								<h3 class='thumbnail-title'>{$title}</h3>
								<p class='thumbnail-description'>
								</p>
							</div>
						</div>
					</li>";
		}

		?>

		</ul>
	</div>
</div>

<script type="text/javascript">

    jQuery(document).ready(function($){
        $('#text-thumbnails-slider').advancedSlider({
			width: 1113,
			height: 345,
			skin: 'text-thumbnail-pointer',
			shadow: false,
			effectType: 'swipe',
			overrideTransition: true,
			slideButtons: false,
			thumbnailType: 'scroller',
			thumbnailWidth: 158,
			thumbnailHeight: 120,
			thumbnailArrows: false,
			maximumVisibleThumbnails: 7,
			keyboardNavigation: true,
			slideArrows: false,
			timerToggle: true,
			slideshowDelay: 1000 * 10,
			pauseSlideshowOnHover:true, 
        });
    });

</script>