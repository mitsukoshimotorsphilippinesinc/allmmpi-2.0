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

	public function organization()
	{
		$this->template->view('ldap/organization/dashboard');	
	}

	public function user() 
	{
		$this->template->view('ldap/user/dashboard');		
	}


	public function user_add() 
	{
		$this->template->view('ldap/user/add');	
	}

	public function create_ldap_file()
	{

		$username = trim($this->input->post('username'));
        $fname = $this->input->post('fname');
        $mname = $this->input->post('mname');
        $lname = $this->input->post('lname');
        $email = $this->input->post('email');
        $password = $this->input->post('password');     
        $country_code = $this->input->post('country_code');
        $area_code = $this->input->post('area_code');
        $mobile_number = $this->input->post('mobile_number');
        $gender = $this->input->post('gender');
        $marital_status = $this->input->post('marital_status');
        $nationality = $this->input->post('nationality');
        $tin_number = $this->input->post('tin_number');
        $address = $this->input->post('address');
        $is_ajax = $this->input->post('is_ajax');
        $department_name = $this->input->post('department_name');;

		$myfile = fopen("/tmp/" . $username . ".ldif", "a+") or die("Unable to open file!");
		$txt = "dn: uid=" . $username . ",ou=" . $department_name . ",dc=mitsukoshimotors,dc=com\nchangetype: add\nobjectClass: inetOrgPerson\ncn: " . $fname . $lname . " \nsn: " . $lname . "\nuserPassword: $password\nuid: " . $username;	
		fwrite($myfile, $txt);
		fclose($myfile);

		echo json_encode(array("status"=>1,"message"=>"LDAP FILE CREATED."));

	}
}