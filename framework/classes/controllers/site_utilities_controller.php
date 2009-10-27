<?php
require_once('initEnvironment.php');
require_once('processTemplateArray.php');

/*
* 

DONE delete still needs to implemented
implement ignore file list

make user interface
figure out interface to initialize (esp baseUrl)
cause web page to display progressively, show files as they are moved
make list only version of executeFTP

make command line compatible
allow spec of http transfer file name so can redirect to page of interest after upload

performance:
make sendFile check if mkdir is needed
make it so it only logs in once

make it display through smarty in a web usage

*/

$transferDirectory=new transferDirectory('/Users/tqwhite/Documents/webdev/transformedByColor/transformedbycolor.com/');

$itemArray['local']='/Users/tqwhite/Documents/webdev/transformedByColor/transformedbycolor.com/docRoot/';
$itemArray['remote']='/public_html/';
$transferDirectory->pathTranslationArray[]=$itemArray; 


$itemArray['baseUrl']='http://transformedbycolor.com/';
$itemArray['docRootDir']='/public_html/';$transferDirectory->urlTranslationArray=$itemArray; 

echo "<div style=font-size:8pt;>addExclusionString is not working properly</div>";

$transferDirectory->addExclusionString('template_c');
$transferDirectory->addExclusionString('cache');

$transferDirectory->uploadFileTemplate='
	<div style=margin-bottom:10pt;font-family:sans-serif;font-size:8pt;>
		<div style=color:green;>
			UPLOAD status=<!result!> <!link!>
			</div>
		<div style=color:gray;>
			source: <!localPath!><br>dest: <!destPath!>
			</div>
		</div>';

$transferDirectory->deleteFileTemplate='
	<div style=margin-bottom:10pt;font-family:sans-serif;font-size:8pt;>
		<div style=color:green;>
			DELETE status=<!result!>
			</div>
		<div style=color:gray;>
			source: <!localPath!><br>dest: <!destPath!>
			</div>
		</div>';
		
$transferDirectory->dryRunFlag=false; //false says we're in production, upload files
$transferDirectory->initDatabase=false; //true says init db even though dryRunFlag==true, ignored if dryRunFlag==false


$transferDirectory->ftpUser='drumw';
$transferDirectory->ftpPass='fwosty';
$transferDirectory->ftpHost='transformedbycolor.com';	

//execute the FTP
	$transferDirectory->analyzeFiles();
	$transferDirectory->executeFTP($fileArray);
	$transferDirectory->saveThisObject($fileArray);
	

	//dump($transferDirectory->deletedFilesArray);
	
	
//Display =======================================================================

//dump($transferDirectory->uploadReportArray);

 
$transferDirectory->showArrayProperty($transferDirectory->errorReportArray);
	
$outString="

<table cellpadding=0 cellspacing=0 border=0 style=width:100%>
<TR>
<TD style=width:50%>
{$transferDirectory->showArrayProperty($transferDirectory->processStatusArray)}

{$transferDirectory->showArrayProperty($transferDirectory->uploadReportArray)}

{$transferDirectory->showArrayProperty($transferDirectory->deleteReportArray)}

{$transferDirectory->showArrayProperty($transferDirectory->errorReportArray)}

</TD>
<TD style=width:50%>

&nbsp;
</TD>
</TR>
</TABLE>

";
	
/*
	var $transferDirectory->pathTranslationArray;
	
	var $urlTranslationArray;
	
	var $transferDirectory->uploadFileTemplate;
	var $deleteFileTemplate;
*/

echo $outString;

?>