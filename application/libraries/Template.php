<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template {
	
	private $_module = '';
	private $_controller = '';
	private $_method = '';
	
	private $_path = '';
	private $_theme = '';
	private $_name = '';
	private $_is_mobile = false;
	
	private $_title = '';
	
	private $_ci;
	
	private $_data = array();
	private $_content = '';
	
	/**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($config = array()) 
	{
		$this->_ci =& get_instance();
		
		if (!empty($config))
		{
			$this->initialize($config);
		}
		
		// No locations set in config?
		if ($this->_path === '')
		{
			// Let's use this obvious default
			$this->_path = APPPATH . 'template/';
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array	$config
	 * @return	void
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			$this->{'_'.$key} = $val;
		}
		
		// Modular Separation / Modular Extensions has been detected
		if (method_exists( $this->_ci->router, 'fetch_module' ))
		{
			$this->_module = $this->_ci->router->fetch_module();
		}

		// What controllers or methods are in use
		$this->_controller	= $this->_ci->router->fetch_class();
		$this->_method 		= $this->_ci->router->fetch_method();
		
		// Load user agent library if not loaded
		$this->_ci->load->library('user_agent');

		// We'll want to know this later
		$this->_is_mobile = $this->_ci->agent->is_mobile();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic Get function to get data
	 *
	 * @access	public
	 * @param	string	$name
	 * @return	mixed
	 */
	public function __get($name)
	{
		if ($name == 'title') return $this->_title;
		if ($name == 'theme') return $this->_theme;
		if ($name == 'name') return $this->_name;
		if ($name == 'path') return $this->_path;
		
		return isset($this->_data[$name]) ? $this->_data[$name] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Magic Set function to set data
	 *
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	mixed
	 */
	public function __set($name, $value)
	{
		if ($name == 'title') 
		{
			$this->_title = $value;
			return;
		} else if ($name == 'theme') {
			$this->_theme = $value;
			return;
		} else if ($name == 'name') {
			$this->_name = $value;
			return;
		} else if ($name == 'path') {
			$this->_path = $value;
			return;
		}
		$this->_data[$name] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Set data using a chain-able method. Provide two strings or an array of data.
	 *
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	object	$this
	 */
	public function set($name, $value = NULL)
	{
		// Lots of things! Set them all
		if (is_array($name) OR is_object($name))
		{
			foreach ($name as $item => $value)
			{
				$this->_data[$item] = $value;
			}
		}

		// Just one thing, set that
		else
		{
			$this->_data[$name] = $value;
		}

		return $this;
	}
	
	// --------------------------------------------------------------------
	
	
	public function view($view, $module_name = null, $return = false, $content_only = false)
	{	
		
		if (empty($module_name)) $module_name = $this->_module;
		
		Events::trigger('before_render');
		
		// assigne the title
		$this->_data['title'] = $this->_title;
		// get the view file content
		$this->_data['content'] = '';
		if (!empty($view))
		{
			$this->_data['content'] = $this->_ci->load->view($view, $this->_data, TRUE, $module_name);
		}
		
		// fullpath to template
		$path = $this->_path.'/'.$this->_theme.'/'.$this->_name;
		$this->_content =  $this->_ci->load->view($path, $this->_data, TRUE);
		
		Events::trigger('after_render');
		
		if ($this->_cache_on)
		{
			if (!empty($this->_cache_key))
			{
				$this->_ci->memcachedlib->set($this->_cache_key, $this->_content, $this->_cache_timeout);
			}
		}
		
		if (!$return)
			$this->_ci->output->set_output($this->_content);
		else
		{
			if ($content_only)
				return $this->_sanitize_html($this->_data['content']);
			else
				return $this->_sanitize_html($this->_content);
		}
			
			
	}
	
	private function _sanitize_html($buffer)
	{
	    $search = array(
	        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
	        '/[^\S ]+\</s', //strip whitespaces before tags, except space
	        '/(\s)+/s'  // shorten multiple whitespace sequences
	        );
	    $replace = array(
	        '>',
	        '<',
	        '\\1'
	        );
	    $buffer = preg_replace($search, $replace, $buffer);

	    return $buffer;
	}
	
}