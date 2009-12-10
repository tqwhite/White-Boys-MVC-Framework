<?php
namespace mvc\controllers;
/** 
* OpenSourceController contains actions for pages 
* accessed by a URL of the form http://domain.com/openSource/action
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class OpenSource extends \mvc\controllers\BaseController{

/**
* Calculates default page info, renders appropriate template
* -presently it's demo pages
*
* @param none
* @return none
* @author TQ White II
*
*/
public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
		//get_called_class()
	}

public function index($pageNum=''){
		
		if (empty($pageNum)){
		$pageNum=1;
		}
		
		switch ($pageNum){
		default:
		case '1':
			$templateName='index';
			break;
		case '2':
			$templateName='page2';
			break;
		}
	
		$templatePath=$this->_getTemplatePath($templateName);
		$page=new \mvc\views\BaseView($templatePath);
		$page->render();
		
		
	}
} //end of class