<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media_upload extends Admin_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'title',
			'label' => 'Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'body',
			'label' => 'Body',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_display',
			'label' => 'Publication Status',
			'rules' => 'trim|required'
		)
	);
	
	private $_months = array(
		'0' => 'Month',
		'1' => 'January',
		'2' => 'February',
		'3' => 'March',
		'4' => 'April',
		'5' => 'May',
		'6' => 'June',
		'7' => 'July',
		'8' => 'August',
		'9' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
	);
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('asset_model');
		// load pager library
		$this->load->library('pager');
		$this->load->helper('breadcrumb_helper');		
		//$this->set_navigation('media_upload');
	}

	public function index()
	{
		$this->media_upload();
	}
	
	public function media_upload()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = null;
		} else {			
			$where = "`{$search_by}` LIKE LOWER('%{$search_text}%')";
		}
		
		$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/operations/media_upload/index",
		    'total_items' => $this->asset_model->get_media_upload_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;

		$this->template->media_uploads = $this->asset_model->get_media_upload($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');
		$this->template->view('media_upload/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				
				$is_display = set_value('is_display');
			
				$data = array(
					'title' => set_value('title'),
					'description' => set_value('description'),
					'body' => $this->form_validation->set_value('body'),
					'is_display' => $is_display,
					'user_id' => $this->user->user_id
				);
				$this->asset_model->insert_media_uploads($data);
				
				$insert_id = $this->asset_model->insert_id();
				
				/*//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_media_upload_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'MEDIA UPLOADS',
					'table_name' => 'sm_media_uploads',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_media_upload_log_data);
				*/

				redirect('/operations/media_upload');
				return;
			}
		}
		
		$this->template->view('media_upload/add');
	}
	
	/*
	public function edit($media_upload_id = 0)
	{
		$media_upload = $this->asset_model->get_media_uploads_by_id($media_upload_id);
		
		if ($_POST and !empty($media_upload))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				$is_display = set_value('is_display');
				
				$data = array(
					'title' => set_value('title'),
					'body' => $this->form_validation->set_value('body'),
					'description' => $this->form_validation->set_value('description'),
					'is_display' => $is_display,
					'user_id' => $this->user->user_id,
					'update_timestamp' => date("Y-m-d H:i:s")
				);
				
				$this->asset_model->update_media_uploads($data, array('media_upload_id' => $media_upload_id));
				
				
				//logging of action
				$details_before = array('id' => $media_upload_id, 'details' => array());
				$details_after = array('id' => $media_upload_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($media_upload->$k != $v)
					{
						$details_before['details'][$k] = $media_upload->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_media_upload_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'MEDIA UPLOADS',
					'table_name' => 'sm_media_uploads',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_media_upload_log_data);
				
				redirect('/operations/media_upload');
				return;
			}
		}
		
		$this->template->media_upload = $media_upload;
		$this->template->view('media_uploads/edit');

	}
	
	public function delete($media_upload_id = 0)
	{
		$media_upload = $this->asset_model->get_media_uploads_by_id($media_upload_id);

		if ($_POST and !empty($media_upload))
		{
			$_media_upload_id = $this->input->post('media_upload_id');
			if (!empty($_media_upload_id)) if ($_media_upload_id == $media_upload_id)
			{
				$this->asset_model->delete_media_uploads(array('media_upload_id' => $media_upload_id));
				
				//logging of action
				$details_before = array('id' => $media_upload_id, 'details' => $media_upload);
				$details_before = json_encode($details_before);
				
				$delete_media_upload_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'MEDIA UPLOADS',
					'table_name' => 'sm_media_uploads',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_media_upload_log_data);
				
				redirect('/operations/media_upload');
				return;
			}
		}
		
		
		$this->template->media_upload = $media_upload;
		$this->template->view('media_uploads/delete');

	}
	
	public function view($media_upload_id = 0)
	{
		$media_upload = $this->asset_model->get_media_uploads_by_id($media_upload_id);

		$this->template->media_upload = $media_upload;
		$this->template->view('media_uploads/view');

	}*/
}