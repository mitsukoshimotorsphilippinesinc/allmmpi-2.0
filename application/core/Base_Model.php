<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base_model
 *
 * Sets up basic model functions. All user created model classes should
 * extend this to gain access to its basic database model functions.
 *
 */
class Base_Model extends CI_Model {

	/**
	 * Constructor
	 */
	function __construct() {
		// Inherit from parent class
		parent::__construct();

		// Create empty function array
		$this->_TABLES = array();

	}
	
	/**
	 * Fetch
	 *
	 * Fetch table rows from table related to $name. Check no custom
	 * fetch method exists before hand.
	 *
	 * @access public
	 * @param string $name Table Name
	 * @param mixed $fields Fields to return from table
	 * @param array $limit Rows to limit search to
	 * @param mixed $where Return rows that match this
	 * @return Query Object
	 */
	function fetch($name, $fields=null, $where=null, $orderby=null, $limit=null)
	{
		$func = '_fetch_'.$name;

		if(method_exists($this,$func))
		{
			// There is an overide function
			return call_user_func_array(array($this,$func), array($fields,$limit,$where));
		}
		else
		{
			// clear previous get request
			$this->db->flush_cache();
			
			// No override function, procede with fetch
			($fields!=null) ? $this->db->select($fields) : '';
			($where!=null) ? $this->db->where($where) : '';
			($limit!=null) ? $this->db->limit($limit['rows'],$limit['offset']) : '';
			($orderby!=null) ? $this->db->order_by($orderby) : '';
			
			// set table to use
			$this->db->from($this->_TABLES[$name]);
			$result = $this->db->get();
			return $result;
		}
	}

	/**
	 * Insert
	 *
	 * Insert new table data into table related to by $name
	 * Check no custom insert method exists before hand.
	 *
	 * @access public
	 * @param string $name Table Name
	 * @param array $data Data to insert
	 * @return Query Object
	 */
	function insert($name, $data)
	{

		$func = '_insert_' . $name;
		if(method_exists($this,$func))
		{
			// There is an overide function
			return call_user_func_array(array($this,$func), array($data));
		}
		else
		{
			// No override function, procede with insert
			return $this->db->insert($this->_TABLES[$name],$data);
		}
	}

	/**
	 * 
	 * @return int
	 */
	function insert_id() {
		return $this->db->insert_id(); // always fetch from read server
	}

	/**
	 * Update
	 *
	 * Update data in table related to by $name
	 * Check no custom update method exists before hand.
	 *
	 * @access public
	 * @param string $name Table Name
	 * @param array $values Data to change
	 * @param mixed $where Rows to update
	 * @return Query Object
	 */
	function update($name, $values, $where)
	{

		$func = '_update_' . $name;
		if(method_exists($this,$func))
		{
			// There is an overide function
			return call_user_func_array(array($this,$func), array($values,$where));
		}
		else
		{
			// No overside function, procede with general insert
			$this->db->where($where);
			return $this->db->update($this->_TABLES[$name],$values);
		}
	}

	/**
	 * Delete
	 *
	 * Delete rows from table related to by $name
	 * Check no custom delete method exists before hand.
	 *
	 * @access public
	 * @param string $name Table Name
	 * @param mixed $where Rows to delete
	 * @return Query Object
	 */
	function delete($name, $where)
	{

		$func = '_delete_' . $name;
		if(method_exists($this, $func))
		{
			// There is an overide function
			return call_user_func_array(array($this,$func), array($where));
		}
		else
		{
			// No overside function, procede with general insert
			$this->db->where($where);
			return $this->db->delete($this->_TABLES[$name]);
		}
	}

	function escape($value) {
		return $this->db->escape($value);
	}

}

/* End of file PHP script */
