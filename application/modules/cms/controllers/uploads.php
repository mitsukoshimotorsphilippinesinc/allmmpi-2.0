<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uploads extends CMS_Controller
{
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('uploads');
	}

	public function index()
	{
		$this->uploads();
	}
	
	public function uploads()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/cms/uploads/index/',
		    'total_items' => $this->contents_model->get_image_upload_count(),
		    'per_page' => 20,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->uploads = $this->contents_model->get_image_uploads(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('uploads/list');
	}
	
	public function update_gallery()
	{
		$image_id = abs($this->input->post('image_id'));
		
		$this->contents_model->delete_image_uploads(array("image_id" => $image_id));
		
		
		
		$this->return_json('ok','Picture Deleted');
	}
}