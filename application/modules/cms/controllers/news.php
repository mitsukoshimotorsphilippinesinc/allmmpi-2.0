<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'title',
			'label' => 'Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'sub_title',
			'label' => 'Sub Title',
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
			'rules' => 'trim|required'
		),
		array(
			'field' => 'image_filename',
			'label' => 'Teaser Image',
			'rules' => 'trim'
		),
		array(
			'field' => 'tags',
			'label' => 'Tags',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_published',
			'label' => 'Publication Status',
			'rules' => 'trim|required'
		)
	);
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('news');
	}

	public function index()
	{
		$this->news();
	}
	
	public function news()
	{
		$uri_segment = 4;
		$filter = "all";
		$where = "`type` = 'news'";
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/cms/news/index",
		    'total_items' => $this->contents_model->get_featured_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->news = $this->contents_model->get_featured($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('news/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				
				$image_filename = set_value('image_filename');
				$path = FCPATH . "assets/uploads/{$image_filename}.jpg";
				
				// insert the new news
				$data = array(
					'type' => 'news',
					'title' => $this->form_validation->set_value('title'),
					'sub_title' => $this->form_validation->set_value('sub_title'),
					'body' => $this->form_validation->set_value('body'),
					'excerpt' => $this->form_validation->set_value('excerpt'),
					'tags' => set_value('tags'),
					'is_published' => set_value('is_published'),
					'image_filename' => set_value('image_filename'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_featured($data);
				
				$featured_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $featured_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_news_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'NEWS',
					'table_name' => 'sm_featured',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_news_log_data);
				
				//update image if not empty
				if(!empty($image_filename))
				{
					$data = array(
						'image_filename' => "news_{$featured_id}.jpg"
					);
					$this->contents_model->update_featured($data,array("featured_id" => $featured_id));
					
					//logging of action
					$details_before = array('id' => $featured_id, 'details' => array('image_filename' => ""));
					$details_after = array('id' => $featured_id, 'details' => $data);
					$details_before = json_encode($details_before);
					$details_after = json_encode($details_after);
					$update_event_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'NEWS',
						'table_name' => 'sm_featured',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $update_event_log_data);
					
					$location = $this->config->item("media_url") . "/news";
					$location = substr($location,1,strlen($location));
					$fullpath = FCPATH . $location . "/news_{$featured_id}.jpg";
										
					$_ret = copy($path, $fullpath);
					if ($_ret) unlink($path);
				}
				redirect('/cms/news');
				return;
			}
		}
		
		$this->template->view('news/add');
	}
	
	public function edit($featured_id = 0)
	{
		$news = $this->contents_model->get_featured_by_id($featured_id);
		
		if ($_POST and !empty($news))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{

				// insert the new news
				$data = array(
					'type' => 'news',
					'title' => set_value('title'),
					'sub_title' => set_value('sub_title'),
					'body' => $this->form_validation->set_value('body'),
					'excerpt' => set_value('excerpt'),
					'tags' => set_value('tags'),
					'is_published' => set_value('is_published'),
					'image_filename' => set_value('image_filename'),
					'user_id' => $this->user->user_id,
					'update_timestamp' => date("Y-m-d H:i:s")
				);
				
				$this->contents_model->update_featured($data, array('featured_id' => $featured_id));
				
				//logging of action
				$details_before = array('id' => $featured_id, 'details' => array());
				$details_after = array('id' => $featured_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($news->$k != $v)
					{
						$details_before['details'][$k] = $news->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_news_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'NEWS',
					'table_name' => 'sm_featured',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_news_log_data);
				
				redirect('/cms/news');
				return;
			}
		}
		
		$this->template->news = $news;
		$this->template->view('news/edit');

	}
	
	public function delete($featured_id = 0)
	{
		$news = $this->contents_model->get_featured_by_id($featured_id);

		if ($_POST and !empty($news))
		{
			$_featured_id = $this->input->post('featured_id');
			if (!empty($_featured_id)) if ($_featured_id == $featured_id)
			{
				$this->contents_model->delete_featured(array('featured_id' => $featured_id));
				
				//logging of action
				$details_before = array('id' => $featured_id, 'details' => $news);
				$details_before = json_encode($details_before);
				
				$delete_news_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'NEWS',
					'table_name' => 'sm_featured',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_news_log_data);
				
				redirect('/cms/news');
				return;
			}
		}
		
		
		$this->template->news = $news;
		$this->template->view('news/delete');

	}
	
	public function view($featured_id = 0)
	{
		$news = $this->contents_model->get_featured_by_id($featured_id);

		$this->template->news = $news;
		$this->template->view('news/view');

	}
	
	public function rmv_temp_img()
	{
		$image_filename = $this->input->post("image_filename");
		$action = $this->input->post("action");
		
		$path = "";
		
		if($action == "add")
		{
			$path = FCPATH . "assets/uploads/{$image_filename}.jpg";
		}
		else if($action == "edit")
		{
			$location = $this->config->item("media_url") . "/news";
			$location = substr($location,1,strlen($location));
			
			$path = FCPATH . $location . "/{$image_filename}";
		}
		if(!is_file($path))
		{
			$this->return_json("error","There was an error in your request. The file you wanted to remove either does not exist or is not a file.");
			return;
		}
		
		unlink($path);
		
		$this->return_json("ok","Teaser image removed");
		return;
	}
}