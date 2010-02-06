<?php






//Display =======================================================================


$outString="
<div style=color:red;margin-top:20px;margin-bottom:15px;font-family:sans-serif;font-size:13pt;>
Host FTP Transfer Status
</div>
<table cellpadding=0 cellspacing=0 border=0 style=width:100%;margin-left:20px;>
<TR>
<TD style=width:50%>
{$transferDirectory->showArrayProperty($transferDirectory->processStatusArray)}

<div style=color:red;margin-top:20px;margin-bottom:10px;font-family:sans-serif;font-size:10pt;>Upload list</div>
{$transferDirectory->showArrayProperty($transferDirectory->uploadReportArray)}

<div style=color:red;margin-top:20px;margin-bottom:10px;font-family:sans-serif;font-size:10pt;>Delete list</div>
{$transferDirectory->showArrayProperty($transferDirectory->deleteReportArray)}

<div style=color:red;margin-top:20px;margin-bottom:10px;font-family:sans-serif;font-size:10pt;>Error Messages</div>
{$transferDirectory->showArrayProperty($transferDirectory->errorReportArray)}

<div style=color:red;margin-top:20px;margin-bottom:10px;font-family:sans-serif;font-size:10pt;>Exclusion strings</div>
{$transferDirectory->showArrayProperty($transferDirectory->exclusionStringArray)}

<div style=color:red;margin-top:20px;margin-bottom:10px;font-family:sans-serif;font-size:10pt;>Exclusion list</div>
{$transferDirectory->showArrayProperty($transferDirectory->excludedFilesArray)}

</TD>
<TD style=width:50%>

&nbsp;
</TD>
</TR>
</TABLE>

";

$outString="
		<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01//EN'>
		<html>
		<head>
		  <title>FILE TRANSFER</title>
		</head>
		
		<body style='font-family:sans-serif;font-size:10pt;'>
		$outString
		</body>
		</html>
	";
echo $outString;

