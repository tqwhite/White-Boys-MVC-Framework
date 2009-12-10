<?php
namespace mvc\views;
/**
* Base mechanism for display of pages 
* accessed by a URL of the form http://domain.com/openSource/action
*
* @package WhiteBoysFramework
*/
class BaseView extends \mvc\BaseClass{
	
	private $templatePathName;
	
	private $viewScopedValuesArray=array();
	
	private $templateExtension='ctp';
	
/**
* Generates a view object and, optionally, sets the template
*
* @param $templatePathName (optional)
* @return none
* @author TQ White II
*
*/	
public function __construct($templatePathName=''){
		$this->templatePathName=$templatePathName;
	}
/**
* Set View-scoped Value makes a value available to a display template.
*
* @param $varName: name by which template will refer to this value
* @param $value: value of the template variable
* @return none
* @author TQ White II
*
*/	
public function SetViewScopedValue($varName, $value){
		//todo: restrict to declared variables
	
		$this->viewScopedValuesArray[$varName]=$value;
	}
	
private function _getHtmlDISCARD(){
		$templatePathName=$this->templatePathName;
		
	}
	
private function _createScopeAndRun($filePath){
	
		foreach ($this->viewScopedValuesArray as $label=>$data){
			$$label=$data;
		}

		require_once($filePath); //do the actual rendering

	}
	
public function render(){
		$mvcRoot=MVCROOT;
		$ds=DS;
	
		$fileString=$this->templatePathName;

		$filePath="$mvcRoot/views/$fileString.{$this->templateExtension}";
				
		if (file_exists($filePath)){
			$this->_createScopeAndRun($filePath);
			return;
		}
		else{
		exit("FATAL ERROR: Trying to render missing template. Invalid filePath: $filePath.");
		}
	}

} //end of class