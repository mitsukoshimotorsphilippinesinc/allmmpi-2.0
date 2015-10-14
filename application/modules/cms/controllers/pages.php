<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'title',
			'label' => 'Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'slug',
			'label' => 'Slug',
			'rules' => 'trim|required|callback__check_slug'
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
		$this->set_navigation('pages');
	}

	public function index()
	{
		$this->pages();
	}

	public function pages()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/cms/pages/index/',
		    'total_items' => $this->contents_model->get_content_count(array('content_type' => 'page')),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->pages = $this->contents_model->get_contents(array('content_type' => 'page'), array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('pages/list');
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
					'user_id' => $this->user->user_id,
					'title' => set_value('title'),
					'slug' => set_value('slug'),
					'body' => $this->form_validation->set_value('body'),
					'thumb' => set_value('thumb'),
					'excerpt' => $this->form_validation->set_value('excerpt'),
					'is_active' => set_value('is_active'),
					'path' => '',
					'content_type' => 'page'
				);
				$this->contents_model->insert_content($data);
				
				$insert_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_page_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAGES',
					'table_name' => 'sm_contents',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_page_log_data);
				
				redirect('/cms/pages');
				return;
			}
		}
		$this->template->view('pages/add');

	}

	public function edit($content_id = 0)
	{
		$page = $this->contents_model->get_content_by_id($content_id);

		if ($_POST and !empty($page))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{

				// insert the new content
				$data = array(
					'parent_content_id' => 0,
					'user_id' => $this->user->user_id,
					'title' => set_value('title'),
					'slug' => set_value('slug'),
					'body' => $this->form_validation->set_value('body'),
					'thumb' => set_value('thumb'),
					'excerpt' => $this->form_validation->set_value('excerpt'),
					'is_active' => set_value('is_active'),
					'path' => '',
					'content_type' => 'page'
				);

				$this->contents_model->update_content($data, array('content_id' => $content_id));
				
				//logging of action
				$details_before = array('id' => $content_id, 'details' => array());
				$details_after = array('id' => $content_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($page->$k != $v)
					{
						$details_before['details'][$k] = $page->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAGES',
					'table_name' => 'sm_contents',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_announcement_log_data);
				
				redirect('/cms/pages');
				return;
			}
		}

		$this->template->page = $page;
		$this->template->view('pages/edit');

	}

	public function delete($content_id = 0)
	{
		$page = $this->contents_model->get_content_by_id($content_id);

		if ($_POST and !empty($page))
		{
			$_content_id = $this->input->post('content_id');
			if (!empty($_content_id)) if ($_content_id == $content_id)
			{
				$this->contents_model->delete_content(array('content_id' => $content_id));
				
				//logging of action
				$details_before = array('id' => $content_id, 'details' => $page);
				$details_before = json_encode($details_before);
				
				$delete_page_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAGES',
					'table_name' => 'sm_contents',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_page_log_data);
				
				redirect('/cms/pages');
				return;
			}
		}

		$this->template->page = $page;
		$this->template->view('pages/delete');

	}

	public function view($content_id = 0)
	{
		$page = $this->contents_model->get_content_by_id($content_id);

		$this->template->page = $page;
		$this->template->view('pages/view');

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
