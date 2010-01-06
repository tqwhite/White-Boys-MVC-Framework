<?php
namespace configs;
/**
 * AppConfig is a static class that managees configuration variables
 *
 * @package default
 * @author TQ White II
 **/
class AppConfig {

	public static function initialize(){
		define('DS', DIRECTORY_SEPARATOR);
		define('ROOT', dirname(dirname(dirname(__FILE__))).'/');
		
		define('FRAMEWORKROOT', ROOT.'framework/');
		define('MVCROOT', ROOT.'framework/mvc/');
		define('CONFIGROOT', ROOT.'framework/configs/');
		define('LIBROOT', ROOT.'framework/library/');
		define('HTMLROOT', ROOT.'public_html/');
		
		define('USE_DOCTRINE', true);
		define('DOCTRINEROOT', FRAMEWORKROOT.'library/services/doctrine/');
		
		
		//initialize system stuff
		
		if (!isset($_SERVER['SYSTEM_TYPE'])){
			define('SYSTEM_TYPE', 'production');
		}
		else{
			define('SYSTEM_TYPE', $_SERVER['SYSTEM_TYPE']); //usually this is 'development' from httpd.conf
		}
	}
	
	public static function getServerVar($name){
	
		if (isset($_SERVER[$name])){
			return $_SERVER[$name];
		}
		else{
			return '';
		}
	}

} //end of class