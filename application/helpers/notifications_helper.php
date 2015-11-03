<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function send_email($params=array())
{	
	$ci = ci();
	
	// load email library
	$ci->load->library("email");		

	$ci->email->from($params["from"]);
	$ci->email->to($params["to"]);

	$ci->email->cc("dante.pangan@mitsukoshimotors.com");
	//$ci->email->bcc($params["bcc"]);

	$ci->email->subject($params["subject"]);
	$ci->email->message($params["message"]);

	$result = $ci->email->send();
	
	if ($result)
		return "message sent";
	else
		return "message not sent";		
}
