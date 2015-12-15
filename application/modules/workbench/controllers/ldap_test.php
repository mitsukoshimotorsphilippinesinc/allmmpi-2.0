<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ldap_test extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		//$this->db = $this->load->database('default', TRUE);
	}

	public function index()
	{
		
		$ldaprdn  = 'cn=Ryan Rosaldo,ou=h.o,dc=mitsukoshimotors,dc=com';     // ldap rdn or dn
		$ldappass = 'rootpass';  // associated password


		$ldapconn = ldap_connect("195.100.100.77")
    		or die("Could not connect to LDAP server.");

		if ($ldapconn) {

			    // binding to ldap server
			    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

			    // verify binding
			    if ($ldapbind) {
			        echo "LDAP bind successful...";
			    } else {
			        echo "LDAP bind failed...";
			    }

			}
	}

}

