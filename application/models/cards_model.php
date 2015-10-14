<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cards_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(				
				'card_types' => 'rf_card_types',
				'card_series' => 'rf_card_series',
				'card_type_series' => 'rf_card_type_series',
				'rs_cards' => 'is_rs_cards',
				'sp_cards' => 'is_sp_cards',
				'card_types_view' => 'rf_card_types_view',
				'card_type_bonuses' => 'rf_card_type_bonuses',
				'modifiers' => 'rf_modifiers',
				'card_modifiers' => 'rf_card_modifiers',
				'upgrade_card_mapping' => 'rf_upgrade_card_mapping',
				'card_product_selections' => 'tr_card_product_selections'
		);
	}
	
	
	/* -- FOR CARD TYPES MODULE -- */	
	function get_card_types($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('card_types', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_card_types_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('card_types', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_card_type_by_id($voucher_type_id) {
        $result = $this->get_card_types(array('card_type_id' => $voucher_type_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_card_type_by_code($code) {
        $result = $this->get_card_types(array('code' => $code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_card_types($data) 
    {
        return $this->insert('card_types', $data);
    }
	
	function update_card_types($data, $where) 
    {
        return $this->update('card_types', $data, $where);
    }	

	function delete_card_types($where) 
    {
        return $this->delete('card_types', $where);
    }

    /* -- FOR CARD TYPE SERIES MODULE -- */
    function get_card_type_series($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('card_type_series', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_card_type_series($data) 
    {
        return $this->insert('card_type_series', $data);
    }
	
	function update_card_type_series($data, $where) 
    {
        return $this->update('card_type_series', $data, $where);
    }	

	function delete_card_type_series($where) 
    {
        return $this->delete('card_type_series', $where);
    }

    /* -- FOR CARD TYPE BONUSES MODULE -- */
    function get_card_type_bonuses($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('card_type_bonuses', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

	function get_card_type_bonuses_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('card_type_bonuses', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }	
	
    function insert_card_type_bonuses($data) 
    {
        return $this->insert('card_type_bonuses', $data);
    }
	
	function update_card_type_bonuses($data, $where) 
    {
        return $this->update('card_type_bonuses', $data, $where);
    }	

	function delete_card_type_bonuses($where) 
    {
        return $this->delete('card_type_bonuses', $where);
    }

	/* -- FOR CARD SERIES MODULE -- */	
	function get_card_series($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('card_series', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_card_series_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('card_series', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_card_series_by_id($voucher_type_id) {
        $result = $this->get_card_series(array('card_series_id' => $voucher_type_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_card_series_by_series_number($series_number) {
        $result = $this->get_card_series(array('series_number' => $series_number));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_card_series($data) 
    {
        return $this->insert('card_series', $data);
    }
	
	function update_card_series($data, $where) 
    {
        return $this->update('card_series', $data, $where);
    }	

	function delete_card_series($where) 
    {
        return $this->delete('card_series', $where);
    }

	/* -- FOR RS CARD -- */	
	function get_rs_card($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('rs_cards', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_rs_card_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('rs_cards', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_rs_card_by_id($rs_card_id) {
        $result = $this->get_rs_card(array('rs_card_id' => $rs_card_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_rs_card_by_card_id($card_id) {
        $result = $this->get_rs_card(array('card_id' => $card_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_rs_card($data) 
    {
        return $this->insert('rs_cards', $data);
    }
	
	function update_rs_card($data, $where) 
    {
    	$this->update_card_type_series_used($data,$where,'is_rs_cards');
        return $this->update('rs_cards', $data, $where);
    }	

	function delete_rs_card($where) 
    {
        return $this->delete('rs_cards', $where);
    }

	/* -- FOR SP MASTER -- */	
	function get_sp_card($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('sp_cards', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_sp_card_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('sp_cards', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_sp_card_by_id($sp_card_id) {
        $result = $this->get_sp_card(array('sp_card_id' => $sp_card_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_sp_card_by_card_id($card_id) {
        $result = $this->get_sp_card(array('card_id' => $card_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_sp_card($data) 
    {
        return $this->insert('sp_cards', $data);
    }
	
	function update_sp_card($data, $where) 
    {
    	$this->update_card_type_series_used($data,$where,'is_sp_cards');
        return $this->update('sp_cards', $data, $where);
    }	

	function delete_sp_card($where) 
    {
        return $this->delete('sp_cards', $where);
    }
	
	/* -- FOR RF CARD TYPES VIEW -- */	
	function get_card_types_view($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('card_types_view', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_card_types_view_count($where = null) {
        $query = $this->fetch('card_types_view', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_card_types_view_by_id($card_type_id) {
        $result = $this->get_card_types_view(array('card_type_id' => $card_type_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_card_types_view_by_series($series_number) {
        $result = $this->get_card_types_view(array('series_number' => $series_number));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function parse_rfid_cards($cards_list = "")
	{
		if(strcmp($cards_list,"") == 0) return array();
		
		//length of card ids, to be used for padding left with zeroes
		$card_id_length = 10;
		
		$cards_list = explode(",", $cards_list);
		
		//parse for dashes, expand into an array
		for($sp=0; $sp<count($cards_list);$sp++)
		{
			$list = $cards_list[$sp];
			if(strpos($list,"-") != false)
			{
				$ctr = $sp;
				$first = substr($list, 0,strpos($list,"-"));
				$last = substr($list,strpos($list,"-")+1);
				
				//insert new elements from dahsed number
				for($i=$first;$i<=$last;$i++)
				{
					//array shift to the right to make way for the new entry
					$j = count($cards_list);
					do{
						$cards_list[$j] = $cards_list[$j-1];
						
						$j--;
					}while($j > $ctr);
					
					$cards_list[$ctr++] = str_pad($i,$card_id_length,"0",STR_PAD_LEFT);
				}
				//replace dashed number with the rest of the array
				array_splice($cards_list,$ctr,count($cards_list),array_slice($cards_list,$ctr+1));
				//replace $key with ctr to apply the change in size
				$sp = $ctr;
			}
		}
		
		return $cards_list;
	}
	
	function parse_pay_cards($cards_list = "")
	{
		if(strcmp($cards_list,"") == 0) return array();
		
		//length of card ids, to be used for padding left with zeroes
		$card_id_length = 10;
		
		$cards_list = explode(",", $cards_list);
		
		//parse for dashes, expand into an array
		for($sp=0; $sp<count($cards_list);$sp++)
		{
			$list = $cards_list[$sp];
			if(strpos($list,"-") != false)
			{
				$ctr = $sp;
				$first = substr($list, 0,strpos($list,"-"));
				$last = substr($list,strpos($list,"-")+1);
				
				//insert new elements from dahsed number
				for($i=$first;$i<=$last;$i++)
				{
					//array shift to the right to make way for the new entry
					$j = count($cards_list);
					do{
						$cards_list[$j] = $cards_list[$j-1];
						
						$j--;
					}while($j > $ctr);
					
					$cards_list[$ctr++] = str_pad($i,$card_id_length,"0",STR_PAD_LEFT);
				}
				//replace dashed number with the rest of the array
				array_splice($cards_list,$ctr,count($cards_list),array_slice($cards_list,$ctr+1));
				//replace $key with ctr to apply the change in size
				$sp = $ctr;
			}
		}
		
		return $cards_list;
	}

	/*parse entry for rs and sp cards*/
	function parse_rs_sp_cards($cards_list = "")
	{
		if(strcmp($cards_list,"") == 0) return array();
		
		//length of card ids, to be used for padding left with zeroes
		$card_id_length = 10;
		
		$cards_list = explode(",", $cards_list);
		
		//parse for dashes, expand into an array
		for($sp=0; $sp<count($cards_list);$sp++)
		{
			$list = $cards_list[$sp];
			if(strpos($list,"-") != false)
			{
				$ctr = $sp;
				$first = substr($list, 0,strpos($list,"-"));
				$last = substr($list,strpos($list,"-")+1);
				
				//insert new elements from dahsed number
				for($i=$first;$i<=$last;$i++)
				{
					//array shift to the right to make way for the new entry
					$j = count($cards_list);
					do{
						$cards_list[$j] = $cards_list[$j-1];
						
						$j--;
					}while($j > $ctr);
					
					$cards_list[$ctr++] = str_pad($i,$card_id_length,"0",STR_PAD_LEFT);
				}
				//replace dashed number with the rest of the array
				array_splice($cards_list,$ctr,count($cards_list),array_slice($cards_list,$ctr+1));
				//replace $key with ctr to apply the change in size
				$sp = $ctr;
				$sp--;
			}
		}
		
		return $cards_list;
	}
	
	/*check if sp cards are already existing, activated, used, or invalidated*/
	function check_sp_cards($cards_list = array())
	{
		if(empty($cards_list)) return array("status"=> "ok","msg" => "No errors found.","errors" => array());
		
		//check cards for errors
		$has_error = false;
		$errors = array();
		foreach($cards_list as $card_id)
		{
			$err = array();
			$err = (object) $err;
			$card = $this->get_sp_card_by_card_id($card_id);
			if(empty($card))
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} does not exist.";
				array_push($errors,$err);
				continue;
			}
			
			/*if(strcmp($card->status,"ACTIVE") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} has already been released.";
				array_push($errors,$err);
				continue;
			}*/
			
			if(strcmp($card->status,"USED") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} has already been used.";
				array_push($errors,$err);
				continue;
			}
			
			if(strcmp($card->status,"INVALID") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} invalid.";
				array_push($errors,$err);
				continue;
			}
			
			if(strcmp($card->type,"P2P") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} is a P2P Card.";
				array_push($errors, $err);
				continue;
			}
		}
		
		//return errors
		if($has_error)
		{
			return array("status"=> "error","msg" => "Errors found.","errors" => $errors);
		}
		
		return array("status"=> "ok","msg" => "No errors found.","errors" => array());
	}
	
	/*check if sp cards are already existing, activated, used, or invalidated*/
	function check_p2p_cards($cards_list = array())
	{
		if(empty($cards_list)) return array("status"=> "ok","msg" => "No errors found.","errors" => array());
		
		//check cards for errors
		$has_error = false;
		$errors = array();
		foreach($cards_list as $card_id)
		{
			$err = array();
			$err = (object) $err;
			$card = $this->get_sp_card_by_card_id($card_id);
			if(empty($card))
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} does not exist.";
				array_push($errors,$err);
				continue;
			}
			
			/*if(strcmp($card->status,"ACTIVE") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} has already been released.";
				array_push($errors,$err);
				continue;
			}*/
			
			if(strcmp($card->status,"USED") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} has already been used.";
				array_push($errors,$err);
				continue;
			}
			
			if(strcmp($card->status,"INVALID") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} invalid.";
				array_push($errors,$err);
				continue;
			}

			if(strcmp($card->type,"P2P") != 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} is not a P2P Card.";
				array_push($errors, $err);
				continue;
			}
		}
		
		//return errors
		if($has_error)
		{
			return array("status"=> "error","msg" => "Errors found.","errors" => $errors);
		}
		
		return array("status"=> "ok","msg" => "No errors found.","errors" => array());
	}
	
	function release_sp_cards($released_to = "",$cards_list = array())
	{
		if(empty($cards_list)) return;
				
		//tag cards as activated
		$this->db->where_in('card_id', array_values($cards_list));
		return $this->db->update($this->_TABLES['sp_cards'],array("status" => "ACTIVE","released_to" => $released_to,"released_timestamp" => date('Y-m-d H:i:s')));
	}
	
	function check_rs_cards($cards_list = array())
	{
		if(empty($cards_list)) return array("status"=> "ok","msg" => "No errors found.","errors" => array());
		
		//check cards for errors
		$has_error = false;
		$errors = array();
		foreach($cards_list as $card_id)
		{
			$err = array();
			$err = (object) $err;
			$card = $this->get_rs_card_by_card_id($card_id);
			if(empty($card))
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} does not exist.";
				array_push($errors,$err);
				continue;
			}
			
			/*if(strcmp($card->status,"ACTIVE") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} has already been released.";
				array_push($errors,$err);
				continue;
			}*/
			
			if(strcmp($card->status,"USED") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} has already been used.";
				array_push($errors,$err);
				continue;
			}
			
			if(strcmp($card->status,"INVALID") == 0)
			{
				$has_error = true;
				$err->card_id = $card_id;
				$err->error = "Card ID {$card_id} invalid.";
				array_push($errors,$err);
				continue;
			}
		}
		
		//return errors
		if($has_error)
		{
			return array("status"=> "error","msg" => "Errors found.","errors" => $errors);
		}
		
		return array("status"=> "ok","msg" => "No errors found.","errors" => array());
	}
	
	function release_rs_cards($released_to = "",$cards_list = array())
	{
		if(empty($cards_list)) return;
		
		//tag cards as activated
		$this->db->where_in('card_id',  array_values($cards_list));
		return $this->db->update($this->_TABLES['rs_cards'],array("status" => "ACTIVE","released_to" => $released_to,"released_timestamp" => date('Y-m-d H:i:s')));
	}

	/* CARD MODIFIERS */
	function get_modifiers($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('modifiers', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_modifiers($data)
    {
        return $this->insert('modifiers', $data);
    }

    function delete_modifier($where)
    {
    	return $this->delete('modifiers',$where);
    }

    function update_modifier($data, $where) 
    {
        return $this->update('modifiers', $data, $where);
    }	

    function get_modifiers_by_card_type($type)
    {
    	// get card type
		$card_type = $this->get_card_types(array(
			'code' => $type
		));
		$card_type = $card_type[0];

		// get card type modifiers
		$card_modifiers = $this->get_card_modifiers(array(
			'card_type_id' => $card_type->card_type_id
		));
		$ret = array();
		foreach($card_modifiers as $mod)
		{
			$modifier = $this->get_modifiers(array(
				'modifier_id' => $mod->modifier_id
			));
			$ret[] = $modifier[0]->modifier_name;
		}
		return $ret;
    }

	/* CARD MODIFIERS */
	function get_card_modifiers($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('card_modifiers', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_card_modifiers($data)
    {
        return $this->insert('card_modifiers', $data);
    }

    function delete_card_modifier($where)
    {
    	return $this->delete('card_modifiers',$where);
    }

    function update_card_modifier($data, $where) 
    {
        return $this->update('card_modifiers', $data, $where);
    }	

    function update_card_type_series_used($data,$where,$table = '')
    {
    	$increment = false;
    	$autogenerated = false;
		$tmp_where = '';
		if(is_array($data))
		{
			if(isset($data['status']))
				if(strtolower($data['status']) == 'used')
					$increment = true;
		}
		else
		{
			if(stripos($data,'used'))
				$increment = true;
		}

		if($increment)
		{
			if(is_array($where))
			{
				if(isset($where['card_id']))
				{
					$card_id = $where['card_id'];
				}
				
				if(isset($where['sp_card_id']))
				{
					$sp_card = $this->get_sp_card(array('sp_card_id'=>$where['sp_card_id']));
					if(sizeof($sp_card) > 0)
						$card_id = 	$sp_card[0]->card_id;
				}

				if(isset($where['rs_card_id']))
				{
					$rs_card = $this->get_rs_card(array('rs_card_id'=>$where['rs_card_id']));
					if(sizeof($rs_card) > 0)
						$card_id = 	$rs_card[0]->card_id;
				}

			}
			else
			{
				$tmp = explode('=',$where);
	
				if(trim($tmp[0]) == 'card_id')
				{
					$card_id = trim(str_replace("'","",$tmp[1]));
				}

			}

			//check if autogen
			if($table == 'is_rs_cards')
			{
				$rs_card_details = $this->get_rs_card_by_card_id($card_id);
				if($rs_card_details)
					$autogenerated = $rs_card_details->autogenerated;
			}

			if($table == 'is_sp_cards')
			{
				$sp_card_details = $this->get_sp_card_by_card_id($card_id);
				if($sp_card_details)
					$autogenerated = $sp_card_details->autogenerated;
			}	

			if($autogenerated)
				return;

			$where = "card_series_type_id > 0 AND ".$card_id." BETWEEN starting_index AND ending_index";
			$card_type = $this->get_card_type_series($where);

			$used = $card_type[0]->used + 1;
			$this->update_card_type_series(array('used'=>$used),array('card_series_type_id'=>$card_type[0]->card_series_type_id));
		}

		return;
		
    }

    /* upgrade_card_mapping */
    function get_upgrade_card_mapping($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('upgrade_card_mapping', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_upgrade_card_mapping($data)
    {
        return $this->insert('upgrade_card_mapping', $data);
    }

    function update_upgrade_card_mapping($data, $where)
    {
        return $this->update('upgrade_card_mapping', $data, $where);
    }

    function delete_upgrade_card_mapping($where)
    {
        return $this->delete('upgrade_card_mapping', $where);
    }

    /* card_product_selections */
    function get_card_product_selections($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('card_product_selections', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_card_product_selections($data)
    {
        return $this->insert('card_product_selections', $data);
    }

    function update_card_product_selections($data, $where)
    {
        return $this->update('card_product_selections', $data, $where);
    }

    function delete_card_product_selections($where)
    {
        return $this->delete('card_product_selections', $where);
    }

    function get_card_product_selections_count($where = null) {
        $query = $this->fetch('card_types', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }

    function generate_card_type($card_type_code)
    {
    	$this->load->model('settings_model');
    	$card_type_data = $this->get_card_types(array('code' => $card_type_code));
    	if(sizeof($card_type_data) == 0) return false;

    	$card_type_data = $card_type_data[0];
    	$char_pool = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
    	$current_card_length = $this->settings->autogenerate_card_length;
    	$pretty_code = str_replace("-", "", $card_type_code);
    	$append_length = $current_card_length - strlen($pretty_code);
    	$max_capacity = pow(strlen($char_pool), $append_length);
    	
    	$method = ($card_type_data->is_package)?'get_sp_card':'get_rs_card';
    	$where = "LEFT(card_id," . strlen($pretty_code) . ") = '{$pretty_code}' AND LENGTH(card_id) = '{$current_card_length}' ";
    	$existing_cards = $this->$method($where, null, null, "COUNT(1) AS cnt");
    	$existing_cards = $existing_cards[0];

    	if($existing_cards->cnt > ($max_capacity - 1000)) {
    		$current_card_length++;
    		$this->settings_model->update_settings(array(
    			'value' => $current_card_length
    		), array(
    			'slug' => 'autogenerate_card_length'
    		));
    	}
    	
    	$generated = "";
    	do {
    		// try generate
    		$generated = "";
			for ($i = 0; $i < $append_length; $i++) $generated .= $char_pool[rand(0, strlen($char_pool) - 1)];

			// check if existing
			$tmp_card_id = $pretty_code . $generated;
			$existing_card = $this->$method(array('card_id' => $tmp_card_id), null, null, "COUNT(1) AS cnt");
    	} while ($existing_card[0]->cnt > 0);
    	
		$card_id = $pretty_code . $generated;
		$card_code = strtoupper(substr(md5($pretty_code . $card_id . date("Y-m-d H:i:s")), 1, 10));

		$return_array = array(
			'card_id' => $card_id,
			'card_code' => $card_code
		);

		return $return_array;
    }
}    

// end of file