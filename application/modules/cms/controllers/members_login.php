<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Members_login extends CMS_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'slide_name',
			'label' => 'Slide Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim'
		),
		array(
			'field' => 'priority_id',
			'label' => 'Priority Number',
			'rules' => 'trim'
		),
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
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
			if($search_by == "slide_name")
			{
				$where = "`slide_name` LIKE LOWER('%{$search_text}%')";
			}
			elseif($search_by == "priority_id")
			{
				$where = "{$search_by} = $search_text";
			}
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}


		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/cms/members_login/index/",
		    'total_items' => $this->contents_model->get_members_login_ads_count($where),
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
		$this->template->member_login_ads = $this->contents_model->get_members_login_ads($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "priority_id, slide_name ASC");
		$this->template->view('members_login/list');
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
	
	public function edit($members_login_ad_id = 0)
	{
		$featured_member_login_ad = $this->contents_model->get_members_login_ad_by_id($members_login_ad_id);
		
		if ($_POST and !empty($featured_member_login_ad))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{			
				//$where = "is_active = 1";
				//$active_ads_count = $this->contents_model->get_members_login_ads_count($where);
				//
				//if ($active_ads_count >= 3) {
				//	//$this->template->view('members_login/error_modal');
				//	//return;
				//}
				
				// update member
				$data = array(
					'slide_name' => set_value('slide_name'),
					'description' => set_value('description'),
					'is_active' => set_value('is_active'),
					'priority_id' => set_value('priority_id')
				);

				//var_dump($data);				
				$this->contents_model->update_members_login_ads($data, array('members_login_ad_id' => $members_login_ad_id));

				redirect('/cms/members_login');
				return;
			}
		}
		
		$this->template->featured_member_login_ad = $featured_member_login_ad;
		$this->template->view('members_login/edit');
	}
	
	public function delete($members_login_ad_id = 0)
	{
		$members_login_ad = $this->contents_model->get_members_login_ad_by_id($members_login_ad_id);
		
		if ($_POST and !empty($members_login_ad))
		{
			$_members_login_ad_id = $this->input->post('members_login_ad_id');
			if (!empty($_members_login_ad_id)) if ($_members_login_ad_id == $members_login_ad_id)
			{
				$this->contents_model->delete_members_login_ads(array('members_login_ad_id' => $members_login_ad_id));
				
				//logging of swf_actiongeturl(url, target)
				$details_before = array('id' => $members_login_ad_id, 'details' => $members_login_ad);
				$details_before = json_encode($details_before);
				
				$delete_member_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'MEMBERS LOGIN ADS',
					'table_name' => 'sm_members_login_ads',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_member_log_data);
				
				redirect('/cms/members_login');
				return;
			}
		}
		
		$this->template->featured_member_login_ad = $members_login_ad;
		$this->template->view('members_login/delete');
		
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
		$members_login_ad_id = $this->input->post('members_login_ad_id');
		
		$data = array("image_filename"=>$filename);
		$where = array(
			'members_login_ad_id' => $members_login_ad_id,
		);
		
		$this->contents_model->update_members_login_ads($data, $where);
		
		$this->return_json('ok','');
	}
	
	public function add_featured_modal()
	{
		
		$html = $this->load->view('cms/members_login/add_modal',"",TRUE);
			
		echo json_encode(array("status"=>1,"html"=>$html));
	}
	
	public function add_process()
	{
		if($_POST)
		{
			$slide_name = $this->input->post('slide_name');
			$description = $this->input->post('description');
			$priority_id = $this->input->post('priority_id');			
			
			$data = array(
				'slide_name' => $slide_name,
				'description' => $description,
				'priority_id' => $priority_id,				
				'user_id' => $this->user->user_id
			);
			
			$this->contents_model->insert_members_login_ads($data);
			
			$insert_id = $this->contents_model->insert_id();
			
			//logging of action
			$details_after = array('id' => $insert_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$add_member_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'MEMBERS LOGIN ADS',
				'table_name' => 'sm_members_login_ads',
				'action' => 'ADD',
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $add_member_log_data);
			
			$this->return_json("ok", 'Add New Members Login Ad', array("id"=>$insert_id)); 
			return;
		}
	}
	
	
	public function view_member($members_login_ad_id = 0)
	{	
		$featured_members_login_ad = $this->contents_model->get_members_login_ad_by_id($members_login_ad_id);
		
		$this->template->featured_members_login_ad = $featured_members_login_ad;
		$this->template->view('members_login/view_ad_details');
	}
}