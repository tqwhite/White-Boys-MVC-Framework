<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(__FILE__))));

define('CLASSROOT', ROOT.'/framework/classes/');
define('CONFIGROOT', ROOT.'/framework/configs/');


/** Configuration Variables **/

//initialize system stuff

if (!isset($_SERVER['SYSTEM_TYPE'])){
	define('SYSTEM_TYPE', 'production');
}
else{
	define('SYSTEM_TYPE', $_SERVER['SYSTEM_TYPE']); //usually this is 'development' from httpd.conf
}


define('DB_NAME', 'yourdatabasename');
define('DB_USER', 'yourusername');
define('DB_PASSWORD', 'yourpassword');
define('DB_HOST', 'localhost');

