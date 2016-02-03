<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ldap extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
	
		$this->load->model('information_technology_model');
		$this->load->model('human_relations_model');		
		$this->load->library('pager');				
		$this->load->helper("breadcrumb_helper");
		$this->load->helper("systems_helper");

		$this->db_information_technology = $this->load->database('information_technology', TRUE);

	}

	public $segment_name = "ldap";

	public function index()
	{		
		$this->template->view('ldap/dashboard');
	}

	public function add() 
	{
		$this->template->view('ldap/add');	
	}
}