<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faqs extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'question',
			'label' => 'Question',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'body',
			'label' => 'body',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_published',
			'label' => 'Publish Status',
			'rules' => 'trim|required'
		),
	);
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('faqs');
	}

	public function index()
	{
		$this->faqs();
	}
	
	public function faqs()
	{
		$this->template->max_order = $this->contents_model->get_faqs_count();
		$this->template->faqs = $this->contents_model->get_faqs(null,null,'ordering ASC');
		$this->template->view('faqs/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				$ordering = $this->contents_model->get_faqs_count();
				
				// insert the new faqs
				$data = array(
					'question' => set_value('question'),
					'body' => $this->form_validation->set_value('body'),
					'ordering' => $ordering + 1,
					'is_published' => set_value('is_published'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_faqs($data);
				
				$faqs_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $faqs_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_faq_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FAQS',
					'table_name' => 'sm_faqs',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_faq_log_data);
				
				redirect('/cms/faqs');
				return;
			}
		}
		
		$this->template->view('faqs/add');
	}
	
	public function edit($faqs_id = 0)
	{
		$faq = $this->contents_model->get_faq_by_id($faqs_id);
		
		
		if ($_POST and !empty($faq))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				
				// update the faqs
				$data = array(
					'question' => set_value('question'),
					'body' => $this->form_validation->set_value('body'),
					'is_published' => set_value('is_published'),
				);

				$this->contents_model->update_faqs($data, array('faqs_id' => $faqs_id));
				
				//logging of action
				$details_before = array('id' => $faqs_id, 'details' => array());
				$details_after = array('id' => $faqs_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($faq->$k != $v)
					{
						$details_before['details'][$k] = $faq->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_faq_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FAQS',
					'table_name' => 'sm_faqs',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_faq_log_data);
				
				redirect('/cms/faqs');
				return;
			}
		}
		
		$this->template->faq = $faq;
		$this->template->view('faqs/edit');

	}
	
	public function delete($faqs_id = 0)
	{
		$faq = $this->contents_model->get_faq_by_id($faqs_id);

		if ($_POST and !empty($faq))
		{
			$_faqs_id = $this->input->post('faqs_id');
			if (!empty($_faqs_id)) if ($_faqs_id == $faqs_id)
			{
				
				$max_order = $this->contents_model->get_faqs_count();
				
				if($faq->ordering != $max_order)
				{
					$other_faq = $this->contents_model->get_faqs(array("ordering" => $faq->ordering + 1));					
					
					//update the navigations after this entry
					while(!empty($other_faq))
					{
						$other_faq = $other_faq[0];
						
						$data = array(
							'ordering' => $other_faq->ordering - 1,
						);
						$this->contents_model->update_faqs($data, array('faqs_id' => $other_faq->faqs_id));

						$details_before = array('id' => $other_faq->faqs_id, 'details' => array('ordering' => $other_faq->ordering));
						$details_before = json_encode($details_before);

						$details_after = array('id' => $other_faq->faqs_id, 'details' => $data);
						$details_after = json_encode($details_after);
						$update_faq_log_data = array(
							'user_id' => $this->user->user_id,
							'module_name' => 'FAQS',
							'table_name' => 'sm_faqs',
							'action' => 'UPDATE',
							'details_before' => $details_before,
							'details_after' => $details_after,
							'remarks' => "",
						);

						$this->tracking_model->insert_logs('admin', $update_faq_log_data);

						$other_faq = $this->contents_model->get_faqs(array("ordering" => $other_faq->ordering + 1));
					}
				}
				
				$this->contents_model->delete_faqs(array('faqs_id' => $faqs_id));
				
				//logging of action
				$details_before = array('id' => $faqs_id, 'details' => $faq);
				$details_before = json_encode($details_before);
				
				$delete_faq_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FAQS',
					'table_name' => 'sm_faqs',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_faq_log_data);
				
				redirect('/cms/faqs');
				return;
			}
		}

		$this->template->faq = $faq;
		$this->template->view('faqs/delete');

	}
	
	public function view($faqs_id = 0)
	{
		$faq = $this->contents_model->get_faq_by_id($faqs_id);

		$this->template->faq = $faq;
		$this->template->view('faqs/view');

	}
	
	public function order($direction = "", $faqs_id = 0)
	{
		$faq = $this->contents_model->get_faq_by_id($faqs_id);
		
		if(($direction == "up" || $direction == "down") && !empty($faq))
		{
			$max_order = $this->contents_model->get_faqs_count();

			// get switch item with
			$_other_order = 0;
			if ($direction == 'up' && $faq->ordering > 1)
				$_other_order = $faq->ordering - 1;

			if ($direction == 'down' && $faq->ordering < $max_order)
				$_other_order = $faq->ordering + 1;

			if ($_other_order > 0)
			{
				$_other_faq = $this->contents_model->get_faq_by_ordering($_other_order);
				$this->contents_model->update_faqs(array('ordering' => $_other_faq->ordering), array('faqs_id' => $faq->faqs_id));
				
				//logging of action
				$details_before = array('id' =>  $faq->faqs_id, 'details' => array("ordering" => $faq->ordering));
				$details_after = array('id' =>  $faq->faqs_id, 'details' => array("ordering" => $_other_faq->ordering));

				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_faq_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FAQS',
					'table_name' => 'sm_faqs',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_faq_log_data);
				
				$this->contents_model->update_faqs(array('ordering' => $faq->ordering), array('faqs_id' => $_other_faq->faqs_id));
				
				//logging of action
				$details_before = array('id' =>  $_other_faq->faqs_id, 'details' => array("ordering" => $_other_faq->ordering));
				$details_after = array('id' =>  $_other_faq->faqs_id, 'details' => array("ordering" => $faq->ordering));

				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_faq_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FAQS',
					'table_name' => 'sm_faqs',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_faq_log_data);
			}
		}
		
		redirect('/cms/faqs');
		return;
	}
}