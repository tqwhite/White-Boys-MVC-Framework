<?php

class BaseController{

	protected $entryClassName;
	
	protected $unknownActionHandlerName='index'; //default can be changed by child class
	
public function __construct(){
		//this space left intentionally blank, but that may change
	}
	
public function setUnknownActionHandlerName($value){
		$this->unknownActionHandlerName=$value;
	}
	
public function getUnknownActionHandlerName(){
		return $this->unknownActionHandlerName;
	}
	
protected function _getTemplatePath($templateName){
		$ds=DS;
		$className=get_class($this);
		
		$templateName=str_replace("$className::", '', $templateName);
		$className=preg_replace('/controller$/i', '', $className);
		
		$templatePath=camelCaseToUnderscore("$className$ds$templateName");
	
		return $templatePath;
	}

} //end of class