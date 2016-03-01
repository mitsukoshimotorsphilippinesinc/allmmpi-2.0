<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ldap_test extends Base_Controller {

	private $_validation_rule = array(
		array(
			'field' => 'server_ip',
			'label' => 'Server IP / Domaim',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'common_name',
			'label' => 'CN',
			'rules' => 'trim|required'
		),
		//array(
		//	'field' => 'organizational_unit',
		//	'label' => 'OU',
		//	'rules' => 'trim|required'
		//),
		array(
			'field' => 'domain_component',
			'label' => 'DC',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'trim|required'
		)

	);

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		//$this->template->view("ldap_test/dashboard");
		$this->process();
	}
	

	public function process() {
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{

				//$ldaprdn  = 'cn='. set_value('common_name') .',ou='. set_value('organizational_unit') .',dc=' . set_value('domain_component') . ',dc=com';     // ldap rdn or dn
				
				$ldaprdn  = 'cn='. set_value('common_name') .',dc=' . set_value('domain_component') . ',dc=com';     // ldap rdn or dn

				$ldappass = set_value('password');
									
				$ldapconn = ldap_connect(set_value('server_ip'), "390")
    				or die("Could not connect to LDAP server.");

	    		echo "Connected to server via " . set_value('server_ip')  . " port: 390 " . $ldapconn . "<br/>";	

	    		if ($ldapconn) {

					ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
					ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

					echo $ldaprdn . "<br/>password: " . $ldappass;

				    // binding to ldap server				  
				    //$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
				    //$ldapbind = ldap_bind($ldapconn, "cn=zentyal,dc=mmpimotors,dc=com", "c67CF@tT6MmH29kv/XCU");
				    //$ldapbind = ldap_bind($ldapconn, "cn=zentyal,dc=mmpimotors,dc=com", "c67CF@tT6MmH29kv/XCU");
				    $ldapbind = ldap_bind($ldapconn, "cn=zentyal,dc=mmpimotors,dc=com", "FDeL1YAN1UHzvMDEMZ8m");
					//$ldapbind = ldap_bind($ldapconn);

				    // verify binding
				    if ($ldapbind) {
				        echo "LDAP bind successful...";


				    //    $sr=ldap_search($ldapbind, "dn=mitsukoshimotors,dn=com", "admin*");

				      //  $sr=ldap_search($ldapbind, "dn=mitsukoshimotors,dn=com", "admin*");
				         //$sr=ldap_search($ldapconn, "DC=mmpimotors,DC=com","(&(objectCategory=person)(samaccountname=*))");
				        
				        //$filter="CN=mikko.concepcion";

				        if (!($search = ldap_search($ldapconn, "dc=mmpimotors,dc=com", "uid=matt.concepcion"))) {
						     die("Unable to search ldap server");
						} else {

							$entries = ldap_get_entries($ldapconn, $search);
							var_dump($entries);

					        // Display key data for each entry.
					        for ($i=0; $i<$entries["count"]; $i++) {
					            echo "<p>DN: " . $entries[$i]["dn"] . "<br />";
					            //echo "Uid: " . $entries[$i]["uid"][0] . "<br />";
					            //echo "Email: " . $entries[$i]["mail"][0] . "</p>";
					        }

					        ldap_close($ldapconn);


						}

				        //$read = ldap_search($ldapconn, $ldaprdn, "ou*")
						//     or exit(">>Unable to search ldap server<<");
						
						//$info = ldap_get_entries($connect, $read);

				    } else {
				        echo "LDAP bind failed...";
				    }

				}


				return;
			}
		}
				
		$this->template->view("ldap_test/dashboard");
	}



	public function proceed() 
	{
		
		$ldaprdn  = 'cn=ryan.rosaldo,ou=h.o,dc=mitsukoshimotors,dc=com';     // ldap rdn or dn
		$ldappass = 'rootpass';  // associated password

		//$ldaprdn  = 'cn=admin,ou=groups,dc=mitsukoshimotors,dc=com';     // ldap rdn or dn
		//$ldappass = 'rootpass';  // associated password


		//$ldaprdn  = 'cn=mikko,ou=Groups,dc=mitsukoshimotors,dc=com';     // ldap rdn or dn
		//$ldappass = 'rootpass';  // associated password

		$ldapconn = ldap_connect("195.100.100.77", "390")
		//$ldapconn = ldap_connect("195.100.100.52")
    		or die("Could not connect to LDAP server.");

    	echo $ldapconn . "<br/>";	

		if ($ldapconn) {

			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

				echo $ldaprdn . "<br/>" . $ldappass;

			    // binding to ldap server
			    //$ldapbind = ldap_bind($ldapconn);
			    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

			    // verify binding
			    if ($ldapbind) {
			        echo "LDAP bind successful...";


			      //  $sr=ldap_search($ldapbind, "dn=mitsukoshimotors,dn=com", "admin*");

			        //$read = ldap_search($ldapconn, $ldaprdn, "ou*")
					//     or exit(">>Unable to search ldap server<<");
					
					//$info = ldap_get_entries($connect, $read);

			    } else {
			        echo "LDAP bind failed...";
			    }

			}
	}

/*
	$ldap_columns = NULL;
	$ldap_connection = NULL;
	$ldap_password = 'rootpass';
	$ldap_username = 'cn=ryan.rosaldo,ou=h.o,dc=mitsukoshimotors,dc=com';

	//------------------------------------------------------------------------------
	// Connect to the LDAP server.
	//------------------------------------------------------------------------------
	$ldap_connection = ldap_connect("195.100.100.77");
	if (FALSE === $ldap_connection){
	    die("<p>Failed to connect to the LDAP server: 195.100.100.77</p>");
	}

	ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
	ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

	if (TRUE !== ldap_bind($ldap_connection, $ldap_username, $ldap_password)){
	    die('<p>Failed to bind to LDAP server.</p>');
	}

	//------------------------------------------------------------------------------
	// Get a list of all Active Directory users.
	//------------------------------------------------------------------------------
	//$ldap_base_dn = 'DC=xyz,DC=local';
	$ldap_base_dn = 'DC=com';
	//$search_filter = "(&(objectCategory=person))";
	$search_filter = "";
	$result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter);
	if (FALSE !== $result){
	    $entries = ldap_get_entries($ldap_connection, $result);
	    if ($entries['count'] > 0){
	        $odd = 0;
	        foreach ($entries[0] AS $key => $value){
	            if (0 === $odd%2){
	                $ldap_columns[] = $key;
	            }
	            $odd++;
	        }
	        echo '<table class="data">';
	        echo '<tr>';
	        $header_count = 0;
	        foreach ($ldap_columns AS $col_name){
	            if (0 === $header_count++){
	                echo '<th class="ul">';
	            }else if (count($ldap_columns) === $header_count){
	                echo '<th class="ur">';
	            }else{
	                echo '<th class="u">';
	            }
	            echo $col_name .'</th>';
	        }
	        echo '</tr>';
	        for ($i = 0; $i < $entries['count']; $i++){
	            echo '<tr>';
	            $td_count = 0;
	            foreach ($ldap_columns AS $col_name){
	                if (0 === $td_count++){
	                    echo '<td class="l">';
	                }else{
	                    echo '<td>';
	                }
	                if (isset($entries[$i][$col_name])){
	                    $output = NULL;
	                    if ('lastlogon' === $col_name || 'lastlogontimestamp' === $col_name){
	                        $output = date('D M d, Y @ H:i:s', ($entries[$i][$col_name][0] / 10000000) - 11676009600);
	                    }else{
	                        $output = $entries[$i][$col_name][0];
	                    }
	                    echo $output .'</td>';
	                }
	            }
	            echo '</tr>';
	        }
	        echo '</table>';
	    }
	}
	ldap_unbind($ldap_connection); // Clean up after ourselves.
	

	}
*/
}

