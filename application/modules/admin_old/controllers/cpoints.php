<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cpoints extends Systems_Controller 
{
	function __construct() 
	{
  		parent::__construct();

		$this->set_navigation('cpoints');
		$this->load->library('pager');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
	}

	public function conversion_rate()
	{
		$cpoint_ratios = $this->members_model->get_cpoint_ratio();
		$this->template->cpoint_ratios = $cpoint_ratios;
		$this->template->view('cpoints/conversion_rate');
	}

	public function edit_ratio()
	{
		$id = $this->input->post('id');
		$new_val = $this->input->post('new_val');

		if(empty($id) || empty($new_val))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->members_model->get_cpoint_ratio(array('ratio_id' => $id));
		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Conversion not Found');
			return;
		}

		$this->members_model->update_cpoint_ratio(array(
			'cpoint_ratio' => $new_val
		), array(
			'ratio_id' => $id
		));

		$this->return_json(1, 'Ok');
	}

	public function converter_modal()
	{
		$member_id = $this->input->post('member_id');

		if(empty($member_id))
		{
			$this->return_json(0, 'Invalid Member ID');
			return;
		}

		$ratios = $this->members_model->get_cpoint_ratio();
		$member_details = $this->members_model->get_member_by_id($member_id);

		$this->template->ratios = $ratios;
		$this->template->member_details = $member_details;
		$html = $this->template->view('admin/cpoints/converter', 'members', true, true);
		$this->return_json(1, 'ok', array('html' => $html, 'member_details' => $member_details, 'ratios' => $ratios));
	}

	public function convert()
	{
		$type = $this->input->post('type');
		$amount = $this->input->post('amount');
		$member_id = $this->input->post('member_id');

		if(empty($type) || empty($amount))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$member_details = $this->members_model->get_member_by_id($member_id);
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
			'user_id' => $this->user->user_id,
			'module_name' => 'C POINTS',
			'table_name' => 'cm_members',
			'action' => 'UPDATE',
			'details_before' => json_encode($member_details_before),
			'details_after' => json_encode($member_details_after),
			'remarks' => 'converted using ' . $type
		);
		$this->tracking_model->insert_logs('admin', $log_data);

		$this->return_json(1, 'Success');
		return;
	}
}
