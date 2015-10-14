<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simulation extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("members_model");
	}
	
	public function index() 
	{
	}
	
	public function igpsm()
	{
		$account_id = $this->input->get_post("account_id");
		
		// get member_account
		$member_account = $this->members_model->get_member_account_by_account_id($account_id);

		$html = "<h2>{$account_id}</h2>";
		
		if (!empty($member_account))
		{
			$address = $member_account->node_address;		

			$address_length_count = strlen($address);

			$addresses = array();

			// get addresses of all uplines
			for ($i=1;$i<=$address_length_count;$i++) 
				$addresses[] = "'" . substr($address,0,$i) . "'";

			$new_addresses = implode(",",$addresses);

			// get only all uplines and all active and company accounts (account_status_id 1 and 3)
			$accounts = $this->members_model->get_member_accounts("node_address in ({$new_addresses})",NULL,"LENGTH(node_address) DESC");

			$html .= "<table class='table table-condensed table-striped table-bordered'>";

			$html .= "
			<thead><tr>
				<th>Account ID</th>
				<th>Position</th>
				<th>Left SP</th>
				<th>Right SP</th>
				<th>Pairs SP</th>
				<th>GC SP</th>
				<th>Flushout SP</th>
				<th>Left VP</th>
				<th>Right VP</th>
				<th>Pairs VP</th>
				<th>GC VP</th>
				<th>Flushout VP</th>
				<th>Left RS</th>
				<th>Right RS</th>
				<th>Pairs RS</th>
				<th>GC RS</th>
				<th>Flushout RS</th>
				<th>Referral Bonus</th>
				<th>SP Pairing Bonus</th>
				<th>SP GC</th>
				<th>TP Pairing Bonus</th>
				<th>TP GC</th>
				<th>VP Pairing Bonus</th>
				<th>VP GC</th>
			</tr></thead>
			";
			// credit points and check for pairing
			foreach($accounts as $a)
			{
				$position = substr($a->node_address,-1) == 1 ? "R" : "L";
				
				$sql = "SELECT sum(`referral_bonus`) as `referral_bonus`,sum(`pairing_bonus_sp`) as `pairing_bonus_sp`,sum(`gift_cheque_sp`) as `gift_cheque_sp`,sum(`pairing_bonus_vp`) as `pairing_bonus_vp`,sum(`gift_cheque_vp`) as `gift_cheque_vp`,sum(`pairing_bonus_tp`) as `pairing_bonus_tp`,sum(`gift_cheque_tp`) as `gift_cheque_tp` FROM `cm_member_earnings` where `account_id` = '{$a->account_id}'";
				
				$query = $this->db->query($sql);
				
				$earnings = $query->result();
				$earnings = $earnings[0];
				// display points and check for pairings
				$html .= "
				<tbody><tr>
					<td>{$a->account_id}</td>
					<td>{$position}</td>
					<td align='right'>{$a->left_sp}</td>
					<td align='right'>{$a->right_sp}</td>
					<td align='right'>{$a->pairs_sp}</td>
					<td align='right'>{$a->gc_sp}</td>
					<td align='right'>{$a->flushout_sp}</td>
					<td align='right'>{$a->left_vp}</td>
					<td align='right'>{$a->right_vp}</td>
					<td align='right'>{$a->pairs_vp}</td>
					<td align='right'>{$a->gc_vp}</td>
					<td align='right'>{$a->flushout_vp}</td>
					<td align='right'>{$a->left_rs}</td>
					<td align='right'>{$a->right_rs}</td>
					<td align='right'>{$a->pairs_rs}</td>
					<td align='right'>{$a->gc_rs}</td>
					<td align='right'>{$a->flushout_rs}</td>
					<td align='right'>{$earnings->referral_bonus}</td>
					<td align='right'>{$earnings->pairing_bonus_sp}</td>
					<td align='right'>{$earnings->gift_cheque_sp}</td>
					<td align='right'>{$earnings->pairing_bonus_tp}</td>
					<td align='right'>{$earnings->gift_cheque_tp}</td>
					<td align='right'>{$earnings->pairing_bonus_vp}</td>
					<td align='right'>{$earnings->gift_cheque_vp}</td>
				</tr></tbody>";
			}		

			$html .= "</table>";
		}		

		$this->template->html = $html;
		$this->template->view('simulation');

	}	
	
	public function earnings()
	{
		$account_id = $this->input->get_post("account_id");
		
		// get member_account
		$member_account = $this->members_model->get_member_account_by_account_id($account_id);

		$html = "<h2>{$account_id}</h2>";
		
		if (!empty($member_account))
		{
			$address = $member_account->node_address;		

			$address_length_count = strlen($address);

			$addresses = array();

			// get addresses of all uplines
			for ($i=1;$i<=$address_length_count;$i++) 
				$addresses[] = "'" . substr($address,0,$i) . "'";

			$new_addresses = implode(",",$addresses);

			// get only all uplines and all active and company accounts (account_status_id 1 and 3)
			$accounts = $this->members_model->get_member_accounts("node_address in ({$new_addresses})",NULL,"LENGTH(node_address) DESC","account_id");
			
			$account_ids = array();
			foreach($accounts as $a)
				$account_ids[] = "'{$a->account_id}'";
			
			$account_ids = implode(",",$account_ids);
			
			$earnings = $this->members_model->get_member_earnings("account_id in ({$account_ids})",NULL,"earning_id DESC");			

			$html .= "<table class='table table-condensed table-striped table-bordered'>";

			$html .= "
			<thead><tr>
				<th>Account ID</th>
				<th>Pairing SP</th>
				<th>GC SP</th>
				<th>Pairing RS</th>
				<th>GC RS</th>
			</tr></thead>
			";
			// credit points and check for pairing
			foreach($earnings as $e)
			{

				// display points and check for pairings
				$html .= "
				<tbody><tr>
					<td>{$e->account_id}</td>
					<td align='right'>{$e->pairing_bonus_sp}</td>
					<td align='right'>{$e->gift_cheque_sp}</td>
					<td align='right'>{$e->pairing_bonus_rs}</td>
					<td align='right'>{$e->gift_cheque_rs}</td>
				</tr></tbody>";
			}		

			$html .= "</table>";
		}		

		$this->template->html = $html;
		$this->template->view('simulation');

	}	
	
	public function unilevel()
	{
		$account_id = $this->input->get_post("account_id");
		
		// get member_account
		$member_account = $this->members_model->get_member_account_by_account_id($account_id);

		$html = "<h2>{$account_id}</h2>";
		
		if (!empty($member_account))
		{
			// address of the one who encoded the rs card
			$uni_node_address = $member_account->uni_node;

			$_parsed = explode('.',$uni_node_address);

			$address_count = count($_parsed);

			$upline_addresses = array();

			// get all addresses including the one who encoded the sales
			for($i=$address_count - 1;$i>=0;$i--)
			{
				$new_address = array();
				for($j=0;$j<=$i;$j++) $new_address[] = $_parsed[$j];
				$upline_address = implode(".",$new_address);			
				$upline_addresses[] = "'{$upline_address}'";			
			}

			// get upline_accounts
			$upline_addresses = implode(",",$upline_addresses);

			// get all uplines
			//$accounts = $this->members_model->get_member_accounts("uni_node in ({$upline_addresses})",NULL,"LENGTH(uni_node) DESC");		
			$sql = "
				SELECT
					a.account_id,
					a.account_status_id,
					a.left_rs,
					a.right_rs,
					a.pairs_rs,
					a.gc_rs,
					a.flushout_rs,
					b.pairing_bonus_rs,
					b.gift_cheque_rs,
					b.unilevel_commission
				FROM
					cm_member_accounts a					
				LEFT JOIN
					cm_member_earnings b
				ON
					a.account_id = b.account_id
				WHERE
					a.uni_node in ({$upline_addresses})
				ORDER BY
					LENGTH(a.uni_node) DESC";
					
			$query = $this->db->query($sql);

			// credit repeat sales commission to active upline addresses
			$counter = 0;			
			$html .= "<table class='table table-condensed table-striped table-bordered'>";
			$html .= "
			<thead><tr>
				<th>Account ID</th>
				<th>Status</th>
				<th>Count</th>
				<th>Left RS</th>
				<th>Right RS</th>
				<th>Pairs RS</th>
				<th>GC RS</th>
				<th>Flushout RS</th>
				<th>Pairs RS</th>
				<th>GC RS</th>
				<th>Unilevel Commission</th>
			</tr></thead>
			";
			// credit points and check for pairing
			foreach($query->result() as $a)
			{
				$status = $a->account_status_id<>2 ? "ACTIVE" : "INACTIVE";
				
				if ($status=='ACTIVE') 
				{
					$counter++;
					$count = $counter;
				}
				else
				{
					$count = '';
				}
				
				// display points and check for pairings
				$html .= "
				<tbody><tr>
					<td>{$a->account_id}</td>
					<td>{$status}</td>
					<td align='right'>{$count}</td>
					<td align='right'>{$a->left_rs}</td>
					<td align='right'>{$a->right_rs}</td>
					<td align='right'>{$a->pairs_rs}</td>
					<td align='right'>{$a->gc_rs}</td>
					<td align='right'>{$a->flushout_rs}</td>
					<td align='right'>{$a->pairing_bonus_rs}</td>
					<td align='right'>{$a->gift_cheque_rs}</td>
					<td align='right'>{$a->unilevel_commission}</td>
				</tr></tbody>";
				
				if ($counter>=10) break; 
			}		

			$html .= "</table>";

		}			

		$this->template->html = $html;
		$this->template->view('simulation');

	}

}