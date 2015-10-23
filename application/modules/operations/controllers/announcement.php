<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcement extends Admin_Controller
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

		$this->load->model('asset_model');
		// load pager library
		$this->load->library('pager');
		$this->load->helper('breadcrumb_helper');
	}

	public function index()
	{
		$this->announcement();
	}
	
	public function announcement()
	{

		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/operations/announcement/index",
		    'total_items' => $this->asset_model->get_announcement_count(),
		    'per_page' => 10,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);
		$this->template->announcements = $this->asset_model->get_announcement(NULL, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp");
		$this->template->view('announcement/list');
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
				//	$published = $this->asset_model->get_announcements(array("is_published" => 1));
				//	$this->asset_model->update_announcements(array("is_published" => 0), array("is_published" => 1));
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
				$this->asset_model->insert_announcement($data);
				
				$insert_id = $this->asset_model->insert_id();
				
				/*//logging of action
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
				*/
				
				redirect('/operations/announcement');
				return;
			}
		}
		
		$this->template->view('announcement/add');
	}
	
	
	public function edit($announcement_id = 0)
	{
		$announcement = $this->asset_model->get_announcement_by_id($announcement_id);
		
		if ($_POST and !empty($announcement))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				$is_published = set_value('is_published');
				//if($is_published == 1)
				//{
				//	$published = $this->asset_model->get_announcements(array("is_published" => 1));
				//	$this->asset_model->update_announcements(array("is_published" => 0), array("is_published" => 1));
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
				
				$this->asset_model->update_announcement($data, array('announcement_id' => $announcement_id));
				
				
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
				/*$details_before = json_encode($details_before);
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
				*/
				
				redirect('/operations/announcement');
				return;
			}
		}
		
		$this->template->announcement = $announcement;
		$this->template->view('announcement/edit');

	}
	
	
	public function delete($announcement_id = 0)
	{
		$announcement = $this->asset_model->get_announcement_by_id($announcement_id);

		if ($_POST and !empty($announcement))
		{
			$_announcement_id = $this->input->post('announcement_id');
			if (!empty($_announcement_id)) if ($_announcement_id == $announcement_id)
			{
				$this->asset_model->delete_announcement(array('announcement_id' => $announcement_id));
				
				/*//logging of action
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

				$this->tracking_model->insert_logs('admin', $delete_announcement_log_data);*/
				
				redirect('/operations/announcement');
				return;
			}
		}
		
		
		$this->template->announcement = $announcement;
		$this->template->view('announcement/delete');

	}
	
	public function view($announcement_id = 0)
	{
		$announcement = $this->asset_model->get_announcement_by_id($announcement_id);

		$this->template->announcement = $announcement;
		$this->template->view('announcement/view');

	}

	public function comments()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/operations/announcement/index",
		    'total_items' => $this->asset_model->get_announcement_count(),
		    'per_page' => 10,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);
		$this->template->announcements = $this->asset_model->get_announcement(NULL, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp");
		$this->template->view('announcement/comments');
	}

	public function view_with_comments($announcement_id = 0)
	{
		$announcement = $this->asset_model->get_announcement_by_id($announcement_id);

		$config = array(
		    'pagination_url' => "/operations/announcement/view_with_comments",
		    'total_items' => $this->asset_model->get_announcement_count(),
		    'per_page' => 10,
		    'uri_segment' => 4
		);
		
		$this->pager->set_config($config);
		$this->template->comments = $this->asset_model->get_announcement_message("announcement_id = " . $announcement_id, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp");
		$this->template->announcement = $announcement;		
		$this->template->view('announcement/view_with_comments');		
	}

	public function delete_comment()
	{
		$announcement_message_id = $this->input->post("announcement_message_id");

		$setting_details = $this->setting_model->get_setting_by_slug("announcement_comment_message_removal");
		$setting_details->value;

		// update
		$data = array(
				"is_removed" => 1
			);
		$where = "announcement_message_id = " . $announcement_message_id;
		$this->asset_model->update_announcement_message($data, $where);

		$html = "You have successfully removed the comment message.";

		$this->return_json("1", "Delete Comment", array("html" => $html));

	}

	public function reply_comment()
	{
		$announcement_message_id = $this->input->post("announcement_message_id");

		$html = "<p>Please enter your Admin comment here:<br/><br/>
					<label><strong>ADMIN:</strong></label>
					<textarea style='width:400px;' id='admin_comment_box'></textarea>
					<br/>
					<label id='admin_comment_box-error' style='color:red;display:none;'>Error! Comment message should not be blank.</label>
				</p>";

		$this->return_json("1", "Reply Comment", array("html" => $html));

	}

	public function reply_comment_proceed()
	{
		$announcement_message_id = abs($this->input->post("announcement_message_id"));
		$admin_message = trim($this->input->post("admin_message"));
		$announcement_id = abs($this->input->post("announcement_id"));

		$announcement_message_details = $this->asset_model->get_announcement_message_by_id($announcement_message_id);

		$data = array(
				"from_id_number" => 'n/a',
				"to_id_number" =>  $announcement_message_details->from_id_number,
				"message" => $admin_message,
				"announcement_id" => $announcement_id
			);

		$this->asset_model->insert_announcement_message($data);

		$html = "You have successfully posted an Admin comment.";

		$this->return_json("1", "Proceed Admin Comment", array("html" => $html));
	}
}