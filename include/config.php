<?php

/**	Requiring Database Constants set in seperate file 
*	outside of www folder
*/
	require 'session.php';
	
	$config = require_once('db.php');

	/**	Creating PDO parameters
	*/
	
	$charset = "utf8";
	$dsn = "mysql:host=".$config['DB_SERVER'].";dbname=".$config['DB_NAME'].";charset=$charset";

	$opt = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_PERSISTENT=>true, PDO::MYSQL_ATTR_FOUND_ROWS => true,];

	/**	Instantiate New PDO Connection Instance 
	*/
	$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], $opt);