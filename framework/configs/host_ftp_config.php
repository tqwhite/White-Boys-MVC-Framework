<?php

class HostFtpConfig {

public static $local=ROOT;
public static $remote='/';

public static $baseUrl='http://tqwhite.org/';
public static $docRootDir='/public_html/';

public static $ftpUser='tq.org';
public static $ftpPass='tq3141';
public static $ftpHost='tqwhite.org';	

public function copyIntoTransferDirectoryObject($transferDirectory){

	$itemArray=array();
	
		$itemArray['local']=self::$local;
		$itemArray['remote']=self::$remote;
		$transferDirectory->pathTranslationArray[]=$itemArray; 
	
	$itemArray=array();
	
		$itemArray['baseUrl']=self::$baseUrl;
		$itemArray['docRootDir']=self::$docRootDir;
		$transferDirectory->urlTranslationArray=$itemArray;
	
	$transferDirectory->ftpUser=self::$ftpUser;
	$transferDirectory->ftpPass=self::$ftpPass;
	$transferDirectory->ftpHost=self::$ftpHost;

}

public function test($transferDirectory){
$transferDirectory->testVar='goodbye';
}

} //end of class