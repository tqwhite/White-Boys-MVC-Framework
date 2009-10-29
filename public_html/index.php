<?php
require_once('../framework/library/initEnvironment.php');

if (empty($_GET['pathString'])){
	$pathString='start';
}
else{
	$pathString=$_GET['pathString'];
}

dispatchFirst($pathString);