<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contents extends Systems_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'title',
			'label' => 'Title',
			'rules' => 'trim|required|min_length[3]'
		),
		array(
			'field' => 'content_type',
			'label' => 'Content Type',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'slug',
			'label' => 'Slug',
			'rules' => 'trim|required|min_length[3]|callback__check_slug'
		),
		array(
			'field' => 'thumb',
			'label' => 'Thumbnail URL',
			'rules' => 'trim'
		),
		array(
			'field' => 'body',
			'label' => 'Body',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'excerpt',
			'label' => 'Excerpt',
			'rules' => 'trim'
		),
		array(
			'field' => 'is_active',
			'label' => 'Status',
			'rules' => 'trim|required|numeric'
		),
	);

	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('contents');
	}

	public function index()
	{
		$this->view();
	}

	public function view()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";
		
		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/contents/view/',
		    'total_items' => $this->contents_model->get_content_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;

		$this->pager->set_config($config);
		$this->template->contents = $this->contents_model->get_contents($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('contents/list');
	}

	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				// insert the new content
				$data = array(
					'parent_content_id' => 0,
					'user_id' => 0,
					'title' => set_value('title'),
					'slug' => set_value('slug'),
					'body' => $this->form_validation->set_value('body'),
					'thumb' => set_value('thumb'),
					'excerpt' => set_value('excerpt'),
					'is_active' => set_value('is_active'),
					'path' => '',
					'content_type' => set_value('content_type')
				);
				$this->contents_model->insert_content($data);
				
				$insert_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_contents_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'CONTENTS',
					'table_name' => 'sm_contents',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_contents_log_data);
				
				redirect('/admin/contents');
				return;
			}
		}
		$this->template->view('contents/add');

	}

	public function edit($content_id = 0)
	{
		$content = $this->contents_model->get_content_by_id($content_id);

		if ($_POST and !empty($content))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{

				// insert the new content
				$data = array(
					'parent_content_id' => 0,
					'user_id' => 0,
					'title' => set_value('title'),
					'slug' => set_value('slug'),
					'body' => $this->form_validation->set_value('body'),
					'thumb' => set_value('thumb'),
					'excerpt' => set_value('excerpt'),
					'is_active' => set_value('is_active'),
					'path' => '',
					'content_type' => set_value('content_type')
				);

				$this->contents_model->update_content($data, array('content_id' => $content_id));
				
				//logging of action
				$data_before = array(
					'parent_content_id' => $content->parent_content_id,
					'user_id' => $content->user_id,
					'title' => $content->title,
					'slug' => $content->slug,
					'body' => $content->body,
					'thumb' => $content->thumb,
					'excerpt' => $content->excerpt,
					'is_active' => $content->is_active,
					'path' => $content->path,
					'content_type' => $content->content_type
				);
				
				$details_before = array('id' => $content_id, 'details' => $data_before);
				$details_before = json_encode($details_before);
				
				$details_after = array('id' => $content_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$update_contents_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'CONTENTS',
					'table_name' => 'sm_contents',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_contents_log_data);
				
				redirect('/admin/contents');
				return;
			}
		}

		$this->template->contents = $content;
		$this->template->view('contents/edit');

	}

	public function delete($content_id = 0)
	{
		$content = $this->contents_model->get_content_by_id($content_id);

		if ($_POST and !empty($content))
		{
			$_content_id = $this->input->post('content_id');
			if (!empty($_content_id)) if ($_content_id == $content_id)
			{
				$this->contents_model->delete_content(array('content_id' => $content_id));
				
				//logging of action
				$data_before = array(
					'parent_content_id' => $content->parent_content_id,
					'user_id' => $content->user_id,
					'title' => $content->title,
					'slug' => $content->slug,
					'body' => $content->body,
					'thumb' => $content->thumb,
					'excerpt' => $content->excerpt,
					'is_active' => $content->is_active,
					'path' => $content->path,
					'content_type' => $content->content_type
				);
				
				$details_before = array('id' => $content_id, 'details' => $data_before);
				$details_before = json_encode($details_before);
				$update_contents_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'CONTENTS',
					'table_name' => 'sm_contents',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_contents_log_data);
				
				redirect('/admin/contents');
				return;
			}
		}

		$this->template->contents = $content;
		$this->template->view('contents/delete');

	}

	public function _check_slug($slug)
	{


		$content = $this->contents_model->get_content_by_slug($slug);

		if (!empty($content))
		{
			// get the orig content code
			$orig_slug = trim($this->input->post('orig_slug'));
			if (!empty($orig_slug))
			{
				if ($orig_slug == $slug)
				{
					return TRUE;
				}
			}

			$this->form_validation->set_message('_check_slug', 'Slug "'.$slug.'" already exists!');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

}
