<?php


function camelCaseToUnderscore($inString){
	$inString=preg_replace('/^([A-Z])/e', 'strtolower("\1")', $inString);
	$inString=preg_replace('/([A-Z])/', '_\1', $inString);
	$inString=preg_replace('*/_*', '/', $inString); //don't want spurious slash if first letter is upper case
	$outString=strtolower($inString);
	return $outString;
}
