<?php
namespace mvc\controllers;
/** 
 * SiteUtilities contains actions for pages 
 * accessed by a URL of the form http://domain.com/start/action
 * or http://domain.com
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class Bookmarks extends \mvc\controllers\BaseController{

/**
* Initialize object
* -sets $this->entryClassName
*
* @param none
* @return none
* @author TQ White II
*
*/
public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
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
	
		$page=new \mvc\views\BaseView($this->_getTemplatePath('displayMessage'));
		$page->SetViewScopedValue('messageArray', $messageArray);
	
		$messageArray[]="hello world<BR>";
		$page->SetViewScopedValue('messageArray', $messageArray);

		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: startController::index");
		$page->SetViewScopedValue('messageArray', $messageArray);
		$page->render();
		
		return;
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

public function add(){
	
		$page=new \mvc\views\BaseView($this->_getTemplatePath('displayMessage'));
		$page->SetViewScopedValue('messageArray', $messageArray);
	
		$messageArray[]="hello world<BR>";
		$page->SetViewScopedValue('messageArray', $messageArray);

		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: startController::index");
		$page->SetViewScopedValue('messageArray', $messageArray);
		$page->render();
		
		return;
	}

public function postCatcher(){
		
		$outArray['received']=dump($_POST, 'php received _POST', false, false);
		
		$bookmarkList=new \mvc\models\BookmarkList();
        $urlList=$bookmarkList->getList('general');
		$bookmarkList->addBookmark($_POST);        
	

		$page=new \mvc\views\BaseView($this->_getTemplatePath('json'));
		$page->SetViewScopedValue('data', $outArray);
		$page->render();
}

public function show(){
        $tmp=new \mvc\models\BookmarkList();
        $urlList=$tmp->getList('general');
		
		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: startController::index");
		$page->SetViewScopedValue('urlList', $urlList);
		$page->render();
}

public function display(){
        $bookMarkList=new \mvc\models\BookmarkList();
        $urlList=$bookMarkList->getList('grant');
		
		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "Display Bookmarks");
		$page->SetViewScopedValue('urlList', $urlList);
		$page->render();
}


} //end of class

