<?
class transferDirectory{
	
	public $ftpUser;
	public $ftpPass;
	public $ftpHost;	
	
	public $dryRunFlag=false;
	public $initDatabase=false;
	
	private $baseDirPath;
	private $controlFileName;
	private $referenceFileArray;
	private $globArray;
	
	public $exclusionStringArray=array(
		''=>'nocopy',
		''=>'/system/'
		);
	
	public $unchangedArray=array();
	public $needsUploadArray=array();
	public $newFileArray=array();
	public $deletedFilesArray=array();
	public $allFilesArray=array();
	
	public	$processStatusArray=array();
	
	public $helpArray=array();
	public $uploadReportArray=array();
	public $deleteReportArray=array();
	public $errorReportArray=array();		//eg, justkidding.com

	public $pathTranslationArray;
	public $urlTranslationArray;
	
	public $arrayItemWrapperTemplate="<div style=font-family:sans-serif;font-size:8pt;color:green;><!message!></div>";
	public $uploadFileTemplate;
	public $deleteFileTemplate;
	public $linkTemplate='(<A href=<!url!> target=_new>here</A>)';

	
public function showArrayProperty($property){
	if (is_array($property)){
		foreach ($property as $label=>$data){
		if (!empty($this->arrayItemWrapperTemplate)){
			$outString.=str_replace("<!message!>", $data, $this->arrayItemWrapperTemplate);
		}
		else{
		
			$outString.=$data;
		}
		}
	}
return($outString);
}
	
public function showHelp(){

$outString="

File analysis excludes files that:<P>
1) have names starting with period<BR>
2) have the string 'nocopy' anywhere in their file path (eg, ~/foo/nocopy/bar, or nocopy.php<BR>


";

$this->helpArray[]=$outString;

}

public function __construct($baseDirPath){
	
	
		$this->baseDirPath=$baseDirPath; 
		$this->controlFileName='.ftpControl';
		

@		$tmp=$this->getThisObject($this->baseDirPath.$this->controlFileName);

	if (is_object($tmp)){
		$tmpArray=(array) $tmp; //thanks Tyler
		foreach ($tmpArray as $label=>$data){
			$labelSplit = explode("\0", $label);
			if (is_array($labelSplit)){
			$labelName=array_pop($labelSplit);;
			$this->$labelName=$data;
			}

		unset($tmp);
		}
		
	$tmp=__FUNCTION__.": restored class from file<BR>";
	$this->processStatusArray[]=$tmp;

	}
	
	

	$this->processStatusArray=array();
	
	$this->helpArray=array();
	$this->uploadReportArray=array();
	$this->deleteReportArray=array();
	$this->errorReportArray=array();

	$this->pathTranslationArray=array();
	$this->urlTranslationArray=array();
	
	
	//these are retained until FTP activity changes them
		 $this->needsUploadArray=array();
		 $this->newFileArray=array();
	
	$this->globArray=$this->getAllFilesInDir($this->baseDirPath);
		
	//moved to client page: $this->analyzeFiles();
}
private function getThisObject($filePath){
	$resultString = file_get_contents($filePath);
	return(unserialize($resultString));
}
public function saveThisObject(){

			if ($this->dryRunFlag==false or $this->initDatabase==true){
			
	if (!is_dir($this->baseDirPath)){
		mkdir($this->baseDirPath, 0755, true);
	}
	$filePath=$this->baseDirPath.$this->controlFileName;
	$contentString=serialize($this);
	$result=file_put_contents($filePath, $contentString);
	
	}
			else{
			$this->processStatusArray[]="dry run flag set, database not saved";
			}
	
	return(true);
}	
private function getAllFilesInDir($dirPath){
	    $fileListArray = Array();
	    $resultList= glob($dirPath.'*', GLOB_MARK | GLOB_NOSORT);   
	    foreach($resultList as $item){
	        if(substr($item,-1)!=DIRECTORY_SEPARATOR)
	            $fileListArray[] = $item;
	        else
	            $fileListArray = array_merge($fileListArray, $this->getAllFilesInDir($item));
}
	
	    return $fileListArray;
	}
private function hashFile($filePath){
	$fileString=file_get_contents($filePath);
	return(md5($fileString));
}

private function checkExclusion($filePath){

foreach ($this->exclusionStringArray as $label=>$data){
	if (contains($data, strtolower($filePath))){
		return(true);
	}
}
return(false);
}

public function analyzeFiles(){

	$this->referenceFileArray=$this->allFilesArray;
	$potentiallyDeletedFilesArray=$this->allFilesArray; //will remove found files to leave deletable ones behind
	$newAllFilesArray=array();

	foreach ($this->globArray as $label=>$filePath){

		$fileName=basename($filePath);
		
		
		if (!$this->checkExclusion($filePath) and substr($fileName, 0, 1)!='.' and $fileName!=__FILENAME__){	
			$fileName=basename($filePath);
			$fileHash=$this->hashFile($filePath);
			$nameHash=md5($filePath);
			

			unset($potentiallyDeletedFilesArray[$nameHash]);


			if (is_array($this->referenceFileArray[$nameHash])){
			
				if ($fileHash==$this->referenceFileArray[$nameHash]['fileHash']){
					$this->unchangedArray[$nameHash]=$this->referenceFileArray[$nameHash];
					
						
					$newAllFilesArray[$nameHash]['fileHash']=$fileHash;
					$newAllFilesArray[$nameHash]['filePath']=$filePath;
						
			}
				else{

					$this->needsUploadArray[$nameHash]['fileHash']=$fileHash;
					$this->needsUploadArray[$nameHash]['filePath']=$filePath;
					
					$newAllFilesArray[$nameHash]['fileHash']=$fileHash;
					$newAllFilesArray[$nameHash]['filePath']=$filePath;
					
				}
				
			}
			else{
					$this->newFileArray[$nameHash]['fileHash']=$fileHash;
					$this->newFileArray[$nameHash]['filePath']=$filePath;
					
					$newAllFilesArray[$nameHash]['fileHash']=$fileHash;
					$newAllFilesArray[$nameHash]['filePath']=$filePath;

					
			}

		}
	}
	$this->deletedFilesArray=$potentiallyDeletedFilesArray; //all files found by glob were removed
	$this->allFilesArray=$newAllFilesArray;
}
private function figureFtpPath($localPath){
/*
	$localPath=realpath($localPath);
	if (empty($localPath)){
		return('');
	}
*/	
	$ftpOutPath=str_replace($this->baseDirPath, '', $localPath);
	
	if (is_array($this->pathTranslationArray)){
	
		foreach ($this->pathTranslationArray as $label=>$pathArray){
		
			if (contains($pathArray['local'], $localPath)){
			
				$ftpOutPath=str_replace($pathArray['local'], $pathArray['remote'], $localPath);
			
			}
		
		}
	
	}
return ($ftpOutPath);
}
public function sendFile($localPath){
	
	$destPath=$this->figureFtpPath($localPath);
	
	if (!empty($destPath)){
		try {


			
			if ($this->dryRunFlag==false){
			
				$ftp = new Ftp;
				$ftp->connect($this->ftpHost);
				$ftp->login($this->ftpUser, $this->ftpPass);
				$ftp->pasv(true);
				
			//	$result=$ftp->nlist('./public_html'); //get name list, ie, ls, sort of
			
	
				$result=$ftp->mkDirRecursive(dirname($destPath));
				
				$result=$ftp->put('./'.$destPath, $localPath, FTP_BINARY);
				$ftp->close();
			
			}
			else{
			
				$this->processStatusArray['ftpMessage']="dry run flag set, no ftp executed";
			}
			
			$url=str_replace($this->urlTranslationArray['docRootDir'], $this->urlTranslationArray['baseUrl'], $destPath);
	
			if(contains($this->urlTranslationArray['baseUrl'], $url)==1){
		
			//file is in docrRoot, needs link
				
				$report['url']=$url;
				
			}
			else {
				
				$report['url']='';
			}

		//	$report=$this->uploadFileTemplate;
			
		//	$report=str_replace('<!link!>', $linkTemplate, $report);

			
			$report['function']='upload';
			$report['destPath']=$destPath;
			$report['localPath']=$localPath;
			$report['result']=$result;
			
					
			
		}
			catch (FtpException $e) {
			
			$report['function']='upload';
			$report['destPath']=$destPath;
			$report['localPath']=$localPath;
			$report['result']=$e->getMessage();
			
			
			}
			
		$this->uploadReportArray[]=processTemplateArray($this->uploadFileTemplate, $report);


	}

}
public function deleteFile($localPath){
	
	$destPath=$this->figureFtpPath($localPath);

	if (!empty($destPath) or 5==5){
		try {
		
		if ($this->dryRunFlag==false){
			
			$ftp = new Ftp;
			$ftp->connect($this->ftpHost);
			$ftp->login($this->ftpUser, $this->ftpPass);
	
			
			$result=$ftp->delete('./'.$destPath);
			$ftp->close();
			
			}
			else{
			$this->processStatusArray[]="dry run flag set, no ftp executed";
			}


			$report['function']='delete';
			$report['destPath']=$destPath;
			$report['localPath']=$localPath;
			$report['result']=$result;
			
			
		}
			catch (FtpException $e) {
			
			$report['function']='delete';
			$report['destPath']=$destPath;
			$report['localPath']=$localPath;
			$report['result']=$e->getMessage();
			
			}

		$this->deleteReportArray[]=processTemplateArray($this->uploadFileTemplate, $report);


	}

}
public function executeFTP(){


	foreach ($this->newFileArray as $label=>$data){
		
		$this->sendFile($data['filePath']);
	
	}
	
	foreach ($this->needsUploadArray as $label=>$data){
	
		$this->sendFile($data['filePath']);
	
	}
	

	foreach ($this->deletedFilesArray as $label=>$data){
	
		$this->deleteFile($data['filePath']);
	
	}
	
	
}
public function showFileArray($fileArray){
	$outString='';
	
	foreach ($fileArray as $label=>$data){
		$showPath=str_replace($this->baseDirPath, '', $data);
		$showName=basename($data);
		$outString.="<TR><TD>$showName=</TD><TD>$showPath</TD></TR>";
	}
	
	$outString="
	<TABLE cellpadding=0 cellspacing=0 border=0>
		$outString
	</TABLE>
	";
	echo $outString;
}
public function addExclusionString($item){
	$this->exclusionStringArray[]=$item;
}
} //end of class
