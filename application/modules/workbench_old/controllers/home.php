<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Base_Controller
{

	function __construct() 
	{
  		parent::__construct();
	}

	public function index() 
	{
		$this->template->view('home');
	}
	
	public function db_timezone()
	{
		$sql = "select now();";
		$query = $this->db->query($sql);
		$result = $query->result();
		$query->free_result();
		echo json_encode($result);
	}
}