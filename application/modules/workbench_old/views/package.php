<?php
echo css('slider/base/advanced-slider-base.css');
echo css('slider/text-thumbnail-pointer/text-thumbnail-pointer.css');
echo css('slider/custom.css');
echo js('libs/slider/jquery.touchSwipe.min.js');
echo js('libs/slider/jquery.advancedSlider.min.js');
?>


<div class="container">
    <div class="row">

        <div class="advanced-slider" id="text-thumbnails-slider">
            <ul class="slides">
                <li class="slide">
                    <?php
                        $limit = array("rows"=>1,"offset"=>0);
                        $news = $this->contents_model->get_news("is_published = 1",$limit,"insert_timestamp DESC");
                        foreach ($news as $n)
                        {
                            $title1 = $n->title;
                            $pretty_date = date("F j, Y",strtotime($n->insert_timestamp));
                            $news_url = $n->news_id;
                        }
                    ?>
                    <!-- if image will use the full slide area
                    <img class="image" src="" />
                    -->

                    <div class="layer static" data-horizontal="480" data-vertical="20" data-width="450">
                        <div class="content-box">
                            <p class="title"><a href="/main/news/view/<?=$news_url?>"><?= $title1 ?></a></p>
                            <p><?= $n->short_body ?></p>
                            <a class="btn btn-info" href="<?=$this->config->item('base_url')?>/main/news">See more News Updates and Events</a>
                        </div>
                    </div>
                    <div class="thumbnail">
                        <span class="pointer"></span>
                        <div class="thumbnail-text">
                            <h3 class="thumbnail-title"><?= $title1 ?></h3>
                            <p class="thumbnail-description">
                                <!--
                                <a href="<?=$this->config->item('base_url')?>/main/news">See more News Updates and Events<i class="icon-blue-arrow">icon-blue-arrow</i></a>
                                -->
                            </p>
                        </div>
                    </div>
                </li>


                <li class="slide">
                    <?php
                    $limit = array("rows"=>1,"offset"=>1);
                    $news = $this->contents_model->get_news("is_published = 1",$limit,"insert_timestamp DESC");
                    foreach ($news as $n)
                    {
                        $title2 = $n->title;
                        $pretty_date = date("F j, Y",strtotime($n->insert_timestamp));
                        $news_url = $n->news_id;
                    }
                    ?>
                    <!-- if image will use the full slide area
                    <img class="image" src="" />
                    -->
                    <div class="layer static" data-horizontal="480" data-vertical="20" data-width="450">
                        <div class="content-box">
                            <p class="title"><a href="/main/news/view/<?=$news_url?>"><?= $title2 ?></a></p>
                            <p><?= $n->short_body ?></p>
                            <a class="btn btn-info" href="<?=$this->config->item('base_url')?>/main/news">See more News Updates and Events</a>
                        </div>
                    </div>
                    <div class="thumbnail">
                        <span class="pointer"></span>
                        <div class="thumbnail-text">
                            <h3 class="thumbnail-title"><?= $title2 ?></h3>
                            <p class="thumbnail-description">
                                <!--
                                <a href="<?=$this->config->item('base_url')?>/main/news">See more News Updates and Events<i class="icon-blue-arrow">icon-blue-arrow</i></a>
                                -->
                            </p>
                        </div>
                    </div>
                </li>


                <li class="slide">
                    <?php
                    $limit = array("rows"=>1,"offset"=>2);
                    $news = $this->contents_model->get_news("is_published = 1",$limit,"insert_timestamp DESC");
                    foreach ($news as $n)
                    {
                        $title3 = $n->title;
                        $pretty_date = date("F j, Y",strtotime($n->insert_timestamp));
                        $news_url = $n->news_id;
                    }
                    ?>
                    <!-- if image will use the full slide area
                    <img class="image" src="/assets/js/libs/slider/examples/presentation-assets/images/slides/image7.jpg" />
                    -->
                    <div class="layer static" data-horizontal="480" data-vertical="20" data-width="450">
                        <div class="content-box">
                            <p class="title"><a href="/main/news/view/<?=$news_url?>"><?= $title3 ?></a></p>
                            <p><?= $n->short_body ?></p>
                            <a class="btn btn-info" href="<?=$this->config->item('base_url')?>/main/news">See more News Updates and Events</a>
                        </div>
                    </div>
                    <div class="thumbnail">
                        <span class="pointer"></span>
                        <div class="thumbnail-text">
                            <h3 class="thumbnail-title"><?= $title3 ?></h3>
                            <p class="thumbnail-description">
                                <!--
                                <a href="<?=$this->config->item('base_url')?>/main/news">See more News Updates and Events<i class="icon-blue-arrow">icon-blue-arrow</i></a>
                                -->
                            </p>
                        </div>
                    </div>
                </li>


                <li class="slide" style="background-color:#00bcf9;">
                    <!-- product slides will have larger text area to compensate for the smaller images -->

                    <img class="image" src="" />

                    <div class="layer static feature-img product" data-horizontal="0" data-vertical="0" data-width="470">
                        <img class="image-box" src="/assets/media/packages/starter-standard.png" alt="Standard Packages"/>
                    </div>

                    <div class="layer static" data-horizontal="480" data-vertical="0" data-width="450">
                        <div class="content-box">
                            <h4>Each Standard Package includes:</h4>
                            <ol type="A">
                                <li>RF ID</li>
                                <li>Metrobank Paycard</li>
                                <li>Sales Kit with Catalogue</li>
                                <li>
                                    <p>*Promotional Discount Vouchers</p>
                                    <ul>
                                        <li>SET A (4 Vouchers)</li>
                                    </ul>
                                </li>
                                <li>*1 Pure Touch Hand Sanitizer 30 ml</li>
                            </ol>
                            <h6>*Any promotional offer & free product/s may change without prior notice.</h6>
                            <ul class="pull-right">
                                <li class="pull-right">
                                    <a  href="/pages/starter-packages">See all packages<i class="icon-blue-arrow"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="thumbnail">
                        <span class="pointer"></span>
                        <div class="thumbnail-text">
                            <h3 class="thumbnail-title">Standard Starter Packages</h3>
                            <p class="thumbnail-description">

                            </p>
                        </div>
                    </div>

                </li>


                <li class="slide" style="background-color:#82c843;">

                    <img class="image" src="" />

                   <!--
                    <div class="layer static feature-img product" data-horizontal="0" data-vertical="15" data-width="320">
                        <img class="image-box" src="/assets/media/products/product_89_13517814415.jpg" alt=""/>
                    </div>
                    -->

                    <div class="layer static feature-img product" data-horizontal="0" data-vertical="0" data-width="470">
                        <img class="image-box" src="/assets/media/packages/starter-premium.png" alt="Premium Packages"/>
                    </div>

                    <div class="layer static" data-horizontal="480" data-vertical="0" data-width="450">
                        <div class="content-box">
                            <p class="title">Each Standard Package includes:</p>
                            <ol type="A" class="clearfix">
                                <li>RF ID</li>
                                <li>Metrobank Paycard</li>
                                <li>Sales Kit with Catalogue</li>
                                <li>
                                    <p>*Promotional Discount Vouchers</p>
                                    <ul class="clearfix">
                                        <li>SET A (4 Vouchers)</li>
                                        <li>SET B (4 Vouchers)</li>
                                        <li>SET C (4 Vouchers)</li>
                                    </ul>
                                </li>
                                <li class="pull-left">*1 piece of Vital Denta Max FREE!</li>
                            </ol>
                            <h6>Any promotional offer & free product/s may change without prior notice.</h6>
                            </p>
                            <ul class="pull-right">
                                <li class="pull-right">
                                    <a  href="/pages/starter-packages">See all packages<i class="icon-blue-arrow"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="thumbnail">
                        <span class="pointer"></span>
                        <div class="thumbnail-text" style="background-color: beige">
                            <h3 class="thumbnail-title">Premium Starter Packages</h3>

                        </div>
                    </div>
                </li>

                <li class="slide" style="background-color:#ffc811;">

                    <img class="image" src="" />

                    <div class="layer static feature-img product" data-horizontal="0" data-vertical="0" data-width="470">
                        <img class="image-box" src="/assets/media/packages/starter-ultimate.png" alt="Ultimate Packages"/>
                    </div>

                    <div class="layer static" data-horizontal="480" data-vertical="0" data-width="450">
                        <div class="content-box">
                            <p class="title">Each Standard Package includes:</p>
                            <ol type="A" class="clearfix pull-left">
                                <li>RF ID</li>
                                <li>Metrobank Paycard</li>
                                <li>Sales Kit with Catalogue</li>
                                <li>
                                    <p>*Promotional Discount Vouchers</p>
                                    <ul class="clearfix">
                                        <li>SET A (4 Vouchers)</li>
                                        <li>SET B (4 Vouchers)</li>
                                        <li>SET C (4 Vouchers)</li>
                                        <li>SET D (4 Vouchers)</li>
                                        <li>SET E (4 Vouchers)</li>
                                        <li>SET F (4 Vouchers)</li>
                                        <li>SET G (4 Vouchers)</li>
                                        <li>SET H (4 Vouchers)</li>
                                        <li>SET I (4 Vouchers)</li>
                                    </ul>
                                </li>
                                <li class="pull-left">
                                    <p>*FREE Products!</p>
                                    <ul class="pull-left package-bottom">
                                        <li>1 box of Vital C</li>
                                        <li>1 box of Vital Ipoh White Coffee</li>
                                        <li>1 box of Vital Denta Max</li>
                                        <li>2 pieces of Vital C Soaps 90g (any variant)</li>
                                    </ul>
                                </li>

                            </ol>
                            <h6>Any promotional offer & free product/s may change without prior notice.</h6>
                            </p>
                            <ul class="pull-right">
                                <li class="pull-right">
                                    <a  href="/pages/starter-packages">See all packages<i class="icon-blue-arrow"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="thumbnail">
                        <span class="pointer"></span>
                        <div class="thumbnail-text" style="background-color: yellow">
                            <h3 class="thumbnail-title">Ultimate Starter Packages</h3>
                            <p class="thumbnail-description">
                                <!--Personal Care Line-->
                            </p>
                        </div>
                    </div>
                </li>

            </ul>
        </div>


    </div>
</div>

<script type="text/javascript">

    jQuery(document).ready(function($){
        $('#text-thumbnails-slider').advancedSlider({width: 956,
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
            maximumVisibleThumbnails: 6,
            keyboardNavigation: true,
            slideArrows: false,
            timerToggle: true
        });
    });

</script>