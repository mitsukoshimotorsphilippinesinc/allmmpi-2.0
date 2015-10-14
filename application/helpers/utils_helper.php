<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('hash_hmac')) {

    function hash_hmac($algo='md5', $message, $secret_key) {
        $block_size_md5 = 64; // md5 block size is 64 bytes = 512 bits
        $opad = str_pad('', $block_size_md5, chr(0x5c));
        $ipad = str_pad('', $block_size_md5, chr(0x36));
        if (strlen($secret_key) > $block_size_md5) {
            $secret_key = md5($secret_key, true /*raw binary*/); // $secret_key is now 16 bytes long
        }

        $secret_key = str_pad($secret_key, $block_size_md5, chr(0x00));

        $ipad = $secret_key ^ $ipad; 

        $opad = $secret_key ^ $opad;

        return md5($opad . md5($ipad . $message, true /*raw binary*/));
    } 
}


if (! function_exists('check_image_path'))
{
	function check_image_path($path = '', $no_image = 1, $is_login_ad = 0)
	{
		if (empty($path)) 
		{
			if ($is_login_ad == 1) {
				return "http://placehold.it/500x700/cccccc/333333&text=No+Image";
			} else {
				return "http://placehold.it/300/cccccc/333333&text=No+Image";
			}
		}
		
		// if file exists in the 
		if (0 === stripos($path, 'http://')) {
		   return $path;
		}
		
		if (file_exists(FCPATH.$path)) {
			return image_path($path);
		} else {
			if ($no_image == 1) {  
				if ($is_login_ad == 1) {
					return "http://placehold.it/500x700/cccccc/333333&text=No+Image";
				} else {
					return "http://placehold.it/300/cccccc/333333&text=No+Image";
				}
			}	
		}
	}
}

if (! function_exists('check_image_size'))
{
	function check_image_size($path = '')
	{
		if (empty($path)) 
		{
			return array(0,0);
		}
		
		// if file exists in the 
		if (0 === stripos($path, 'http://')) {
		   return getimagesize($path);
		}
		
		if (file_exists(FCPATH.$path)) {
			return getimagesize(FCPATH.$path);
		} else {
			return array(0,0);
		}
	}
}

if (! function_exists('image_format_from_file'))
{
	function image_format_from_file($filename)
	{
		$format = 'invalid';
		//check if gif
	    if(stristr(strtolower($filename),'.gif')) $format = 'gif';
	    //check if jpg
	    elseif(stristr(strtolower($filename),'.jpg') || stristr(strtolower($filename),'.jpeg')) $format = 'jpg';
	    //check if png
	    elseif(stristr(strtolower($filename),'.png')) $format = 'png';
	
		return $format;
	}
}

if (! function_exists('image_copy'))
{
	function image_copy($filename, $souce_path, $target_path, $size = array('width' => 100, 'height' => 100), $size_as = 'exact', $save_as = false)
	{
		$format = image_format_from_file($filename);
	
		// lowercase the target file
		$target_path = strtolower($target_path);

		if ($format != 'invalid') {
			// Load image
	        $image = null;
	        switch($format) {
	            case 'gif':
	                $image = ImageCreateFromGif($souce_path);
	                break;
	            case 'jpg':
	                $image = ImageCreateFromJpeg($souce_path);
	                break;
	            case 'png':
	                $image = ImageCreateFromPng($souce_path);
	                break;
	        }

	        if ($image === null) {
	            return false;
	        }

			// Get original width and height
	        list($width,$height)=getimagesize($souce_path);
	
			// use orig size for initial destination values
			$_width = $width;
			$_width = $height;
	
			// override desination size if definded
			if (!empty($size)) {
				$_width = $size['width'];
				$_height = $size['height'];
			}
			

			// serve as default image
	        $image_resized = $image;

	        // New width with aspect ratio
	        $newWidth= $_width;
	        $newHeight = $_height;
			$pad_x = 0;
			$pad_y = 0;

			if ($width != $_width && $height != $_height) 
			{
				if ($width > $height) 
				{
					$percentage = ($_height / $height);
					$pad_x = ($width - $height) / 2;
		        } 
				else 
				{
					$percentage = ($_width / $width);
					$pad_y = ($height - $width) / 2;
		        }
		
				$newWidth = ($width * $percentage);
				$newHeight = ($height * $percentage);
			}
	        
			// apply aspec ratio
			if ($size_as == 'aspect')
			{
				$image_resized = imagecreatetruecolor($newWidth,$newHeight);
				$pad_x = 0;
				$pad_y = 0;
			}
			else 
			{ // assume that other than aspect ratio its exact size
				$image_resized = imagecreatetruecolor($_width,$_height);
			}

			imagealphablending( $image_resized, false );
			imagesavealpha( $image_resized, true );
			$black = imagecolorallocate($image_resized, 0, 0, 0);
			imagecolortransparent($image_resized, $black);
	        imagecopyresized($image_resized, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
			
			// check if there is file destination override
			if ($save_as !== false)
			{
				$format = $save_as;
				$target_path = preg_replace('"\.(jpg|jpeg|gif|png)$"', '.'.$format, $target_path);
			}
			
			
			// save the resized image
			if ($format == 'png')
			{
				imagepng($image_resized, $target_path);
			}
			else if ($format == 'jpg')
			{
				imagejpeg($image_resized, $target_path);
			}
			else
			{
				// assuming gif
				imagegif($image_resized, $target_path);
			}
				
			
			return true;
		}
		
		return false;
	}
}

if ( ! function_exists('get_fu_headers')){
	function get_fu_headers() {
	    $headers = array();
	    foreach ($_SERVER as $k => $v)
		{
	        if (substr($k, 0, 5) == "HTTP_")
			{
	            $k = str_replace('_', ' ', substr($k, 5));
	            $k = str_replace(' ', '-', ucwords(strtolower($k)));
	            $headers[$k] = $v;
			}
		}
	    return $headers;
	}
}

/*
 * This will generate HTML select tag based on json data
 * json should have keys: code and name
 * example:
 *   [{"code":"PH", "name":"Philippines"},{"code":"US", "name":"United State"}]
 */
if ( ! function_exists('build_select_tag')){
	function build_select_tag($json, $selected_value = '', $extra = '') {
		if (is_string($json)) 
			$_codes = json_decode($json);
		else 
			$_codes = $json;
			
		$html = "<select {$extra} >";
		foreach ($_codes as $item) {
			$selected_item = '';
			if ($selected_value == $item->code) $selected_item = 'selected';
			$html .= "<option value='{$item->code}' {$selected_item}>{$item->name}</option>";
		}
		$html .= "</select>";
		
		return $html;
	}
}

// check username - registration
if ( ! function_exists('check_username')){
    
    function check_username($username) {
		$result = ctype_alnum($username);
		// result return 1 if username is alphanum
		return 1 == $result;
    }
}

if (!function_exists('slugify')) {
	function slugify($text)
	{
		$text = strtolower($text);
		$text = preg_replace('/[^-a-zA-Z0-9\s_]+/i', '', $text);
		$text = preg_replace('/-/i', '_', $text);
		$text = preg_replace('/\s/i', '-', $text);
		return $text;
	}
}

if (!function_exists('sanitize_html')) {
	function sanitize_html($buffer)
	{
	    $search = array(
	        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
	        '/[^\S ]+\</s', //strip whitespaces before tags, except space
	        '/(\s)+/s'  // shorten multiple whitespace sequences
	        );
	    $replace = array(
	        '>',
	        '<',
	        '\\1'
	        );
	    $buffer = preg_replace($search, $replace, $buffer);

	    return $buffer;
	}
}

/*
 * This will send out sms message thru sun api
 */
if ( ! function_exists('send_sunapi_sms')){
	function send_sunapi_sms($to , $msg, $login_url, $send_url, $originator, $username, $password) {
		
		$url = $login_url . "?" . http_build_query(array('user' => $username, 'pass' => $password));
		$ret = file_get_contents($url);
		$ret = explode(",", $ret);
		error_log('send_sunapi_sms : ' . $ret);
		if ($ret[0] == '20100') { // logged in ok
			$session = $ret[2];
			$url = $send_url . "?" . http_build_query(array('user' => $username, 'pass' => $password, 'from' => $originator, 'to' => $to, 'msg' => $msg));
			$ret =  file_get_contents($url);
			error_log('send_sunapi_sms : ' . $ret);
			return true;
		}
		
		return false;
	}
}

if ( ! function_exists('sanitize_html')) {
	function sanitize_html($buffer)
	{
	    $search = array(
	        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
	        '/[^\S ]+\</s', //strip whitespaces before tags, except space
	        '/(\s)+/s'  // shorten multiple whitespace sequences
	        );
	    $replace = array(
	        '>',
	        '<',
	        '\\1'
	        );
	    $buffer = preg_replace($search, $replace, $buffer);

	    return $buffer;
	}
}

if (! function_exists('render_image'))
{
	function render_image($filename, $size = array('width' => 100, 'height' => 100), $size_as = 'actual')
	{
		ini_set('memory_limit', '256M'); // raise the allowed memory temporary for this process
		$format = image_format_from_file($filename);

		if ($format != 'invalid') {
			// Load image
	        $image = null;
	        switch($format) {
	            case 'gif':
	                $image = ImageCreateFromGif($filename);
	                break;
	            case 'jpg':
	                $image = ImageCreateFromJpeg($filename);
	                break;
	            case 'png':
	                $image = ImageCreateFromPng($filename);
	                break;
	        }

	        if ($image === null) {
	            return false;
	        }

			if ($size_as == 'actual')
			{
				// save the resized image
				if ($format == 'png')
				{
					imagepng($image);
				}
				else if ($format == 'jpg')
				{
					imagejpeg($image);
				}
				else
				{
					// assuming gif
					imagegif($image);
				}
				return true;
			}

			// Get original width and height
	        list($width,$height)=getimagesize($filename);
	
			// use orig size for initial destination values
			$_width = $width;
			$_height = $height;
	
			// override desination size if definded
			if (!empty($size)) {
				$_width = $size['width'];
				$_height = $size['height'];
			}
			

			// serve as default image
	        $image_resized = $image;

	        // New width with aspect ratio
	        $newWidth= $_width;
	        $newHeight = $_height;
			$pad_x = 0;
			$pad_y = 0;

			if ($width != $_width && $height != $_height) 
			{
				if ($width > $height) 
				{
					$percentage = ($_height / $height);
					$pad_x = ($width - $height) / 2;
		        } 
				else 
				{
					$percentage = ($_width / $width);
					$pad_y = ($height - $width) / 2;
		        }
		
				$newWidth = ($width * $percentage);
				$newHeight = ($height * $percentage);
			}
	        
			// apply aspec ratio
			if ($size_as == 'aspect')
			{
				$image_resized = imagecreatetruecolor($newWidth,$newHeight);
				$pad_x = 0;
				$pad_y = 0;
			}
			else 
			{ // assume that other than aspect ratio its actual size
				$image_resized = imagecreatetruecolor($_width,$_height);
			}
		
			imagealphablending( $image_resized, false );
			imagesavealpha( $image_resized, true );
			$black = imagecolorallocate($image_resized, 0, 0, 0);
			imagecolortransparent($image_resized, $black);
	        imagecopyresampled($image_resized, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
			
			// save the resized image
			if ($format == 'png')
			{
				imagepng($image_resized);
			}
			else if ($format == 'jpg')
			{
				imagejpeg($image_resized);
			}
			else
			{
				// assuming gif
				imagegif($image_resized);
			}
			imagedestroy($image);
			imagedestroy($image_resized);
			
			return true;
		}
		
		return false;
	}
}


/*
 * Execute
 */
if (!function_exists('run_cli')){
	function run_cli($module, $params = '') {
		error_log("run_cli-----");
	    $query = $params;
	    $root_path = FCPATH;
	    $cmd = "/usr/bin/php-cli {$root_path}cli.php --run='{$module}' --query='{$query}' --log-file=/dev/null";
	    $outputfile = "/tmp/mmpi_execute_" . date("YmdHis") . ".log";
	    $pidfile = "/tmp/mmpi_execute_pid_" . date("YmdHis") . ".log";
	    exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile),$cli_status,$cli_return);
	}
}

/*
 * Execute Job
 */
if (!function_exists('job_exec')){
	function job_exec($job_id, $separate_process = false) {
		$root_path = FCPATH;
		$com_script = "/usr/bin/php {$root_path}jobs.php jobs process {$job_id} >> /dev/null 2>&1";
		//$com_script = "C:/xampp/php {$root_path}jobs.php jobs process {$job_id} >> /dev/null 2>&1";
		$com_script .= ($separate_process)?' &':'';

		var_dump($com_script);
		
		exec($com_script);
	}
}