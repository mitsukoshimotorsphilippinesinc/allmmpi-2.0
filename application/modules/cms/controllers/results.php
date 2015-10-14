<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Results extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'result',
			'label' => 'Result',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'member_name',
			'label' => 'Member Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_published',
			'label' => 'Publish Status',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_featured',
			'label' => 'Featured Status',
			'rules' => 'trim|required'
		)
	);

	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('results');
	}

	public function index()
	{
		$this->results();
	}

	public function results()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/cms/results/index/',
		    'total_items' => $this->contents_model->get_result_count(),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->results = $this->contents_model->get_results(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('results/list');
	}

	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				$this->contents_model->update_result(array('is_featured' => 0),array());
				
				// insert the new results
				$data = array(
					'result' => set_value('result'),
					'member_name' => set_value('member_name'),
					'is_published' => set_value('is_published'),
					'is_featured' => set_value('is_featured'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_result($data);
				
				$insert_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_result_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'RESULTS',
					'table_name' => 'sm_results',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_result_log_data);
				
				redirect('/cms/results');
				return;
			}
		}
		$this->template->view('results/add');
	}

	public function edit($result_id = 0)
	{
		$result = $this->contents_model->get_result_by_id($result_id);


		if ($_POST and !empty($result))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{
				$this->contents_model->update_result(array('is_featured' => 0),array());
				// insert the new results
				$data = array(
					'result' => set_value('result'),
					'member_name' => set_value('member_name'),
					'is_published' => set_value('is_published'),
					'is_featured' => set_value('is_featured'),
				);

				$this->contents_model->update_result($data, array('result_id' => $result_id));
				
				//logging of action
				$details_before = array('id' => $result_id, 'details' => array());
				$details_after = array('id' => $result_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($result->$k != $v)
					{
						$details_before['details'][$k] = $result->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_result_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'RESULTS',
					'table_name' => 'sm_results',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_result_log_data);
				
				redirect('/cms/results');
				return;
			}
		}

		$this->template->result = $result;
		$this->template->view('results/edit');

	}

	public function delete($result_id = 0)
	{
		$result = $this->contents_model->get_result_by_id($result_id);

		if ($_POST and !empty($result))
		{
			$_result_id = $this->input->post('result_id');
			if (!empty($_result_id)) if ($_result_id == $result_id)
			{
				$this->contents_model->delete_result(array('result_id' => $result_id));
				
				//logging of action
				$details_before = array('id' => $result_id, 'details' => $result);
				$details_before = json_encode($details_before);
				
				$delete_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'RESULTS',
					'table_name' => 'sm_results',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_announcement_log_data);
				
				redirect('/cms/results');
				return;
			}
		}

		$this->template->result = $result;
		$this->template->view('results/delete');

	}

	public function view($result_id = 0)
	{
		$result = $this->contents_model->get_result_by_id($result_id);

		$this->template->result = $result;
		$this->template->view('results/view');

	}

	public function update_image()
	{
		$filename = $this->input->post('filename');
		$result_id = $this->input->post('result_id');

		$data = array("image_filename"=>$filename);

		$this->contents_model->update_result($data,"result_id = {$result_id}");
		
		//logging of action
		$details_before = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		$details_after = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		
		$details_before = json_encode($details_before);
		$details_after = json_encode($details_after);
		$update_result_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'RESULTS',
			'table_name' => 'sm_results',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_result_log_data);
		
		$this->return_json('ok','');
	}
}