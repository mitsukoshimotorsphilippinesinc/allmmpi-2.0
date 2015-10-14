<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ess extends Systems_Controller 
{
	function __construct() 
	{
  		parent::__construct();

		$this->set_navigation('ess');
		$this->load->model('members_model');
		$this->load->model('cards_model');
		$this->load->library('pager2');
	}
	
	public function index() 
	{ 
		$this->view();
	}
	
	public function view()
	{
		// get card series
		$series = $this->cards_model->get_card_series(null, null, 'series_number DESC', null);
		$sorterd_series = array();
		foreach($series as $serie){ $sorterd_series[$serie->is_package][] = $serie->series_number; }

		$this->template->sorterd_series = $sorterd_series;
		$this->template->view('ess/dashboard');
	}

	public function get_leader_list()
	{
		$page = $this->input->post('page');
		if(empty($page)) $page = 1;

		$sql = "SELECT * FROM rf_member_list_for_encoded_sales_summary";
		$query = $this->db->query($sql);
		$leaders = $query->result();

		$records_per_page = 10;
		$offset = ($page - 1) * $records_per_page;
        $offset = ($offset < 0 ? 0 : $offset);

		$this->pager2->set_config(array(
            'total_items' => sizeof($leaders),
            'per_page' => $records_per_page,
            'offset' => $offset,
            'adjacents' => 2,
            'type' => 'ajax'
        ));

        $pagination = $this->pager2->create_links();

        $sql = "SELECT * FROM rf_member_list_for_encoded_sales_summary LIMIT {$offset}, {$records_per_page} ";
		$query = $this->db->query($sql);
		$leaders = $query->result();

		$this->return_json(1, 'Success', array('leaders' => $leaders, 'pagination' => $pagination));
		return;
	}

	public function get_accounts()
	{
		$member_id = $this->input->post('member_id');

		if(empty($member_id)) 
		{
			$this->return_json(0, "Invalid Member ID");
			return;
		}

		$member_data = $this->members_model->get_members(array('member_id' => $member_id));
		if(sizeof($member_data) == 0)
		{
			$this->return_json(0, "Member not found");
			return;
		}

		$account_ids = $this->members_model->get_member_accounts(array('member_id' => $member_id), null, null, array('member_id','account_id'));

		$this->return_json(1, "Success", array('account_ids' => $account_ids));
		return;
	}

	public function add_leader()
	{
		$member_id = $this->input->post('member_id');
		$account_id = $this->input->post('account_id');

		// checks
		if(empty($member_id) || empty($account_id)) 
		{
			$this->return_json(0, "Invalid Details");
			return;
		}

		$member_data = $this->members_model->get_members(array('member_id' => $member_id));
		if(sizeof($member_data) == 0)
		{
			$this->return_json(0, "Member not found");
			return;
		}
		$member_data = $member_data[0];

		$account_data = $this->members_model->get_member_accounts(array(
			'member_id' => $member_id,
			'account_id' => $account_id
		));
		if(sizeof($account_data) == 0)
		{
			$this->return_json(0, "Account not found");
			return;
		}
		$account_data = $account_data[0];

		$sql = "SELECT * FROM rf_member_list_for_encoded_sales_summary WHERE member_id = '{$member_id}' AND account_id = '{$account_id}' ";
		$query = $this->db->query($sql);
		$existing_leader = $query->result();
		if(sizeof($existing_leader) > 0)
		{
			$this->return_json(0, "Leader already exist");
			return;
		}

		// add member - account as leader
		$full_name = $member_data->first_name . " " . $member_data->middle_name . " " . $member_data->last_name;
		$sql = "
		INSERT INTO
			rf_member_list_for_encoded_sales_summary
		(
			`member_id`,
			`account_id`,
			`name`,
			`node`
		)
		VALUES
		(
			'{$member_id}',
			'{$account_id}',
			'{$full_name}',
			'{$account_data->node_address}'
		)
		";
		$this->db->query($sql);

		$this->return_json(1, "Success");
		return;
	}

	public function leader_activation()
	{
		$id = $this->input->post('id');
		$active = $this->input->post('active');

		// checks
		if(empty($id)) 
		{
			$this->return_json(0, "Invalid Details");
			return;
		}

		$sql = "
		UPDATE
			rf_member_list_for_encoded_sales_summary
		SET
			is_active = '{$active}'
		WHERE
			accountid = '{$id}'
		";
		$this->db->query($sql);

		$this->return_json(1, "Success");
		return;
	}

	public function generate_ess()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$selected_series = $this->input->post('selected_series');
		$json_selected_series = urlencode(json_encode($selected_series));

		//print_r($json_selected_series);

		$pretty_start_date = str_replace("-", "", $start_date);
		$pretty_end_date = str_replace("-", "", $end_date);

		// run backend process
		$root_path = FCPATH;
		$log_path = FCPATH . "assets/media/tmp/ess_generation_{$pretty_start_date}_{$pretty_end_date}.log";
		$exec = "/usr/bin/php {$root_path}jobs.php jobs encoding generate_ess_start {$this->user->user_id} {$json_selected_series} {$start_date} {$end_date} >> {$log_path} &";
		//print_r($exec);
		$output = exec($exec);

		$this->return_json(1, "Success", array('exec' => $exec, 'output' => $output));
		return;
	}

}
