<?php
class OpenSourceController extends BaseController{

public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
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
		$page=new BaseView($templatePath);
		$page->render();
		
		
	}
} //end of class