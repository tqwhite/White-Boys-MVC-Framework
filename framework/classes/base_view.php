<?php

class BaseView{
	
	private $templatePathName;
	
	private $viewScopedValuesArray=array();
	
	private $templateExtension='ctp';
	
	
public function __construct($templatePathName=''){
		$this->templatePathName=$templatePathName;
	}
	
public function SetViewScopedValue($varName, $value){
		//todo: restrict to declared variables
	
		$this->viewScopedValuesArray[$varName]=$value;
	}
	
private function _getHtml(){
		$templatePathName=$this->templatePathName;
		
		
	}
	
private function _createScopeAndRun($filePath){
	
		foreach ($this->viewScopedValuesArray as $label=>$data){
			$$label=$data;
		}

		require_once($filePath); //do the actual rendering

	}
	
public function render(){
		$classRoot=CLASSROOT;
		$ds=DS;
	
		$fileString=$this->templatePathName;

		$filePath="$classRoot/views/$fileString.{$this->templateExtension}";
				
		if (file_exists($filePath)){
			$this->_createScopeAndRun($filePath);
			return;
		}
		else{
		exit("FATAL ERROR: Trying to render missing template. Invalid filePath: $filePath.");
		}
	}

} //end of class