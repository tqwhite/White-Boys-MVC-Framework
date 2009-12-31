<?php
namespace library\services\qdb; //has to match namespace reference in client code
/**
 * MysqlAccess - simple access to a mysql server.
 *
 * @author     TQ White II
 * @license    Creative Commons Attribution-Share Alike 3.0 United States License
 * @link       http://tqwhite.org/openSource
 * @version    1.0
 * @download	http://github.com/tqwhite/White-Boys-MVC-Framework
 */
class MysqlAccess
{

private $mysqlHandle;
private $dbName;

public $lastQueryString;
public $lastQueryResult;
public $rowCount;

public function __construct($dbName=''){

	$server=\configs\MysqlConfig::getValue('server');
	$username=\configs\MysqlConfig::getValue('username');
	$password=\configs\MysqlConfig::getValue('password');
	$this->mysqlHandle=mysql_connect($server, $username, $password);
	
	
	if (!empty($dbName)){
		$result=$this->setDatabase($dbName);
	}
	
}

public function setDatabase($dbName){
	$this->dbName=$dbName;
	$result=mysql_select_db($this->dbName, $this->mysqlHandle);
	return $result;
	
}

private function passThrough($resultResource){
	return $resultResource;
}

private function recordListArray($resultResource){

	if (!empty($resultResource)){
		$record=mysql_fetch_array($resultResource, MYSQL_ASSOC);
		while (!empty($record)) {
			$outArray[]=$record; 
			$record=mysql_fetch_array($resultResource, MYSQL_ASSOC);
		}
	}
return $outArray;
}

private function recordArray($resultResource){

	$outArray=mysql_fetch_array($resultResource, MYSQL_ASSOC);
	if (mysql_num_rows($resultResource)>1){
		$outArray['additionalRecords']=array();
		$record=mysql_fetch_array($resultResource, MYSQL_ASSOC);
		while (!empty($record)) {
			$outArray['additionalRecords'][]=$record; 
			$record=mysql_fetch_array($resultResource, MYSQL_ASSOC);
		}
	}
return $outArray;

}

public function executeQueryString($queryString, $formattingRoutineName='recordListArray'){
	$this->lastQueryString=$queryString;
	$this->lastQueryResult=mysql_query($queryString, $this->mysqlHandle);
	$this->rowCount=mysql_num_rows($this->lastQueryResult);
	$output=$this->$formattingRoutineName($this->lastQueryResult);
	return ($output);
}

public function getValue($fieldName){
	return $this->$fieldName;
}

} //end of class