<div class="page-header clearfix">
	<center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">S4S <small style="color:#FFFFFF">(System for System)</small></h2></center>
</div>

<div class="page-header">
  <h2><?= $course_details->pp_name; ?> <br/><small><?= $course_details->pp_description; ?></small></h2>
</div>

<?php
foreach ($asset_details as $ad) {

echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#../../../media/s4s/{$ad->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";
//echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#http://portal.mmpi.local/assets/media/s4s/{$ad->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";

}
?>