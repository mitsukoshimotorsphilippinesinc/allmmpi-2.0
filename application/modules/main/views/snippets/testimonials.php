<?php
$this->load->model('contents_model');
$where = array(
    'is_approved' => 1
);
$testimonials = $this->contents_model->get_testimonials($where);
$testimonials_count = count($testimonials);
$img = "";
if(!empty($testimonials))
{
    $random = rand(0, $testimonials_count-1);
    $random_testimonial = $testimonials[$random];
    $img = "/assets/media/testimonials/{$random_testimonial->image_filename}";
}

$img = check_image_path($img);
?>

<div class='testimonials row-fluid'>
    <article class="testimonials span8">
        <h2 class="">Testimonials</h2>
        <!--<a href="" class="span1"><img src="<?=$this->config->item('img_url')?>/avatar_bg.png" alt="avatar"></a>-->
        <ul class="span12">
            <li>
                <i class="quotation"></i>
				<span>
					<img class="pull-left" style="padding-right:16px; max-width: 100px; max-height: 100px;" src="<?= $img; ?>">
                    <?= $random_testimonial->member_name; ?> , <?= $random_testimonial->member_details; ?>
				</span>
                <p><?= $random_testimonial->body; ?></p>
            </li>
        </ul>
        <div class="row-fluid">
            <p class="span12"><a class="pull-right" href="/main/testimonials">See more testimonials<i class="icon-blue-arrow"></i></a></p>
        </div>

    </article>
    <article>
        <?php
        // twitter feeds
        echo $this->load->view('snippets/tweets', NULL, TRUE,'main');
        ?>
    </article>
</div>