<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Members extends CMS_Controller
{
	private $_validation_rule = array(
		//array(
		//	'field' => 'member_id',
		//	'label' => 'Member ID',
		//	'rules' => 'trim|required'
		//),
		
		array(
			'field' => 'achievement_id',
			'label' => 'Achievement ID',
			'rules' => 'abs'
		),
		array(
			'field' => 'member_name',
			'label' => 'Member Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'group_name',
			'label' => 'Group Name',
			'rules' => 'trim'
		)
	);
		
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		$this->load->model('members_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('members');
	}
	
	
	public function index() 
	{
		$this->members();
	}
	
	public function members()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = null;
		} else {
			if($search_by == "name")
			{
				$where = "`member_name` LIKE LOWER('%{$search_text}%')";
			}
			elseif($search_by == "member_id")
			{
				$where = "{$search_by} = $search_text";
			}
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}


		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/cms/members/index/",
		    'total_items' => $this->contents_model->get_featured_members_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		//$this->template->type = $type;

		//$this->template->members = $this->members_model->get_members($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->featured_members = $this->contents_model->get_featured_members($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "achievement_id ASC");
		$this->template->view('members/list');
	}
	
	public function view_profile() {
		
		$member_id = $this->input->post("member_id");
		
		$member = $this->members_model->get_member_by_id($member_id);
		
		if(empty($member))
		{
			$this->return_json("error","This Member does not exist!");
			return;
		}
		
		$achievement_html = $this->_achievement_html($member);
		
		$achievement_details = $achievement_html["achievement_details"];
		$options = $achievement_html["options"];
		$has_achievements = $achievement_html["has_achievements"];
		
		$image = "";
		$featured_member = $this->contents_model->get_featured_member_by_member_id($member_id);
		$featured_member = $featured_member[0];
		
		if(!empty($featured_member))
		{
			$image = "{$this->config->item("media_url")}/members/{$featured_member->image_filename}";
		}
		
		$image = check_image_path($image);
		
		$data = array(
			"member_picture" => "<img src='{$image}'>",
			"member_id" => $member->member_id,
			"member_name" => "{$member->first_name} {$member->middle_name} {$member->last_name}",
			"member_since" => $member->insert_timestamp,
			"available_achievements" => $options,
			"achievement_details" => $achievement_details,
			"has_achievements" => $has_achievements
		);
		
		$this->return_json("ok", "Show Member", $data);
		return;
	}
	
	//public function edit($featured_member_id = 0)
	//{
	//	$featured_member = $this->contents_model->get_featured_member_by_id($featured_member_id);
	//	
	//	if ($_POST and !empty($featured_member))
	//	{
	//		// post done here
	//		$this->form_validation->set_rules($this->_validation_rule);
    //
	//		if ($this->form_validation->run())
	//		{
	//		
	//			// update member
	//			$data = array(
	//				'member_name' => set_value('member_name'),
	//				'group_name' => set_value('group_name'),
	//				'achievement_id' => set_value('achievement_id')
	//			);
    //
	//			$this->contents_model->update_featured_members($data, array('featured_member_id' => $featured_member_id));
    //
	//			redirect('/cms/members');
	//			return;
	//		}
	//	}
	//	
	//	$member_achievements = $this->contents_model->get_member_achievements();
	//	
	//	
	//	$this->template->featured_member = $featured_member;
	//	$this->template->member_achievements = $member_achievements;
	//	$this->template->view('members/edit');
	//}
	
	public function edit($featured_member_id = 0)
	{
		$featured_member = $this->contents_model->get_featured_member_by_id($featured_member_id);
		
		if ($_POST and !empty($featured_member))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{
				// update member
				$data = array(
					'member_name' => set_value('member_name'),
					'group_name' => set_value('group_name'),
					'achievement_id' => set_value('achievement_id')
				);

				$this->contents_model->update_featured_members($data, array('featured_member_id' => $featured_member_id));

				redirect('/cms/members');
				return;
			}
		}
		
		$member_achievements = $this->contents_model->get_member_achievements();
		
		$this->template->featured_member = $featured_member;
		$this->template->member_achievements = $member_achievements;
		$this->template->view('members/edit');
	}
	
	
	
	public function delete($featured_member_id = 0)
	{
		$featured_member = $this->contents_model->get_featured_member_by_id($featured_member_id);
		
		if ($_POST and !empty($featured_member))
		{
			$_featured_member_id = $this->input->post('featured_member_id');
			if (!empty($_featured_member_id)) if ($_featured_member_id == $featured_member_id)
			{
				$this->contents_model->delete_featured_members(array('featured_member_id' => $featured_member_id));
				
				//logging of action
				$details_before = array('id' => $featured_member_id, 'details' => $featured_member);
				$details_before = json_encode($details_before);
				
				$delete_member_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'FEATURED MEMBERS',
					'table_name' => 'sm_featured_members',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_member_log_data);
				
				redirect('/cms/members');
				return;
			}
		}
		
		$this->template->featured_member = $featured_member;
		$this->template->view('members/delete');
		
	}
	
	public function member_achievements()
	{
		$member_id = $this->input->post("member_id");
		$achievement_id = $this->input->post("achievement_id");
		$action = $this->input->post("action");
		
		$member = $this->members_model->get_member_by_id($member_id);
		
		if(empty($member))
		{
			$this->return_json("error","This member does not exist!");
			return;
		}
		
		$achievement = $this->contents_model->get_member_achievement_by_id($achievement_id);
		
		if(empty($achievement))
		{
			$this->return_json("error","This achievement does not exist!");
			return;
		}
		
		$data = array(
			"member_id" => $member_id,
			"achievement_id" => $achievement_id
		);
		
		if($action == "add")
		{
			$data["title"] = $achievement->title;
			$data["user_id"] = $this->user->user_id;
			$this->contents_model->insert_featured_members($data);
		}
		elseif($action == "remove")
		{
			$this->contents_model->delete_featured_members($data);
		}
		else
		{
			$this->return_json("error","This action does not exist.");
			return;
		}
		
		$achievement_html = $this->_achievement_html($member);
		
		$this->return_json("ok","Achievement successfully added.",array("available_achievements" => $achievement_html["options"],"achievement_details" => $achievement_html["achievement_details"], "has_achievements" => $achievement_html["has_achievements"]));
		return;
	}
	
	public function _achievement_html($member)
	{
		$achievements = $this->contents_model->get_featured_member_by_member_id($member->member_id);
		
		$achievement_ids = array();
		$achievement_details = "";
		if(!empty($achievements))
		{
			foreach($achievements as $a)
			{
				array_push($achievement_ids,$a->achievement_id);
				$achievement_details .= "<tr><td>{$a->title}</td><td>{$a->insert_timestamp}</td><td><a data='$member->member_id|{$a->achievement_id}' class='btn btn-danger btn-small btn-remove-achievement' title='remove'><i class='icon-remove icon-white'></i></a></td></tr>";
				$has_achievements = 1;
			}
		}
		else
		{
			$achievement_details = "<tr><td colspan='3'>No Achievements Found</td></tr>";
			$has_achievements = 0;
		}
		
		$available_achievements = $this->contents_model->get_available_member_achievements($achievement_ids);
		
		$options = "";
		if(!empty($available_achievements))
		{
			foreach($available_achievements as $available)
			{
				$options .= "<option value='{$available->achievement_id}'>{$available->title}</option>";
			}
		}
		
		
		return array("achievement_details" =>$achievement_details, "options" => $options, "has_achievements" => $has_achievements);
	}
	
	public function update_image()
	{
		$filename = $this->input->post('filename');
		$featured_member_id = $this->input->post('featured_member_id');
		
		$data = array("image_filename"=>$filename);
		$where = array(
			'featured_member_id' => $featured_member_id,
		);
		
		$this->contents_model->update_featured_members($data, $where);
		
		$this->return_json('ok','');
	}
	
	public function add_featured_modal()
	{
		
		$html = $this->load->view('cms/members/add_modal',"",TRUE);
			
		echo json_encode(array("status"=>1,"html"=>$html));
	}
	
	public function add_process()
	{
		if($_POST)
		{
			$member_id = $this->input->post('member_id');
			$member_name = $this->input->post('member_name');
			$group_name = $this->input->post('group_name');
			$achievement_type_id = $this->input->post('achievement_type_id');
			$title = $this->contents_model->get_member_achievement_by_id($achievement_type_id);
			
			$data = array(
				'member_id' => $member_id,
				'member_name' => $member_name,
				'group_name' => $group_name,
				'achievement_id' => $achievement_type_id,
				'title' => $title->achievement_name,
				'user_id' => $this->user->user_id
			);
			
			$this->contents_model->insert_featured_members($data);
			
			$insert_id = $this->contents_model->insert_id();
			
			//logging of action
			$details_after = array('id' => $insert_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$add_member_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'FEATURED MEMBERS',
				'table_name' => 'sm_featured_members',
				'action' => 'ADD',
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $add_member_log_data);
			
			$this->return_json("ok", 'Add New Featured Member Success', array("id"=>$insert_id)); 
			return;
		}
	}
	
	
	public function view_member($featured_member_id = 0)
	{	
		$featured_member = $this->contents_model->get_featured_member_by_id($featured_member_id);
		
		$this->template->featured_member = $featured_member;
		$this->template->view('members/view_member_details');
	}
	
	public function modal_search()
	{	
		$search_by = trim($this->input->post("search_by"));
		$search_text = trim($this->input->post("search_text"));
		
		$search_url = "";
		$searchBy = $search_by;
			
		if (($search_text == "") || empty($search_text)) {
			$where = NULL;					
		} else {
			if ($search_by == "name") {
				$where = "last_name LIKE '%{$search_text}%' OR first_name LIKE '%{$search_text}%' OR middle_name LIKE '%{$search_text}%'";
			} else if ($search_by == "account_id") {				
				$where = "account_id LIKE '%{$search_text}%'";								
			} else {															
				$where = "{$search_by} = '{$search_text}'";
			}
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;			
		}
	
		// initialize pagination class
		// set pagination data
		if ($search_by == "account_id") {
			$config = array(
			    'pagination_url' => "/cms/members/index/",
			    'total_items' => $this->members_model->get_member_accounts_count($where),
			    'per_page' => 10,
			    'uri_segment' => 4,
			);
		} else {		
			$config = array(
			    'pagination_url' => "/cms/members/index/",
			    'total_items' => $this->members_model->get_members_count($where),
			    'per_page' => 10,
			    'uri_segment' => 4,
			);
		}
        
		$this->pager->set_config($config);
		
		
		
		if ($search_by == "account_id") {
			
			$member_details = $this->members_model->get_member_accounts($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'account_id');
        
			foreach($member_details as $md)
			{							
				$mud = $this->members_model->get_member_by_id($md->member_id);
				
				$md->first_name = "None";
				if(!empty($mud)) $md->first_name = $mud->first_name;
				$md->middle_name = "None";
				if(!empty($mud)) $md->middle_name = $mud->middle_name;
				$md->last_name = "None";
				if(!empty($mud)) $md->last_name = $mud->last_name;
				$md->email = "None";
				if(!empty($mud)) $md->email = $mud->email;
				$md->mobile_number = "None";
				if(!empty($mud)) $md->mobile_number = $mud->mobile_number;
				$md->is_active = "1";
				if(!empty($mud)) $md->is_active = $mud->is_active;				
				$md->is_on_hold = 0;
				if(!empty($mud)) $md->is_on_hold = $mud->is_on_hold;
				
				// check all accounts of member with the same member_id
				$ma = $this->members_model->get_member_accounts_by_member_ids($md->member_id);
				$account_id_list = "";
				
				$md->account_id = "None";
				if(!empty($ma)) {					
				foreach($ma as $account) {
						if ($account->account_id == $search_text) {
							$account_id_list .= "<strong style='color:#468847'>{$account->account_id}</strong><br/>";
						} else {
							$account_id_list .= "{$account->account_id}<br/>";
						}
					}
				}
				$md->account_id = $account_id_list;
				
			}
			
		} else {
			$member_details = $this->members_model->get_members($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'last_name');		

			
			foreach($member_details as $md)
			{
				$account_id_list = "";
				$ma = $this->members_model->get_member_accounts_by_member_ids($md->member_id);
				$md->account_id = "None";
				if(!empty($ma)) {					
					foreach($ma as $account) {
						$account_id_list .= "{$account->account_id}<br/>";
					}
				}
				$md->account_id = $account_id_list;
			}			
		}
		
		
		
		$html = "";
		foreach($member_details as $md)
		{
			$proper_name = $md->first_name . ' ' . $md->middle_name . ' ' . $md->last_name;
			$proper_name = strtoupper($proper_name);
			$html .= "<tr>
						<td>{$proper_name}</td>
						<td><button id='btn-select-member' class='btn btn-small btn-info' title='Select' data='{$md->member_id}'><i class='icon-plus icon-white'></i></button></td>
					  </tr>";
		}
		
		$this->return_json("ok", 'Search Member Successful', array("html"=>$html)); 
		return;	
	}
	
	public function modal_add_details() {
		$member_id = abs($this->input->post("member_id"));
		
		// get data
		$md = $this->members_model->get_member_by_id($member_id);
		
		$member_name = $md->first_name . ' ' . $md->middle_name . ' ' . $md->last_name;
		$group_name = $md->group_name;
		
		$this->return_json("ok", 'Add New Featured Member Success', array("member_id"=>$member_id, "member_name"=>$member_name, "group_name"=>$group_name)); 
		return;
		
		
		
	}
	
}