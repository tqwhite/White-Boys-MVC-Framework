<?php
namespace configs;

class MysqlConfig {

private static $server='localhost';
private static $username='root';
private static $password='dbPass';
private static $driver='pdo_mysql';

public function getValue($name){
	return self::$$name;
}

} //end of class