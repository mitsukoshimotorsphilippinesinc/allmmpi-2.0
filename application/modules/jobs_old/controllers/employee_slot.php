<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	  if (!defined('CRON_SHELL')) exit('This script is accessed thru CRON script only');

class Employee_slot extends Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model('items_model');
		$this->load->model('employees_model');
	}
	
	public function index() 
	{
		echo "Reset Employee Slot...";
	}

	public function reset() 
	{
		// run this script every end or start of the month to reset the product slots of employees
		$update_employee_slots_sql = "UPDATE 
			`ad_employee_products` `a`
		LEFT JOIN 
			`is_products` `b` 
		ON
		    `a`.`product_id` = `b`.`product_id`
		SET
		    `a`.`available_qty` = `b`.`employee_slots`";
		
		$this->db->query($update_employee_slots_sql);
	}
}