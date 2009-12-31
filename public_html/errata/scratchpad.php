<?php
/**
 * Welcome to Doctrine 2.
 * 
 * This is the index file of the sandbox. The first section of this file
 * demonstrates the bootstrapping and configuration procedure of Doctrine 2.
 * Below that section you can place your test code and experiment.
 */

namespace Sandbox;

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache,
    Entities\User, Entities\Address;


include('../../framework/configs/init_environment.php');
require '../../framework/library/services/doctrine/lib/Doctrine/Common/ClassLoader.php';

// Set up class loading. You could use different autoloaders, provided by your favorite framework,
// if you want to.

$doctrineClassLoader = new ClassLoader('Doctrine', realpath(LIBROOT . '/services/doctrine/lib'));
$doctrineClassLoader->register();
$entitiesClassLoader = new ClassLoader('Entities', LIBROOT . '/services/doctrine/tools/sandbox');
$entitiesClassLoader->register();
$proxiesClassLoader = new ClassLoader('Proxies', LIBROOT . '/services/doctrine/tools/sandbox');
$proxiesClassLoader->register();

// Set up caches
$config = new Configuration;
$cache = new ApcCache;
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

// Proxy configuration
$config->setProxyDir(LIBROOT . '/services/doctrine/tools/sandbox/Proxies');
$config->setProxyNamespace('Proxies');

// Database connection information
$connectionOptions = array(
	'host' => \configs\MysqlConfig::getValue('server'),
    'user' => \configs\MysqlConfig::getValue('username'),
    'password' => \configs\MysqlConfig::getValue('password')
    );
    
$connectionOptions['driver']='pdo_mysql';
$connectionOptions['dbname']='phpDB';

// Create EntityManager
$em = EntityManager::create($connectionOptions, $config);

echo "Sandbox app merely creates a record in phpDB. Serial number below should change for each new one<BR>";

$user = new \Entities\User;
$userName='Garfield-'.time();
$user->setName($userName);
$em->persist($user);
$em->flush();

echo "

<style stype='text/css'>

@font-face{
font-family:tqtest;
src:url(../elements/fonts/FatPixels.ttf)
}

</style>
<div style='color:gray;font-family:tqtest;font-size:24pt;padding:10px;border:1pt solid gray;margin:15px;'>
	User <span style=color:orange;>$userName</span> saved!
</div>


";

exit();


//<iframe width="600px" height="500px" src="http://m.supersaas.com/schedule/demo/Therapist"></iframe>

?>