<?php
require_once('../framework/library/initEnvironment.php');

//dump($_POST, '_POST');
//dump($_GET, '_GET');
//dump($_SERVER, '_SERVER');


if (empty($_GET['pathString'])){
	$pathString='start';
}
else{
	$pathString=$_GET['pathString'];
}

dispatchFirst($pathString);