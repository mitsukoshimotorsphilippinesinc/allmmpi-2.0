<div class="page-header clearfix">
	<center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">My Job Description</small></h2></center>
</div>

<?php

$proper_position_name = ucfirst($position_details->position_name);

if (empty($job_description_asset_details)) {
	echo "<center class='alert'><h3>Your JD document is not available yet.<br/>Please contact your supervisor or manager to address your concern. <br/>Thank you.</h3><center>";	
} else {

?>

	<div class="page-header">  
	  <h2><?= $proper_position_name; ?> </h2>
	</div>

	<?php

	echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#../../../media/jd/{$job_description_asset_details->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";

}

?>