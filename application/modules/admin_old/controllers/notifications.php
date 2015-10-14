<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications extends Systems_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'title',
			'label' => 'Title',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'body',
			'label' => 'Body',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_published',
			'label' => 'Publication Status',
			'rules' => 'trim|required'
		)
	);
	
	private $_months = array(
		'0' => 'Month',
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
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('tools');
	}

	public function index()
	{
		$this->announcements();
	}
	
	public function announcements()
	{

		//// initialize pagination class
		//// set pagination data
		//$config = array(
		//    'pagination_url' => "/cms/announcements/index",
		//    'total_items' => $this->contents_model->get_announcements_count(),
		//    'per_page' => 10,
		//    'uri_segment' => 4
		//);
        //
		//$this->pager->set_config($config);
		//$this->template->announcements = $this->contents_model->get_announcements(NULL, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('notifications/dashboard');
	}
	
	function listing() {
		
		$page = abs($this->input->post("page"));
		$search_by = trim($this->input->post("search_by"));
		$search_text = trim($this->input->post("search_text"));
		$recipient_list_email = trim($this->input->post("recipient_list_email"));
		$recipient_list_sms = trim($this->input->post("recipient_list_sms"));
		$send_via_option = trim($this->input->post("send_via_option"));
		
		if ($page==0) $page = 1;
		
		$where = "";
		
		if (!empty($search_by) && !empty($search_text)) {
			$where = "LOWER({$search_by}) LIKE LOWER('%{$search_text}%')";
		}
		
		$result = $this->_pagination($page,$where);

		$members = $result['members'];
		$pagination = $result['pagination'];
		
		if (empty($members)) {
			$html = "<br/><div>- No Results -</div>";
		} else {
			// 20120111 added page
			$html = $this->_render_html($members, $page, $recipient_list_email, $recipient_list_sms, $send_via_option);
		}
		

		echo json_encode(array('status' => 1, 'html' => $html, 'current_page' => $pagination['current_page'], 'total_records' => $pagination['total_records'], 'records_per_page' => $pagination['records_per_page'], 'pager_adjacents' => $pagination['pager_adjacents'] ));
		return;
		
	}

	private function _pagination($current_page,$where=""){
	
		if ($current_page<=0) $current_page = 1;

		$records_per_page = 10;
		$pager_adjacents = 3;

		$offset = ($current_page - 1) * $records_per_page;
		$offset = ($offset < 0 ? 0 : $offset);

		$limit = array(
			"offset"=>$offset,
			"rows"=>$records_per_page
		);

		$members  = $this->members_model->get_members($where, $limit, 'insert_timestamp DESC');
		$total_records = $this->members_model->get_members_count($where);

		$pagination = array (
			'current_page'=>$current_page,
			'pager_adjacents'=>$pager_adjacents,
			'records_per_page'=>$records_per_page,
			'total_records'=>$total_records
		);

		$return_data = array("members"=>$members,"pagination"=>$pagination);
		
		return $return_data;
	}

	private function _render_html($members, $page, $recipient_list_email, $recipient_list_sms, $send_via_option) {
		
		$html = "";
		foreach($members as $m) {
			
			$pretty_name = $m->last_name . ", " . $m->first_name . " " . $m->middle_name;
			$pretty_name = strtoupper($pretty_name);
			
			$mobile_number = ($m->mobile_number);
			
			$html .= "<tr>
				<td>{$m->member_id}</td>								
				<td>{$pretty_name}</td>
				<td>{$m->email}</td>
				<td>{$mobile_number}</td>
				<td style='padding-top:15px;'>";
            				
			// SMS
			if ($send_via_option == 'sms') {
			
				$string_count = 0;
				if (!(empty($mobile_number))) {
					$string_count = substr_count($recipient_list_sms, $mobile_number);
				}
				
				if ($string_count == 0) {
					//$html .="<a style='cursor:pointer;' class='btn_member_add fbbutton special' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><span class='list_add_{$m->member_id}' >Add to list</span></a></td></tr>";
					
					$html .= "<a class='btn btn-small btn-success btn_{$m->member_id} btn_member_add' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><i class='icon-ok icon-white icon_{$m->member_id}'></i><span class='list_add_{$m->member_id}'>Add to list</span></a></td></tr>";		
					
				} else {
					//$html .="<a style='cursor:pointer;' class='btn_member_add fbbutton special' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><span class='list_add_{$m->member_id}' >Remove</span></a></td></tr>";	
					
					$html .= "<a class='btn btn-small btn-success btn_{$m->member_id} btn_member_add' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><i class='icon-remove icon-white icon_{$m->member_id}'></i><span class='list_add_{$m->member_id}'>Remove</span></a></td></tr>";						
							
				}
			// EMAIL
			} else if ($send_via_option == 'email') {
				$string_count = 0;
				if (!(empty($m->email))) {
					$string_count = substr_count($recipient_list_email, $m->email);
				}
			
				if ($string_count == 0) {
					//$html .="<a style='cursor:pointer;' class='btn_member_add fbbutton special' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><span class='list_add_{$m->member_id}' >Add to list</span></a></td></tr>";
							
					$html .= "<a class='btn btn-small btn-success btn_{$m->member_id} btn_member_add' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><i class='icon-ok icon-white icon_{$m->member_id}'></i><span class='list_add_{$m->member_id}'>Add to list</span></a></td></tr>";		
							
				} else {
					//$html .="<a style='cursor:pointer;' class='btn_member_add fbbutton special' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><span class='list_add_{$m->member_id}' >Remove</span></a></td></tr>";
					
					$html .= "<a class='btn btn-small btn-success btn_{$m->member_id} btn_member_add' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><i class='icon-remove icon-white icon_{$m->member_id}'></i><span class='list_add_{$m->member_id}'>Remove</span></a></td></tr>";	
							
				}				
			} else {
			// BOTH
				$string_count_email = 0;
				if (!(empty($m->email))) {
					$string_count_email = substr_count($recipient_list_email, $m->email);
				}
				
				$string_count_sms = 0;
				if (!(empty($mobile_number))) {
					$string_count_sms = substr_count($recipient_list_sms, $mobile_number);
				}
			
				if (($string_count_email == 0) && ($string_count_sms == 0)) {
					//$html .="<a style='cursor:pointer;' class='btn_member_add fbbutton special' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><span class='list_add_{$m->member_id}' >Add to list</span></a></td></tr>";
							
					$html .= "<a class='btn btn-small btn-success btn_{$m->member_id} btn_member_add' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><i class='icon-ok icon-white icon_{$m->member_id}'></i><span class='list_add_{$m->member_id}'>Add to list</span></a></td></tr>";				
					
				} else {
					//$html .="<a style='cursor:pointer;' class='btn_member_add fbbutton special' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><span class='list_add_{$m->member_id}' >Remove</span></a></td></tr>";	
							
					$html .= "<a class='btn btn-small btn-success btn_{$m->member_id} btn_member_add' data='{$m->member_id}|{$page}|{$m->email}|{$mobile_number}'><i class='icon-remove icon-white icon_{$m->member_id}'></i><span class='list_add_{$m->member_id}'>Remove</span></a></td></tr>";		
				}				
			}
			
		}
		
		return $html;
		
	}
	
	function add_to_list() {		
		$_recipient_list_email = trim($this->input->post('_recipient_list_email'));
		$_recipient_list_sms = trim($this->input->post('_recipient_list_sms'));
		$_send_via_option = trim($this->input->post('_send_via_option'));
        $_member_id = abs($this->input->post('_member_id'));
		$_email = trim($this->input->post('_email'));
		$_page = abs($this->input->post('_page'));
		$_mobile_number = trim($this->input->post('_mobile_number'));
		
		// get member info
		$member  = $this->members_model->get_member_by_id($_member_id);
		//$pretty_name = $member->first_name . " " . $member->middle_name . " " . $member->last_name;
		$pretty_name = $member->first_name . " " . $member->last_name;
		$pretty_name = strtoupper(preg_replace('~[\\\\/:*?"<>|]~', '',$pretty_name));
		
		if($_send_via_option == 'email') {
			if (($_email == '') || empty($_email)) {
				echo json_encode(array('status' => 0, 'message' => 'No Email found on ' . $pretty_name . '\'s account.'));
				return;
			} else {				
				if (empty($_recipient_list_email) || ($_recipient_list_email == '')) {
					$_recipient_list_email .=  $pretty_name ."(" . $_member_id .")<". $_email . ">";
				} else {
					$_recipient_list_email .=  ";". $pretty_name ."(" . $_member_id .")<". $_email . ">";
				}
			}
		} else if($_send_via_option == 'sms') {
			if (($_mobile_number == '') || empty($_mobile_number)) {
				echo json_encode(array('status' => 0, 'message' => 'No Mobile Number found on ' . $pretty_name . '\'s account.'));
				return;
			} else {
				$mobile_num = $member->mobile_number;
				if (empty($_recipient_list_sms) || ($_recipient_list_sms == '')) {				
					$_recipient_list_sms .=  $pretty_name ."(" . $_member_id .")<". $mobile_num . ">";
				} else {
					$_recipient_list_sms .=  ";". $pretty_name ."(" . $_member_id .")<". $mobile_num . ">";
				}
			}
		} else {
			// for both sms and email
			if ((($_email == '') || empty($_email)) && (($_mobile_number == '') || empty($_mobile_number))) {
				echo json_encode(array('status' => 0, 'message' => 'No Email AND Mobile Number found on ' . $pretty_name . '\'s account.'));
				return;
			}
			
			// email
			if (($_email == '') || empty($_email)) {			
			} else {				
				if (empty($_recipient_list_email) || ($_recipient_list_email == '')) {
					$_recipient_list_email .=  $pretty_name ."(" . $_member_id .")<". $_email . ">";
				} else {
					$_recipient_list_email .=  ";". $pretty_name ."(" . $_member_id .")<". $_email . ">";
				}
			}
			
			// mobile number
			if (($_mobile_number == '') || empty($_mobile_number)) {			
			} else {
				$mobile_num = $member->mobile_number;
				if (empty($_recipient_list_sms) || ($_recipient_list_sms == '')) {				
					$_recipient_list_sms .=  $pretty_name ."(" . $_member_id .")<". $mobile_num . ">";
				} else {
					$_recipient_list_sms .=  ";". $pretty_name ."(" . $_member_id .")<". $mobile_num .">";
				}                              
			}
			
		}
		
		//$data = array('recipient_list_email' => $_recipient_list_email);
		
		echo json_encode(array('status' => 1, 'message' => 'Ok', 'recipient_list_email' => $_recipient_list_email,  'recipient_list_sms' => $_recipient_list_sms));
		return;
	}
	
	function remove_from_list() {				
		$_recipient_list_email = trim($this->input->post('_recipient_list_email'));
		$_recipient_list_sms = trim($this->input->post('_recipient_list_sms'));
		$_send_via_option = trim($this->input->post('_send_via_option'));
        $_member_id = abs($this->input->post('_member_id'));
		$_email = trim($this->input->post('_email'));
		$_page = abs($this->input->post('_page'));
		$_mobile_number = abs($this->input->post('_mobile_number'));
		
		$new_list_email = "";
		$new_list_sms = "";
		
		// get member info
		$member  = $this->members_model->get_member_by_id($_member_id);
		
		//$pretty_name = $member->first_name . " " . $member->middle_name . " " . $member->last_name;
		$pretty_name = $member->first_name . " " . $member->last_name;
		$pretty_name = strtoupper(preg_replace('~[\\\\/:*?"<>|]~', '',$pretty_name));

		
		if ($_send_via_option == 'email') {	
			// ------------
			// REMOVE EMAIL
			// ------------
			if (empty($_recipient_list_email) || ($_recipient_list_email == '')) {
				echo json_encode(array('status' => 0, 'message' => 'No recipient to be removed.'));
				return;
			}
			
			$email_format = $pretty_name ."<". $_email . ">";	
			$email_format_length = strlen($email_format);
			
			// check if list = email to be removed
			if ($_recipient_list_email == $email_format) {
				// only email on the list
				$new_list_email = str_replace($email_format, "", $_recipient_list_email);
			} else {
				// check if email to be removed is first on the list
				$first_email_on_list = substr($_recipient_list_email, 0, $email_format_length);	
				
				if ($first_email_on_list == $email_format) {
					// first on the list
					$new_list_email = str_replace($email_format . ";", "", $_recipient_list_email);
				} else {
					$new_list_email = str_replace(";". $pretty_name . "(" . $_member_id .")<". $_email . ">", "", $_recipient_list_email);
				}
			}			
		} else if ($_send_via_option == 'sms') {			
			// --------------------
			// REMOVE MOBILE NUMBER
			// --------------------
			if (empty($_recipient_list_sms) || ($_recipient_list_sms == '')) {
				echo json_encode(array('status' => 0, 'message' => 'No recipient to be removed.'));
				return;
			}
			
			// get mobile number
			$mobile_num = $member->mobile_number;
			$decoded_mobile_num = $mobile_num;
			// set the format
			$mobile_num_format = $pretty_name ."(" . $_member_id .")<". $decoded_mobile_num . ">";	
			$mobile_num_format_length = strlen($mobile_num_format);
			
			// check if list = email to be removed
			if ($_recipient_list_sms == $mobile_num_format) {
				// only mobile number on the list
				$new_list_sms = str_replace($mobile_num_format, "", $_recipient_list_sms);
			} else {
				// check if email to be removed is first on the list
				$first_mobile_number_on_list = substr($_recipient_list_sms, 0, $mobile_num_format_length);
				
				if ($first_mobile_number_on_list == $mobile_num_format) {
					// first on the list
					$new_list_sms = str_replace($pretty_name ."(" . $_member_id .")<". $decoded_mobile_num . ">;", "", $_recipient_list_sms);			
				} else {
					$new_list_sms = str_replace(";". $pretty_name ."(" . $_member_id .")<". $decoded_mobile_num . ">", "", $_recipient_list_sms);			
				}
			}
			
		}else {
			// for both sms and email
			
		}
		
		echo json_encode(array('status' => 1, 'message' => 'Ok', 'recipient_list_email' => $new_list_email, 'recipient_list_sms' => $new_list_sms));
		return;
	}
	function send_email_message() {
		$recipient_list = trim($this->input->post('recipient_list'));
        $sender_message = trim($this->input->post('sender_message'));		
		$send_to_option = trim($this->input->post('send_to_option'));
		$is_charged_option = abs($this->input->post('is_charged_option'));
		$cost_sms = $this->input->post('cost_sms');
		$cost_email = $this->input->post('cost_email');

		//var_dump($recipient_list . '|' . $sender_message . '|' . $send_to_option . "|" . $is_charged_option . '|' . $cost_sms . '|' . $cost_email);
		$primary_member_name_list = "";
		$primary_email_list =  "";
		
		
		if ($send_to_option == 'selected') {
			// ---------------
			// SELECTED Emails
			// ---------------
			$email_recipients = $recipient_list;
			$recipient_data = explode(";", $recipient_list);
			foreach($recipient_data as $rd) {
				// location of "<"
				$pos_openbrace = strpos($rd, "<");
				// collect emails
				$primary_member_name_list .= substr($rd, 0, $pos_openbrace) . "|" ;

				$primary_email_list .= substr($rd, strpos($rd, "<") + 1, (strlen($rd) - 2) - strpos($rd, "<")) . "|";

				$params = array(
					"email_message"=>$sender_message,
					"cost_email"=>$cost_email,
				);	
			}

			$data = array(
				"member_id"=> 0,
				"is_charged"=>$is_charged_option,
				"batch_email_addresses"=>$primary_email_list,
				"type"=>"mass-notification-email",
				"tariff"=>$cost_email,
				"params"=>$params
			);
			
			//var_dump($primary_email_list);

			//send email
			Modules::run('jobs/notifications/send_email_batch',$data); 	
			
		} else {
			// ----------
			// ALL EMAILS
			// ----------
            $email_recipients = "ALL MEMBERS";
			//$all_mobile_numbers = $this->members_model->get_members("LENGTH(mobile_number) >= 10");
			// get total count
			$all_emails_total_count = $this->members_model->get_members_count("LENGTH(email) >= 5 AND email LIKE '%@%.%'");
			
			if ($all_emails_total_count == 0)  {
				echo json_encode(array('status' => 0, 'message' => 'No email found in DB.'));
				return;			
			}
			
			$total_loop_count = ceil($all_emails_total_count/5000);
			//$total_loop_count = 0;
			
			for($i=0;$i <= $total_loop_count; $i++) {
				$primary_email_list = "";
				$all_emails = $this->members_model->get_members("LENGTH(email) >= 5 AND email LIKE '%@%.%'", array('offset' => $i * 5000, 'rows' => 5000));
			
				foreach($all_emails as $aem) {
					$primary_email_list .= $aem->email . "|" ;
				}
				
				$params = array(
					"email_message"=>$sender_message,
					"cost_email"=>$cost_email,
				);
				
				$data = array(
					"member_id"=> 0,
					"is_charged"=>$is_charged_option,
					"batch_email_addresses"=>$primary_email_list,
					"type"=>"mass-notification-email",
					"tariff"=>$cost_email,
					"params"=>$params
				);
				
				
				//send email
				Modules::run('jobs/notifications/send_email_batch',$data);
				
			}			
		}
		
		// log to tr_admin_logs	
    
		echo json_encode(array('status' => 1, 'msg' => 'Ok', 'email_recipients' => $email_recipients));

		return;
	}

	function send_sms_message() {	
		$recipient_list = trim($this->input->post('recipient_list'));
        $sender_message = trim($this->input->post('sender_message'));		
		$send_to_option = trim($this->input->post('send_to_option'));
		$is_charged_option = abs($this->input->post('is_charged_option'));
		$cost_sms = $this->input->post('cost_sms');
		$cost_email = $this->input->post('cost_email');
		
		$primary_member_code_list = "";
		$member_id_list =  "";
		
		
		if ($send_to_option == 'selected') {
			// -----------------------
			// SELECTED MOBILE NUMBERS
			// -----------------------
			$sms_recipients = $recipient_list;
			$recipient_data = explode(";", $recipient_list);
			foreach($recipient_data as $rd) {
				// location of "<"
				$pos_openbrace = strpos($rd, "<");
				// collect emails
				$primary_member_code_list .= substr($rd, 0, $pos_openbrace) . ";" ;	
			}
			
		} else {
			// ------------------
			// ALL MOBILE NUMBERS
			// ------------------
            $sms_recipients = "ALL MEMBERS";
			
			$all_mobile_numbers = $this->members_model->get_members("LENGTH(mobile_number) >= 10");
			// get total count
			$all_mobile_numbers_total_count = $this->members_model->get_members_count("LENGTH(mobile_number) >= 10");
			
			if ($all_mobile_numbers_total_count == 0)  {
				echo json_encode(array('status' => 0, 'message' => 'No mobile number found in DB.'));
				return;			
			}
			
			$total_loop_count = ceil($all_mobile_numbers_total_count/1000);
			//$total_loop_count = 0;
			
			for($i=0;$i <= $total_loop_count; $i++) {
				$primary_member_code_list = "";
				$all_mobile_numbers = $this->members_model->get_members("LENGTH(mobile_number) >= 10", array('offset' => $i * 1000, 'rows' => 1000));
			
				foreach($all_mobile_numbers as $amn) {
					$primary_member_code_list .= $amn->member_id. ":" . $amn->mobile_number . "|" ;
				}
				
				$params = array(
					"sms_message"=>$sender_message,
					"cost_sms"=>$cost_sms
				);
				
				$data = array(
					"member_id"=> "0",
					"account_id"=>"0",
					"is_charged"=>$is_charged_option,
					"mobile_number"=>$primary_member_code_list,
					"type"=>"mass-notification-sms",
					"tariff"=>$cost_sms,
					"params"=>$params
				);
            
				//send sms to user
				Modules::run('notifications/send_sms_batch',$data);
				
			}			
		}
		
		// log to tr_admin_logs	
    
		echo json_encode(array('status' => 1, 'msg' => 'Ok', 'sms_recipients' => $sms_recipients));
        return;
	}
	
	public function send_tb_sms($data=array()) {
		$this->load->model("contents_model");
		$this->load->model("sms_model");		
		
		$content = $this->contents_model->get_template($data['type'],$data['params']);		
		
		// check identifier
		$identifier = "smart";
		$area_code = substr($data['mobile_number'], -10, 3);
		$number = substr($data['mobile_number'], -7);
		$prefix = $this->sms_model->get_telco_prefix(array('prefix'=>$area_code));
		if(!empty($prefix)){
			$identifier = $prefix[0]->identifier;
		}
		
		$insert_data = array(
			"identifier"=>$identifier,
			"member_id"=>$data["member_id"],
			"account_id"=>$data["account_id"],
			"mobile_number"=>$data["mobile_number"],
			"message"=>$content->body,
			"is_charged"=>$data["is_charged"],
			"tariff"=>$data["tariff"],
		);
		
		if($this->settings->sms_use_sun_api){
			$insert_data['identifier'] = "sun_api";
			$insert_data['status'] = "sent";
			
			// use sun api
			$access = json_decode($this->settings->sun_api_access_credential);
			
			$originator = $this->settings->sun_api_originator1;
			$login_url = $this->settings->sun_api_login_url;
			$send_url = $this->settings->sun_api_send_url;
			$to = '0' . $area_code . $number;
			$ret = send_sunapi_sms($to , $content->body, $login_url, $send_url, $originator, $access->user, $access->pass);

			if(!$ret) $insert_data['status'] = "failed";
			
		}
		
		$this->sms_model->insert_sms_outgoing($insert_data);	
		
	}
	

	public function send_sms_batch($data=array()) {
		$this->load->model("contents_model");
		$this->load->model("sms_model");		
		
		$content = $this->contents_model->get_template($data['type'],$data['params']);		
		
		$batch_mobile_numbers = $data['mobile_number']; // semicolon delimited
		
		$member_ids_and_mobile_numbers = explode("|", $batch_mobile_numbers);
		
		foreach($member_ids_and_mobile_numbers as $mn) {
			
			// parse
			$parsed_mid_mobnum = explode(":", $mn);
			
			$member_id = $parsed_mid_mobnum[0];
			$mobile_number = $parsed_mid_mobnum[1];
			
			// check identifier
			$identifier = "smart";
			$area_code = substr($mobile_number, -10, 3);
			$number = substr($mobile_number, -7);
			$prefix = $this->sms_model->get_telco_prefix(array('prefix'=>$area_code));
			if(!empty($prefix)){
				$identifier = $prefix[0]->identifier;
			}
			
			$insert_data = array(
				"identifier"=>$identifier,
				"is_charged"=>$data['is_charged'],
				"tariff"=>$data['tariff'],
				"member_id"=>$member_id,
				"mobile_number"=>"09328561112",
				"message"=>$content->body
			);		
			
			print_r($insert_data);
			
			if($this->settings->sms_use_sun_api){
				$insert_data['identifier'] = "sun_api";
				$insert_data['status'] = "sent";
				
				// use sun api
				$access = json_decode($this->settings->sun_api_access_credential);
				
				$originator = $this->settings->sun_api_originator1;
				$login_url = $this->settings->sun_api_login_url;
				$send_url = $this->settings->sun_api_send_url;
				$to = '0' . $area_code . $number;
				$ret = send_sunapi_sms($to , $content->body, $login_url, $send_url, $originator, $access->user, $access->pass);
            
				if(!$ret) $insert_data['status'] = "failed";
				
			}
			
			$this->sms_model->insert_sms_outgoing($insert_data);	
			
			usleep(500);
		}
		return;
		
	}
	
}