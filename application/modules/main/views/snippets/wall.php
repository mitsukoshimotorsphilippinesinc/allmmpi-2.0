<style>
    #slider-2,#slider-3,#slider-4,#slider-5{
        visibility: hidden;
    }
</style>
<?php
	$this->load->model('contents_model');
	$this->load->model('members_model');
?>
<h2 class="">Wall of Fame</h2>
<ul class="wall-of-fame row-fluid">
	<li>
		<span>Grand Masters</span>
		<div id='slider-2' class="slider-horizontal" style="height:300px">
			<?php
				$achievement_id = 1; //grand masters
				$grand_masters = $this->contents_model->get_featured_members_by_achievement_id($achievement_id);
				if(!empty($grand_masters))
				{
					foreach($grand_masters as $g)
					{
						//$member = $this->members_model->get_member_by_id($g->member_id);
						if(!is_null($g->image_filename))
						{
							$image = '/assets/media/members/' . $g->image_filename;
							$image = check_image_path($image);
						}else
						{
							$image = check_image_path($g->image_filename);
						}
			?>
				<div class='member-image'>
					<div class='member-image' style="width:200px;height:200px">
						<img src="<?= $image ?>" class="grand-masters-image" style="max-width:200px;max-height:200px"></img>
					</div>
					<div style="width:200px;height:100px">
						<span><?= $g->member_name; ?></span><br />
						<span><?= $g->group_name; ?></span>
					</div>	
				</div>
			<?php
					}
					
				}
			?>
		</div>
	</li>
	<li>
		<span>Presidents Club</span>
		<div id='slider-3' class="slider-horizontal" style="height:300px">
			<?php
				$member = null;
				$achievement_id = 2; //presidents club
				$presidents_club = $this->contents_model->get_featured_members_by_achievement_id($achievement_id);
				if(!empty($presidents_club))
				{
					foreach($presidents_club as $p)
					{
						//$member = $this->members_model->get_member_by_id($p->member_id);
						if(!is_null($p->image_filename))
						{
							$image = '/assets/media/members/' . $p->image_filename;
							$image = check_image_path($image);
						}else
						{
							$image = check_image_path($p->image_filename);
						}
			?>
				<div class='member-image'>
					<div class='member-image' style="width:200px;height:200px">
						<img src="<?= $image ?>" class="presidents-club-image" style="max-width:200px;max-height:200px"></img>
					</div>
					<div style="width:200px;height:100px">
							<span><?= $p->member_name; ?></span><br />
							<span><?= $p->group_name; ?></span>
					</div>
				</div>
			<?php
					}
				}
			?>
		</div>
	</li>
	<li>
		<span>Elite Club</span>
		<div id='slider-4' class="slider-horizontal" style="height:300px">
			<?php
				$member = null;
				$achievement_id = 3; //millionaires club club
				$millionaires_club = $this->contents_model->get_featured_members_by_achievement_id($achievement_id);
				if(!empty($millionaires_club))
				{
					foreach($millionaires_club as $m)
					{
						//$member = $this->members_model->get_member_by_id($m->member_id);
						if(!is_null($m->image_filename))
						{
							$image = '/assets/media/members/' . $m->image_filename;
							$image = check_image_path($image);
						}else
						{
							$image = check_image_path($m->image_filename);
						}
			?>			
				<div class='member-image'>
					<div class='member-image' style="width:200px;height:200px">
						<img src="<?= $image ?>" class="millionaires-club-image" style="max-width:200px;max-height:200px"></img>
					</div>
					<div style="width:200px;height:100px">
						<span><?= $m->member_name; ?></span><br />
						<span><?= $m->group_name; ?></span>
					</div>
				</div>
			<?php			
					}
				}
			?>
		</div>
	</li>
	<li>
		<span>Results</span>
		<div id='slider-5' class="slider-horizontal" style="height:300px">
			<?php
				$results = $this->contents_model->get_results(array('is_published' => 1), "", "insert_timestamp DESC");
				if(!empty($results))
				{
					foreach($results as $r)
					{
						$image = '/assets/media/results/' . $r->image_filename;
						$image = check_image_path($image);
			?>			
				<div class='result-image'>
					<div class='result-image' style="width:200px;height:200px">
						<img src="<?= $image ?>" class="results-image" style="max-width:200px;max-height:200px"></img>
					</div>
					<div style="width:200px;height:100px">
						<span><?= $r->member_name; ?></span><br/>
						<span><?= $r->result; ?></span>
					</div>
				</div>	
			<?php			
					}
				}
			?>
		</div>
	</li>
</ul>
<div class="row-fluid pager-content">
	<br />
	<br />
	<ul class="famer-pager nav">
		<li><a href="/main/wall/grandmasters">See all Wall of Fame Members<i class="icon-blue-arrow"></i></a></li>
	</ul>
	<!--<p class="pull-right">
		<a href="#" >See all Wall of Fame Member</a>
		<span><a href="#" class="arrow-left-bg">arrow-left-bg</a></span>
		<span><a href="#" class="arrow-right-bg">arrow-right-bg</a></span>
	</p>
	-->
</div>
<?php
	//random number generator
	$slider2_random = rand(2, 5);
	$slider3_random = rand(2, 5);
	$slider4_random = rand(2, 5);
	$slider5_random = rand(2, 5);
?>
<input type="hidden" id="random-slider-2" value="<?= rand(5, 10) * 1000; ?>" />
<input type="hidden" id="random-slider-3" value="<?= rand(5, 10) * 1000; ?>" />
<input type="hidden" id="random-slider-4" value="<?= rand(5, 10) * 1000; ?>" />
<input type="hidden" id="random-slider-5" value="<?= rand(5, 10) * 1000; ?>" />
<script>
	$(document).ready(function (){

	});
	
	$("#slider-2").FlowSlider({
		detectTouchDevice: false,
		infinite: true,
		marginStart: 0,
		marginEnd: 0,
		startPosition: 0,
		position: 0,
		controllers: ["Timer"],
		controllerOptions: [
			{
				el: $(document),
				eventStart: "ready",
				step: 200,
				time: $("#random-slider-2").val(),
				rewind: true
			}
		]
	});
	
	$("#slider-3").FlowSlider({
		detectTouchDevice: false,
		infinite: true,
		marginStart: 0,
		marginEnd: 0,
		startPosition: 0,
		position: 0,
		controllers: ["Timer"],
		controllerOptions: [
			{
				el: $(document),
				eventStart: "ready",
				step: 200,
				time: $("#random-slider-3").val(),
				rewind: true
			}
		]
	});
	
	$("#slider-4").FlowSlider({
		detectTouchDevice: false,
		infinite: true,
		marginStart: 0,
		marginEnd: 0,
		startPosition: 0,
		position: 0,
		controllers: ["Timer"],
		controllerOptions: [
			{
				el: $(document),
				eventStart: "ready",
				step: 200,
				time: $("#random-slider-4").val(),
				rewind: true
			}
		]
	});
	
	$("#slider-5").FlowSlider({
		detectTouchDevice: false,
		infinite: true,
		marginStart: 0,
		marginEnd: 0,
		startPosition: 0,
		position: 0,
		controllers: ["Timer"],
		controllerOptions: [
			{
				el: $(document),
				eventStart: "ready",
				step: 200,
				time: $("#random-slider-5").val(),
				rewind: true
			}
		]
	});

    var imgs = $("#slider-2 .grand-masters-image");
    var count = imgs.length;
    if (count) {

        imgs.each(function(index, elem){

            $(elem).load(function(){
                count--;
                console.log("count:" +count);
                if (count==0) {
                    setTimeout(function(){$("#slider-2").css("visibility","visible")},700);
                }
            });
        });

    }setTimeout(function(){$("#slider-2").css("visibility","visible");},700);

    var imgs = $("#slider-3 .grand-masters-image");
    var count = imgs.length;
    if (count) {

        imgs.each(function(index, elem){

            $(elem).load(function(){
                count--;
                console.log("count:" +count);
                if (count==0) {
                    setTimeout(function(){$("#slider-3").css("visibility","visible")},700);
                }
            });
        });

    }setTimeout(function(){$("#slider-3").css("visibility","visible");},700);

    var imgs = $("#slider-4 .grand-masters-image");
    var count = imgs.length;
    if (count) {

        imgs.each(function(index, elem){

            $(elem).load(function(){
                count--;
                console.log("count:" +count);
                if (count==0) {
                    setTimeout(function(){$("#slider-4").css("visibility","visible")},700);
                }
            });
        });

    }setTimeout(function(){$("#slider-4").css("visibility","visible");},700);

    var imgs = $("#slider-5 .grand-masters-image");
    var count = imgs.length;
    if (count) {

        imgs.each(function(index, elem){

            $(elem).load(function(){
                count--;
                console.log("count:" +count);
                if (count==0) {
                    setTimeout(function(){$("#slider-5").css("visibility","visible")},700);
                }
            });
        });

    }setTimeout(function(){$("#slider-5").css("visibility","visible");},700);

</script>