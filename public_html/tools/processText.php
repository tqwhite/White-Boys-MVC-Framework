<?php
include('../../framework/configs/init_environment.php');

$template='<div><!date!></div>';
	
$titleTemplate='
<div class="container">
<h3><!text!></h3>
<div style=margin-left:20px;>asfsdfsaf</div></div>';

$titles[]='For Student Plans Users Only';
$values[]='March 9:  9:00 a.m.';
$values[]='March 10: 3:00 p.m.';
$values[]='March 11:  1:00 p.m.';

$titles[]='For Minnesota Transition';
$values[]='March 24:  1:00 p.m.';
$values[]='March 25:  3:00 p.m.';
$values[]='March 29:  10:00 a.m.';

$titles[]='For all Transition';
$values[]='February 19: 1:00 p.m. CST';
$values[]='March 2: 3:00 p.m. CST';
$values[]='March 9: 1:00 p.m. CST';

$outString='';

foreach ($values as $data){

$data=preg_replace('/^(.*?:)/', '<strong>\1</strong>', $data);


$outString.=htmlentities(str_replace('<!date!>', $data, $template)).'<BR>';
}



foreach ($titles as $data){
$outString.=htmlentities(str_replace('<!text!>', $data, $titleTemplate)).'<BR>';
}

echo $outString;