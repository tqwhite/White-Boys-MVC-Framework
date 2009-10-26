<?php
class StartController extends BaseController{

public function index(){
	echo "Calculating info for front startController::index<BR>";
	
	$page=new BaseView('start/index'); //todo: obviously, don't hard code
	$page->SetViewScopedValue('title', "A Title Sent by: startController::index");
	$page->SetViewScopedValue('message', "And more words to live by from the correct source of content.");
	$page->render();
	
	echo "<div style=color:green;>Got to the end of the process.</div>";
	return;
}

}