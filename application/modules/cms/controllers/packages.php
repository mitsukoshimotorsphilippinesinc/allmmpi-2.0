<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Packages extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'title',
			'label' => 'Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'body',
			'label' => 'Body',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'image_filename',
			'label' => 'Teaser Image',
			'rules' => 'trim'
		),
		array(
			'field' => 'bg_color',
			'label' => 'Background Color',
			'rules' => 'trim'
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
		$this->set_navigation('packages');
	}

	public function index()
	{
		$this->packages();
	}
	
	public function packages()
	{
		$where = "`type` = 'package'";
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/cms/packages/index",
		    'total_items' => $this->contents_model->get_featured_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->packages = $this->contents_model->get_featured($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('packages/list');
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
				
				// insert the new packages
				$data = array(
					'type' => 'package',
					'title' => $this->form_validation->set_value('title'),
					'body' => $this->form_validation->set_value('body'),
					'bg_color' => $this->form_validation->set_value('bg_color'),
					'is_published' => set_value('is_published'),
					'image_filename' => set_value('image_filename'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_featured($data);
				
				$featured_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $featured_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_package_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PACKAGES',
					'table_name' => 'sm_featured',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_package_log_data);
				
				//update image if not empty
				if(!empty($path))
				{
					$data = array(
						'image_filename' => "package_type_{$featured_id}.jpg"
					);
					$this->contents_model->update_featured($data,array("featured_id" => $featured_id));
					
					//logging of action
					$details_before = array('id' => $featured_id, 'details' => array('image_filename' => ""));
					$details_after = array('id' => $featured_id, 'details' => $data);
					$details_before = json_encode($details_before);
					$details_after = json_encode($details_after);
					$update_package_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PACKAGES',
						'table_name' => 'sm_featured',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $update_package_log_data);
					
					$location = $this->config->item("media_url") . "/package_types";
					$location = substr($location,1,strlen($location));
					$fullpath = FCPATH . $location . "/package_type_{$featured_id}.jpg";
					
					if(is_file($path)) 
					{
						$_ret = copy($path, $fullpath);
						if ($_ret) unlink($path);
					}
					
				}
				redirect('/cms/packages');
				return;
			}
		}
		
		$this->template->view('packages/add');
	}
	
	public function edit($featured_id = 0)
	{
		$package = $this->contents_model->get_featured_by_id($featured_id);
		
		if ($_POST and !empty($package))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{

				// insert the new packages
				$data = array(
					'type' => 'package',
					'title' => set_value('title'),
					'body' => $this->form_validation->set_value('body'),
					'bg_color' => $this->form_validation->set_value('bg_color'),
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
					if($package->$k != $v)
					{
						$details_before['details'][$k] = $package->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_news_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PACKAGES',
					'table_name' => 'sm_featured',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_news_log_data);

				redirect('/cms/packages');
				return;
			}
		}
		
		$this->template->package = $package;
		$this->template->view('packages/edit');

	}
	
	public function delete($featured_id = 0)
	{
		$package = $this->contents_model->get_featured_by_id($featured_id);

		if ($_POST and !empty($package))
		{
			$_featured_id = $this->input->post('featured_id');
			if (!empty($_featured_id)) if ($_featured_id == $featured_id)
			{
				$this->contents_model->delete_featured(array('featured_id' => $featured_id));
				
				//logging of action
				$details_before = array('id' => $featured_id, 'details' => $package);
				$details_before = json_encode($details_before);
				
				$delete_package_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PACKAGES',
					'table_name' => 'sm_featured',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_package_log_data);
				
				redirect('/cms/packages');
				return;
			}
		}
		
		
		$this->template->package = $package;
		$this->template->view('packages/delete');

	}
	
	public function view($featured_id = 0)
	{
		$package = $this->contents_model->get_featured_by_id($featured_id);

		$this->template->package = $package;
		$this->template->view('packages/view');

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
			$location = $this->config->item("media_url") . "/package_types";
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