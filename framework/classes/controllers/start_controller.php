<?php
class StartController extends BaseController{

public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
	}

public function index(){

$urlList=array();
$item=array();

$item['url']='/OpenSource';
$item['text']='Open Source Page 1';
$urlList[]=$item;
$item['url']='/OpenSource/2';
$item['text']='Open Source Page 2';
$urlList[]=$item;
$item['url']='/siteUtilities';
$item['text']='Site Utilities';
$urlList[]=$item;
$item['url']='/start';
$item['text']='Front Page';
$urlList[]=$item;

		
		$page=new BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: startController::index");
		$page->SetViewScopedValue('urlList', $urlList);
		$page->render();
		
		return;
	}

} //end of class