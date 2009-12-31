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

require '../../lib/Doctrine/Common/ClassLoader.php';

// Set up class loading. You could use different autoloaders, provided by your favorite framework,
// if you want to.
$doctrineClassLoader = new ClassLoader('Doctrine', realpath(__DIR__ . '/../../lib'));
echo realpath(__DIR__ . '/../../lib').'<BR>';
echo realpath(__DIR__).'<BR>';
$doctrineClassLoader->register();
$entitiesClassLoader = new ClassLoader('Entities', __DIR__);
$entitiesClassLoader->register();
$proxiesClassLoader = new ClassLoader('Proxies', __DIR__);
$proxiesClassLoader->register();

// Set up caches
$config = new Configuration;
$cache = new ApcCache;
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

// Proxy configuration
$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');

// Database connection information
$connectionOptions = array(
    'driver' => 'pdo_sqlite',
    'path' => 'database.sqlite'
);

$connectionOptions = array(
	'host' => 'localhost',
    'driver' => 'pdo_mysql',
    'dbname' => 'phpDB',
    'user' => 'root',
    'password' => 'dbPass'
    );
    
// Create EntityManager
$em = EntityManager::create($connectionOptions, $config);

echo "PUT YOUR TEST CODE BELOW<BR>";

$user = new \Entities\User;
$user->setName('GarfieldX');
$em->persist($user);
$em->flush();

echo "User saved!" . PHP_EOL;