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
		
		$tmp=new \Entities\BookmarkList(); //not sure if this is giving the line below something to refer to or working around a bug
		$bookmarkList = $this->entityManagerInstance->getRepository('\Entities\BookmarkList')->findBy(array('code'=>'siteUtilities'));

		$urlList=$bookmarkList[0]->getBookmarks(); //this is an array of bookmark objects
		
		if (SYSTEM_TYPE=='development'){
			$bookmark = new \Entities\Bookmark;
			$item=array();
			$item['url']=$this->_getUrlPath('sendFilesToHost');
			$item['anchorText']='Send Files to Host';
			$bookmark->setFromArray($item);
			
			
			$urlList[]=$bookmark;
				
		}
			
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
		$transferDirectory->addExclusionString('_config');
		
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

/**
* Initialize database tables used by 
*
* @param none
* @return none
* @author TQ White II
*
*/
public function generateTables(){

	//drop table bookmarkLists_bookmarks, bookmarkLists, bookmarks;
	
	$em=$this->entityManagerInstance;
	
	$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
	
	$classesArray=array();
	
	$classesArray[]=$em->getClassMetadata('Entities\Bookmark');
	$classesArray[]=$em->getClassMetadata('Entities\BookmarkList');
	
	$tool->createSchema($classesArray);
	//$tool->updateSchema($classesArray); //this causes an error
	
	//==================================================================
	$urlList=array();
	$item=array();
	$domain=\configs\appConfig::getServerVar('HTTP_HOST');
	
	$item['code']='frontPage';
	$item['title']='Front Page List';
	
		$item2['url']='/OpenSource';
			$item2['text']='Open Source Page 1';
			$item['bookmarks'][]=$item2; $item2=array();
		$item2['url']='/OpenSource/2';
			$item2['text']='Open Source Page 2';
			$item['bookmarks'][]=$item2; $item2=array();
		$item2['url']='/siteUtilities';
			$item2['text']='Site Utilities';
			$item['bookmarks'][]=$item2; $item2=array();
		$item2['url']='/errata/scratchpad.php';
			$item2['text']='Scratchpad';
			$item['bookmarks'][]=$item2; $item2=array();

	$urlList[]=$item; $item=array();


	$item['code']='siteUtilities';
	$item['title']='Site Utilities List';
	
		$item2['url']='/start';
			$item2['text']='Front Page';
			$item['bookmarks'][]=$item2; $item2=array();
											
		$item2['url']=$this->_getUrlPath('generateTables');
			$item2['text']='Generate Tables';
			$item['bookmarks'][]=$item2; $item2=array();
					
		$item2['url']=$this->_getUrlPath('environmentInfo');
			$item2['text']='Environment info';
			$item['bookmarks'][]=$item2; $item2=array();
		
		$item2['url']="errata/phpdoc/HTMLframesConverter/index.html";
			$item2['text']='Docs (web)';
			$item['bookmarks'][]=$item2; $item2=array();
		
		$item2['url']="errata/phpdoc/PDFdefaultConverter/documentation.pdf";
			$item2['text']='Docs (pdf)';
			$item['bookmarks'][]=$item2; $item2=array();
					
		$item2['url']=$this->_getUrlPath('demo');
			$item2['text']='Renderer Demo';
			$item['bookmarks'][]=$item2; $item2=array();
		
	$urlList[]=$item; $item=array();
	
	foreach ($urlList as $data){

		$bookmarksArray=$data['bookmarks'];
		unset($data['bookmarks']);
		
		$bookmarkList = new \Entities\BookmarkList;
		$bookmarkList->setFromArray($data);
		
		$messageArray[]="<div style=color:green;margin-top:10px;font-weight:bold;>{$data['code']}={$data['title']}<div>";
		
		if (is_array($bookmarksArray)){
		foreach ($bookmarksArray as $data2){
			$bookmark = new \Entities\Bookmark;
			
			$messageArray[]="<div style=margin-left:10px;color:black;>{$data2['url']}={$data2['text']}</div>";
			
			$bookmark->setUrl($data2['url']);
			$bookmark->setAnchorText($data2['text']);
			
			$em->persist($bookmark);
			
		//	$bookmark->getBookmarkList()->add($bookmarkList);
			$bookmarkList->addBookmark($bookmark);

		}
		}
		else{
			$messageArray[]="<div style=margin-left:10px;color:black;>empty bookmark list</div>";
		}
		
		$em->persist($bookmarkList);
	}
	$em->flush();
	
	//======================================================================

	$messageArray[]="<span style=color:red;>Check DB to confirm tables <a href=http://tqwhite.org.local/errata/executeQuery.php target=_blank>(HERE)</a></span>";
	$page=new \mvc\views\BaseView($this->_getTemplatePath('displayMessage'));
	$page->SetViewScopedValue('messageArray', $messageArray);
	$page->SetViewScopedValue('pageTitle', 'Generate Tables');
	$page->render();
}

/**
* Demo is an example of how to work a view
*
* @param none
* @return none
* @author TQ White II
*
*/

public function demo(){
	
		$page=new \mvc\views\BaseView($this->_getTemplatePath('displayMessage'));
		$page->SetViewScopedValue('messageArray', $messageArray);
	
		$messageArray[]="Dynamic message from the controller<BR>";
		$page->SetViewScopedValue('messageArray', $messageArray);

		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "Demo Page Title");
		$page->SetViewScopedValue('messageArray', $messageArray);
		$page->render();
		
		return;
	}


} //end of class