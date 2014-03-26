<?php
/**
 * MySQL server connection information
 * 
 * This file has configuration information to establish connection to the MySQL server
 *	- hostName = mysql server to connect
 *  - userName = database username to login
 *  - passWord = database password to login
 *  - dataBase = database name
 */
if ($_SERVER['HTTP_HOST'] == '172.21.4.104') { // Local
	$dbConfig['hostName'] = 'localhost';
	$dbConfig['userName'] = 'root';
	$dbConfig['passWord'] = '';
	$dbConfig['dataBase'] = 'intermingl';
}
else {  // Main 
	$dbConfig['hostName'] = 'aa1lp0mi27p9f2t.c5wtujrxixvn.us-west-2.rds.amazonaws.com';
	$dbConfig['userName'] = 'mingldbuser';
	$dbConfig['passWord'] = 'mi2ng0ld1bu4ser';
	$dbConfig['dataBase'] = 'ebdb';
}
?>