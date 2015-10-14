<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class galleries extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'gallery_title',
			'label' => 'Gallery Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'gallery_description',
			'label' => 'Gallery Description',
			'rules' => 'trim'
		),
		array(
			'field' => 'is_published',
			'label' => 'Publish Status',
			'rules' => 'trim|required'
		)
	);
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('galleries');
	}

	public function index()
	{
		$this->galleries();
	}
	
	public function galleries()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/cms/galleries/index/',
		    'total_items' => $this->contents_model->get_gallery_count(),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->galleries = $this->contents_model->get_galleries(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('galleries/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				// insert the new galleries
				$data = array(
					'gallery_title' => set_value('gallery_title'),
					'gallery_description' => set_value('gallery_description'),
					'is_published' => set_value('is_published'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_gallery($data);
				
				$insert_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_gallery_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'GALLERIES',
					'table_name' => 'sm_galleries',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_gallery_log_data);
				
				redirect('/cms/galleries');
				return;
			}
		}
		$this->template->view('galleries/add');
	}
	
	public function edit($gallery_id = 0)
	{
		$gallery = $this->contents_model->get_gallery_by_id($gallery_id);
		
		
		if ($_POST and !empty($gallery))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				
				// insert the new galleries
				$data = array(
					'gallery_title' => set_value('gallery_title'),
					'gallery_description' => set_value('gallery_description'),
					'is_published' => set_value('is_published'),
					'user_id' => $this->user->user_id
				);

				$this->contents_model->update_gallery($data, array('gallery_id' => $gallery_id));
				
				//logging of action
				$details_before = array('id' => $gallery_id, 'details' => array());
				$details_after = array('id' => $gallery_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($gallery->$k != $v)
					{
						$details_before['details'][$k] = $gallery->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_gallery_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'GALLERIES',
					'table_name' => 'sm_galleries',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_gallery_log_data);
				
				redirect('/cms/galleries');
				return;
			}
		}

		$this->template->gallery = $gallery;
		$this->template->view('galleries/edit');

	}
	
	public function delete($gallery_id = 0)
	{
		$gallery = $this->contents_model->get_gallery_by_id($gallery_id);
		$gallery_pictures = $this->contents_model->get_gallery_pictures_by_gallery_id($gallery_id);
		
		if ($_POST and !empty($gallery))
		{
			$_gallery_id = $this->input->post('gallery_id');
			if (!empty($_gallery_id)) if ($_gallery_id == $gallery_id)
			{
				$this->contents_model->delete_gallery(array('gallery_id' => $gallery_id));
				
				//logging of action
				$details_before = array('id' => $gallery_id, 'details' => $gallery);
				$details_before = json_encode($details_before);
				
				$delete_gallery_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'GALLERIES',
					'table_name' => 'sm_galleries',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_gallery_log_data);
				
				$this->contents_model->delete_gallery_pictures(array('gallery_id' => $gallery_id));
				
				foreach($gallery_pictures as $gp)
				{
					//logging of action
					$details_before = array('id' => $gp->picture_id, 'details' => $gp);
					$details_before = json_encode($details_before);

					$delete_gallery_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'GALLERY PICTURES',
						'table_name' => 'sm_gallery_pictures',
						'action' => 'DELETE',
						'details_before' => $details_before,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $delete_gallery_log_data);
				}
				
				redirect('/cms/galleries');
				return;
			}
		}

		$this->template->gallery = $gallery;
		$this->template->view('galleries/delete');

	}
	
	public function view($gallery_id = 0)
	{
		$gallery = $this->contents_model->get_gallery_by_id($gallery_id);
		$gallery_pictures = $this->contents_model->get_gallery_pictures_by_gallery_id($gallery_id);
		
		
		$this->template->gallery = $gallery;
		$this->template->gallery_pictures = $gallery_pictures;
		$this->template->view('galleries/view');

	}
	
	public function update_gallery()
	{
		$picture_id = $this->input->post('picture_id');
		$gallery_id = $this->input->post('gallery_id');
		
		$picture = $this->contents_model->get_gallery_pictures(array("picture_id" => $picture_id, "gallery_id" => $gallery_id));
		$picture = $picture[0];
		
		$this->contents_model->delete_gallery_pictures(array("picture_id" => $picture_id, "gallery_id" => $gallery_id));
		
		//logging of action
		$details_before = array('id' => $picture->picture_id, 'details' => $picture);
		$details_before = json_encode($details_before);

		$delete_gallery_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'GALLERY PICTURES',
			'table_name' => 'sm_gallery_pictures',
			'action' => 'DELETE',
			'details_before' => $details_before,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $delete_gallery_log_data);
		
		$this->return_json('ok','Picture Deleted');
	}
}