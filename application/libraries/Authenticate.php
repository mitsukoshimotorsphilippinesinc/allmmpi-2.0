<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Authenticate {
	
	protected $user = null;
	protected $_hash_salt = 'gbs@auth123';
	protected $_table = 'users';
	protected $_field_id = 'user_id';
	protected $_prefix = '';


	function __construct($config = array()) {
		$this->_ci =& get_instance();
		$this->initialize($config);
	}
	
	public function initialize($config = array()) 
	{
		foreach ($config as $key => $val)
		{
			$this->{'_'.$key} = $val;
		}
	}

	function login($username, $password, $remember = FALSE) 
	{

		$username_or_email = (string) $username;
		$password = (string) $password;
		
		$username_or_email = strtoupper($username_or_email);
		$username_or_email = mysql_real_escape_string($username_or_email);
		
		$where = "(password = '".md5(strtoupper($password))."' OR password = '".md5(strtolower(strtoupper($password)))."') ";
		$where .= "AND UPPER(username) = UPPER('".$username_or_email."')";
		
		//get the member
		$this->user = $this->_ci->db->select('*')
							->from($this->_table)
							->where($where)
							->get()
							->first_row();
	
				
		if (empty($this->user))
		{
			return false;
		}

		$login_hash = sha1($this->_hash_salt.$this->user->{$this->_field_id}.time());
		
		$data = array($this->_prefix."user_id" => $this->user->{$this->_field_id}, $this->_prefix.'login_hash' => $login_hash);
		$this->_ci->session->set_userdata($data);

		$data = array('login_hash' => $login_hash);
		$this->_ci->db->flush_cache();
		$this->_ci->db->where($this->_field_id, $this->user->{$this->_field_id})
					->update($this->_table, array('login_hash' => $login_hash));

		if ($remember) 
		{
			$cookie_name 		= $this->_ci->config->item('cookie_prefix') . $this->_ci->config->item('sess_cookie_name');
			$cookie_values 		= get_cookie($cookie_name);
			delete_cookie($cookie_name);
			$cookie['name'] 	= $this->_ci->config->item('sess_cookie_name');
			$cookie['value']	= $cookie_values;
			$cookie['expire']	= '31968000';
			$cookie['domain']	= $this->_ci->config->item('cookie_domain');
			$cookie['path']		= $this->_ci->config->item('cookie_path');
			$cookie['prefix']	= $this->_ci->config->item('cookie_prefix');
            set_cookie($cookie);
        }

		return true;
	}

	function e_login($username, $password, $remember = FALSE) 
	{

		$username_or_email = (string) $username;
		$password = (string) $password;
		
		$username_or_email = strtoupper($username_or_email);
		$username_or_email = mysql_real_escape_string($username_or_email);
		
		$where = "(password = '".md5(strtoupper($password))."' OR password = '".md5(strtolower(strtoupper($password)))."') ";
		$where .= "AND UPPER(username) = UPPER('".$username_or_email."')";
		
		//get the member
		$this->user = $this->_ci->db->select('*')
							->from($this->_table)
							->where($where)
							->get()
							->first_row();
	
				
		if (empty($this->user))
		{
			return false;
		}

		$login_hash = sha1($this->_hash_salt.$this->user->{$this->_field_id}.time());
		
		$data = array($this->_prefix."user_id" => $this->user->{$this->_field_id}, $this->_prefix.'e_login_hash' => $login_hash);
		$this->_ci->session->set_userdata($data);

		$data = array('e_login_hash' => $login_hash);
		$this->_ci->db->flush_cache();
		$this->_ci->db->where($this->_field_id, $this->user->{$this->_field_id})
					->update($this->_table, array('e_login_hash' => $login_hash));

		if ($remember) 
		{
			$cookie_name 		= $this->_ci->config->item('cookie_prefix') . $this->_ci->config->item('sess_cookie_name');
			$cookie_values 		= get_cookie($cookie_name);
			delete_cookie($cookie_name);
			$cookie['name'] 	= $this->_ci->config->item('sess_cookie_name');
			$cookie['value']	= $cookie_values;
			$cookie['expire']	= '31968000';
			$cookie['domain']	= $this->_ci->config->item('cookie_domain');
			$cookie['path']		= $this->_ci->config->item('cookie_path');
			$cookie['prefix']	= $this->_ci->config->item('cookie_prefix');
            set_cookie($cookie);
        }

		return true;
	}
	
	public function get_user()
	{
		return $this->user;
	}
	
	public function get_user_id()
	{
		return $this->user->{$this->_field_id};
	}

	function logout() 
	{
		$this->_ci->session->unset_userdata($this->_prefix."user_id");
		$this->_ci->session->unset_userdata("selected_account");
		
	} 

	function is_logged_in() 
	{
		$user_id = $this->_ci->session->userdata($this->_prefix."user_id");
		$login_hash = $this->_ci->session->userdata($this->_prefix.'login_hash');

		if (empty($user_id)) {
			return false;
		}

		$this->user = $this->_ci->db->select('*')
							->from($this->_table)
							->where($this->_field_id, $user_id)
							->where('login_hash', $login_hash)
							->get()
							->first_row();

		return !empty($this->user);
    } 

    function e_is_logged_in() 
	{
		$user_id = $this->_ci->session->userdata($this->_prefix."user_id");
		$login_hash = $this->_ci->session->userdata($this->_prefix.'e_login_hash');

		if (empty($user_id)) {
			return false;
		}

		$this->user = $this->_ci->db->select('*')
							->from($this->_table)
							->where($this->_field_id, $user_id)
							->where('e_login_hash', $login_hash)
							->get()
							->first_row();

		return !empty($this->user);
    } 

}