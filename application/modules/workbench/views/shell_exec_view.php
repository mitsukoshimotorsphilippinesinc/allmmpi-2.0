<?php

	$old_path = getcwd();
	chdir('/var/www/allmmpi');
	//$output = shell_exec('./script.sh var1 var2');
	$output = shell_exec('ls -l | grep license');
	chdir($old_path);
	echo "<pre>$output</pre>";

	
	$connection = ssh2_connect('195.100.100.25', 22);
	ssh2_auth_password($connection, 'webmaster', 'mmpi2015');

	//$stream = ssh2_exec($connection, '/usr/local/bin/php -i');
	//ssh2_exec($connection, 'cd /var/www/html/allmmpi');
	$stream = ssh2_exec($connection, 'ls --help');

	stream_set_blocking($stream, true); 
    
    $data = ""; 
    while ($buf = fread($stream, 4096)) { 
        $data .= $buf; 
    }    

	echo "<pre>$data</pre>";	
  	
?>