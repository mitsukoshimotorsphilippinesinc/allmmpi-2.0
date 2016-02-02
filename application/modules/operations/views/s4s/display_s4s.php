<h2>View S4S  <a i class='btn btn-small close-button' style="float:right">Close</a></h2>
<hr/>

<div class="page-header clearfix">
	<center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">S4S <small style="color:#FFFFFF">(System for System)</small></h2></center>
</div>

<div class="page-header">
  <h2><?= $course_details->pp_name; ?> <br/><small><?= $course_details->pp_description; ?></small></h2>
</div>

<?php
$department_details = $this->human_relations_model->get_department_by_id($course_details->department_id);

foreach ($asset_details as $ad) {

echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#../../../media/s4s/{$department_details->url}/{$ad->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";
}


?>
<br/>
<h2><a class='btn btn-small close-button' style="float:right">Close</a></h2>

<script>
$(".close-button").click(function(e){
	window.close();
})
</script>