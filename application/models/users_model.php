<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'users' => 'ad_users',
			'user_privileges'=>'ad_user_privileges',
			'user_privileges_view'=>'ad_user_privileges_view',
			'user_facilities' => 'ad_user_facilities',
			'user_facilities_view' => 'ad_user_facilities_view',
			'user_roles' => 'ad_user_roles',
			'user_role_privileges'=>'ad_user_role_privileges',
			'user_role_privileges_view'=>'ad_user_role_privileges_view'
		);

	}

	function get_users($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('users', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_user($data) 
	{
		return $this->insert('users', $data);
	}

	function update_users($data, $where) 
	{
		return $this->update('users', $data, $where);
	}

	function delete_users($where) 
	{
		return $this->delete('users', $where);
	}

	function get_user_by_id($user_id) 
	{
		$result = $this->get_users(array('user_id' => $user_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_user_by_username($username) 
	{
		$result = $this->get_users(array('username' => $username));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_user_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('users', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	
	function get_user_facilities($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('user_facilities_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_user_facility_by_user_id($user_id)
	{
		$result = $this->get_user_facilities(array('user_id' => $user_id));
		return $result;
	}
	
	function get_default_user_facility($user_id)
	{
		$result = $this->get_user_facilities(array('user_id' => $user_id,'is_default' => 1));
		return $result[0];
	}
	
	function insert_user_facilities($data) 
	{
		return $this->insert('user_facilities', $data);
	}

	function update_user_facilities($data, $where) 
	{
		return $this->update('user_facilities', $data, $where);
	}

	function delete_user_facilities($where) 
	{
		return $this->delete('user_facilities', $where);
	}

	
	function search_users($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['users']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	
	function user_search_count($search,$query)
	{
		// do a sql count instead of row count
		$query = $this->search_users($search,$query,NULL,NULL,'count(1) as cnt');
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_user_privileges($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('user_privileges_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_user_privilege($data) 
	{
		return $this->insert('user_privileges', $data);
	}

	function update_user_privileges($data, $where) 
	{
		return $this->update('user_privileges', $data, $where);
	}

	function delete_user_privileges($where) 
	{
		return $this->delete('user_privileges', $where);
	}

	function get_user_privilege_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('user_privileges', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function is_user_allowed($uri_string)
	{
		
		$privileges = $this->get_user_privileges("user_id  = {$this->user->user_id}");
		
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
		$privileges = $this->get_user_privileges(array("user_id"=>$user_id, "privilege_code" => $privilege_code));
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
	
	function get_user_role_privileges($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('user_role_privileges_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_user_role_privilege($data) 
	{
		return $this->insert('user_role_privileges', $data);
	}

	function update_user_role_privileges($data, $where) 
	{
		return $this->update('user_role_privileges', $data, $where);
	}

	function delete_user_role_privileges($where) 
	{
		return $this->delete('user_role_privileges', $where);
	}

	function get_user_role_privilege_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('user_role_privileges', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
}