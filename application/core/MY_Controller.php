<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

// hard loading the underscore php
include_once(APPPATH."libraries/underscore.php");

/*
 * Base Controller
 */

class Base_Controller extends MX_Controller {
	
	// the name of the module that this controller instance actually belong to.
	public $module;
	
	// the name of the controller calss for the curent class instance.
	public $controller_name;
	
	// the name of the method of the current request.
	public $method;
	
	// the session/logged-in user for admin
	public $user;
	
	// the session/logged-in user for members site
	public $member;
	
	// selected menu/navigation code
	public $selected_menu;
	
	public $setting;
	
	public $subdomain;
	
	// set if result should be cached
	public $cache_on = false;
	public $cache_timeout = 120; // 120 seconds

	public function __construct() 
	{
		parent::__construct();

		$this->load->library('MemcachedLib');
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: text/html; charset=utf-8'); // to ensure that we are using UT8

		// implement no page browser caching
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT');
		
		// load session, setting (to follow)
		$this->load->library(array('template', 'authenticate', 'setting'));
		
		// set the form validation delimiters
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		
		// assign the user to CI
		ci()->user = $this->user = new ArrayClass(array('user_id' => 0, 'first_name' => 'Guest', 'last_name' => ''));

		// assign the user to CI
		ci()->member = $this->member = new ArrayClass(array('member_id' => 0, 'first_name' => 'Guest', 'last_name' => ''));
		
		// Work out module, controller and method and make them accessable throught the CI instance
		ci()->module = $this->module = $this->router->fetch_module();
		ci()->controller_name = $this->controller_name = $this->router->fetch_class();
		ci()->controller = $this->controller = $this->router->fetch_class();
		ci()->method = $this->method = $this->router->fetch_method();
		
		ci()->is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
		
		// re-sssing selected menu to ci
		ci()->selected_menu = $this->selected_menu;
		
		if ($this->agent->is_mobile()){
			ci()->is_mobile = $this->is_mobile = true;
		} else {
			ci()->is_mobile = $this->is_mobile = false;
		}
		
		// Loaded after $this->current_user is set so that data can be used everywhere
		$this->load->model(array());
		
		// List available module permissions for this user
		//ci()->permissions = $this->permissions = $this->current_user ? $this->permission_m->get_group($this->current_user->group_id) : array();

		// register events
		Events::register('before_render', array($this, 'before'));
		Events::register('after_render', array($this, 'after'));
		
		$this->setting = new Setting();
				
		$this->template->initialize(array(
			'path' => '../../template',
			'theme' => 'site',
			'name' => 'main'
		));
		
		$this->template->title = $this->config->item('title');
		
		$this->authentication();
	}
	
	public function before()
	{
		
	}
	
	public function after()
	{
		
	}
	
	/*
	 * Set to use caching
	 * dafault to cache 2 minutes 
	 */
	public function cache($timeout = 120)
	{
		$uri =	$this->config->item('base_url').
				$this->config->item('index_page').
				$this->uri->uri_string.
				json_encode(array_merge($_POST,$_GET));
			
		$cache_key = md5($uri);
		$cache_data = $this->memcachedlib->get($cache_key, $timeout);
		if ($cache_data !== false)
		{
			// check if cached data is json
			$cache_obj = json_decode($cache_data);
			if ($cache_obj !== null) {
				$this->output->set_header('Content-Type: application/json; charset=utf-8');
			}
			$this->output->_display($cache_data);
			exit;
		}

		$this->cache_on = true;
		$this->cache_timeout = $timeout;
		$this->template->cache_on = true;
		$this->template->cache_key = $cache_key;
		$this->template->cache_timeout = $timeout;
	}
	
	/**
	 * Build the entire JSON output, setting the headers for response.
	 *
	 * @access	public
	 * @param	array	$data
	 * @return	void
	 */
	public function return_json($status = 'ok', $msg = '', $data = array())
	{
		$_data = array(
			'status' => $status,
			'msg' => $msg,
			'data' => $data
		);
		
		
		if ($this->cache_on) {
			$uri =	$this->config->item('base_url').
					$this->config->item('index_page').
					$this->uri->uri_string.
					json_encode(array_merge($_POST,$_GET));
						
			$cache_key = md5($uri);
			
			ci()->memcachedlib->set($cache_key, json_encode((object) $_data), $this->cache_timeout);
		}
		
		
		ci()->output->set_header('Content-Type: application/json; charset=utf-8');
		ci()->output->set_output(json_encode((object) $_data));
	}
	
	/**
	 * Set the selected menu
	 *
	 * @param string $menu_element_id
	 * @param string $sub_menu_element_id
	 */
    function set_navigation($navigation_code ) {
        ci()->selected_menu = $this->selected_menu = $navigation_code;
    }
	
	function authentication()
	{
		// configure authenticate
		$auth_config = array(
			'table' => 'sa_user',
			'field_id' => 'id_number',
			'hash_salt' => 'gbs@admin8896',
			'prefix' => 'sa_'
		);
		$this->authenticate->initialize($auth_config);
		
		if ($this->authenticate->is_logged_in()) {

			ci()->user = $this->user = $this->authenticate->get_user();

			// load members model
			$this->load->model('human_relations_model');

			//var_dump($this->user->id_number);
			$employee = $this->human_relations_model->get_employment_information_view_by_id($this->user->id_number);

			ci()->employee = $this->employee = $employee;
		}
	}
	
}


/**
 * Returns the CodeIgniter object.
 *
 * Example: ci()->db->get('table');
 *
 * @return \CI_Controller
 */
function ci()
{
	return get_instance();
}

// end of file MY_Controller.php