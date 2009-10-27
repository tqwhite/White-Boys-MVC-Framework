<?php

class BaseView{

//properties ======================

private $templatePathName;

private $viewScopedValuesArray;

private $templateExtension='ctp';


//methods==========================

public function __construct($templatePathName=''){

	$this->templatePathName=$templatePathName;
	echo "Passing through baseView::_construct for {$this->templatePathName}<BR>";
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
	echo "filePath2=$filePath<BR>";
	require_once($filePath);
	
}

public function render(){
	
	echo "Passing through BaseView::render<BR>";
	$classRoot=CLASSROOT;
	$ds=DS;

	$fileString=$this->templatePathName;
	
	$filePath="$classRoot/views/$fileString.{$this->templateExtension}";
	echo "filePath=$filePath<BR>";
	
	if (file_exists($filePath)){
		$this->_createScopeAndRun($filePath);
		return;
	}
	else{
	exit("FATAL ERROR: Trying to render template $filePath. It does not exist.");
	}
}

}