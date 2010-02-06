$(document).ready(function() { //START of ready =============================== *****

if (bookmarkEntryFormPosition=='null'){
	bookmarkEntryFormPosition='prepend';
	}
if (bookmarkEntryFormDestSelector=='null'){
	bookmarkEntryFormDestSelector='body';
	}
if (bookmarkEntryFormRespondDestSelector=='null'){
	bookmarkEntryFormRespondDestSelector='body';
	}

	switch(bookmarkEntryFormPosition){
		case 'prepend':
			$(bookmarkEntryFormDestSelector).prepend(formHtml);
			break;
		case 'append':
			$(bookmarkEntryFormDestSelector).append(formHtml);
			break;
		case 'replace':
			$(bookmarkEntryFormDestSelector).html(formHtml);
			break;
	}
				
alert('next step: send bookmark list code to save routine. currently its only saving to 3');			
		$("#enterBookmark").ajaxForm(ajaxSetupOptions);
		$('#bmSubmit').click(function(){$("#bmSubmit").submit();});


   
}); //end of onReady =============================== *****

// $("#myform").ajaxForm();

ajaxSetupOptions={
	  url: "postCatcher",
	  global: false,
	  type: "POST",
	  cache:false,
	  success: showResult,
	  dataType: 'json'
	};
	
function testAjax(){

	$.ajaxSetup(ajaxSetupOptions);
	
	$.ajax({ data: infoForPhp });

}

function showResult(a, b){
viewVar=a;
	$(bookmarkEntryFormRespondDestSelector).after('<div style=color:green;>'+a.received+'</div>');
	return true;
}

	
infoForPhp={
a: 100,
B: 'hello'
};
	
formHtml="\
<div id='main'>\
<form action='' method='post' id='enterBookmark'>\
<table cellpadding=0 cellspacing=0 border=0>\
<tr><td>URL:</td><td><input type=text name='url' id='url'></td></tr>\
<tr><td>Text:</td><td><input type=text name='anchorText' id='anchorText'></td></tr>\
<tr><td colspan=2><div id='bmSubmit'>Submit</div></td></tr>\
</table>\
</form>\
</div>\
";
	