<?php


$outString="
<div style=font-family:sans-serif;margin-bottom:15px;>
<div style='margin:15px;border:1pt solid gray;background:#BBBB66;color:red;font-family:sans-serif;height:100px;width:400px;padding-top:20px;text-align:center;'>
<div style=font-weight:bold;margin-bottom:5px;>
$title</div>
$message<P>
</div>
<div style=font-size:80%;margin-left:15px;>Brought to you by<BR>".str_replace('tq.org', 'tq.org<BR>', __FILE__)."</div>
</div>
";


echo $outString;

