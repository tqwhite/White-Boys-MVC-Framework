<?php

include('../../framework/library/services/ssh_tmp.php');
include('../../framework/library/tools/dump.php');

echo "

<style stype='text/css'>

@font-face{
font-family:tqtest;
src:url(../elements/fonts/FatPixels.ttf)
}

</style>
<div style='font-family:tqtest;font-size:24pt;padding:10px;border:1pt solid gray;margin:15px;'>
This is a test of the @font-face css tag.

</div>


";
/*
$s = new SshTmp('38.108.46.145', '22123');
$s->loginWithPassword('org.tqwhite', 'tq3141');
echo $s->execCommandBlock('pwd');
*/
//ssh2_connect ( string $host [, int $port = 22 [, array $methods [, array $callbacks ]]] )

$sshHandle=ssh2_connect('38.108.46.145', '22123');
ssh2_auth_password($sshHandle, 'org.tqwhite', 'tq3141');


$stream=ssh2_exec($sshHandle, 'pwd');
stream_set_blocking($stream, true);
$tmp=stream_get_contents($stream);
$tmp=str_replace("\n", '<br>', $tmp);
echo($tmp."<hr>");


$stream=ssh2_exec($sshHandle, 'ls');
stream_set_blocking($stream, true);
$tmp=stream_get_contents($stream);
$tmp=str_replace("\n", '<br>', $tmp);
echo($tmp."<hr>");


$ftpHandle=ssh2_sftp($sshHandle);
$tmp=ssh2_sftp_realpath($ftpHandle, '.');
dump($tmp);

$tmp=ssh2_methods_negotiated($sshHandle);
dump($tmp);

$tmp=ssh2_fingerprint($sshHandle);
dump($tmp);

$stream=ssh2_exec($sshHandle, '/usr/bin/php -1');
$tmp=stream_get_contents($stream);
//$tmp=str_replace("\n", '<br>', $tmp);
dump($tmp);


exit();


//<iframe width="600px" height="500px" src="http://m.supersaas.com/schedule/demo/Therapist"></iframe>

?>