<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounts extends Site_Controller 
{	
	function __construct() 
	{
  		parent::__construct();
        $this->load->library("pager");
        $this->load->model('cards_model');
        $this->load->model('contents_model');
	}
	
	private $_months = array(
		'0' => '-',
		'1' => 'January',
		'2' => 'February',
		'3' => 'March',
		'4' => 'April',
		'5' => 'May',
		'6' => 'June',
		'7' => 'July',
		'8' => 'August',
		'9' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
	);
	
	public function index() 
	{

		$member_id = $this->member->member_id;

		// get card types
		$card_types = $this->cards_model->get_card_types(null, null, null, array('code', 'name'));
		$types = array();
		foreach($card_types as $item) $types[$item->code] = $item->name;
		$this->template->card_types = $types;
		
		$this->template->accounts = $this->members_model->get_member_accounts("member_id = ".$member_id,NULL,"insert_timestamp ASC");
		$this->template->current_page = 'accounts';
		$this->template->genealogy_css = $this->genealogy_internal_css();
        $this->template->view('accounts');
	}
	
	public function get_account_html()
	{
		$member_id = $this->input->post("member_id");
		$account_id = $this->input->post("account_id");
		
		$member = $this->members_model->get_member_by_id($member_id);
		
		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("html" => ""));
			return;
		}
		
		$account = $this->members_model->get_member_account_by_account_id($account_id);
		
		if(empty($account) || is_null($account))
		{
			$this->return_json("error","This account does not exist!",array("html" => ""));
			return;
		}
		
		// -- START
		$all_card_type_details = array();
		$where = "display_on_dashboard = 1";
		$card_type_details = $this->cards_model->get_card_types($where, '', 'card_type_id');
		
		foreach ($card_type_details as $ctd) {
			$card_type_details = $this->cards_model->get_card_type_by_code($ctd->code);
			
			$where = array(
					'member_id' => $member_id,
					'account_id' => $account_id,
					'card_type_id' => $card_type_details->card_type_id
				);	

			//$where = "member_id = '{$member_id}' AND account_id = '{$account_id}' AND card_type_id = '{$card_type_details->card_type_id}'";
			$account_pairing = $this->members_model->get_member_account_pairing($where);
			
			//if (sizeof($account_pairing) == 0) {

			//	$test = 0;
			//	
			//	$account_pairing['pair_count'] = 0;
			//	$account_pairing['left_count'] = 0;
			//	$account_pairing['right_count'] = 0;
			//	$all_card_type_pairing[$ctd->code] = $account_pairing;
			//} else {
			//	$test = 1;
				$all_card_type_pairing[$ctd->code] = $account_pairing;
				
			//}
			
			//var_dump($where . '|' . $test);
		}
		
		//var_dump($all_card_type_pairing);
		
		// -- END
		
		// get points to other table
		$sp_card_type = $this->cards_model->get_card_type_by_code("SP");
		$where = "member_id = '{$member_id}' AND account_id = '{$account_id}' AND card_type_id = '{$sp_card_type->card_type_id}'";
		$sp_account_pairing = $this->members_model->get_member_account_pairing($where);
					
		$vp_card_type = $this->cards_model->get_card_type_by_code("VP");
		$where = "member_id = '{$member_id}' AND account_id = '{$account_id}' AND card_type_id = '{$vp_card_type->card_type_id}'";
		$vp_account_pairing = $this->members_model->get_member_account_pairing($where);
		
		$rs_card_type = $this->cards_model->get_card_type_by_code("RS");
		$where = "member_id = '{$member_id}' AND account_id = '{$account_id}' AND card_type_id = '{$rs_card_type->card_type_id}'";
		$rs_account_pairing = $this->members_model->get_member_account_pairing($where);

		$erhm_card_type = $this->cards_model->get_card_type_by_code("ERHM");
		$where = "member_id = '{$member_id}' AND account_id = '{$account_id}' AND card_type_id = '{$erhm_card_type->card_type_id}'";
		$erhm_account_pairing = $this->members_model->get_member_account_pairing($where);

		$up1_card_type = $this->cards_model->get_card_type_by_code("UP1");
		$where = "member_id = '{$member_id}' AND account_id = '{$account_id}' AND card_type_id = '{$up1_card_type->card_type_id}'";
		$up1_account_pairing = $this->members_model->get_member_account_pairing($where);
		
		$p2p_card_type = $this->cards_model->get_card_type_by_code("P2P");
		$where = "member_id = '{$member_id}' AND account_id = '{$account_id}' AND card_type_id = '{$p2p_card_type->card_type_id}'";
		$p2p_account_pairing = $this->members_model->get_member_account_pairing($where);

		if(empty($account) || is_null($account))
		{
			$this->return_json("error","This account does not exist!",array("html" => ""));
			return;
		}
		
		$data = array();
		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i >= 2007; $i--)
			$_years[$i] = $i;
		
		$account_type = $this->members_model->get_member_account_type_by_id($account->account_type_id);
		
		
		
		$account_status = $this->members_model->get_member_account_status_by_id($account->account_status_id);
		
		$total_referrals = $this->members_model->get_member_accounts(array('sponsor_id' => $account_id));
		
		$upline_account = $this->members_model->get_member_account_by_account_id($account->upline_id);
		$sponsor_account = $this->members_model->get_member_account_by_account_id($account->sponsor_id);
		$upline = null;
		$sponsor = null;
		if (!empty($upline_account))
		{
			$upline = $this->members_model->get_member_by_id($upline_account->member_id);
		}
		
		if (!empty($sponsor_account))
		{
			$sponsor = $this->members_model->get_member_by_id($sponsor_account->member_id);
		}

		// check if user has 15 or more accounts
		$member_accounts = $this->members_model->get_member_accounts("member_id = {$this->member->member_id}");
		$no_of_accounts = count($member_accounts);

		// get member's max pair
		$member_achievement = $this->contents_model->get_featured_members(array('member_id' => $this->member->member_id));
		$achievement_id = (sizeof($member_achievement) > 0)?$member_achievement[0]->achievement_id:0;
		$achievement = $this->contents_model->get_member_achievements(array('member_achievement_id' => $achievement_id));
		$achievement = $achievement[0];
		
		$data = array(
			"member_id" => $member_id,
			"account_id" => $account_id,
			"account" => $account,
			"upline" => $upline,
			"sponsor" => $sponsor,
			"months" => $this->_months,
			"days" => $_days,
			"years" => $_years,
			"status" => $account_status->account_status,
			"account_type" => $account_type->description,
			"total_referrals" => count($total_referrals),
			"no_of_accounts" => $no_of_accounts,
			"sp_account_pairing" => (sizeof($sp_account_pairing)==0)?false:$sp_account_pairing[0],
			"vp_account_pairing" => (sizeof($vp_account_pairing)==0)?false:$vp_account_pairing[0],
			"rs_account_pairing" => (sizeof($rs_account_pairing)==0)?false:$rs_account_pairing[0],
			"erhm_account_pairing" => (sizeof($erhm_account_pairing)==0)?false:$erhm_account_pairing[0],
			"up1_account_pairing" => (sizeof($up1_account_pairing)==0)?false:$up1_account_pairing[0],
			"p2p_account_pairing" => (sizeof($p2p_account_pairing)==0)?false:$p2p_account_pairing[0],
			"all_card_type_pairing" => $all_card_type_pairing,
			"max_pairs" => $achievement->max_pairs
		);
		
		$html = $this->load->view("account_summary",$data,true);
		
		$this->return_json("ok","HTML retrieved",array("html" => $html));
		return;
		
	}
	
	public function add()
	{		
		$member_id = $this->input->post("_member_id");
        $is_modal = $this->input->post("_is_modal");
        $upline_id = 0;
        $position = "";

        if ($is_modal == 1) {
            // via genealogy
            $upline_id = $this->input->post("_upline_id");
            $position = $this->input->post("_position");
            if ($position == 1) {
                $position = "right";
            } else {
                $position = "left";
            }
        }
		
		$where = "member_id = {$member_id}";
        $account_details = $this->members_model->get_member_accounts($where,null,"account_id");

        // check if member has 15 accounts
        if (count($account_details) >= $this->settings->max_no_of_accounts) {
            $this->return_json("error","Max number of accounts reached",array("html" => "You already reached the maximum number of accounts allowed."));
            return;
        }

		//$this->template->account_details = $account_details;
		$data = array(
			"account_details"=>$account_details,
            "is_modal"=>$is_modal,
            "upline_id"=>$upline_id,
            "position"=>$position
		);

		$html = $this->load->view("members/account/dashboard",$data,true);
		
		// status,msg, data
		$this->return_json("ok","No error",array("html" => $html));
		return;
		
	}

    public function check() {
        $member_account_id = $this->input->post("_member_account_id");

        // get account details
        $account_details = $this->members_model->get_member_account_by_account_id($member_account_id);

        // get member_details
        $member_details = $this->members_model->get_member_by_id($account_details->member_id);

        $sender_proper_name = ucfirst($this->member->first_name) . " " .  ucfirst($this->member->last_name);

        //var_dump($member_details->email);
        if (($member_details->email == NULL) || ($member_details->email == "")) {
			$html = "Email Address not found.";
            $this->return_json("error","No Email Found",array("html" => $html));
	        return;
        } else {
            $html = "<label><strong>TO: </strong></label> <input style='width:300px;' disabled='disabled' value='{$member_details->email}' />
                     <br/><br/>
                     <label><strong>MESSAGE: </strong></label> <textarea style='width:300px;height:100px;' id='member_message'></textarea>
                    ";
        }

        // status,msg, data
        $this->return_json("ok","No error",array("html" => $html, "email_to" => $member_details->email, "email_from" => $this->member->email, "sender" => $sender_proper_name, "mobile_number_from" => $this->member->mobile_number));
        return;

    }

    public function send_message() {
        $email_to = trim($this->input->post("_email_to"));
        $email_from = trim($this->input->post("_email_from"));
        $sender_name = $this->input->post("_sender_name");
        $message = $this->input->post("_message");
        $mobile_number_from = $this->input->post("_mobile_number_from");

        $params = array(
            "email_from"=>ucfirst($email_from),
            "sender_name"=>ucfirst($sender_name),
            "mobile_number_from"=>ucfirst($mobile_number_from),
            "message"=>$message
        );

        $data = array(
            "email"=>$email_to,
            "type"=>"send_private_message",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);

        // TODO : INSERT TO TRACKING TABLE

        $html = "Private Message Sent";

        // status,msg, data
        $this->return_json("ok","No error",array("html" => $html));
        return;

    }
	
	public function sms_notification() {
		$sms_notification = abs($this->input->post("sms_notification"));
        $account_id = trim($this->input->post("account_id"));
		
		if ($sms_notification == 1) {
			$title = "Disable Sms Notification :: Confirm";
			$html = "You are to <strong>disable</strong> the commission and pairing notification via sms for your <strong>{$account_id}</strong>. <br/>Do you want to proceed?";
		} else {
			$html = "You are to <strong>enable</strong> the commission and pairing notification via sms for your <strong>{$account_id}</strong>. <br/>Do you want to proceed?";
			$title = "Enable Sms Notification :: Confirm";
		}
		
		
        $this->return_json("1","No error",array("title" => $title, "html" => $html));
        return;
		
	}

    private function genealogy_internal_css()
	{
		$css_str = '';

		$where = array('status'=>'ACTIVE', 'is_package'=>'1');

		$card_types = $this->cards_model->get_card_types($where);

		if($card_types)
		{
			foreach($card_types as $card_type)
			{	
				if($card_type->color != '')
				{
					$css_str .= ".node-".strtolower($card_type->code)."-color { \n";
					$css_str .= "\tbackground-color:{$card_type->color} !important; \n";
					$css_str .= "}\n";
					$css_str .= ".node-".strtolower($card_type->code)." { \n";
					$css_str .= "\tbackground-color:{$card_type->color} !important; \n";
					$css_str .= "}\n\n";
				}
			}
			$css_str = "<style>\n".$css_str."\n</style>";
		}
		return $css_str;
	}
	
	public function update_sms_notification() {
		$sms_notification = abs($this->input->post("sms_notification"));
        $account_id = trim($this->input->post("account_id"));
		
		if (($sms_notification > 1) || ($sms_notification < 0)) {
			$title = "Sms Notification :: Error";
			$html = "There is something wrong with the required data [missing notification value].";
			
			$this->return_json("0","error",array("title" => $title, "html" => $html));
			return;	
		} 
		
		$data = array(
			"sms_notification" => $sms_notification
		);
		
		$details_before = array('account_id' =>  $account_id, 'details' => $data);
		$details_before = json_encode($details_before);
		
		// update cm_member_accounts	
		$new_notif_value = 1;
		$update_type = "ENABLE";
		if ($sms_notification == 1)	{
			$new_notif_value = 0;
			$update_type = "DISABLE";
		}
		
		$data = array(
			"sms_notification" => $new_notif_value
		);
		
		$where = "account_id = {$account_id}";
		$this->members_model->update_member_accounts($data, $where);
		
		// update tr_member_logs
		// member logs 
		$details_after = array('account_id' =>  $account_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_log_data = array(
			'member_id' => $this->member->member_id,
			'module_name' => 'SMS NOTIFICATION - ' . $update_type,
			'table_name' => 'cm_member_accounts',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);
		$this->tracking_model->insert_logs('members', $new_member_log_data);
		
		if ($sms_notification == 1) {
			$title = "Disable Sms Notification :: Successful";
			$html = "You have <strong>disabled</strong> the commission and pairing notification via sms for your <strong>{$account_id}</strong>.";
		} else {
			$html = "You have <strong>enabled</strong> the commission and pairing notification via sms for your <strong>{$account_id}</strong>.";
			$title = "Enable Sms Notification :: Successful";
		}
		
        $this->return_json("1","ok",array("title" => $title, "html" => $html));
        return;	
	}
}