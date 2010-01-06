<?phpnamespace configs;use Doctrine\Common\ClassLoader,    Doctrine\ORM\Configuration,    Doctrine\ORM\EntityManager,    Doctrine\Common\Cache\ApcCache;$ds=DIRECTORY_SEPARATOR;//error_reporting(E_ALL | E_STRICT);require_once("app_config.php");AppConfig::initialize(); //define constants, etcfunction getTools(){	$ds=DIRECTORY_SEPARATOR;	$prefix=LIBROOT."tools{$ds}";	require_once("{$prefix}dump.php");	require_once("{$prefix}contains.php");	require_once("{$prefix}processTemplateArray.php");	require_once("{$prefix}camelCaseToUnderscore.php");}function dispatchFirst($pathString){	$ds=DS;		$pathArray=explode($ds, $pathString);		$controller=array_shift($pathArray);		$provisionalParameterArray=$pathArray;	$action=array_shift($pathArray);	$parameterArray=$pathArray; //whatever is left is parameters	$className="\\mvc\\controllers\\$controller";		$controllerObj=new $className;		if (empty($action)){$action='index';}		if (method_exists($controllerObj, $action)){		call_user_func_array(array($controllerObj,$action),$parameterArray);	}	else{		if (method_exists($controllerObj, 'getUnknownActionHandlerName')){			$action=$controllerObj->getUnknownActionHandlerName();			call_user_func_array(array($controllerObj,$action),$provisionalParameterArray);		}		else{			exit("make it so that unknown action goes to the front page or something");		}	}}class qLoad{	function qAutoload($className){		try{			$ds=DIRECTORY_SEPARATOR;						$fileName=str_replace('\\', '/', $className); //convert namespace to filepath			$fileName=camelCaseToUnderscore($fileName);			$fileName=FRAMEWORKROOT."$fileName.php";						if (!file_exists($fileName)){				throw new \Exception("fileErr");			}						require_once($fileName);			return true;		}				catch (\Exception $e){			if ($e->getMessage()=='fileErr'){				$message="					FATAL ERROR: Class $className cannot be found at<BR>					function: $fileName (".__FUNCTION__.")					";				//exit($message);				return false;			}			else{				$message="					FATAL ERROR: Class $className had an unknown problem at<BR>					function: $fileName (".__FUNCTION__.")					";				//exit($message);				return false;						}		}			}}//end of class/********************************* Complete my setup********************************/$qLoadObj=new qLoad;spl_autoload_register(array($qLoadObj, 'qAutoload'));getTools();if (SYSTEM_TYPE=='development'){require_once("host_ftp_config.php");}/********************************* Doctrine setup********************************/require DOCTRINEROOT.'lib/Doctrine/Common/ClassLoader.php';class InitDoctrine{public static function createEntityManager(){	$doctrineClassLoader = new ClassLoader('Doctrine', realpath(DOCTRINEROOT.'lib'));		$doctrineClassLoader->register();	$entitiesClassLoader = new ClassLoader('Entities', MVCROOT . '/models');		$entitiesClassLoader->register();	$proxiesClassLoader = new ClassLoader('Proxies', MVCROOT . '/models');		$proxiesClassLoader->register();		// Set up caches		$config = new Configuration;		//$cache = new ApcCache;		$config->setMetadataCacheImpl($cache);		$config->setQueryCacheImpl($cache);		// Proxy configuration		$config->setProxyDir(MVCROOT . '/models/Proxies');		$config->setProxyNamespace('Proxies');		// Database connection information		$connectionOptions = array(			'host' => \configs\MysqlConfig::getValue('server'),			'user' => \configs\MysqlConfig::getValue('username'),			'password' => \configs\MysqlConfig::getValue('password'),			'driver' => \configs\MysqlConfig::getValue('driver'),			'dbname' => 'ormDB'			);			// Create EntityManager		$em = EntityManager::create($connectionOptions, $config);		return $em;	}} //end of InitDoctrine