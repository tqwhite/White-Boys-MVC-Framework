<?php

include('../../framework/configs/init_environment.php');
/*
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

//exit();


//<iframe width="600px" height="500px" src="http://m.supersaas.com/schedule/demo/Therapist"></iframe>
*/

ob_start();

$tmp=get_loaded_extensions();

 $ext= new ReflectionExtension('standard');
dump($ext->getINIEntries());
 dump($ext);

echo "<HR>";
Reflection::export(new ReflectionClass('\mvc\controllers\OpenSource'));


$outString=ob_get_contents();
ob_end_clean();
echo nl2br($outString);

?>