<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Warehouse extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
	
		$this->load->model('spare_parts_model');
		$this->load->model('human_relations_model');
		$this->load->model('warehouse_model');
		$this->load->library('pager');		
		$this->load->helper("spare_parts_helper");
		$this->load->helper("breadcrumb_helper");

		$this->db_spare_parts = $this->load->database('spare_parts', TRUE);

	}

	public $segment_name = "warehouse";

	public function index()
	{		
		$this->template->view('warehouse/dashboard');
	}

	public function reservation()
	{

		
		//var_dump($this->user);

		$where = "status = 'PENDING'";
		$reservations = $this->spare_parts_model->get_warehouse_reservation($where);

		var_dump($reservations);

		$this->template->view('warehouse/reservation_listing');	
	}

}	