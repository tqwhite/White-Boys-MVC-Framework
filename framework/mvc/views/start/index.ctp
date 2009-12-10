<?php

$linkString='';
foreach ($urlList as $data){
$linkString.="<a href={$data['url']}>{$data['text']}</a><p/>";
}

/*
<link rel="stylesheet" href="style-original.css" />
<link rel="alternate"
       type="application/atom+xml"
       title="My Weblog feed"
       href="/feed/" />
*/

$outString="

<!doctype html>
<html lang='en'>
<head>
<meta charset='utf-8'>

<div style=font-family:sans-serif;margin-bottom:15px;>
<div style='margin:15px;border:1pt solid gray;background:#BBBB66;color:883333;font-family:sans-serif;width:400px;padding-top:20px;text-align:center;'>
<div style=font-weight:bold;margin-bottom:5px;>
$title</div>
<div style=font-size:10pt;>
$linkString
</div>
</div>
<div style=font-size:80%;margin-left:15px;>Brought to you by<BR>".str_replace('tq.org', 'tq.org<BR>', __FILE__)."</div>
</div>
";


echo $outString;

