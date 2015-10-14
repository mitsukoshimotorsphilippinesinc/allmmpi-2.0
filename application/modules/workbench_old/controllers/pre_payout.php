<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pre_payout extends Admin_Controller {
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model("members_model");
		$this->load->model("jobs_model");
	}
	
	function index()
	{
		echo "Pre-payout report consisting details for payout per group per member per account";
	}
	
	function view()
	{
		$this->template->view("pre_payout_report/group_report");
	}
	
}
