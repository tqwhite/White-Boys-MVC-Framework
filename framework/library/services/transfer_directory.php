<?
namespace library\services;
class transferDirectory{
	
	public $ftpUser;
	public $ftpPass;
	public $ftpHost;
	public $ftpPort;
	
	private $ftp; //handle for sftp instance
	
	public $dryRunFlag=false;
	public $initDatabase=false;
	
	private $baseDirPath;
	private $controlFileName;
	private $controlFilePath;
	private $referenceFileArray;
	private $globArray;
	
	
	private $mandatoryExcludes=array('nocopy','/system/' );
	public $exclusionStringArray=array();
	
	public $unchangedArray=array();
	public $needsUploadArray=array();
	public $newFileArray=array();
	public $deletedFilesArray=array();
	public $allFilesArray=array();
	public $excludedFilesArray=array();
	public $uploadedFileCount=0;
	
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

	
public function __construct($baseDirPath){
	
	
		$this->baseDirPath=$baseDirPath; 
		$this->controlFileName='.ftpControl';
		$this->controlFilePath=$this->baseDirPath.DIRECTORY_SEPARATOR.$this->controlFileName;
		
		
		$this->exclusionStringArray=$this->mandatoryExcludes;
		$this->addExclusionString($this->controlFileName);
	
	

@		$tmp=$this->getThisObject($this->controlFilePath);

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
			
			$this->processStatusArray=array();
			$this->processStatusArray[]="found control file: {$this->controlFilePath}";
			$this->processStatusArray[]="restored class from {$this->controlFileName}";
	
		}
	
	$this->uploadedFileCount=0;
	$this->helpArray=array();
	$this->uploadReportArray=array();
	$this->deleteReportArray=array();
	$this->errorReportArray=array();
	$this->excludedFilesArray=array();

	$this->pathTranslationArray=array();
	$this->urlTranslationArray=array();
	
	
	//these are retained until FTP activity changes them
		 $this->needsUploadArray=array();
		 $this->newFileArray=array();
	
	$this->globArray=$this->getAllFilesInDir($this->baseDirPath);
		
	//moved to client page: $this->analyzeFiles();
}


public function showArrayProperty($property){
	$outString='';
	if (is_array($property)){
		foreach ($property as $label=>$data){
			if (!empty($this->arrayItemWrapperTemplate)){
				$outString.=str_replace("<!message!>", $data, $this->arrayItemWrapperTemplate);
			}
			else{
			
				$outString.=$data;
			}
		}
	if (empty($outString)){
	
				$outString.=str_replace("<!message!>", '--none--', $this->arrayItemWrapperTemplate);
	}
	}
return($outString);
}

public function getControlFilePath(){
	return $this->controlFilePath;
	}
	
public function showHelp(){
	$stringList='';
	foreach ($this->exclusionStringArray as $data){
		$stringList.="$data<BR>";
	}

	$outString="
	
	File analysis excludes and does not upload:
	1) the transferDirectory control file ({$this->controlFileName}<BR>
	2) files that have any of the following strings anywhere in their file path (eg, ~/foo/XXX/bar, or XXX.php<p/>
	$stringList
	";
	
	$this->helpArray[]=$outString;

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

			$filePath=$this->controlFilePath;
			$contentString=serialize($this);
			$result=file_put_contents($filePath, $contentString);
	
		}
		else{
			$this->processStatusArray[]="dry run flag set, control file ({$this->controlFileName}) not saved";
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

private function fileIsExcluded($filePath){
	foreach ($this->exclusionStringArray as $label=>$data){
			if (contains($data, strtolower($filePath))){
				$this->excludedFilesArray[]=$filePath;
				return(true); //true means exclude, don't send, even if changed
			}
		}
	
	return(false); //false means it is NOT excluded and should be sent if changed
}

public function analyzeFiles(){
	$this->referenceFileArray=$this->allFilesArray;
	$potentiallyDeletedFilesArray=$this->allFilesArray; //will remove found files to leave deletable ones behind
	$newAllFilesArray=array();

	foreach ($this->globArray as $label=>$filePath){

		$fileName=basename($filePath);
		
		if (!$this->fileIsExcluded($filePath)){	
			$fileName=basename($filePath);
			$fileHash=$this->hashFile($filePath);
			$nameHash=md5($filePath);
			

			unset($potentiallyDeletedFilesArray[$nameHash]);


			if (isset($this->referenceFileArray[$nameHash])){
			
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

public function initConnection(){
			if ($this->dryRunFlag==false){
			
			//	$this->ftp = new Ftp;
				$this->ftp = new Sftp;
				$this->ftp->connect($this->ftpHost, $this->ftpPort);
				$this->ftp->login($this->ftpUser, $this->ftpPass);
				$this->ftp->pasv(true);
			
			}
}

public function closeConnection(){
			if ($this->dryRunFlag==false){
				$this->ftp->close();			
			}
}

public function sendFile($localPath){
	
	$destPath=$this->figureFtpPath($localPath);
	
	if (!empty($destPath)){
		try {


			
			if ($this->dryRunFlag==false){


			//	$result=$this->ftp->nlist('./public_html'); //get name list, ie, ls, sort of
			

				$result=$this->ftp->mkDirRecursive('./'.dirname($destPath));
				echo "<div style=color:red;font-size:8pt;>TransferDirectory::sendFile says, Have not tested prefix on mkdir with standard ftp, only sftp</div>";
				
				$result=$this->ftp->put('./'.$destPath, $localPath, FTP_BINARY);
				
				
			$this->uploadedFileCount++;
			$this->processStatusArray['ftpMessage']="uploaded {$this->uploadedFileCount} files";
			
			}
			else{
			
				$this->processStatusArray['ftpMessage']="dry run flag set, no ftp executed";
				$result='';
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

			$result=$this->ftp->delete('./'.$destPath);
			
			}
			else{
			$this->processStatusArray['deleteMessage']="dry run flag set, no ftp deletions executed";
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

public function clearExclusionString(){
	$this->exclusionStringArray=array();
	$this->exclusionStringArray=$this->mandatoryExcludes;
	$this->addExclusionString($this->controlFileName);
}

public function addExclusionString($item){
	$this->exclusionStringArray[]=$item;
}

} //end of class
