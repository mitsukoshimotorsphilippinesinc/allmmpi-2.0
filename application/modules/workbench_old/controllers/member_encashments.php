<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_encashments extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('members_model');
	}

	public function index() 
	{
		echo "Member_encashments Index.";
	}
	
	
	public function credit_funds()
	{
		
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$sql_encashments = "
			SELECT
				member_id,
				SUM(amount) as amount
			FROM cm_member_encashments
			WHERE `type`='TO FUNDS' AND start_date = '{$start_date}' AND end_date = '{$end_date}'
			GROUP BY member_id";
		
		$query = $this->db->query($sql_encashments);
		
		$result = $query->result();
		
		$this->load->model('members_model');
		
		foreach($result as $r){
			echo $r->member_id . "|" . $r->amount . "<br />";
			$this->members_model->credit_funds($r->member_id,$r->amount,"FUNDS","Payout ({$start_date} to {$end_date})");
		}
		
		
	}
	
	
}
