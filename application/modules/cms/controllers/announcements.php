<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcements extends CMS_Controller
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
		$this->set_navigation('announcements');
	}

	public function index()
	{
		$this->announcements();
	}
	
	public function announcements()
	{

		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/cms/announcements/index",
		    'total_items' => $this->contents_model->get_announcements_count(),
		    'per_page' => 10,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);
		$this->template->announcements = $this->contents_model->get_announcements(NULL, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('announcements/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				
				$is_published = set_value('is_published');
				//if($is_published == 1)
				//{
				//	$published = $this->contents_model->get_announcements(array("is_published" => 1));
				//	$this->contents_model->update_announcements(array("is_published" => 0), array("is_published" => 1));
				//	
				//	foreach($published as $p)
				//	{
				//		//logging of action
				//		$details_before = array('id' => $p->announcement_id, 'details' => array("is_published" => 1));
				//		$details_after = array('id' => $p->announcement_id, 'details' => array("is_published" => 0));
                //
				//		$details_before = json_encode($details_before);
				//		$details_after = json_encode($details_after);
				//		$update_announcement_log_data = array(
				//			'user_id' => $this->user->user_id,
				//			'module_name' => 'ANNOUNCEMENTS',
				//			'table_name' => 'sm_announcements',
				//			'action' => 'UPDATE',
				//			'details_before' => $details_before,
				//			'details_after' => $details_after,
				//			'remarks' => "",
				//		);
                //
				//		$this->tracking_model->insert_logs('admin', $update_announcement_log_data);
				//	}
				//}
				
				// insert the new announcements
				$data = array(
					'title' => set_value('title'),
					'body' => $this->form_validation->set_value('body'),
					'is_published' => $is_published,
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_announcements($data);
				
				$insert_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'ANNOUNCEMENTS',
					'table_name' => 'sm_announcements',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_announcement_log_data);
				
				redirect('/cms/announcements');
				return;
			}
		}
		
		$this->template->view('announcements/add');
	}
	
	public function edit($announcement_id = 0)
	{
		$announcement = $this->contents_model->get_announcement_by_id($announcement_id);
		
		if ($_POST and !empty($announcement))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				$is_published = set_value('is_published');
				//if($is_published == 1)
				//{
				//	$published = $this->contents_model->get_announcements(array("is_published" => 1));
				//	$this->contents_model->update_announcements(array("is_published" => 0), array("is_published" => 1));
				//	
				//	foreach($published as $p)
				//	{
				//		//logging of action
				//		$details_before = array('id' => $p->announcement_id, 'details' => array("is_published" => 1));
				//		$details_after = array('id' => $p->announcement_id, 'details' => array("is_published" => 0));
                //
				//		$details_before = json_encode($details_before);
				//		$details_after = json_encode($details_after);
				//		$update_announcement_log_data = array(
				//			'user_id' => $this->user->user_id,
				//			'module_name' => 'ANNOUNCEMENTS',
				//			'table_name' => 'sm_announcements',
				//			'action' => 'UPDATE',
				//			'details_before' => $details_before,
				//			'details_after' => $details_after,
				//			'remarks' => "",
				//		);
                //
				//		$this->tracking_model->insert_logs('admin', $update_announcement_log_data);
				//	}
				//}
				// insert the new announcements
				$data = array(
					'title' => set_value('title'),
					'body' => $this->form_validation->set_value('body'),
					'is_published' => $is_published,
					'user_id' => $this->user->user_id,
					'update_timestamp' => date("Y-m-d H:i:s")
				);
				
				$this->contents_model->update_announcements($data, array('announcement_id' => $announcement_id));
				
				
				//logging of action
				$details_before = array('id' => $announcement_id, 'details' => array());
				$details_after = array('id' => $announcement_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($announcement->$k != $v)
					{
						$details_before['details'][$k] = $announcement->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'ANNOUNCEMENTS',
					'table_name' => 'sm_announcements',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_announcement_log_data);
				
				redirect('/cms/announcements');
				return;
			}
		}
		
		$this->template->announcement = $announcement;
		$this->template->view('announcements/edit');

	}
	
	public function delete($announcement_id = 0)
	{
		$announcement = $this->contents_model->get_announcement_by_id($announcement_id);

		if ($_POST and !empty($announcement))
		{
			$_announcement_id = $this->input->post('announcement_id');
			if (!empty($_announcement_id)) if ($_announcement_id == $announcement_id)
			{
				$this->contents_model->delete_announcements(array('announcement_id' => $announcement_id));
				
				//logging of action
				$details_before = array('id' => $announcement_id, 'details' => $announcement);
				$details_before = json_encode($details_before);
				
				$delete_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'ANNOUNCEMENTS',
					'table_name' => 'sm_announcements',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_announcement_log_data);
				
				redirect('/cms/announcements');
				return;
			}
		}
		
		
		$this->template->announcement = $announcement;
		$this->template->view('announcements/delete');

	}
	
	public function view($announcement_id = 0)
	{
		$announcement = $this->contents_model->get_announcement_by_id($announcement_id);

		$this->template->announcement = $announcement;
		$this->template->view('announcements/view');

	}
}