<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sms_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(				
			'sms_incoming' => 'tr_sms_incoming',
			'sms_outgoing' => 'tr_sms_outgoing',
			'telco_prefix' => 'rf_telco_prefix'
		);
	}
	
	//INCOMING
	function get_sms_incoming($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('sms_incoming', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_sms_incoming_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('sms_incoming', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_sms_incoming_by_id($id) {
        $result = $this->get_sms_incoming(array('sms_incoming_id' => $id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_sms_incoming($data) 
    {
        return $this->insert('sms_incoming', $data);
    }
	
	function update_sms_incoming($data, $where) 
    {
        return $this->update('sms_incoming', $data, $where);
    }	

	function delete_sms_incoming($where) 
    {
        return $this->delete('sms_incoming', $where);
    }
	
	
	// OUTGOING
	function get_sms_outgoing($where = null, $limit = null, $orderby = null, $fields = null) {
	    $query = $this->fetch('sms_outgoing', $fields, $where, $orderby, $limit);
	    $row = $query->result();
	    $query->free_result();
	    return $row;
	}

	function get_sms_outgoing_count($where = null) {
	    // do a sql count instead of row count
	    $query = $this->fetch('sms_outgoing', 'count(1) as cnt', $where);
	    $row = $query->first_row();
	    $query->free_result();
	    return $row->cnt;
	}

	function get_sms_outgoing_by_id($id) {
	    $result = $this->get_sms_outgoing(array('sms_outgoing_id' => $id));
	    $row = NULL;
	    if (count($result) > 0) {
	        $row = $result[0];
	    }
	    return $row;
	}

	function insert_sms_outgoing($data) 
	{
	    return $this->insert('sms_outgoing', $data);
	}

	function update_sms_outgoing($data, $where) 
	{
	    return $this->update('sms_outgoing', $data, $where);
	}	

	function delete_sms_outgoing($where) 
	{
	    return $this->delete('sms_outgoing', $where);
	}
	
	// TELCO PREFIX
	function get_telco_prefix($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('telco_prefix', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	


}    

// end of file