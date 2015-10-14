<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ess extends Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		echo "encoded sales summary";
	}

	public function process()
	{
		
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		//$card_types_list = $this->input->get_post('card_types');

		//$start_date = '2012-12-01';
		//$end_date = '2012-12-31';
		
		// DROP tmp_encoded_sales		
		$sql = "DROP TABLE IF EXISTS tmp_encoded_sales;";
		$this->db->query($sql);
		
		// CREATE tmp_encoded_sales		
		$sql = "
		CREATE TABLE tmp_encoded_sales (
			member_id INT,
			account_id VARCHAR(64),
			node_address TEXT,
			type VARCHAR(2),
			card_count INT,
			KEY (member_id),
			KEY (account_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";		
		$this->db->query($sql);
		
		// GET LIST OF MEMBERS FOR ENCODED SALES SUMMARY
		
		$sql = "SELECT * FROM rf_member_list_for_encoded_sales_summary WHERE is_active = 1";
		$query = $this->db->query($sql);
		$result = $query->result();
		
		//$card_types = explode("_",$card_types_list);
		//$card_types = array('73','74','75','76','78','88','89','99');

		//$sp_cards = array('65','76','78','88','89','93','99');

		$sp_cards = array('11','12','76','78','88','89','99');	
		$rs_cards = array('73','74','75');
		
		
		// FOR SP CARDS
		foreach ($sp_cards as $t)
		{	
			if($t != '75')
			{
				foreach ($result as $r)
				{
					$member_id = $r->member_id;
					$account_id = $r->account_id;
					$type = $t;
					$address = $r->node;
					
					$sql = "
						INSERT INTO tmp_encoded_sales (member_id,account_id,type,node_address,card_count)
						(SELECT {$member_id},'{$account_id}','{$type}','{$address}',count(card_id) FROM is_sp_cards WHERE date(used_timestamp) between '{$start_date}' and '{$end_date}' and substring(card_id,1,2)='{$type}' and card_id in (SELECT account_id FROM cm_member_accounts WHERE node_address LIKE '{$address}%'))";
					$this->db->query($sql);
				}
			}
		}
		
		// FOR RS CARDS		
		foreach($rs_cards as $t)
		{
			foreach ($result as $r)
			{
				$member_id = $r->member_id;
				$account_id = $r->account_id;
				//$type = '75';
				$type = $t;
				$address = $r->node;
				
				$sql = "
					INSERT INTO tmp_encoded_sales (member_id,account_id,type,node_address,card_count)
					(SELECT {$member_id},'{$account_id}','{$type}','{$address}',count(card_id) FROM is_rs_cards WHERE date(used_timestamp) between '{$start_date}' and '{$end_date}' and substring(card_id,1,2)='{$type}' and account_id in (SELECT account_id FROM cm_member_accounts WHERE node_address LIKE '{$address}%'))";
				$this->db->query($sql);
			}
		}


	}
}