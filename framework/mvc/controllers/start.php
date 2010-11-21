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



public function index(){

	//initialization
		$root=ROOT; //this constant is defined on my system. Probably not yours.
		$externalImageDirPath=$root.'framework'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'external_images';
		$externalImageDirPath=$root.'public_html'.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'external_images';
		$contents='';
		$quantity=0;
		$blockSize=1000;
		$maxSize=1000000;
		$sourceUrl='http://justkidding.com/tmp.txt';
		$sourceUrl='http://justkidding.com/PIX/bush120507.png';
		$sourceUrl='http://justkidding.com/dbPIX/changedPrioritiesAhead-1218796352.jpg';
		
	
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
			$fileObj=new fileDemo();
			$fileObj->setValue('destDirPath', $externalImageDirPath);
			$fileObj->setValue('destFileName', basename($urlArray['path']));
			
			$fileObj->setValue('fileContents', $contents);
			if (!$fileObj->putData()){
				throw new \Exception($fileObj->getValue('errorMessage'));
			}
				
			$message="successfully wrote file: {$fileObj->getFilePath()}";
		}
	
		catch (\Exception $e){
			$message=$e->getMessage();
		}
	
	//	echo $message;
	
/*

	try{
		$fileObj=new fileDemo();
		$fileObj->setValue('destDirPath', ROOT.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'external_images');
		$fileObj->setValue('destFileName', 'tmp.txt');
		if (!$fileObj->getData()){
			throw new \Exception($fileObj->getValue('errorMessage'));
		}
		
		$editCopy=$fileObj->getValue('fileContents');
		$newVersion=$editCopy.PHP_EOL.$editCopy;
		
		$fileObj->setValue('fileContents', $newVersion);
		if (!$fileObj->putData()){
			throw new \Exception($fileObj->getValue('errorMessage'));
		}
			
		$message="successfully wrote file: {$fileObj->getFilePath()}";
	}
	
	catch (\Exception $e){
		$message=$e->getMessage();
	}
	*/
	
	$page=new \mvc\views\BaseView($this->_getTemplatePath(__METHOD__));
	$page->SetViewScopedValue('message', $message);
	$page->SetViewScopedValue('imagePath', $fileObj->getFilePath());
	$page->render();
}

} //end of class

class fileDemo {

	private $destDirPath;
	private $destFileName;
	private $fileContents;
	
	private $errorMessage;
	
	private $fileHandle;

	public function setValue($name, $value){
		$this->$name=$value;
	}
	
	public function getValue($name){
		return $this->$name;
	}
	
	private function _checkFileExists(){
		$filePath=$this->destDirPath.DIRECTORY_SEPARATOR.$this->destFileName;
		return file_exists($filePath);
	}
	
	private function _checkDirExists(){
		return is_dir($this->destDirPath);
	}
	
	public function getFileName(){
		return $this->destFileName;
	}
	
	public function getFilePath(){
		return $this->destDirPath.DIRECTORY_SEPARATOR.$this->destFileName;
	}
	
	/** 
	 * Write case validates parameters and saves data
	 *
	 */
	public function putData(){
	
		try {
			if (empty($this->fileContents)){
				throw new \Exception("can't write empty string in fileDemo::putData");
			}
			
			if (!$this->_checkDirExists()){
				throw new \Exception("invalid directory path supplied to fileDemo::putData");
			}
			
			$this->fileHandle=fopen($this->getFilePath(), 'wb');
			fwrite($this->fileHandle, $this->fileContents);
			fclose($this->fileHandle);
		
			if (!$this->_checkFileExists()){
				throw new \Exception("could not create file (probably permissions) in fileDemo::putData");
			}
			
			return true;
		}
		
		catch (\Exception $e){
			$this->errorMessage=$e->getMessage();
			return false;
		}
	}	
	/** 
	 * Read case validates parameters and gets data
	 *
	 * @author  TQ White II <tq@tqwhite.com>
	 *
	 */
	public function getData(){

	
		try {
			
			if (!$this->_checkFileExists()){
				throw new \Exception("file ({$this->getFilePath()}) does not exist fileDemo::getData");
			}
			
			$size=filesize($this->getFilePath());
			
			$this->fileHandle=fopen($this->getFilePath(), 'rb');
			$this->fileContents=fread($this->fileHandle, $size);
			fclose($this->fileHandle);
		
			if ($this->fileContents===false){
				throw new \Exception("could not read file ({$this->getFilePath()}) in fileDemo::getData");
			}
			
			return true;
		}
		
		catch (\Exception $e){
			$this->errorMessage=$e->getMessage();
			return false;
		}
	}

}//end of class