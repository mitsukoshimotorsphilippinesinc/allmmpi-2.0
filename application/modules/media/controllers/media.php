<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media extends Base_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('members_model');
	}
	
	public function index($cmd = '', $img_info = '')
	{
		// the browser will send a $_SERVER['HTTP_IF_MODIFIED_SINCE'] if it has a cached copy 
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
		  // if the browser has a cached version of this image, send 304
		  header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true, 304);
		  exit;
		}
		
		$cmd = strtolower($cmd);
		$_method_name = '_'.$cmd;

		if (method_exists($this, $_method_name))
		{
			if ($this->{$_method_name}($img_info))
			{
				return;
			}
		} else 
		{
			echo "method does not exists";
		}
		
		// cmd not found
		$url = 'http://placehold.it/100/cccccc/333333&text=%20';
		header('Location: '.$url);
		return;
	}

	private function _profile($img_info = false)
	{
		if ($img_info === false) return false;
		if (strlen($img_info) == 0) return false;
		
		// remove extension 
		$pos = strpos($img_info, '.');
		if ($pos == 0) $pos = strlen($img_info);
		$img_info = substr($img_info, 0, $pos);


		$img_info = explode("_", $img_info);
		$member_id = $img_info[0];
		$ratio = "";
		if (count($img_info) > 1) $ratio = $img_info[1];

		$member = $this->members_model->get_member_by_id($member_id);

		if (empty($member)) return false;

		$img_filename = trim($member->image_filename);

		$not_found_or_blank = true;
		$media_dir = FCPATH.'assets/media/members/';

		if (strlen($img_filename) > 0)
		{	
			$not_found_or_blank = !file_exists($media_dir.$img_filename);
		}

		if ($not_found_or_blank)
		{
			if (strtolower($member->sex) == 'f')
				$img_filename = 'female.jpg';
			else
				$img_filename = 'male.jpg';
		}

		$this->_render($media_dir.$img_filename, $ratio);

		return true;
	}

	private function _render($filename, $new_ratio)
	{ 

		if(file_exists($filename)){ 
			$mime = get_mime_by_extension($filename);
			$_ratio = '';
			
			header('Content-Type: '.$mime);
			header('Content-Disposition: inline; filename="'.$filename.'";');
			header("Cache-Control: private, max-age=10800, pre-check=10800");
			header("Pragma: private");
			header("Expires: " . date(DATE_RFC822,strtotime(" 30 day")));
			
			// get actual size
			list($width,$height)=getimagesize($filename);
			$size = array('width' => $width, 'height' => $height);

			// check if there will be size overide
			if (!empty($new_ratio)) 
			{
				$_val = explode('x', $new_ratio);
				$is_number = false;
				if (count($_val) == 1)
				{
					$is_number = is_numeric($_val[0]);
					if ($is_number)
					{
						$size = array('width' => $_val[0], 'height' => $_val[0]);
						$_ratio = 'aspect';
					}
					
				} 
				else if (count($_val) > 1)
				{
					$is_number = is_numeric($_val[0]) && is_numeric($_val[1]);
					if ($is_number)
					{
						$size = array('width' => $_val[0], 'height' => $_val[1]);
						$_ratio = 'aspect';
					}
				}
				if (!empty($ratio))
				 	if ($ratio == 'actual' || $ratio == 'aspect' || $ratio == 'exact') 
						$_ratio = $ratio;
					else
					{
						if (!empty($_ratio)) $_ratio = 'actual';
					}
				
			}
			render_image($filename, $size, $_ratio);
			return true; 
		}
		
		return false;
	}
	
}
