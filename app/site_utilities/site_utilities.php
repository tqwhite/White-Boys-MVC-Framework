<?php
namespace app\SiteUtilities;
/** 
* SiteUtilities contains actions for pages 
* accessed by a URL of the form http://domain.com/siteUtilities/action
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class SiteUtilities extends \framework\mvc\BaseController{


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
		
		$urlList['phpInfo']='/siteUtilities/environmentInfo';
		$urlList['demo']='/siteUtilities/demo';
			
		$page=new \framework\mvc\BaseView(FRAMEWORKVIEWS.'showArray');
		$page->SetViewScopedValue('title', "A Title Sent by: SiteUtilitiesController::index");
		$page->SetViewScopedValue('urlList', $urlList);
		$page->SetViewScopedValue('type', 'link');
		$page->SetViewScopedValue('message', "And more words to live by from the correct source of content.");
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
		$page=new \framework\mvc\BaseView($this->_getTemplatePath(__METHOD__));
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
	
		$page=new \framework\mvc\BaseView($this->_getTemplatePath('displayMessage'));
		$page->SetViewScopedValue('messageArray', $messageArray);
	
		$messageArray[]="Dynamic message from the controller<BR>";
		$page->SetViewScopedValue('messageArray', $messageArray);

		$page=new \framework\mvc\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "Demo Page Title");
		$page->SetViewScopedValue('messageArray', $messageArray);
		$page->render();
		
		return;
	}


} //end of class