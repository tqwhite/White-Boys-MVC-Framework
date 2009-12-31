<?php


$outString="
<div style=font-family:sans-serif;margin-bottom:15px;>
<div style='margin:15px;border:1pt solid gray;background:#BBBB66;color:#994444;font-family:sans-serif;height:100px;width:400px;padding-top:20px;text-align:center;'>
<div style=font-weight:bold;margin-bottom:5px;>
This is page one.
</div>
In this case, we have static text. The controller serves only as a dispatcher. It provides no values.
</div>
<div style=font-size:80%;margin-left:15px;>Brought to you by<BR>".str_replace('tq.org', 'tq.org<BR>', __FILE__)."</div>

<div style=margin-top:10px;>
<a rel='license' href='http://creativecommons.org/licenses/by-sa/3.0/us/'>
<img alt='Creative Commons License' style='border-width:0' src='http://i.creativecommons.org/l/by-sa/3.0/us/88x31.png' /></a><br />
<span xmlns:dc='http://purl.org/dc/elements/1.1/' property='dc:title'>White Boys Mvc Framework</span> by <a xmlns:cc='http://creativecommons.org/ns#' href='http://tqwhite.org/OpenSource' property='cc:attributionName' rel='cc:attributionURL'>White Boys</a> is licensed under a <a rel='license' href='http://creativecommons.org/licenses/by-sa/3.0/us/'>Creative Commons Attribution-Share Alike 3.0 United States License</a>.
</div>
<div style=margin-top:10px;>
Source Code at: <a href=http://github.com/tqwhite/White-Boys-MVC-Framework>GitHub</a>
</div>

</div>
";


echo $outString;

