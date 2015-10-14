<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Advertisements extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'advertisement_title',
			'label' => 'Advertisement Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'advertisement_description',
			'label' => 'Advertisement Description',
			'rules' => 'trim'
		),
		array(
			'field' => 'url',
			'label' => 'URL',
			'rules' => 'trim'
		),
		array(
			'field' => 'is_active',
			'label' => 'Active Status',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'with_active_period',
			'label' => 'With Active Period Status',
			'rules' => 'trim'
		),
		array(
			'field' => 'active_from',
			'label' => 'Active From',
			'rules' => 'trim|callback__check_active_from'
		),
		array(
			'field' => 'active_until',
			'label' => 'Active Until',
			'rules' => 'trim|callback__check_active_until'
		)
	);
	
	private $_months = array(
		'0' => '-',
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
		$this->set_navigation('advertisements');
	}

	public function index()
	{
		$this->advertisements();
	}

	public function advertisements()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/cms/advertisements/index/',
		    'total_items' => $this->contents_model->get_advertisement_count(),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		$this->template->advertisements = $this->contents_model->get_advertisements(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('advertisements/list');
	}

	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				
				// insert the new advertisements
				$data = array(
					'advertisement_title' => set_value('advertisement_title'),
					'advertisement_description' => set_value('advertisement_description'),
					'url' => set_value('url'),
					'is_active' => set_value('is_active'),
					'with_active_period' => set_value('with_active_period'),
					'active_from' => set_value('active_from'),
					'active_until' => set_value('active_until'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_advertisement($data);
				
				$advertisement_id = $this->contents_model->insert_id();
				
				redirect('/cms/advertisements/view/'.$advertisement_id);
				return;
			}
		}
		
		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i <= $_today['year'] + 5; $i++)
			$_years[$i] = $i;
		
		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->view('advertisements/add');
	}

	public function edit($advertisement_id = 0)
	{
		$advertisement = $this->contents_model->get_advertisement_by_id($advertisement_id);

		if ($_POST and !empty($advertisement))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{
				// insert the new advertisements
				$data = array(
					'advertisement_title' => set_value('advertisement_title'),
					'advertisement_description' => set_value('advertisement_description'),
					'url' => set_value('url'),
					'is_active' => set_value('is_active'),
					'with_active_period' => set_value('with_active_period'),
					'active_from' => set_value('active_from'),
					'active_until' => set_value('active_until'),
					'user_id' => $this->user->user_id
				);

				$this->contents_model->update_advertisement($data, array('advertisement_id' => $advertisement_id));

				redirect('/cms/advertisements');
				return;
			}
		}
		
		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i <= $_today['year'] + 5; $i++)
			$_years[$i] = $i;
		
		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->advertisement = $advertisement;
		$this->template->view('advertisements/edit');

	}

	public function delete($advertisement_id = 0)
	{
		$advertisement = $this->contents_model->get_advertisement_by_id($advertisement_id);

		if ($_POST and !empty($advertisement))
		{
			$_advertisement_id = $this->input->post('advertisement_id');
			if (!empty($_advertisement_id)) if ($_advertisement_id == $advertisement_id)
			{
				$this->contents_model->delete_advertisement(array('advertisement_id' => $advertisement_id));
				redirect('/cms/advertisements');
				return;
			}
		}

		$this->template->advertisement = $advertisement;
		$this->template->view('advertisements/delete');

	}

	public function view($advertisement_id = 0)
	{
		$advertisement = $this->contents_model->get_advertisement_by_id($advertisement_id);

		$this->template->advertisement = $advertisement;
		$this->template->view('advertisements/view');

	}

	public function update_image()
	{
		$image_filename = $this->input->post('filename');
		$advertisement_id = $this->input->post('advertisement_id');

		$this->contents_model->update_advertisement(array("image_filename" => $image_filename), array("advertisement_id" => $advertisement_id));

		$this->return_json('ok','Picture Updated');
	}
	
	public function _check_active_from($active_from)
	{
		$with_active_period = $this->input->post('with_active_period');
		
		if($with_active_period)
		{
			if(empty($active_from))
			{
				$this->form_validation->set_message('_check_active_from', 'The Active From Date is required.');
				return false;
			}
		}
		else
		{
			return true;
		}
		
		return true;
	}
	
	public function _check_active_until($active_until)
	{
		$with_active_period = $this->input->post('with_active_period');
		$active_from = $this->input->post('active_from');
		if($with_active_period)
		{
			if(empty($active_until))
			{
				$this->form_validation->set_message('_check_active_until', 'The Active Until Date is required.');
				return false;
			}
			
			if($active_from > $active_until)
			{
				$this->form_validation->set_message('_check_active_until', 'The Active Until Date must not be earlier than the Active From Date.');
				return false;
			}
		}
		else
		{
			return true;
		}
		
		return true;
	}
}