<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Earners extends CMS_Controller
{	
	private $_validation_rule = array(
		array(
			'field' => 'member_name',
			'label' => 'Member Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'group_name',
			'label' => 'Group Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'earner_type_id',
			'label' => 'Earner Type',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'position',
			'label' => 'Rank',
			'rules' => 'trim|required'
		)
	);
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		$this->load->model('members_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('earners');
	}
	
	
	public function index() 
	{
		$this->earners();
	}
	
	public function earners()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = "`is_active` = 1";
		} else {
			if($search_by == "name")
			{
				$where = "`is_active` = 1 AND `member_name` LIKE LOWER('%{$search_text}%')";
			}
		}

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->template->top_earners = $this->contents_model->get_top_earners($where, null, "earner_type_id ASC, position ASC");
		$this->template->view('earners/list');
	}
	
	public function edit()
	{
		$type = $this->input->post("type");
		$earner_id = $this->input->post("earner_id");
		$rank = $this->input->post("rank");

		$earner = $this->contents_model->get_top_earner_by_id($earner_id);
		
		$member_name = "";
		$group_name = "";
		
		if(!empty($earner))
		{
			$member_name = $earner->member_name;
			$group_name = $earner->group_name;
		}
		
		$data = array(
			"earner_id" => $earner_id,
			"rank" => $rank,
			"member_name" => $member_name,
			"group_name" => $group_name
		);
		
		$html = $this->load->view("earners/edit",$data,true);
		
		$this->return_json("ok","",array("html" => $html));

	}
	
	public function update()
	{
		$earner_id = $this->input->post("earner_id");
		$member_name = $this->input->post("member_name");
		$group_name = $this->input->post("group_name");
		
		$top_earner = $this->contents_model->get_top_earner_by_id($earner_id);
		
		$data = array(
			'member_name' => $member_name,
			'group_name' => $group_name,
			'user_id' => $this->user->user_id
		);
		$this->contents_model->update_top_earners($data,array("earner_id" => $earner_id));

		//logging of action
		$details_before = array('id' => $earner_id, 'details' => array('member_name' => $top_earner->member_name,'group_name' => $top_earner->group_name,'user_id' => $top_earner->user_id));
		$details_after = array('id' => $earner_id, 'details' => $data);

		$details_before = json_encode($details_before);
		$details_after = json_encode($details_after);
		$update_earner_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'TOP EARNERS',
			'table_name' => 'sm_top_earners',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_earner_log_data);
		
		$this->return_json("ok","Rank updated successfully!");
		return;
	}
}