<?php
class SiteUtilitiesController extends BaseController{


public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
	}

public function index(){

		$page=new BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: SiteUtilitiesController::index");
		$page->SetViewScopedValue('message', "And more words to live by from the correct source of content.");
		$page->render();
	}

} //end of class