<?php
namespace framework\mvc;
/** 
 * Base Controller
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class BaseController extends \framework\mvc\BaseClass{

	protected $entryClassName;
	
	protected $unknownActionHandlerName='index'; //default can be changed by child class
	
public function setUnknownActionHandlerName($value){
		$this->unknownActionHandlerName=$value;
	}
	
public function getUnknownActionHandlerName(){
		return $this->unknownActionHandlerName;
	}
	
protected function _getTemplatePath($templateName){
		$ds=DS;
		$fullClassName=get_class($this);

		$nameSpace=__NAMESPACE__;

		$simpleClassName=str_replace(__NAMESPACE__.'\\', '', $fullClassName);

		$templateName=str_replace("$fullClassName::", '', $templateName);

		$simpleClassName=preg_replace('/controller$/i', '', $simpleClassName);
		
		$templatePath=camelCaseToUnderscore("$simpleClassName$ds$templateName");

		return $templatePath;
	}

protected function _getControllerNameForUrl(){
	return str_replace(__NAMESPACE__.'\\', '', $this->entryClassName);
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