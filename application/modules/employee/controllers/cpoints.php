<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cpoints extends Site_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('members_model');
		$this->load->model('tracking_model');
	}
	
	public function index() {

	}

	public function converter_modal()
	{
		$ratios = $this->members_model->get_cpoint_ratio();
		$member_details = $this->members_model->get_member_by_id($this->member->member_id);

		$this->template->ratios = $ratios;
		$this->template->member_details = $member_details;
		$html = $this->template->view('cpoints/converter', 'members', true, true);
		$this->return_json(1, 'ok', array('html' => $html, 'member_details' => $member_details, 'ratios' => $ratios));
	}

	public function convert()
	{
		$type = $this->input->post('type');
		$amount = $this->input->post('amount');

		if(empty($type) || empty($amount))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$member_details = $this->members_model->get_member_by_id($this->member->member_id);
		$member_details_before = $member_details;
		$field = strtolower($type);
		if($member_details->$field < $amount)
		{
			$this->return_json(0, 'Not enough {$type}');
			return;
		}

		$ratio = $this->members_model->get_cpoint_ratio(array(
			'from_type' => $type
		));
		if(sizeof($ratio) == 0) {
			$this->return_json(0, 'Unknown Type');
			return;
		}
		$ratio = $ratio[0];

		// deduct type & add cpoints
		$this->members_model->update_member(array(
			$field => ($member_details->$field - $amount),
			'cpoints' => ($member_details->cpoints + ($ratio->cpoint_ratio * $amount))
		), array(
			'member_id' => $member_details->member_id
		));		

		// log
		$member_details_after = $this->members_model->get_member_by_id($this->member->member_id);
		$log_data = array(
			'member_id' => $this->member->member_id,
			'module_name' => 'C POINTS',
			'table_name' => 'cm_members',
			'action' => 'UPDATE',
			'details_before' => json_encode($member_details_before),
			'details_after' => json_encode($member_details_after),
			'remarks' => 'converted using ' . $type
		);
		$this->tracking_model->insert_logs('members', $log_data);

		$this->return_json(1, 'Success');
		return;
	}
}
