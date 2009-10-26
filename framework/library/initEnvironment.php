<?

//test change for git

$ds=DIRECTORY_SEPARATOR;
require_once("..{$ds}framework{$ds}configs{$ds}appConfig.php");

function getTools(){
	$ds=DIRECTORY_SEPARATOR;
	require_once("tools{$ds}dump.php");
}

if (!isset($_SERVER['SYSTEM_TYPE'])){
	define('SYSTEM_TYPE', 'production');
}
else{
	define('SYSTEM_TYPE', $_SERVER['SYSTEM_TYPE']); //usually this is 'development' from httpd.conf
}

switch (SYSTEM_TYPE){
	case 'development':
		getTools();
		break;
	case 'production':
	default:
		break;
}

function dispatchFirst($pathString){

	$ds=DS;
	$rootDirPath=ROOT;
	
	$pathArray=explode($ds, $pathString);
	
	$controller=array_shift($pathArray);
	$controller="{$controller}Controller";
	$action=array_shift($pathArray);
	$parameterArray=$pathArray; //whatever is left is parameters
	
	echo "controller=$controller<BR>";
	echo "action=$action<BR>";
	echo "parameterArray=$parameterArray<BR><BR>r";

	$controllerObj=new $controller;
	if (empty($action)){$action='index';}
	
	if (method_exists($controllerObj, $action)){
		call_user_func_array(array($controllerObj,$action),$parameterArray);
	}
	else{
	exit("make it so that this goes to the front page or something");
	}

}


function __autoload($className){

	$ds=DIRECTORY_SEPARATOR;
	$rootDirPath=ROOT;
	
	$fileName=$className;
	$fileName=preg_replace('/controller$/i', '_controller', $fileName);
	$fileName=preg_replace('/model$/i', '_model', $fileName);
	$fileName=preg_replace('/view$/i', '_view', $fileName);
	
	$fileName=strtolower($fileName);
	$classDirName='mvc';
	
	//maybe it's a controller
	$subDirName='controllers';
	$trialFilePath="$rootDirPath$ds$classDirName$ds$subDirName$ds$fileName.php";

	if (file_exists($trialFilePath)){
		require_once($trialFilePath);
		return;
	}
	
	//maybe it's a modell
	$subDirName='models';
	$trialFilePath="$rootDirPath$ds$classDirName$ds$subDirName$ds$fileName.php";
	if (file_exists($trialFilePath)){
		require_once($trialFilePath);
		return;
	}
	
	//maybe its a base class or something
	$subDirName='';
	$trialFilePath="$rootDirPath$ds$classDirName$ds$subDirName$ds$fileName.php";
	if (file_exists($trialFilePath)){
		require_once($trialFilePath);
		return;
	}
	
	exit("FATAL ERROR: Class $className cannot be found<BR>");
	
}