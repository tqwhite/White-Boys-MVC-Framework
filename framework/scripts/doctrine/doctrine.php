<?php

require_once '../../../framework/configs/init_environment.php'; //includes reference to ClassLoader.php

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
$classLoader->setIncludePath(__DIR__ . '/../lib');
$classLoader->register();

$configuration = new \Doctrine\Common\Cli\Configuration();

$cli = new \Doctrine\Common\Cli\CliController($configuration);
$cli->run($_SERVER['argv']);

//$em is created in init_environment.php since everything Doctrine ORM seems to want it.