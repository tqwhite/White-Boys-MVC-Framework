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

protected function _getControllerNameForUrl(){
	return preg_replace('/Controller$/', '', $this->entryClassName);
}
	
protected function _getUrlPath($templateName=''){
	$controller=$this->_getControllerNameForUrl();
	if (empty($templateName)){
		$path="/$controller";
	}
	else{
		$path="/$controller/$templateName";
	}
	return $path;
}

} //end of class