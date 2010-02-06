<?php
/*
* Prepare controller data =======================================================================
*/

// n/a

/*
* Specify head elements =======================================================================
*/

$this->addPageTitle("Add Boomkark");

$this->addJsLink("http://tqwhite.org.local/js/jquery.js");
$this->addJsLink("http://tqwhite.org.local/js/jquery.form.js");

$this->addJsInclude("../framework/mvc/views/bookmarks/jsIncludes/bookmarkLib.js");
$this->addCssInclude("../framework/mvc/views/bookmarks/cssIncludes/bookmarkLib.css");



/*
* Build the displayable stuff ===============================================================
*/

//$this->addToBody("<div id='main'></div>");

/*
* Send the output ===============================================================
*/

echo $this->_assembleOutput($this->htmlPageWrapper);
