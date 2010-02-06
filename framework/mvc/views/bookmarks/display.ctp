<?php


//do the calculations ===============================================================

$linkString='';

$columnCount=3;
$columnCounter=0;
$rowCount=3;
$rowCounter=0;
$inx=0;

for ($i=0; $i<$rowCount; $i++){
	$linkString.="<tr>";
	for ($j=0; $j<$columnCount; $j++){
		$data=$urlList[$inx];
		$inx++;
		if (!empty($data)){
			$linkString.="<td class=bookmarkItem><a href={$data->getUrl()}>{$data->getAnchorText()}</a></td>";
		}
		else{
			$linkString.="<td class=bookmarkItem>&nbsp;</td>";
		}
	}
	$linkString.="</tr>";
	
}

	$entryForm="
		<div class=formContainer>
			<div class=formDiv>
				</div>
			<div class=responseDiv>
				</div>
			<div style=clear:both;></div>
		</div>
	";

	$linkString="<table border=1 class=bookmarkList>$linkString</table>";
	
	$outString="
		$entryForm
		$linkString
	";
	
$this->addToBody($outString);


//organize the page stuff ===============================================================

$this->addJsLink("http://tqwhite.org.local/js/jquery.js");
$this->addJsLink("http://tqwhite.org.local/js/jquery.form.js");



$this->addJsInclude("../framework/mvc/views/bookmarks/jsIncludes/bookmarkLib.js");
$this->addCssInclude("../framework/mvc/views/bookmarks/cssIncludes/bookmarkLib.css");




//Insert code typed into this file into page html
$this->addJsCode("bookmarkEntryFormPosition='prepend'");
$this->addJsCode("bookmarkEntryFormDestSelector='.formDiv'");

$this->addJsCode("bookmarkEntryFormRespondDestSelector='.responseDiv'");

$this->addCssCode("
			.bookmarkList{
				width:100%;
				color:#8888ff;
				border:1pt solid red;
				text-align:center;
				vertical-align:middle;
			}
			.bookmarkItem{
				height:70px;
			}
			.localBody{
				font-family:sans-serif;
				font-size:18pt;
				color:gray;
				margin:20px;
			}
			.formContainer{
				background:#ddbbdd;
			}
			.formDiv{
				background:#ddddbb;
				float:left;
			}
			.responseDiv{
				background:#bbdddd;
				float:right;
			}
			");



$this->addSpecial('<body>', "<body class=localBody>");
$this->addPageTitle($title);
echo $this->_assembleOutput($this->htmlPageWrapper);
