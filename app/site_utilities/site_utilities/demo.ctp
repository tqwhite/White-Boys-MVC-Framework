<?php
//
//Code Sample ===========================================================
//
/*
*The goal is to provide automated page assembly for correct html but
*to also allow complete flexibility to just type simple or special pages.
*
*This file is run by a rendering class (BaseView). It renders whatever
*comes out of this file, whether simply html or the result of php calculation.
*The class provides the ability to receive page elements and assemble them. For
*certain elements (css, js, etc), the class wraps appropriate tags around them
*before inserting. (Refer to BaseView for details.)
*
*This mechanism also allows the editing of javascript (and css) in its own file and
*makes for better code coloring, reusability, etc. This is usually done by putting
*linkable files in the server directory. That makes all javascript visible to all pages
*and doesn't seem right for encapsulation. 
*
*This tool allows the specification of normal
*javascript files in the local View directory which are then 'included' into the 
*html output. That is, any javascript files specified using addJsInclude() (and
*similarly css with addCssInclude()) are copied into the page source. Files can also be
*added as conventional links (addJsLink(), addCssLink()).
*/



//
//Display View-scoped Variables ===========================================================
//

$bodyText='';
foreach ($messageArray as $data){
	$controllerMessages.="<div>$data</div>";
}

$this->addToBody($controllerMessages); //tell rendering engine about it
$this->addToBody("<div  class=test>Static message in the template. The css is manually entered there, too.</div>");
$this->addToBody("<div style=font-size:50%;margin-top:20px>
Note that you should have seen three javascript alerts. One produced by each of the
three mechanisms by which javascript can be added to a page.
</div>
");

//
//Revise <body> tag  ===========================================================
//

$this->addSpecial('<body>', "<body style='font-family:sans-serif;font-size:18pt;color:gray;margin:20px;'>");


//
//Insert page title ===========================================================
//

$this->addPageTitle($title);

//
//Insert javascript and css into <head> section =======================================
//NOTE: include and link paths are specified relative to public_html/index.php
//

//URL for normal <link> and <script> tags
$this->addJsLink("http://tqwhite.org.local/js/test3.js"); //absolute path can be on any server
$this->addCssLink("../css/test.css"); //relative to public_html/index/php

//Insert code stored in external file into page html
$this->addJsInclude("../app/site_utilities/site_utilities/jsIncludes/test.js"); //relative to public_html/index/php
$this->addCssInclude("../app/site_utilities/site_utilities/cssIncludes/test.css"); //relative to public_html/index/php

//Insert code typed into this file into page html
$this->addJsCode("alert('hello from inline javascript');");
$this->addCssCode("
			.test{
				color:#8888ff;
			}
			");



//
//Assemble components into string and wrap with <html> tag, etc ====================================
//

echo $this->_assembleOutput($this->htmlPageWrapper);

//
//Express more html in the conventional php interleaved code way ====================================
//note: this text is inserted outside of the <html> tags generated by _assembleOutput above
//

?>
<div  style=margin-bottom:50px;margin-top:50px;>
<div class='fancyClass'>This manually entered html but the css is inserted dynamically into this file</div>
<div class='linkedClass'>this is also manual but the css is linked (and it's a fancy font!!)</div>
</div>
<?php


//
//PHP code that generates the demo =============================================================
//
$thisPage=file_get_contents(__FILE__);
echo '<div style=font-size:12pt;>'.nl2br(htmlentities($thisPage)).'</div>';
