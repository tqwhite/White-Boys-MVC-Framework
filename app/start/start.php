<?php
namespace app\Start;
/** 
 * SiteUtilities contains actions for pages 
 * accessed by a URL of the form http://domain.com/start/action
 * or http://domain.com
 * 
 * @author  TQ White II <tq@justkidding.com>
 * @package WhiteBoysFramework
 * 
 */
class Start extends \framework\mvc\BaseController{

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
		
		$urlList['fileDemo']='/start/fileDemo';
		$urlList['Site Utilities']='/siteUtilities';

		$page=new \framework\mvc\BaseView(FRAMEWORKVIEWS.'showArray');
		$page->SetViewScopedValue('title', "A Title Sent by: ".__METHOD__);
		$page->SetViewScopedValue('urlList', $urlList);
		$page->SetViewScopedValue('type', 'link');
		$page->SetViewScopedValue('message', "And more words to live by from the correct source of content.");
		$page->render();
	}



public function fileDemo(){ //this method relies on variable length parameter list (see func_num_args())

	//initialization
		$root=ROOT; //this constant is defined on my system. Probably not yours.
		$externalImageDirPath=MEDIADEST;
		$contents='';
		$quantity=0;
		$blockSize=1000;
		$maxSize=1000000;
	
	if (func_num_args()>1){
		foreach (func_get_args() as $label=>$data){
			if ($data=='http:'){
				$sourceUrl='http:/';
			}
			else{
				$sourceUrl.="/$data";
			}
		}
	}
	
	if (empty($sourceUrl)){ 
		$sourceUrl=MEDIAROOT.'wbmvc/logo.jpg';
		$message="<div style='margin-top:20px;padding:10px;color:green;font-size:12pt;border:1pt solid green;width:600px;'>Add an image URL to the end of this pages URL.<p/>Eg, http://YourDomain.com/start/fileDemo/<span style=color:black;>http://justkidding.com/PIX/maitinis.jpg</span></div>";
	}
	
	//get source data from web
		try {
			$handle=fopen($sourceUrl, 'rb');
			while (!feof($handle)){
				$thisBlock=fread($handle, $blockSize);
				$contents.=$thisBlock;
			
				$quantity=$quantity+strlen($thisBlock);
				if ($quantity>$maxSize){
					throw new \Exception("file is too big");
				}
			}
			fclose($handle);
	
	//write section using demo class instance
			$urlArray=parse_url($sourceUrl);
			$fileObj=new \app\start\FileDemo();
			$fileObj->setValue('destDirPath', $externalImageDirPath);
			$fileObj->setValue('destFileName', basename($urlArray['path']));
			
			$fileObj->setValue('fileContents', $contents);
			if (!$fileObj->putData()){
				throw new \Exception($fileObj->getValue('errorMessage'));
			}
				
			$message.="successfully wrote file: {$fileObj->getFilePath()}<p/>";
		}
	
		catch (\Exception $e){
			$message=$e->getMessage();
		}

	
	$page=new \framework\mvc\BaseView($this->_getTemplatePath(__METHOD__));
	$page->SetViewScopedValue('message', $message);
	$page->SetViewScopedValue('imagePath', $fileObj->getFilePath());
	$page->render();
}

} //end of class

