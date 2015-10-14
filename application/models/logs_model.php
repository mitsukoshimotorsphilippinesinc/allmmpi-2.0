<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logs_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'inventory_logs' => 'tr_inventory_logs',
			'cards_logging' => 'tr_cards_logging',
			'igpsm_sales_logs' => 'tr_igpsm_sales_logs',
			'voided_rs' => 'tr_voided_rs_cards',
			'voided_cards' => 'tr_voided_used_cards'
		);

	}

	function get_inventory_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('inventory_logs', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }
	
	function insert_inventory_log($data) 
	{
		return $this->insert('inventory_logs', $data);
	}

	function get_inventory_log_by_id($inventory_log_id) 
	{
		$result = $this->get_navigations(array('inventory_log_id' => $inventory_log_id), null, "insert_timestamp DESC");
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_inventory_logs_by_table_name($table_name) 
	{
		return $this->get_navigations(array('table_name' => $table_name),null,"insert_timestamp DESC");
	}
	
	function get_inventory_logs_by_user_id($user_id) 
	{
		return $this->get_navigations(array('user_id' => $user_id),null,"insert_timestamp DESC");
	}

	function get_inventory_log_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('inventory_logs', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_cards_logging($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('cards_logging', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }
	
	
	function insert_cards_log($transaction_id = "0", $cards_entered = array(), $member_id = 0)
	{
		foreach($cards_entered as $type => $cards)
		{
			if(strcmp($type,"SP") == 0 || strcmp($type,"P2P") == 0 || strcmp($type,"RS") == 0 || strcmp($type,"RF") == 0 || strcmp($type,"METROBANK") == 0)
			{
				$data = array();
				if(!empty($cards))
				{
					foreach($cards as $card_id)
					{
						$user_id = 0;
						if(!empty($this->user->user_id)) $user_id = $this->user->user_id;
						
						$row = array(
							'type' => $type,
							'card_id' => $card_id,
							'transaction_id' => $transaction_id,
							'cards_entered' => json_encode($cards_entered),
							'user_id' => $user_id,
							'member_id' => $member_id
						);

						array_push($data,$row);
					}

					$this->db->insert_batch('tr_cards_logging', $data);	
				}
			}
			else
			{
				continue;
			}
		}
	}
	
	function update_cards_log($data, $where)
	{
		return $this->update('cards_logging', $data, $where);
	}
	
	function get_igpsm_sales_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('igpsm_sales_logs', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }
	
	function insert_igpsm_sales_logs($data) 
	{
		return $this->insert('igpsm_sales_logs', $data);
	}
	
	function get_voided_rs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('voided_rs', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }
	
	function insert_voided_rs($data) 
	{
		return $this->insert('voided_rs', $data);
	}
	
	function update_voided_rs($data, $where)
	{
		return $this->update('voided_rs', $data, $where);
	}
	
	function delete_voided_rs($where) 
    {
        return $this->delete('voided_rs', $where);
    }

	function get_voided_cards($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('voided_cards', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }
	
	function insert_voided_card($data) 
	{
		return $this->insert('voided_cards', $data);
	}
	
	function update_voided_card($data, $where)
	{
		return $this->update('voided_cards', $data, $where);
	}
	
	function delete_voided_card($where) 
    {
        return $this->delete('voided_cards', $where);
    }
}