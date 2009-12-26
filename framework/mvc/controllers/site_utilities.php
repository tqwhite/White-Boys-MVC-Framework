<?php
namespace mvc\controllers;
/** 
* SiteUtilities contains actions for pages 
* accessed by a URL of the form http://domain.com/siteUtilities/action
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class SiteUtilities extends \mvc\controllers\BaseController{


public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
		
		$this->unknownActionHandlerName='dispatchSpecial';
	}

/**
* NOT FINISHED
* -a list of links
*
* @param none
* @return none
* @author TQ White II
* @example http://domain.com/controller/
*
*/	
public function dispatchSpecial(){
	//this is just a demo
	$args=func_get_args();
	$unknownMethod=array_shift($args); //first element is always the method
	switch ($unknownMethod){
		case '':
			$this->index(); //allows urls like http://domain.com/siteUtilities
			break;
		default:
			$this->index();
			break;
	}
}

/**
* Calculates default page info, renders appropriate template
* -a list of links
*
* @param none
* @return none
* @author TQ White II
*
*/
public function index(){
		$urlList=array();
		$item=array();
		$domain=\configs\appConfig::getServerVar('HTTP_HOST');
		
		if (SYSTEM_TYPE=='development'){
			$item['url']=$this->_getUrlPath('sendFilesToHost');
			$item['text']='Send Files to Host';
			$urlList[]=$item;
		}
			
					
		$item['url']=$this->_getUrlPath('environmentInfo');
		$item['text']='Environment info';
		$urlList[]=$item;
		$item['url']="errata/phpdoc/HTMLframesConverter/index.html";
		$item['text']='Docs (web)';
		$urlList[]=$item;
		
		$item['url']="errata/phpdoc/PDFdefaultConverter/documentation.pdf";
		$item['text']='Docs (pdf)';
		$urlList[]=$item;

		
		$item['url']='/start';
		$item['text']='Front Page';
		$urlList[]=$item;
		
		

		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: SiteUtilitiesController::index");
		$page->SetViewScopedValue('urlList', $urlList);
		$page->SetViewScopedValue('message', "And more words to live by from the correct source of content.");
		$page->render();
	}

/**
* Uploads changed development files to web host using FTP
* -based on configs/host_ftp_config.php
*
* @param none
* @return none
* @author TQ White II
*
*/	
public function sendFilesToHost(){

		if (SYSTEM_TYPE!='development'){
			die("Host FTP Sync can only be used from development systems. This is not one of those.");
			}

		/*
			make user interface
			figure out interface to initialize (esp baseUrl)
			cause web page to display progressively, show files as they are moved
			make list only version of executeFTP
			
			make command line compatible
			allow spec of http transfer file name so can redirect to page of interest after upload
			
			performance:
			make sendFile check if mkdir is needed
			make it so it only logs in once
		*/
	
		$transferDirectory=new \library\services\transferDirectory(dirname(ROOT));
		
		\configs\HostFtpConfig::copyIntoTransferDirectoryObject($transferDirectory); //from configs/host_ftp_config.php




						
		$transferDirectory->dryRunFlag=false; //false says we're in production, upload files
		$transferDirectory->initDatabase=false; //true says init db even though dryRunFlag==true, ignored if dryRunFlag==false




		$transferDirectory->initConnection();
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
		
		//execute the FTP
			$transferDirectory->analyzeFiles();
			$transferDirectory->executeFTP();
			$transferDirectory->saveThisObject();
			$transferDirectory->closeConnection();
		
			//dump($transferDirectory->deletedFilesArray);
		
				$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
				$page->SetViewScopedValue('transferDirectory', $transferDirectory);
				$page->render();

}

/**
* Displays a variety of system info for developers
* -phpInfo(), $_SERVER
*
* @param none
* @return none
* @author TQ White II
*
*/
public function environmentInfo(){
		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->render();
}

} //end of class