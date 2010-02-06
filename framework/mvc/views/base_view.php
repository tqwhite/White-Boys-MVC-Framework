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
	
	public $pageElements;
	
	private $htmlPageWrapper="
		<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN'
		   'http://www.w3.org/TR/html4/strict.dtd'>
		<html>
		<head>
			<title><!--title--></title>
			<!--cssLink-->
			<!--cssCode-->
			<!--cssInclude-->
			<!--jsLink-->
			<!--jsCode-->
			<!--jsInclude-->
			<!--otherHead-->
		</head>
		<body>
			<!--bodyText-->
		</body>
		</html>
		";
	
	private $cssCodeWrapper="
		<style type='text/css'>
			<!--bodyText-->
		</style>
		";
	
	private $jsCodeWrapper="
		<script type='text/javascript'>
		/* <![CDATA[ */
			<!--bodyText-->
		/* ]]> */
		</script>
		";
	
	private $cssLinkWrapper="<link rel='stylesheet' type='text/css' href='<!--url-->' />";
	private $jsLinkWrapper="<script type='text/javascript' src='<!--url-->'></script>";
	
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
	
private function _assembleOutput($template){
	$elementsArray=$this->pageElements;
	$outString=$template;
	if (is_array($elementsArray)){
	
		foreach ($elementsArray as $label=>$data){
		
			if (is_array($data)){
			$subString='';
			switch ($label){
				case '<!--jsLink-->':
					foreach ($data as $label2=>$data2){
						$subString.=str_replace('<!--url-->', $data2, $this->jsLinkWrapper);
					}
					break;
				case '<!--cssLink-->':
					foreach ($data as $label2=>$data2){
						$subString.=str_replace('<!--url-->', $data2, $this->cssLinkWrapper);
					}
					break;
				case '<!--jsInclude-->':
					foreach ($data as $label2=>$data2){
						$data2=file_get_contents($data2);
						$subString.=str_replace('<!--bodyText-->', $data2, $this->jsCodeWrapper);
					}
					break;
				case '<!--cssInclude-->':
					foreach ($data as $label2=>$data2){
						$data2=file_get_contents($data2);
						$subString.=str_replace('<!--bodyText-->', $data2, $this->cssCodeWrapper);
					}
					break;
				case '<!--jsCode-->':
					foreach ($data as $label2=>$data2){
						$subString.=str_replace('<!--bodyText-->', $data2, $this->jsCodeWrapper);
					}
					break;
				case '<!--cssCode-->':
					foreach ($data as $label2=>$data2){
						$subString.=str_replace('<!--bodyText-->', $data2, $this->cssCodeWrapper);
					}
					break;
				case '<!--bodyText-->':
					foreach ($data as $label2=>$data2){
						$subString.="$data2\n\n";
					}
					break;
			}
			$outString=str_replace($label, $subString, $outString);
			}
		
			$outString=str_replace($label, $data, $outString);
		}
	
	}
	

	
	//return $this->_tidy($outString)->value;
	return $this->_clearTokens($outString);
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
		exit("FATAL ERROR: Trying to render missing template. Invalid filePath: $filePath (BaseView::render).");
		}
	}
	
private function _tidy($html){
	//config options listed at: http://tidy.sourceforge.net/docs/quickref.html
	//built in for easier evaluation of accessability, etc. not really done
	$config = array(
			   'indent'         => true,
			   'output-xhtml'   => true,
			   'wrap'           => 200);
	
	// Tidy
	$tidy = new \tidy;
	$tidy->parseString($html, $config, 'utf8');
	$tidy->cleanRepair();
	return $tidy;
}

private function _clearTokens($html){

$patternArray[]='<!--title-->'; $replaceArray[]='';
$patternArray[]='<!--bodyText-->'; $replaceArray[]='';
$patternArray[]='<!--jsLink-->'; $replaceArray[]='';
$patternArray[]='<!--cssLink-->'; $replaceArray[]='';
$patternArray[]='<!--jsInclude-->'; $replaceArray[]='';
$patternArray[]='<!--cssInclude-->'; $replaceArray[]='';
$patternArray[]='<!--jsCode-->'; $replaceArray[]='';
$patternArray[]='<!--cssCode-->'; $replaceArray[]='';
$patternArray[]='<!--otherHead-->'; $replaceArray[]='';

return str_replace($patternArray, $replaceArray, $html);

}

protected function addSpecial($string, $item){
	//eg, "<body>" becomes <body style=color:red;>
	$this->pageElements[$string]=$item;
}
protected function addPageTitle($item){
	$this->pageElements['<!--title-->']=$item;
}
protected function addToBody($item){
	$this->pageElements['<!--bodyText-->'][]=$item;
}

protected function addJsLink($item){
	$this->pageElements['<!--jsLink-->'][]=$item;
}
protected function addCssLink($item){
	$this->pageElements['<!--cssLink-->'][]=$item;
}
protected function addJsInclude($item){
	$this->pageElements['<!--jsInclude-->'][]=$item;
}
protected function addCssInclude($item){
	$this->pageElements['<!--cssInclude-->'][]=$item;
}
protected function addJsCode($item){
	$this->pageElements['<!--jsCode-->'][]=$item;
}
protected function addCssCode($item){
	$this->pageElements['<!--cssCode-->'][]=$item;
}
protected function addOtherHead($item){
	$this->pageElements['<!--otherHead-->'][]=$item;
}

} //end of class