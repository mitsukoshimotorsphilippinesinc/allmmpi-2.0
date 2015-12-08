<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shell_exec extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db = $this->load->database('default', TRUE);
	}

	/*// SSH Host 
    private $ssh_host = '125.100.100.25'; 
    // SSH Port 
    private $ssh_port = 22; 
    // SSH Server Fingerprint 
    private $ssh_server_fp = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; 
    // SSH Username 
    private $ssh_auth_user = 'webmaster'; 
    // SSH Public Key File 
    private $ssh_auth_pub = '/home/username/.ssh/id_rsa.pub'; 
    // SSH Private Key File 
    private $ssh_auth_priv = '/home/username/.ssh/id_rsa'; 
    // SSH Private Key Passphrase (null == no passphrase) 
    private $ssh_auth_pass = 'mmpi2015'; 
    // SSH Connection 
    private $connection; 	
	*/

	public function index()
	{
		
		/*$this->connect();

		$this->exec("ll");

		$this->disconnect();*/

		$this->template->view('shell_exec_view');
	}

    /*public function connect() { 
        if (!($this->connection = ssh2_connect($this->ssh_host, $this->ssh_port))) { 
            throw new Exception('Cannot connect to server'); 
        } 
        $fingerprint = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX); 
        if (strcmp($this->ssh_server_fp, $fingerprint) !== 0) { 
            throw new Exception('Unable to verify server identity!'); 
        } 
        if (!ssh2_auth_pubkey_file($this->connection, $this->ssh_auth_user, $this->ssh_auth_pub, $this->ssh_auth_priv, $this->ssh_auth_pass)) { 
            throw new Exception('Autentication rejected by server'); 
        } 
    } 
    public function exec($cmd) { 
        if (!($stream = ssh2_exec($this->connection, $cmd))) { 
            throw new Exception('SSH command failed'); 
        } 
        stream_set_blocking($stream, true); 
        $data = ""; 
        while ($buf = fread($stream, 4096)) { 
            $data .= $buf; 
        } 
        fclose($stream); 
        return $data; 
    } 
    public function disconnect() { 
        $this->exec('echo "EXITING" && exit;'); 
        $this->connection = null; 
    } 
    public function __destruct() { 
        $this->disconnect(); 
    } */

}