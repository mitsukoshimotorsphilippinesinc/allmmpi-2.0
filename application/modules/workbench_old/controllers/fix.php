<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*

DROP TABLE IF EXISTS tmp_account_gc_ctr;
CREATE TABLE `tmp_account_gc_ctr` (
  `account_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_id` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_gc_ctr` int(11) NOT NULL DEFAULT '0',
  `last_gc_ctr` int(11) NOT NULL DEFAULT '0',
  `prev_gc_ctr` int(11) NOT NULL DEFAULT '0',
  `last_gc_credit_log_id` int(11) NOT NULL DEFAULT '0',
  `prev_gc_credit_log_id` int(11) NOT NULL DEFAULT '0',
  `last_gc_credit_log_ids` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `prev_gc_credit_log_ids` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `last_gc_timestamp` datetime DEFAULT NULL,
  `prev_gc_timestamp` datetime DEFAULT NULL,
  `updated` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS tmp_account_gc_ctr_tracking;
CREATE TABLE `tmp_account_gc_ctr_tracking` (
  `account_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_id` int(11) unsigned NOT NULL DEFAULT '0',
  `credit_log_id` int(11) NOT NULL DEFAULT '0',
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `old_funds` decimal(10,2) NOT NULL DEFAULT '0.00',
  `new_funds` decimal(10,2) NOT NULL DEFAULT '0.00',
  `old_gift_cheques` decimal(10,2) NOT NULL DEFAULT '0.00',
  `new_gift_cheques` decimal(10,2) NOT NULL DEFAULT '0.00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

class Fix extends Base_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('members_model');
		$this->load->model('tracking_model');

		set_time_limit(0);
	}

	public function index() {
		
		echo "Workbench :: Fix";
		
	}

	public function info()
	{
		phpinfo();
		exit();
	}
	
	public function gc_pair_ctr()
	{

		// loop thru all accounts
		$rows = array();
		$limit = array('offset' => 0, 'rows' => 10000);

		print "Start : ".date("Y-m-d H:i:s")."<br/>";
		ob_flush();
		flush();

		do {
			usleep(1);
			unset($rows);
			gc_collect_cycles();
			$rows = array();
			$rows = $this->members_model->get_member_accounts(null, $limit);

			foreach ($rows as $item)
			{

				$data = array(
					'account_id' => $item->account_id,
					'member_id' => empty($item->member_id) ? 0 : $item->member_id,
					'updated_gc_ctr' => 0,
					'last_gc_ctr' => 0,
					'prev_gc_ctr' => 0,
					'last_gc_credit_log_id' => 0,
					'prev_gc_credit_log_id' => 0,
					'last_gc_credit_log_ids' => '',
					'prev_gc_credit_log_ids' => '',
					'last_gc_timestamp' => null,
					'prev_gc_timestamp' => null,
					'updated' => 0
				);
				$where = "account_id = '".$item->account_id."' and transaction_code in (101,106)";
				$credit_logs = $this->tracking_model->get_acct_credit_logs($where, array('offset' => 0, 'rows' => 20), "credit_log_id DESC");
				$last_gc_credit_log_ids = array();
				$prev_gc_credit_log_ids = array();
				$last_found = false;
				foreach ($credit_logs as $log_item)
				{
					if ($log_item->transaction_code == '101')
					{
						if (!$last_found) 
						{
							$data['last_gc_ctr'] += 1;
							array_push($last_gc_credit_log_ids, $log_item->credit_log_id);
						}
						else
						{
							$data['prev_gc_ctr'] += 1;
							array_push($prev_gc_credit_log_ids, $log_item->credit_log_id);
						}
							
					}
					else
					{
						if (!$last_found)
						{
							$last_found = true;
							array_push($last_gc_credit_log_ids, $log_item->credit_log_id);
							$data['last_gc_credit_log_id'] = $log_item->credit_log_id;
							$data['last_gc_timestamp'] = $log_item->insert_timestamp;
						}
						else
						{
							array_push($prev_gc_credit_log_ids, $log_item->credit_log_id);
							$data['prev_gc_credit_log_id'] = $log_item->credit_log_id;
							$data['prev_gc_timestamp'] = $log_item->insert_timestamp;
							break; // break the loop
						}
					}
				}

				$data['last_gc_credit_log_ids'] = json_encode($last_gc_credit_log_ids);
				$data['prev_gc_credit_log_ids'] = json_encode($prev_gc_credit_log_ids);

				$this->db->insert('tmp_account_gc_ctr',$data);

			}

			if (count($rows) > 0)
			{
				print "[".date("Y-m-d H:i:s")."] - Finished processing ".($limit['offset']+count($rows))." accounts.<br/>";
				ob_flush();
				flush();
			}
			
			$limit['offset'] += 10000;
		} while (count($rows) > 0);
		print "Done : ".date("Y-m-d H:i:s")."<br/>";
	}

	public function gc_pair_fix()
	{
		// select * from tmp_account_gc_ctr where prev_gc_ctr < 4 and last_gc_timestamp >= '2012-12-08 00:00:00'
		// 
		// Credit gift cheque for fifth pair from <card_id>
		// Credit SP pairing bonus from <card_id>
		

		$rows = array();
		$limit = array('offset' => 0, 'rows' => 10000);
		$where = "prev_gc_ctr < 4 and last_gc_timestamp >= '2012-12-08 00:00:00'";

		print "Start : ".date("Y-m-d H:i:s")."<br/>";
		print "=========================================<br/>";
		ob_flush();
		flush();

		do {
			usleep(1);
			$this->db->where($where);
			$this->db->limit($limit['rows'],$limit['offset']);
			$this->db->from('tmp_account_gc_ctr');
			$query = $this->db->get();
			$rows = $query->result();
			$query->free_result();

			foreach ($rows as $item)
			{

				print "Account ID : ".$item->account_id."<br/>";
				print "Credit Log ID : ".$item->last_gc_credit_log_id."<br/>";

				$last_gc_ctr = $item->last_gc_ctr;
				$prev_gc_ctr = $item->prev_gc_ctr;
				$credit_log_ids = array_reverse(json_decode($item->last_gc_credit_log_ids));
				$prev_credit_log_ids = array_reverse(json_decode($item->prev_gc_credit_log_ids));
				print "Last GC Counter : ".$last_gc_ctr."<br/>";
				print json_encode($credit_log_ids)."<br/>";
				print "Prev GC Counter : ".$prev_gc_ctr."<br/>";
				print json_encode($prev_credit_log_ids)."<br/>";

				$idx = 4 - $prev_gc_ctr;
				if ($last_gc_ctr == 0) $idx = 0;

				print "IDX : ".$idx."<br/>";


				if ($idx == 0 || !isset($credit_log_ids[$idx]))
				{
					// just convert gc to sp
					print "Covert GC to SP :<br/>";
					print json_encode($this->_change_credit_log_gc_to_sp($item->last_gc_credit_log_id));
					print "<br/>";
					$last_gc_ctr = count($credit_log_ids)+intval($item->prev_gc_ctr);
				}
				else
				{
					// convert gc to sp based on $last_gc_credit_log_id
					// convert sp to gc based on $idx of $credit_log_ids
					print "Covert GC to SP :<br/>";
					print json_encode($this->_change_credit_log_gc_to_sp($item->last_gc_credit_log_id, false));
					print "<br/>";
					print "Covert SP to GC :<br/>";
					print json_encode($this->_change_credit_log_sp_to_gc($credit_log_ids[$idx], false));
					print "<br/>";
					$last_gc_ctr = $last_gc_ctr - $idx;
					
				}

				$sql = "UPDATE tmp_account_gc_ctr SET updated_gc_ctr = {$last_gc_ctr}, updated = 1 where account_id = '{$item->account_id}'";
				print "Update SQL : " . $sql . "<br/>";
				$this->db->query($sql);

				print "Update Last GC Counter : " . $last_gc_ctr . "<br/>";
				print "---------------------------------------------------------------------------------------<br/>";
				ob_flush();
				flush();

			}

			

			
			if (count($rows) > 0)
			{
				print "[".date("Y-m-d H:i:s")."] - Finished processing ".($limit['offset']+count($rows))." accounts.<br/>";
				ob_flush();
				flush();
			}
			$limit['offset'] += 10000;

		} while (count($rows) > 0);
		print "=========================================<br/>";
		print "Done : ".date("Y-m-d H:i:s")."<br/>";

	}

	public function _change_credit_log_gc_to_sp($credit_log_id, $update_funds = true)
	{
		// Credit SP pairing bonus from <card_id>
		$this->db->where(array('credit_log_id' => $credit_log_id));
		$this->db->from('tr_member_acct_credit_logs');
		$query = $this->db->get();
		$row = $query->first_row();
		$query->free_result();

		if (!empty($row))
			if ($row->transaction_code = "106")
			{
				$amount = $row->amount;
				$card_id = $row->card_id;
				$remarks = 
				$data = array(
					'remarks' => "Credit SP pairing bonus from ".$card_id.".",
					'transaction_code' => '101',
					'type' => 'FUNDS'
				);

				if ($update_funds)
				{
					// SQL Update funds and GC
					$sql = "UPDATE cm_members SET funds = funds + {$amount}, gift_cheques = gift_cheques - {$amount} WHERE member_id = {$row->member_id}";
					$data['insert_timestamp'] = date("Y-m-d H:i:s");
				}

				$this->db->where(array('credit_log_id' => $credit_log_id));
				$this->db->update('tr_member_acct_credit_logs', $data); 

				if ($update_funds)
				{
					// Execute Update funds and GC
					$member1 = $this->members_model->get_member_by_id($row->member_id);
					$this->db->query($sql);
					$member2 = $this->members_model->get_member_by_id($row->member_id);
					// insert tracking
					$data = array(
						'member_id' => $row->member_id,
						'account_id' => $row->account_id,
						'credit_log_id' => $row->credit_log_id,
						'type' => 'gc_to_sp',
						'old_funds' => $member1->funds,
						'new_funds' => $member2->funds,
						'old_gift_cheques' => $member1->gift_cheques,
						'new_gift_cheques' => $member2->gift_cheques
					);
					$this->db->insert('tmp_account_gc_ctr_tracking', $data);
				}
				
			}

		return $row;
	}

	public function _change_credit_log_sp_to_gc($credit_log_id, $update_funds = true)
	{
		// Credit gift cheque for fifth pair from <card_id>
		$this->db->where(array('credit_log_id' => $credit_log_id));
		$this->db->from('tr_member_acct_credit_logs');
		$query = $this->db->get();
		$row = $query->first_row();
		$query->free_result();

		if (!empty($row))
			if ($row->transaction_code = "106")
			{
				$amount = $row->amount;
				$card_id = $row->card_id;
				$remarks = 
				$data = array(
					'remarks' => "Credit gift cheque for fifth pair from ".$card_id.".",
					'transaction_code' => '106',
					'type' => 'GC'
				);

				if ($update_funds)
				{
					// SQL Update funds and GC
					$sql = "UPDATE cm_members SET funds = funds - {$amount}, gift_cheques = gift_cheques + {$amount} WHERE member_id = {$row->member_id}";
					$data['insert_timestamp'] = date("Y-m-d H:i:s");
				}

				$this->db->where(array('credit_log_id' => $credit_log_id));
				$this->db->update('tr_member_acct_credit_logs', $data); 

				if ($update_funds)
				{
					// Execute Update funds and GC
					$member1 = $this->members_model->get_member_by_id($row->member_id);
					$this->db->query($sql);
					$member2 = $this->members_model->get_member_by_id($row->member_id);
					// insert tracking
					$data = array(
						'member_id' => $row->member_id,
						'account_id' => $row->account_id,
						'credit_log_id' => $row->credit_log_id,
						'type' => 'sp_to_gc',
						'old_funds' => $member1->funds,
						'new_funds' => $member2->funds,
						'old_gift_cheques' => $member1->gift_cheques,
						'new_gift_cheques' => $member2->gift_cheques
					);
					$this->db->insert('tmp_account_gc_ctr_tracking', $data);
				}
				
				
			}

		return $row;
	}
	
}
