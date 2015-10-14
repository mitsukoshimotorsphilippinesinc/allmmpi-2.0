<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Machines_model extends Base_model
{
    function __construct() 
    {
        parent::__construct();
        $this->_TABLES = array(            
            'machines' => 'rf_is_machines'
        );
    }		
    
    function get_machines($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('machines', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_machines_count($where = null) 
	{
        $query = $this->fetch('machines', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }

	function get_machine_by_key($machine_key)
	{
		$result = $this->get_machines(array('machine_key' => $machine_key));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

    
     function insert_machines($data) {
        return $this->insert('machines', $data);
    }
    
    function update_machines($data, $where) {
        return $this->update('machines', $data, $where);
    }
    
    function get_machines_by_id($log_id) {
        $result = $this->get_machines(array('log_id' => $log_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
    
}

?>
