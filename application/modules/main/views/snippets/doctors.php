<div class='doctors row-fluid'>
	<article class="span8">
		<h2 class="">Doctor's Profile</h2>
		<a href="" class="span3"><img src="<?=$this->config->item('img_url')?>/doctor_oclarit.jpg" alt="grand master"></a>
		<?php
			$this->load->model('contents_model');
			$doctors = $this->contents_model->get_content_by_slug('doctors-profile');
		?>
		<?php if(!empty($doctors)): ?>
		<ul class="span9">
			<li>
				<span><?= $doctors->title; ?></span>
				<p><?= $doctors->excerpt; ?></p>
			</li>
		</ul>
		<div class="row-fluid">
			<p class="span12" style="display:inline-block;"><a class="pull-right" href="/pages/doctors-profile">See more Doctors’ Profiles<i class="icon-blue-arrow"></i></a></p>
		</div>
		<?php endif; ?>
	</article>
	<article class="span4" style="margin-left: 20px;">
        <?php echo Modules::run('main/calendar/index'); ?>
    </article>
</div> 