<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CMS_Controller
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
			'field' => 'start_date',
			'label' => 'Start Date',
			'rules' => 'trim'
		),
		array(
			'field' => 'start_time_hour',
			'label' => 'Start Time',
			'rules' => 'trim'
		),
		array(
			'field' => 'start_time_minute',
			'label' => 'Start Time',
			'rules' => 'trim'
		),
		array(
			'field' => 'end_date',
			'label' => 'End Date',
			'rules' => 'trim'
		),
		array(
			'field' => 'end_time_hour',
			'label' => 'End Time',
			'rules' => 'trim'
		),
		array(
			'field' => 'end_time_minute',
			'label' => 'End Time',
			'rules' => 'trim'
		),
		array(
			'field' => 'body',
			'label' => 'Body',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'excerpt',
			'label' => 'Short Body',
			'rules' => 'trim'
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

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('events');
	}

	public function index()
	{
		$this->event();
	}
	
	public function event()
	{
		$uri_segment = 4;
		$where = "`type` = 'event'";
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/cms/event/index",
		    'total_items' => $this->contents_model->get_featured_count($where),
		    'per_page' => 10,
		    'uri_segment' => $uri_segment,
		);

		$this->pager->set_config($config);
		$this->template->events = $this->contents_model->get_featured($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('event/list');
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
				
				$start_date = "0000-00-00 00:00:00";
				$end_date = "0000-00-00 00:00:00";
				
				$start_date = set_value('start_date')." ".set_value('start_time_hour').":".set_value('start_time_minute').":00";
				$end_date = set_value('end_date')." ".set_value('end_time_hour').":".set_value('end_time_minute').":00";
				
				// insert the new events
				$data = array(
					'type' => 'event',
					'title' => $this->form_validation->set_value('title'),
					'sub_title' => $this->form_validation->set_value('sub_title'),
					'body' => $this->form_validation->set_value('body'),
					'excerpt' => $this->form_validation->set_value('excerpt'),
					'tags' => set_value('tags'),
					'is_published' => set_value('is_published'),
					'start_date' => $start_date,
					'end_date' => $end_date,
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_featured($data);
				
				$featured_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $featured_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_event_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'EVENTS',
					'table_name' => 'sm_featured',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_event_log_data);
				
				//update image if not empty
				if(!empty($path) && is_file($path))
				{
					$data = array(
						'image_filename' => "event_{$featured_id}.jpg"
					);
					$this->contents_model->update_featured($data,array("featured_id" => $featured_id));
					
					//logging of action
					$details_before = array('id' => $featured_id, 'details' => array('image_filename' => ""));
					$details_after = array('id' => $featured_id, 'details' => $data);
					$details_before = json_encode($details_before);
					$details_after = json_encode($details_after);
					$update_event_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'EVENTS',
						'table_name' => 'sm_featured',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $update_event_log_data);
					
					$location = $this->config->item("media_url") . "/events";
					$location = substr($location,1,strlen($location));
					$fullpath = FCPATH . $location . "/event_{$featured_id}.jpg";
										
					$_ret = copy($path, $fullpath);
					if ($_ret) unlink($path);
				}
				
				redirect('/cms/event');
				return;
			}
		}
		
		$_minutes = array('' => 'Minute');
		for ($i = 0; $i <= 5; $i++)
			for ($j = 0; $j <= 9; $j++)
				$_minutes[$i.$j] = $i.$j;
		
		$_hours = array('' => 'Hour');
		for ($i = 0; $i <= 2; $i++)
		{
			for ($j = 0; $j <= 9; $j++)
			{	
				$_hours[$i.$j] = $i.$j;
				if($i == 2 && $j == 3) break;
			}
		}
		$_days = array('0' => 'Day');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => 'Year');
		$_today = getdate();
		for ($i = $_today['year'] + 3; $i >= $_today['year'] - 1; $i--)
			$_years[$i] = $i;
		
		
		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->hours = $_hours;
		$this->template->minutes = $_minutes;
		$this->template->view('event/add');
	}
	
	public function edit($featured_id = 0)
	{
		$event = $this->contents_model->get_featured_by_id($featured_id);
		
		if ($_POST and !empty($event))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				
				$start_date = "0000-00-00 00:00:00";
				$end_date = "0000-00-00 00:00:00";
				
				$start_date = set_value('start_date')." ".set_value('start_time_hour').":".set_value('start_time_minute').":00";
				$end_date = set_value('end_date')." ".set_value('end_time_hour').":".set_value('end_time_minute').":00";
				
				
				// insert the new events
				$data = array(
					'title' => set_value('title'),
					'sub_title' => set_value('sub_title'),
					'body' => $this->form_validation->set_value('body'),
					'excerpt' => set_value('excerpt'),
					'tags' => set_value('tags'),
					'is_published' => set_value('is_published'),
					'image_filename' => set_value('image_filename'),
					'start_date' => $start_date,
					'end_date' => $end_date,
					'user_id' => $this->user->user_id,
					'update_timestamp' => date("Y-m-d H:i:s")
				);
				
				$this->contents_model->update_featured($data, array('featured_id' => $featured_id));
				
				//logging of action
				$details_before = array('id' => $featured_id, 'details' => array());
				$details_after = array('id' => $featured_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($event->$k != $v)
					{
						$details_before['details'][$k] = $event->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_event_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'EVENTS',
					'table_name' => 'sm_featured',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_event_log_data);
				
				redirect('/cms/event');
				return;
			}
		}

		$_minutes = array('' => 'Minute');
		for ($i = 0; $i <= 5; $i++)
			for ($j = 0; $j <= 9; $j++)
				$_minutes[$i.$j] = $i.$j;
		
		$_hours = array('' => 'Hour');
		for ($i = 0; $i <= 2; $i++)
		{
			for ($j = 0; $j <= 9; $j++)
			{	
				$_hours[$i.$j] = $i.$j;
				if($i == 2 && $j == 3) break;
			}
		}
		$_days = array('0' => 'Day');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => 'Year');
		$_today = getdate();
		for ($i = $_today['year'] + 3; $i >= $_today['year'] - 1; $i--)
			$_years[$i] = $i;
		
		
		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->hours = $_hours;
		$this->template->minutes = $_minutes;
		$this->template->event = $event;
		$this->template->view('event/edit');

	}
	
	public function delete($featured_id = 0)
	{
		$event = $this->contents_model->get_featured_by_id($featured_id);

		if ($_POST and !empty($event))
		{
			$_featured_id = $this->input->post('featured_id');
			if (!empty($_featured_id)) if ($_featured_id == $featured_id)
			{
				$this->contents_model->delete_featured(array('featured_id' => $featured_id));
				
				//logging of action
				$details_before = array('id' => $featured_id, 'details' => $event);
				$details_before = json_encode($details_before);
				
				$delete_event_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'EVENTS',
					'table_name' => 'sm_featured',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_event_log_data);
				
				redirect('/cms/event');
				return;
			}
		}
		
		
		$this->template->event = $event;
		$this->template->view('event/delete');

	}
	
	public function view($featured_id = 0)
	{
		$event = $this->contents_model->get_featured_by_id($featured_id);

		$this->template->event = $event;
		$this->template->view('event/view');

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
			$location = $this->config->item("media_url") . "/events";
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