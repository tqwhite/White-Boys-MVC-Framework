<?php
namespace mvc\controllers;
/** 
 * SiteUtilities contains actions for pages 
 * accessed by a URL of the form http://domain.com/start/action
 * or http://domain.com
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class Start extends \mvc\controllers\BaseController{

/**
* Initialize object
* -sets $this->entryClassName
*
* @param none
* @return none
* @author TQ White II
*
*/
public function __construct(){
		parent::__construct(); //initialize the basic stuff
		$this->entryClassName=__CLASS__;
	}


/**
* Calculates default page info, renders appropriate template
* -a list of links
*
* @param none
* @return none
* @author TQ White II
*
*/
public function index(){

		$urlList=array();
		$item=array();
	
        $tmp=new \mvc\models\BookmarkList(); //not sure if this is giving the line below something to refer to or working around a bug
		$urlList=$tmp->getList('frontPage');
		
		
		$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
		$page->SetViewScopedValue('title', "A Title Sent by: startController::index");
		$page->SetViewScopedValue('urlList', $urlList);
		$page->render();
		
		return;
	}

public function fileDemo(){

	//initialization
		$root=ROOT; //this constant is defined on my system. Probably not yours.
		$externalImageDirPath=$root.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'external_images';
		$contents='';
		$quantity=0;
		$blockSize=1000;
		$maxSize=1000000;
		$sourceUrl='http://justkidding.com/tmp.txt';
		$sourceUrl='http://justkidding.com/PIX/bush120507.png';
		
	
	//get source data from web
		try {
			$handle=fopen($sourceUrl, 'rb');
			while (!feof($handle)){
				$thisBlock=fread($handle, $blockSize);
				$contents.=$thisBlock;
				
				$quantity=$quantity+strlen($thisBlock);
				if ($quantity>$maxSize){
					throw new Exception("file is too big");
				}
			}
			fclose($handle);
	
	//write section using demo class instance
			$urlArray=parse_url($sourceUrl);
			$fileObj=new fileDemo();
			$fileObj->setValue('destDirPath', $externalImageDirPath);
			$fileObj->setValue('destFileName', basename($urlArray['path']));
			
			$fileObj->setValue('fileContents', $contents);
			if (!$fileObj->putData()){
				throw new Exception($fileObj->getValue('errorMessage'));
			}
				
			$message="successfully wrote file: {$fileObj->getFilePath()}";
		}
	
		catch (Exception $e){
			$message=$e->getMessage();
		}
	
		echo $message;
	
/*

	try{
		$fileObj=new fileDemo();
		$fileObj->setValue('destDirPath', ROOT.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'external_images');
		$fileObj->setValue('destFileName', 'tmp.txt');
		if (!$fileObj->getData()){
			throw new Exception($fileObj->getValue('errorMessage'));
		}
		
		$editCopy=$fileObj->getValue('fileContents');
		$newVersion=$editCopy.PHP_EOL.$editCopy;
		
		$fileObj->setValue('fileContents', $newVersion);
		if (!$fileObj->putData()){
			throw new Exception($fileObj->getValue('errorMessage'));
		}
			
		$message="successfully wrote file: {$fileObj->getFilePath()}";
	}
	
	catch (Exception $e){
		$message=$e->getMessage();
	}
	*/
	
	$page=new \views\BaseView($this->_getTemplatePath(__METHOD__));
	$page->SetViewScopedValue('message', $message);
	$page->render();
}

} //end of class