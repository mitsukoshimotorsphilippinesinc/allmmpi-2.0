<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alerts extends CMS_Controller
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
			'field' => 'is_visible',
			'label' => 'Visibility Status',
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
		$this->load->library('pager');
		$this->set_navigation('alert-messages');
	}

	public function index()
	{
		$this->alerts();
	}
	
	public function alerts()
	{

		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/cms/alerts/index",
		    'total_items' => $this->contents_model->get_alert_messages_count(),
		    'per_page' => 10,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);
		$this->template->alerts = $this->contents_model->get_alert_messages(NULL, array(
					'rows' => $this->pager->per_page,
					'offset' => $this->pager->offset));
		$this->template->view('alerts/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				
				$is_visible = set_value('is_visible');
				
				$data = array(
					'title' => set_value('title'),
					'content' => $this->form_validation->set_value('body'),
					'is_visible' => $is_visible
				);
				$start_date = $this->input->post("start_date");
				$end_date = $this->input->post("end_date");
				if(!empty($start_date)) $data['start_timestamp'] = $start_date;
				if(!empty($end_date)) $data['end_timestamp'] = $end_date;
				$this->contents_model->insert_alert_messages($data);
				
				$insert_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_alert_messages_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'ALERT-MESSAGES',
					'table_name' => 'sm_alert_messages',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_alert_messages_log_data);
				
				redirect('/cms/alerts');
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
		
		$this->template->view('alerts/add');
	}
	
	public function edit($message_id = 0)
	{
		$alert_message = $this->contents_model->get_alert_message_by_id($message_id);
		
		if ($_POST and !empty($alert_message))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				$is_visible = set_value('is_visible');
				
				$data = array(
					'title' => set_value('title'),
					'content' => $this->form_validation->set_value('body'),
					'is_visible' => $is_visible
				);
				$start_date = $this->input->post("start_date");
				$end_date = $this->input->post("end_date");
				if(!empty($start_date)) $data['start_timestamp'] = $start_date;
				if(!empty($end_date)) $data['end_timestamp'] = $end_date;
				
				$this->contents_model->update_alert_messages($data, array('message_id' => $message_id));
				
				//logging of action
				$details_before = array('id' => $message_id, 'details' => array());
				$details_after = array('id' => $message_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($alert_message->$k != $v)
					{
						$details_before['details'][$k] = $alert_message->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'ALERT-MESSAGES',
					'table_name' => 'sm_alert_messages',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_announcement_log_data);
				
				redirect('/cms/alerts');
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
		$this->template->alert_message = $alert_message;
		$this->template->view('alerts/edit');

	}
	
	public function delete($message_id = 0)
	{
		$alert_message = $this->contents_model->get_alert_message_by_id($message_id);

		if ($_POST and !empty($alert_message))
		{
			$_message_id = $this->input->post('message_id');
			if (!empty($_message_id)) if ($_message_id == $message_id)
			{
				$this->contents_model->delete_alert_messages(array('message_id' => $message_id));
				
				//logging of action
				$details_before = array('id' => $message_id, 'details' => $alert_message);
				$details_before = json_encode($details_before);
				
				$delete_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'ALERT-MESSAGES',
					'table_name' => 'sm_alert_messages',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_announcement_log_data);
				
				redirect('/cms/alerts');
				return;
			}
		}
		
		
		$this->template->alert_message = $alert_message;
		$this->template->view('alerts/delete');

	}
	
	public function view($message_id = 0)
	{
		$alert_message = $this->contents_model->get_alert_message_by_id($message_id);

		$this->template->alert_message = $alert_message;
		$this->template->view('alerts/view');

	}
}