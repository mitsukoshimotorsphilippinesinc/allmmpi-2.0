<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function send_email($params=array())
{	
	$ci = ci();
	
	// load email library
	$ci->load->library("email");		

	$ci->email->from($params["from"]);
	$ci->email->to($params["to"]);

	//$ci->email->cc("ronaldoamadorgonzales@gmail.com");
	//$ci->email->bcc($params["bcc"]);

	$ci->email->subject($params["subject"]);
	$ci->email->message($params["message"]);

	$result = $ci->email->send();
	
	if ($result)
		return "message sent";
	else
		return "message not sent";		
}

/*
 * sending email thru elastic email
 *
 * ex: echo send_elastic_email("test@test.com", "My Subject", "My Text", "My HTML", "youremail@yourdomain.com", "Your Name");
 */
if ( ! function_exists('send_elastic_email')){
	function send_elastic_email($username, $api_key, $to, $subject, $body_text, $body_html, $from, $fromName)
	{
	    $res = "";

	    $data = "username=".urlencode($username);
	    $data .= "&api_key=".urlencode($api_key);
	    $data .= "&from=".urlencode($from);
	    $data .= "&from_name=".urlencode($fromName);
	    $data .= "&to=".urlencode($to);
	    $data .= "&subject=".urlencode($subject);
	    if($body_html)
	      $data .= "&body_html=".urlencode($body_html);
	    if($body_text)
	      $data .= "&body_text=".urlencode($body_text);

	    $header = "POST /mailer/send HTTP/1.0\r\n";
	    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	    $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
	    $fp = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);

	    if(!$fp)
	      return "ERROR. Could not open connection";
	    else {
	      fputs ($fp, $header.$data);
	      while (!feof($fp)) {
	        $res .= fread ($fp, 1024);
	      }
	      fclose($fp);
	    }
	    return $res;                  
	}
}

/*
 * sending email thru elastic email using curl
 *
 * ex: echo cURLElasticEmail('to_email@domain.com', 'Subject', 'Body', '<strong>HTML Body</strong>', 'from_email@domain.com', 'From Email Name');
 */
if ( ! function_exists('send_elastic_email2')){
	function send_elastic_email2($username, $api_key, $to, $subject, $body_text, $body_html, $from, $from_name)
	{
		
		// Initialize cURL
		$ch = curl_init();
		
		// Set cURL options
		curl_setopt($ch, CURLOPT_URL, 'https://api.elasticemail.com/mailer/send');
		curl_setopt($ch, CURLOPT_POST, 1);

		// Parameter data
		$data = 'username='.urlencode($username).
				'&api_key='.urlencode($api_key).
				'&from='.urlencode($from).
				'&from_name='.urlencode($from_name).
				'&to='.urlencode($to).
				'&subject='.urlencode($subject);

		if($body_html)	$data .= '&body_html='.urlencode($body_html);
		if($body_text)	$data .= '&body_text='.urlencode($body_text);
		
		// Set parameter data to POST fields
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		// Header data
	    	$header = "Content-Type: application/x-www-form-urlencoded\r\n";
	    	$header .= "Content-Length: ".strlen($data)."\r\n\r\n";

		// Set header
		curl_setopt($ch, CURLOPT_HEADER, $header);
		
		// Set to receive server response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// Set cURL to verify SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		
		// Set the path to the certificate used by Elastic Mail API
		curl_setopt($ch, CURLOPT_CAINFO, getcwd()."/DOWNLOADED_CERTIFICATE.CRT");
		
		// Get result
		$result = curl_exec($ch);
		
		// Close cURL
		curl_close($ch);
		
		// Return the response or NULL on failure
		return ($result === false) ? NULL : $result;
		
		// Alternative error checking return
		// return ($result === false) ? 'Curl error: ' . curl_error($ch): $result;
	}
}