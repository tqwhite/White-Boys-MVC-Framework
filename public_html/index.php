<?php
namespace configs;
require_once('../framework/configs/init_environment.php');

if (empty($_GET['pathString'])){
	$pathString='start';
}
else{
	$pathString=$_GET['pathString'];
}

dispatchFirst($pathString);