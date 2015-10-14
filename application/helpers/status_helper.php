<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function pretty_status($status,$status_type_id)
{

	if ($status_type_id==1)
		$text = "<span class='label'>{$status}</span>";
	else if ($status_type_id==2)
		$text = "<span class='label label-success''>{$status}</span>";
	else if ($status_type_id==3)
		$text = "<span class='label label-warning'>{$status}</span>";
	else if ($status_type_id==4)
		$text = "<span class='label label-important'>{$status}</span>";
	else if ($status_type_id==5)
		$text = "<span class='label label-info'>{$status}</span>";
	else	
		$text = "<span class='label'>{$status}</span>";

	return $text;
}
