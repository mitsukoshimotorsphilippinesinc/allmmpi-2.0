<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends Base_Controller {

    function __construct() {
        parent::__construct();
		$this->load->model('tracking_model');
    }

	public function index() {
	}

    public function process()
    {
	
        $target_path = FCPATH . "assets/media/tmp/";
		$allowedExts = array();
		$maxFileSize = 0;
	
		$headers = get_fu_headers();

		$width = abs($this->input->get('width'));
		$height = abs($this->input->get('height'));
		
		$upload_type = trim($this->input->get('type'));
		
		if ($width<=0) $width = 200;
		if ($height<=0) $height =200;
		
		$location = $this->input->get('location');
		
		if ($location=="") $location = "/assets/media/uploads"; 
		else $location = urldecode($location);
		
		$gallery_id = 0;
		
		if(strtolower($upload_type) == "gallery")
		{
			$gallery_id = abs($this->input->get('gallery_id'));
		}
		elseif(strtolower($upload_type) == "general")
		{
			$gallery_id = "general";
		}

		if ($headers['X-Requested-With']=='XMLHttpRequest') { 
			$fileName = $headers['X-File-Name'];
			$fileSize = $headers['X-File-Size'];
			$ext = substr($fileName, strrpos($fileName, '.') + 1);
			if (in_array($ext,$allowedExts) or empty($allowedExts)) {
				if ($fileSize<$maxFileSize or empty($maxFileSize)) {
				$content = file_get_contents("php://input");
				file_put_contents($target_path.$fileName,$content);

				$filename = $this->input->get('filename');
				if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
                
				echo $this->_saveImage($filename, $fileName, $target_path.$fileName, $location, $width, $height,$gallery_id, false);

			} else { echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); };
			} else {
				echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
			}
				
		} else {
			if ($_FILES['file']['name']!='') {
			$fileName= $_FILES['file']['name'];
			$fileSize = $_FILES['file']['size'];
			$ext = substr($fileName, strrpos($fileName, '.') + 1);
			if (in_array($ext,$allowedExts) or empty($allowedExts)) {
				if ($fileSize<$maxFileSize or empty($maxFileSize)) {
			
			$filename = $this->input->get('filename');
			if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
			
            echo $this->_saveImage($filename, $_FILES['file']['name'], $_FILES['file']['tmp_name'],$location,$width,$height,$gallery_id);
			
			} else { echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); };
			} else echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
			} else echo '{"success":false, "details": "No file received."}';
		}
	
	}

	private function _saveImage($filename, $uploading_filename, $tmp_filename, $location, $width, $height, $gallery_id = 0, $using_file = true) {
		
		$format = 'invalid';
		
		$_uploading_filename = $uploading_filename; //$_FILES['Filedata']['name'];
		
		//check if gif
	    if(stristr(strtolower($_uploading_filename),'.gif')) $format = 'gif';
	    //check if jpg
	    elseif(stristr(strtolower($_uploading_filename),'.jpg') || stristr(strtolower($_uploading_filename),'.jpeg')) $format = 'jpg';
	    //check if png
	    elseif(stristr(strtolower($_uploading_filename),'.png')) $format = 'png';
		
		$temp_file = $tmp_filename; //$_FILES['file']['tmp_name'];
		
		$_hash = substr(md5(date('Y-m-d H:i:s')),0,8);
		
		$filename = $filename . "." . $format;
		$target_filename = $filename;
		
		$location = substr($location,1,strlen($location));
		
		$target_fullpath = FCPATH . $location;
		$target_thumb_fullpath = FCPATH . $location. "/thumbnail";
		$fullpath = FCPATH . $location . "/". $filename;
		
		$_ret = true;
		
		if ($using_file) {
			$_ret = move_uploaded_file($temp_file, $fullpath);
			chmod($fullpath, 777);
		} else {
			$_ret = copy($temp_file, $fullpath);
			chmod($fullpath, 777);
			if ($_ret) unlink($temp_file);
		}
		
		if(!$_ret) {
			return json_encode(array('success' => false, 'details' => 'move_uploaded_file failed'));
		} else {

			// resize
			$_width = $width;
			$_height = $height;		
			
			if ($format != 'invalid') {
				// Load image
		        $image = null;
		        switch($format) {
		            case 'gif':
		                $image = ImageCreateFromGif($fullpath);
		                break;
		            case 'jpg':
		                $image = ImageCreateFromJpeg($fullpath);
		                break;
		            case 'png':
		                $image = ImageCreateFromPng($fullpath);
		                break;
		        }

		        if ($image === null) {
		            echo 'Unable to open image';
					exit;
		        }

				// Get original width and height
		        list($width,$height)=getimagesize($fullpath);

				
				
				// serve as default image
		        $image_resized = $image;
				$image_thumb = null;
				if(strcmp($gallery_id, "general") == 0)
				{
					
					$image_thumb = $image;
					
					// New width with aspect ratio
					$newWidth = 100;
					$newHeight = $height * ($newWidth / $width);

					$pad_x = 0;
					$pad_y = 0;
					

					$image_thumb = imagecreatetruecolor($newWidth,$newHeight);
					if ($format == 'png') // png we can actually preserve transparency
					{
						imagecolortransparent($image_thumb, imagecolorallocatealpha($image_thumb, 0, 0, 0, 127));
						imagealphablending($image_thumb, FALSE);
						imagesavealpha($image_thumb, TRUE);
					}	
					imagecopyresampled($image_thumb, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
					
					$target_fullpath = $target_fullpath;
					
					$target_thumb_fullpath = $target_thumb_fullpath;
					
				}
				elseif($gallery_id == 0)
				{
					
					// New width with aspect ratio
					$newWidth= $_width;
					$newHeight = $_height;
					$pad_x = 0;
					$pad_y = 0;

					//old
					/*if ($width > $height) {
						$newWidth=($width/$height)* $_height;
						//$pad_x = ($width - $height) / 2;
					} else {
						$newHeight=($height/$width)* $_width;
						//$pad_y = ($height - $width) / 2;
					}*/
					
					if ($width > $height) {
						$newHeight=($height/$width)* $_width;
					} else {
						$newWidth=($width/$height)* $_height;
					}

					$image_resized = imagecreatetruecolor($newWidth,$newHeight);
					if ($format == 'png') // png we can actually preserve transparency
					{
						imagecolortransparent($image_resized, imagecolorallocatealpha($image_resized, 0, 0, 0, 127));
						imagealphablending($image_resized, FALSE);
						imagesavealpha($image_resized, TRUE);
					}					
					imagecopyresampled($image_resized, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
				}
				else
				{
					
					$image_thumb = $image;
					
					// New width with aspect ratio
					$newWidth = 100;
					$newHeight = $height * ($newWidth / $width);

					$pad_x = 0;
					$pad_y = 0;
					
					// New width with aspect ratio
					$newWidth= $_width;
					$newHeight = $_height;
					$pad_x = 0;
					$pad_y = 0;


					if ($width > $height) {
						$newHeight=($height/$width)* 200;
					} else {
						$newWidth=($width/$height)* 200;
					}
					
					$image_thumb = imagecreatetruecolor($newWidth,$newHeight);
					
					if ($format == 'png') // png we can actually preserve transparency
					{
						imagecolortransparent($image_thumb, imagecolorallocatealpha($image_thumb, 0, 0, 0, 127));
						imagealphablending($image_thumb, FALSE);
						imagesavealpha($image_thumb, TRUE);
					}
					
					imagecopyresampled($image_thumb, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
					
					$target_fullpath = $target_fullpath . "/gallery_" . $gallery_id;
					
					if(!is_dir($target_fullpath))
						mkdir($target_fullpath);
					
					$target_thumb_fullpath = $target_thumb_fullpath . "/gallery_" . $gallery_id;
					
					if(!is_dir($target_thumb_fullpath))
						mkdir($target_thumb_fullpath);
					
				}
				
		        $target_fullpath = $target_fullpath . "/". $target_filename;
				$target_thumb_fullpath = $target_thumb_fullpath . "/". $target_filename;
				
				// Display resized image
		        $thumb_filename = "";
				unlink($fullpath);
				//imagejpeg($image_resized, $target_fullpath);
				
				switch($format) {
		            case 'gif':
		                imagegif($image_resized, $target_fullpath);
		                break;
		            case 'jpg':
		                imagejpeg($image_resized, $target_fullpath);
		                break;
		            case 'png':
		                imagepng($image_resized, $target_fullpath);
		                break;
		        }
				
				
				if(!is_null($image_thumb) && !empty($image_thumb)) 
				{
					//imagejpeg($image_thumb, $target_thumb_fullpath);
					
					switch($format) {
		            case 'gif':
		                imagegif($image_thumb, $target_thumb_fullpath);
		                break;
		            case 'jpg':
		                imagejpeg($image_thumb, $target_thumb_fullpath);
		                break;
		            case 'png':
		                imagepng($image_thumb, $target_thumb_fullpath);
		                break;
		        	}
				}
				
				if(strcmp($gallery_id, "general") == 0)
				{
					$this->load->model("contents_model");
					$data = array(
						"image_filename" => $filename,
						"user_id" => $this->user->user_id
					);
					$this->contents_model->insert_image_uploads($data);
					
					$insert_id = $this->contents_model->insert_id();
					
					//logging of action
					$details_after = array('id' => $insert_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$add_upload_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'IMAGE UPLOADS',
						'table_name' => 'sm_image_uploads',
						'action' => 'ADD',
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $add_upload_log_data);
				}
				elseif($gallery_id != 0)
				{
					$this->load->model("contents_model");
					$data = array(
						"gallery_id" => $gallery_id,
						"image_filename" => $filename,
						"user_id" => $this->user->user_id
					);
					$this->contents_model->insert_gallery_pictures($data);
					
					$insert_id = $this->contents_model->insert_id();
					
					//logging of action
					$details_after = array('id' => $insert_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$add_gallery_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'GALLERY PICTURES',
						'table_name' => 'sm_gallery_pictures',
						'action' => 'ADD',
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $add_gallery_log_data);
				}
				return json_encode(array('success' => true, 'file' => $filename));
			}
		
		}
		
	}
	
	public function process_prod_pack()
    {
	
        $target_path = FCPATH . "assets/media/tmp/";
		$allowedExts = array();
		$maxFileSize = 0;
	
		$headers = get_fu_headers();
		
		$id = abs($this->input->get('id'));
		
		$width = abs($this->input->get('width'));
		$height = abs($this->input->get('height'));
		
		$type = trim($this->input->get('type'));
		
		if ($width<=0) $width = 200;
		if ($height<=0) $height =200;
		
		$location = $this->input->get('location');
		
		if ($location=="") $location = "/assets/media/uploads"; 
		else $location = urldecode($location);
		
		if ($headers['X-Requested-With']=='XMLHttpRequest') { 
			$fileName = $headers['X-File-Name'];
			$fileSize = $headers['X-File-Size'];
			$ext = substr($fileName, strrpos($fileName, '.') + 1);
			if (in_array($ext,$allowedExts) or empty($allowedExts)) {
				if ($fileSize<$maxFileSize or empty($maxFileSize)) {
				$content = file_get_contents("php://input");
				file_put_contents($target_path.$fileName,$content);

				$filename = $this->input->get('filename');
				if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
                
				echo $this->_saveProdPackImage($filename, $fileName, $target_path.$fileName, $location, $width, $height,$id,$type, false);

			} else { echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); };
			} else {
				echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
			}
				
		} else {
			if ($_FILES['file']['name']!='') {
			$fileName= $_FILES['file']['name'];
			$fileSize = $_FILES['file']['size'];
			$ext = substr($fileName, strrpos($fileName, '.') + 1);
			if (in_array($ext,$allowedExts) or empty($allowedExts)) {
				if ($fileSize<$maxFileSize or empty($maxFileSize)) {
			
			$filename = $this->input->get('filename');
			if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
			
            echo $this->_saveProdPackImage($filename, $_FILES['file']['name'], $_FILES['file']['tmp_name'],$location,$width,$height,$id,$type);
			
			} else { echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); };
			} else echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
			} else echo '{"success":false, "details": "No file received."}';
		}
	
	}
	
	private function _saveProdPackImage($filename, $uploading_filename, $tmp_filename, $location, $width, $height, $id = 0, $type = "product", $using_file = true) {
		
		$format = 'invalid';
		
		$_uploading_filename = $uploading_filename; //$_FILES['Filedata']['name'];
		
		//check if gif
	    if(stristr(strtolower($_uploading_filename),'.gif')) $format = 'gif';
	    //check if jpg
	    elseif(stristr(strtolower($_uploading_filename),'.jpg') || stristr(strtolower($_uploading_filename),'.jpeg')) $format = 'jpg';
	    //check if png
	    elseif(stristr(strtolower($_uploading_filename),'.png')) $format = 'png';
		
		$temp_file = $tmp_filename; //$_FILES['file']['tmp_name'];
		
		$_hash = substr(md5(date('Y-m-d H:i:s')),0,8);
		
		$this->load->model("items_model");
		
		$update_function = "";
		
		if($type=="product")
		{
			$product = $this->items_model->get_product_by_id($id);
			$images = json_decode($product->image_filename);
			$update_function = "update_product";
		}
		elseif($type=="package")
		{
			$package = $this->items_model->get_package_by_id($id);
			$images = json_decode($package->image_filename);
			$update_function = "update_package";
		}

		

		$filename = $filename . "_" . time(). rand(0,99) . "." . $format;
		
		
		$target_filename = $filename;
		
		$location = substr($location,1,strlen($location));
		
		$target_fullpath = FCPATH . $location;
		$fullpath = FCPATH . $location . "/". $filename;
		
		$_ret = true;
		
		if ($using_file) {
			$_ret = move_uploaded_file($temp_file, $fullpath);
		} else {
			$_ret = copy($temp_file, $fullpath);
			if ($_ret) unlink($temp_file);
		}
		
		if(!$_ret) {
			return json_encode(array('success' => false, 'details' => 'move_uploaded_file failed'));
		} else {

			// resize
			$_width = $width;
			$_height = $height;		
			
			if ($format != 'invalid') {
				// Load image
		        $image = null;
		        switch($format) {
		            case 'gif':
		                $image = ImageCreateFromGif($fullpath);
		                break;
		            case 'jpg':
		                $image = ImageCreateFromJpeg($fullpath);
		                break;
		            case 'png':
		                $image = ImageCreateFromPng($fullpath);
		                break;
		        }

		        if ($image === null) {
		            echo 'Unable to open image';
					exit;
		        }

				// Get original width and height
		        list($width,$height)=getimagesize($fullpath);

				
				
				// serve as default image
		        $image_resized = $image;
				$image_thumb = null;
				
				
				// New width with aspect ratio
				$newWidth= $_width;
				$newHeight = $_height;
				$pad_x = 0;
				$pad_y = 0;


				if ($width > $height) {
					//$newWidth=($width/$height)* $_height;
					$newHeight=($height/$width)* 200;
					//$pad_x = ($width - $height) / 2;
				} else {
					//$newHeight=($height/$width)* $_width;
					$newWidth=($width/$height)* 200;
					//$pad_y = ($height - $width) / 2;
				}
				//$image_resized = imagecreatetruecolor($_width,$_height);
				$image_resized = imagecreatetruecolor($newWidth,$newHeight);
				
				if ($format == 'png') // png we can actually preserve transparency
				{
					imagecolortransparent($image_resized, imagecolorallocatealpha($image_resized, 0, 0, 0, 127));
					imagealphablending($image_resized, FALSE);
					imagesavealpha($image_resized, TRUE);
				}
				
				imagecopyresampled($image_resized, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
				
				
		        $target_fullpath = $target_fullpath . "/". $target_filename;
				
				// Display resized image
		        $thumb_filename = "";
				unlink($fullpath);
				//imagejpeg($image_resized, $target_fullpath);
				
				switch($format) {
		            case 'gif':
		                imagegif($image_resized, $target_fullpath);
		                break;
		            case 'jpg':
		                imagejpeg($image_resized, $target_fullpath);
		                break;
		            case 'png':
		                imagepng($image_resized, $target_fullpath);
		                break;
		        }
				
				/*if($gallery_id == "general")
				{
					$this->load->model("contents_model");
					$data = array(
						"image_filename" => $filename,
						"user_id" => $this->user->user_id
					);
					$this->contents_model->insert_image_uploads($data);
				}
				elseif($gallery_id != 0)
				{
					$this->load->model("contents_model");
					$data = array(
						"gallery_id" => $gallery_id,
						"image_filename" => $filename,
						"user_id" => $this->user->user_id
					);
					$this->contents_model->insert_gallery_pictures($data);
				}*/
				
				if(empty($images))
				{
					array_push($images, array("url" => $location."/".$filename,"is_default" => true));
				}
				else
				{
					array_push($images, array("url" => $location."/".$filename,"is_default" => false));
				}
				
				$data = array(
					"image_filename" => json_encode($images)
				);

				if($type=="product")
				{
					$old_data = $this->items_model->get_product_by_id($id);
					$details_before = array('id' => $id, 'details' => array('image_filename' => $old_data->image_filename));
					$details_before = json_encode($details_before);
					
					$this->items_model->update_product($data,array("product_id" => $id));
					
					$details_after = array('id' => $id, 'details' => $data);
					$details_after = json_encode($details_after);
					$update_product_image_logs = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PRODUCTS',
						'table_name' => 'is_products',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => ""
					);
					$this->tracking_model->insert_logs('admin', $update_product_image_logs);
				}
				elseif($type=="package")
				{
					$this->items_model->update_package($data,array("package_id" => $id));
				}
				
				return json_encode(array('success' => true, 'file' => $filename));
			}
		
		}
		
	}
}