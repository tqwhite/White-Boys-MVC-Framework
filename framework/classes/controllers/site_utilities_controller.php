<?php
class SiteUtilitiesController extends BaseController{


public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
	}

public function index(){$urlList=array();
		$item=array();

		if (SYSTEM_TYPE=='development'){
			$item['url']=$this->_getUrlPath('sendFilesToHost');
			$item['text']='Send Files to Host';
			$urlList[]=$item;
		}
		
		$item['url']=$this->_getUrlPath('environmentInfo');
		$item['text']='Environment info';
		$urlList[]=$item;
		
		

		$page=new BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: SiteUtilitiesController::index");
		$page->SetViewScopedValue('urlList', $urlList);
		$page->SetViewScopedValue('message', "And more words to live by from the correct source of content.");
		$page->render();
	}
	
public function sendFilesToHost(){

		if (SYSTEM_TYPE!='development'){
			die("Host FTP Sync can only be used from development systems. This is not one of those.");
			}

/*
* 

DONE delete still needs to implemented
implement ignore file list

make user interface
figure out interface to initialize (esp baseUrl)
cause web page to display progressively, show files as they are moved
make list only version of executeFTP

make command line compatible
allow spec of http transfer file name so can redirect to page of interest after upload

performance:
make sendFile check if mkdir is needed
make it so it only logs in once

make it display through smarty in a web usage

*/

$transferDirectory=new transferDirectory(dirname(ROOT));

HostFtpConfig::copyIntoTransferDirectoryObject(&$transferDirectory); //from configs/host_ftp_config.php

$transferDirectory->clearExclusionString();
$transferDirectory->addExclusionString('configs');

$transferDirectory->uploadFileTemplate='
	<div style=margin-bottom:10pt;font-family:sans-serif;font-size:8pt;>
		<div style=color:green;>
			UPLOAD status=<!result!> <!link!>
			</div>
		<div style=color:gray;>
			source: <!localPath!><br>dest: <!destPath!>
			</div>
		</div>';

$transferDirectory->deleteFileTemplate='
	<div style=margin-bottom:10pt;font-family:sans-serif;font-size:8pt;>
		<div style=color:green;>
			DELETE status=<!result!>
			</div>
		<div style=color:gray;>
			source: <!localPath!><br>dest: <!destPath!>
			</div>
		</div>';
		
$transferDirectory->dryRunFlag=false; //false says we're in production, upload files
$transferDirectory->initDatabase=false; //true says init db even though dryRunFlag==true, ignored if dryRunFlag==false






//execute the FTP
	$transferDirectory->analyzeFiles();
	$transferDirectory->executeFTP();
	$transferDirectory->saveThisObject();
	

	//dump($transferDirectory->deletedFilesArray);

		$page=new BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('transferDirectory', $transferDirectory);
		$page->render();

}

public function environmentInfo(){
		$page=new BaseView($this->_getTemplatePath(__METHOD__));
		$page->render();
}

} //end of class