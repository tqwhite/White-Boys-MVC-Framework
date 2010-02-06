<?php
namespace configs;
class HostFtpConfig {

private static $prod_local=ROOT;
private static $prod_remote='/';

private static $prod_baseUrl='http://tqwhite.org/';
private static $prod_docRootDir='/public_html/';

private static $prod_ftpUser='org.tqwhite';
private static $prod_ftpPass='tq3141';
private static $prod_ftpHost='38.108.46.145';	
private static $prod_ftpPort='22123';
public static $prod_connectionType='sftp';		

public function copyIntoTransferDirectoryObject(&$transferDirectory, $destServerType='prod'){

	//$pathTranslationArray
	$localName=$destServerType.'_'.'local';
	$remoteName=$destServerType.'_'.'remote';
	
	//$urlTranslationArray
	$baseUrlName=$destServerType.'_'.'baseUrl'; //this is what it pastes onto the front
	$docRootDirName=$destServerType.'_'.'docRootDir'; //when this string is found in the filepath
	
	$ftpUserName=$destServerType.'_'.'ftpUser';
	$ftpPassName=$destServerType.'_'.'ftpPass';
	$ftpHostName=$destServerType.'_'.'ftpHost';
	$ftpPortName=$destServerType.'_'.'ftpPort';
	$ftpConnectionType=$destServerType.'_'.'connectionType'; //sftp or ftp
	
	if (empty(self::$$ftpHostName)){
		exit("<div style=color:red;font-size:18pt;>There is no destServerType=<span style=color:black;>$destServerType</span> defined in host_ftp_config</div>");
	}

	$itemArray=array();
	
		$itemArray['local']=self::$$localName;
		$itemArray['remote']=self::$$remoteName;
		$transferDirectory->pathTranslationArray[]=$itemArray; 
	
	$itemArray=array();
	
		$itemArray['baseUrl']=self::$$baseUrlName;
		$itemArray['docRootDir']=self::$$docRootDirName;
		$transferDirectory->urlTranslationArray=$itemArray;
	
	$transferDirectory->ftpUser=self::$$ftpUserName;
	$transferDirectory->ftpPass=self::$$ftpPassName;
	$transferDirectory->ftpHost=self::$$ftpHostName;
	$transferDirectory->ftpPort=self::$$ftpPortName;
	$transferDirectory->connectionType=self::$$ftpConnectionType;

}

public function test($transferDirectory){
$transferDirectory->testVar='goodbye';
}

} //end of class