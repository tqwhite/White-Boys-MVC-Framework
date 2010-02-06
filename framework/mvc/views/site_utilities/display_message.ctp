<?php
//Display =======================================================================

$outString='';

foreach ($messageArray as $data){
	$outString.="<div>$data</div>";
}

$outString="
		<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01//EN'>
		<html>
		<head>
		  <title>$pageTitle</title>
		</head>
		
		<body style='font-family:sans-serif;font-size:10pt;'>
		$outString
		</body>
		</html>
	";
echo $outString;

