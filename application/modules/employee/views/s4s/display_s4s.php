<div class="page-header clearfix">
	<center><h2 style="color:gray;">S4S <small>(System for System) </small></h2></center>
</div>

<div class="page-header">
  <h2><?= $course_details->pp_name; ?> <br/><small><?= $course_details->pp_description; ?></small></h2>
</div>

<?php
foreach ($asset_details as $ad) {

echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#../../../uploads/{$ad->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";

}
?>