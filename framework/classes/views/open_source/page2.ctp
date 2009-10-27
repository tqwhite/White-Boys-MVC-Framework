<?php


$outString="
<div style=font-family:sans-serif;margin-bottom:15px;>
<div style='margin:15px;border:1pt solid gray;background:#bb66bb;color:#aaffaa;font-family:sans-serif;width:400px;padding-top:20px;text-align:center;'>
<div style=font-weight:bold;margin-bottom:5px;>
This is page TWO.
</div>
This example makes use of the new facility that works correctly on URLs of the form<br/>
http://domain.com/controllerName/parameter<br/>
Of course, it also works with<br/>

http://domain.com/controllerName/parameter/action/parameter, too<br/>
</div>
<div style=font-size:80%;margin-left:15px;>Brought to you by<BR>".str_replace('tq.org', 'tq.org<BR>', __FILE__)."</div>
</div>
";


echo $outString;

