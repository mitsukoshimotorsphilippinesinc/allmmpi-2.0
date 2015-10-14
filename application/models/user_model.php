<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'user' => 'sa_user',
			'user_privilege'=>'sa_user_privilege',
			'user_privilege_view'=>'sa_user_privilege_view',
			'user_roles' => 'sa_user_roles',
			'user_role_privilege'=>'sa_user_role_privilege',
			'user_role_privilege_view'=>'sa_user_role_privilege_view',
			'privilege'=>'sa_privilege'
		);

	}

	function get_user($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('user', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_user($data) 
	{
		return $this->insert('user', $data);
	}

	function update_user($data, $where) 
	{
		return $this->update('user', $data, $where);
	}

	function delete_user($where) 
	{
		return $this->delete('user', $where);
	}

	function get_user_by_id($user_id) 
	{
		$result = $this->get_user(array('user_id' => $user_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_user_by_id_number($id_number) 
	{
		$result = $this->get_user(array('id_number' => $id_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_user_by_username($username) 
	{
		$result = $this->get_user(array('username' => $username));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_user_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('user', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_user($search, $query, $limit = null, $orderby = null, $fields = null)
	{
		// clear previous get request
		$this->db->flush_cache();

		$this->db->distinct();
		$this->db->like($search,$query,'both');
		
		// No override function, procede with fetch
		($fields!=null) ? $this->db->select($fields) : '';
		($limit!=null) ? $this->db->limit($limit['rows'],$limit['offset']) : '';
		($orderby!=null) ? $this->db->order_by($orderby) : '';

		// set table to use
		$this->db->from($this->_TABLES['user']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	
	function user_search_count($search,$query)
	{
		// do a sql count instead of row count
		$query = $this->search_user($search,$query,NULL,NULL,'count(1) as cnt');
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_user_privilege($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('user_privilege_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_user_privilege($data) 
	{
		return $this->insert('user_privilege', $data);
	}

	function update_user_privilege($data, $where) 
	{
		return $this->update('user_privilege', $data, $where);
	}

	function delete_user_privilege($where) 
	{
		return $this->delete('user_privilege', $where);
	}

	function get_user_privilege_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('user_privilege', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function is_user_allowed($uri_string)
	{
		
		$privileges = $this->get_user_privilege("user_id  = {$this->user->user_id}");
		
		//var_dump($uri_string);

		$result = false;
		
        $uri_strings = array();		
        $privilege_uris = array();		
	
		// merge all privileges
		foreach($privileges as $p)
		{
			$privilege_uris = json_decode($p->privilege_uri,TRUE);	
			$uri_strings = array_merge($uri_strings,$privilege_uris);
		}

		// check all uris parameters
		$uri_strings_with_params = array();
		foreach($uri_strings as $us) 
		{
			$_us_len = strpos($us,"/$");
			if ($_us_len) $uri_strings_with_params[] = substr($us,0,$_us_len);
		}

		// check if uri is in uri_strings
		$result = in_array($uri_string,$uri_strings);
		
		// check if uri is in uri_strings_with_params
		if (!$result)
		{			
			foreach($uri_strings_with_params as $uswp)
			{
				$_uri_string = substr($uri_string,0,strlen($uswp));
				
				if (strtolower($uswp)==strtolower($_uri_string)) 
				{
					$result = true;
					break;
				}
			}
		}

		return $result;
	}

	function is_user_allowed_by_privilege_code($user_id, $privilege_code)
	{
		$privileges = $this->get_user_privilege(array("user_id"=>$user_id, "privilege_code" => $privilege_code));
		return count($privileges) > 0;
	}
	
	function get_user_roles($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('user_roles', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_user_role($data) 
	{
		return $this->insert('user_roles', $data);
	}

	function update_user_roles($data, $where) 
	{
		return $this->update('user_roles', $data, $where);	
	}

	function delete_user_roles($where) 
	{
		return $this->delete('user_roles', $where);
	}

	function get_user_role_by_id($role_id) 
	{
		$result = $this->get_user_roles(array('role_id' => $role_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_user_role_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('user_roles', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_user_role_privilege($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('user_role_privilege_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_user_role_privilege($data) 
	{
		return $this->insert('user_role_privilege', $data);
	}

	function update_user_role_privilege($data, $where) 
	{
		return $this->update('user_role_privilege', $data, $where);
	}

	function delete_user_role_privilege($where) 
	{
		return $this->delete('user_role_privilege', $where);
	}

	function get_user_role_privilege_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('user_role_privilege', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// ==============================================================
	// sa_privilege
	function get_privilege($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('privilege', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_privilege($data) 
	{
		return $this->insert('privilege', $data);
	}

	function update_privilege($data, $where) 
	{
		return $this->update('privilege', $data, $where);
	}

	function delete_privilege($where) 
	{
		return $this->delete('privilege', $where);
	}

	function get_privilege_by_id($privilege_id) 
	{
		$result = $this->get_privilege(array('privilege_id' => $privilege_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_privilege_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('privilege', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
}