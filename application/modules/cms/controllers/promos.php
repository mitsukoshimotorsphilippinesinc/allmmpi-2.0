<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Promos extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'promo_title',
			'label' => 'Promo Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'promo_description',
			'label' => 'Promo Description',
			'rules' => 'trim'
		),
		array(
			'field' => 'promo_text',
			'label' => 'Promo Text',
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
			'label' => 'Active Period',
			'rules' => 'trim'
		),
		array(
			'field' => 'active_start',
			'label' => 'Start Date',
			'rules' => 'trim'
		),
		array(
			'field' => 'active_end',
			'label' => 'End Date',
			'rules' => 'trim'
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
		$this->set_navigation('promos');
	}

	public function index()
	{
		$this->promos();
	}
	
	public function promos()
	{
		$this->template->max_order = $this->contents_model->get_featured_promos_count();
		$this->template->promos = $this->contents_model->get_featured_promos(null,null,'ordering ASC');
		$this->template->view('promos/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				// insert the new promos
				$data = array(
					'promo_title' => set_value('promo_title'),
					'promo_description' => $this->form_validation->set_value('promo_description'),
					'promo_text' => $this->form_validation->set_value('promo_text'),
					'url' => set_value('url'),
					'is_active' => set_value('is_active'),
					'with_active_period' => set_value('with_active_period'),
					'active_start' => set_value('active_start'),
					'active_end' => set_value('active_end'),
					'user_id' => $this->user->user_id
				);
				$this->contents_model->insert_featured_promos($data);
				
				$promo_id = $this->contents_model->insert_id();
				
				$ordering = $this->contents_model->get_featured_promos_count();
				
				$this->contents_model->update_featured_promos(array("ordering"=>$ordering),array("promo_id" => $promo_id));
				
				redirect('/cms/promos');
				return;
			}
		}
		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year'] + 5; $i >= $_today['year'] - 60; $i--)
			$_years[$i] = $i;
		
		
		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->view('promos/add');
	}
	
	public function edit($promo_id = 0)
	{
		$promo = $this->contents_model->get_featured_promo_by_id($promo_id);
		
		
		if ($_POST and !empty($promo))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run())
			{
				
				// update the promo
				$data = array(
					'promo_title' => set_value('promo_title'),
					'promo_description' => $this->form_validation->set_value('promo_description'),
					'promo_text' => $this->form_validation->set_value('promo_text'),
					'url' => set_value('url'),
					'is_active' => set_value('is_active'),
					'with_active_period' => set_value('with_active_period'),
					'active_start' => set_value('active_start'),
					'active_end' => set_value('active_end'),
				);

				$this->contents_model->update_featured_promos($data, array('promo_id' => $promo_id));

				redirect('/cms/promos');
				return;
			}
		}
		
		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year'] + 5; $i >= $_today['year'] - 60; $i--)
			$_years[$i] = $i;
		
		
		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->promo = $promo;
		$this->template->view('promos/edit');

	}
	
	public function delete($promo_id = 0)
	{
		$promo = $this->contents_model->get_featured_promo_by_id($promo_id);

		if ($_POST and !empty($promo))
		{
			$_promo_id = $this->input->post('promo_id');
			if (!empty($_promo_id)) if ($_promo_id == $promo_id)
			{					
				$max_order = $this->contents_model->get_featured_promos_count();
				
				if($promo->ordering != $max_order)
				{
					$_other_promo = $this->contents_model->get_featured_promo_by_ordering($promo->ordering+1);
					//update the navigations after this entry
					while(!empty($_other_promo))
					{
						$data = array(
								'ordering' => $_other_promo->ordering - 1,
						);
						$this->contents_model->update_featured_promos($data, array('promo_id' => $_other_promo->promo_id));
						$_other_promo = $this->contents_model->get_featured_promo_by_ordering($_other_promo->ordering+1);
					}
				}

				$this->contents_model->delete_featured_promos(array('promo_id' => $promo_id));
				redirect('/cms/promos');
				return;
			}
		}

		$this->template->promo = $promo;
		$this->template->view('promos/delete');

	}
	
	public function view($promo_id = 0)
	{
		$promo = $this->contents_model->get_featured_promo_by_id($promo_id);

		
		$this->template->promo = $promo;
		$this->template->view('promos/view');

	}
	
	public function order($direction = "", $promo_id = 0)
	{
		$promo = $this->contents_model->get_featured_promo_by_id($promo_id);
		
		if(($direction == "up" || $direction == "down") && !empty($promo))
		{
			$max_order = $this->contents_model->get_featured_promos_count();

			// get switch item with
			$_other_order = 0;
			if ($direction == 'up' && $promo->ordering > 1)
				$_other_order = $promo->ordering - 1;

			if ($direction == 'down' && $promo->ordering < $max_order)
				$_other_order = $promo->ordering + 1;

			if ($_other_order > 0)
			{
				$_other_promo = $this->contents_model->get_featured_promo_by_ordering($_other_order);
				$this->contents_model->update_featured_promos(array('ordering' => $_other_promo->ordering), array('promo_id' => $promo->promo_id));
				$this->contents_model->update_featured_promos(array('ordering' => $promo->ordering), array('promo_id' => $_other_promo->promo_id));
			}
		}
		
		redirect('/cms/promos');
		return;
	}

	public function update_image()
	{
		$filename = $this->input->post('filename');
		$promo_id = $this->input->post('promo_id');

		$data = array("image_filename"=>$filename);

		$this->contents_model->update_featured_promos($data,"promo_id = {$promo_id}");

		$this->return_json('ok','');
	}
}