<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testimonials extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'member_name',
			'label' => 'Member Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'member_details',
			'label' => 'Member Details',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'body',
			'label' => 'Body',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_approved',
			'label' => 'Approval Status',
			'rules' => 'trim|required'
		)
	);
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('testimonials');
	}

	public function index()
	{
		$this->testimonials();
	}
	
	public function testimonials()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/cms/testimonials/index/',
		    'total_items' => $this->contents_model->get_testimonial_count(),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->testimonials = $this->contents_model->get_testimonials(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('testimonials/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				// insert the new testimonials
				$data = array(
					'member_name' => set_value('member_name'),
					'member_details' => set_value('member_details'),
					'body' => $this->form_validation->set_value('body'),
					'is_approved' => set_value('is_approved'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_testimonial($data);
				
				$insert_id = $this->contents_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_testimonial_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'TESTIMONIALS',
					'table_name' => 'sm_testimonials',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_testimonial_log_data);
				
				redirect('/cms/testimonials');
				return;
			}
		}
		$this->template->view('testimonials/add');
	}
	
	public function edit($testimonial_id = 0)
	{
		$testimonial = $this->contents_model->get_testimonial_by_id($testimonial_id);
		
		
		if ($_POST and !empty($testimonial))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				
				// insert the new testimonials
				$data = array(
					'member_name' => set_value('member_name'),
					'member_details' => set_value('member_details'),
					'body' => $this->form_validation->set_value('body'),
					'is_approved' => set_value('is_approved'),
					'user_id' => $this->user->user_id
				);

				$this->contents_model->update_testimonial($data, array('testimonial_id' => $testimonial_id));
				
				//logging of action
				$details_before = array('id' => $testimonial_id, 'details' => array());
				$details_after = array('id' => $testimonial_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($testimonial->$k != $v)
					{
						$details_before['details'][$k] = $testimonial->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_testimonial_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'TESTIMONIALS',
					'table_name' => 'sm_testimonials',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_testimonial_log_data);
				
				redirect('/cms/testimonials');
				return;
			}
		}

		$this->template->testimonial = $testimonial;
		$this->template->view('testimonials/edit');

	}
	
	public function delete($testimonial_id = 0)
	{
		$testimonial = $this->contents_model->get_testimonial_by_id($testimonial_id);

		if ($_POST and !empty($testimonial))
		{
			$_testimonial_id = $this->input->post('testimonial_id');
			if (!empty($_testimonial_id)) if ($_testimonial_id == $testimonial_id)
			{
				$this->contents_model->delete_testimonial(array('testimonial_id' => $testimonial_id));
				
				//logging of action
				$details_before = array('id' => $testimonial_id, 'details' => $testimonial);
				$details_before = json_encode($details_before);
				
				$delete_testimonial_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'TESTIMONIAL',
					'table_name' => 'sm_testimonials',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_testimonial_log_data);
				
				redirect('/cms/testimonials');
				return;
			}
		}

		$this->template->testimonial = $testimonial;
		$this->template->view('testimonials/delete');

	}
	
	public function view($testimonial_id = 0)
	{
		$testimonial = $this->contents_model->get_testimonial_by_id($testimonial_id);

		$this->template->testimonial = $testimonial;
		$this->template->view('testimonials/view');

	}
	
	public function update_image()
	{
		$filename = $this->input->post('filename');
		$testimonial_id = $this->input->post('testimonial_id');
		
		$data = array("image_filename"=>$filename);
		
		$this->contents_model->update_testimonial($data,"testimonial_id = {$testimonial_id}");
		
		//logging of action
		$details_before = array('id' => $testimonial_id, 'details' => array("image_filename"=>$filename));
		$details_after = array('id' => $testimonial_id, 'details' => array("image_filename"=>$filename));

		$details_before = json_encode($details_before);
		$details_after = json_encode($details_after);
		$update_testimonial_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'TESTIMONIALS',
			'table_name' => 'sm_testimonials',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_testimonial_log_data);
		
		$this->return_json('ok','');
	}
}