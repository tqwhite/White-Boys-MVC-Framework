<?php

echo "<div style=height:50px;><a href=#phpInfo>Jump Down to phpinfo()</A></div>";
dump($_SERVER, '_SERVER');
echo "<div style=height:50px;><a name=phpInfo></div>";
phpinfo();