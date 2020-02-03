// -------------------------------------------------------------
//  Project	Emailer
//	File	MailEdit.js
//
//	Javascript handlers
//
//	Author	John McMillan
//  McMillan Technology Ltd
// -------------------------------------------------------------
/*
function home()

function closeNewMailForm()
function editCell(which)
function meMouseOver(which)
function meMouseOut(which)
function closeEditor()
function editPost()
function traceHtml()

function doSave(which, id)
function lstOpen(rtable)
function mbvManage(obj, id)
function mbvSend(id)
function mbvNewMember(mode)
function mbvEdit(id)
function mbvPost(table)

function openAjax()
function ajax_response(hAjax)
*/
template = 0;
selList = 0;

function home()
{
	document.location.assign('Home.php');
}

function newMessage(type)
{
	document.location="MailCreate.php?type=" + type;
}

// ----------------------------------------
function openNewMailForm()
{
	var el = document.getElementById('messagecreate');
	el.style.visibility = 'visible';
/*				// Hide the 'Manage' drop down
	el = document.getElementById('mbvmanagedd');
	if (el === null)
		return;
	el.style.visibility = 'hidden'; */
}

// ----------------------------------------
//	Close form to create / edit a mail
//
//	Newsletters.php, MLView.php
// ----------------------------------------
function closeNewMailForm()
{
	var el = document.getElementById('messagecreate');
	el.style.visibility = 'hidden';
				// Hide the 'Manage' drop down
	el = document.getElementById('mbvmanagedd');
	if (el === null)
		return;
	el.style.visibility = 'hidden';
}

//	---------- BasicMail.php and SendNews.php ---------

// ---------------------------------------
//	Close list preview window
//
// ---------------------------------------
function closePreview()
{
	el = document.getElementById('listpreview');
	el.style.visibility = 'hidden';
}

// ---------------------------------------
//	Handler for "Preview" button
//
// ---------------------------------------
function previewList()
{
												//	Fetch the list from the drop down
	var el = document.getElementById('bmToList');
	var lst = el.value;
	previewList2(lst);
}

function previewList2(lst)
{
	var hAjax;
	hAjax = new XMLHttpRequest();
	
	hAjax.onreadystatechange=function()
	{
		if (ajax_response(hAjax))
		{
		    var httxt = hAjax.responseText;
			el = document.getElementById('listpreview');
												// Insert text into preview window
			el.innerHTML = httxt + 				// ... and show the close button
				"<br><button type='button' onClick='closePreview()'>Close</button>";
			el.style.visibility = 'visible';
		}
	}

	hAjax.open("GET","AjaxPreviewList.php?lst=" + lst, true);
	hAjax.send();
}

// -------------------------------------
//	Handler for Attach button
//
//	Show the file select element
// -------------------------------------
function bmAttach()
{
	var el = document.getElementById("showAtts");
	el.style.visibility = 'hidden';
	el = document.getElementById('mcAttFile');
	el.style.visibility = 'visible';
	changed = true;
}

// --------------------------------------------
//	Handler for file upload
//
//	Hide the Browse button and show the files
// -------------------------------------------
function fileUpload()
{
	changed = true;
	var el = document.getElementById("mcAttFile");
	var txt = "";

	if ('files' in el) {
    	if (el.files.length == 0) 
    		return;
	}
	else
		return;

	for (var i = 0; i < el.files.length; i++) {
		var file = el.files[i];
		if ('name' in file) 
			txt += file.name + ",";
	}

	el.style.visibility = 'hidden';
	el = document.getElementById("showAtts");
	el.innerHTML = txt;
	el.style.visibility = 'visible';

	el = document.getElementById("mcAttach");
	el.value = txt;
}

//	---------- End of BasicMail.php and SendNews.php ---------

//	---------- MailEdit.php, TeplateEdit.php ---------

// ----------------------------------------
//	Handler for click over a section
//
//	Pass the section text to CKEditor and
//	show the CKE panel
// ----------------------------------------
function editCell(which)
{
	section = 'MCTEdit' + which;
	var el = document.getElementById(section);
	var txt = el.innerHTML;
	el = document.getElementById('editTA');
	el.innerHTML = txt;
	CKEDITOR.instances.editTA.setData(txt);
	el = document.getElementById('editview');
	el.style.visibility = 'visible';
}

// ----------------------------------------
//	Hover functions over a section
//
// ----------------------------------------
function meMouseOver(which)
{
	sId = 'MCTEdit' + which;
	var el = document.getElementById(sId);
	el.style.backgroundColor = '#ffffb3';
}

function meMouseOut(which)
{
	sId = 'MCTEdit' + which;
	var el = document.getElementById(sId);
	el.style.backgroundColor = 'white';		// Need to restore to the section bg
}

function closeEditor()
{
	var el = document.getElementById('editview');
	el.style.visibility = 'hidden';
}

// ----------------------------------------
//	Handler for Done button in edit pane
//
//	Uses global $section
//	Takes text from edit pane, moves back
//	Sets changed flag
// ----------------------------------------
function editPost()
{
	changed = true;

	var txt = CKEDITOR.instances.editTA.getData();
	var el = document.getElementById(section);	// Was section
	el.innerHTML = txt;
							// Hide edit pane
	el = document.getElementById('editview');
	el.style.visibility = 'hidden';
}

function editSubmit()
{
	var el = document.getElementById('maineditpanel');
	var txt = el.innerHTML;
	el = document.getElementById('html');
	el.value = txt;
}

function traceHtml()
{
    var el = document.getElementById('maineditpanel');
    alert (el.innerHTML);
}

// --------------- end of MailEdit.php and Templates.php -------------

// ----------------------------------------
//	Handler for save template
//
//	Parameters	which: template or message
//				id of template or message
//
//	Fetches the html of the message and
//	AJAX call to AjaxEditor, with POST data
//	of mode, id and dta
// -----------------------------------------
function doSave(which, id)
{
	hAjax = openAjax();
	hAjax.onreadystatechange=function()	
	{
		if (ajax_response(hAjax))
		{
		    var httxt = hAjax.responseText;
		    alert(httxt);
		    changed = false;
	    }
    }
										// Fetch the SQL
    var el = document.getElementById('maineditpanel');
    var sql = el.innerHTML;
    
    if (which == 'template')			// Set up the POST data
		var postData = "mode=tpsave&id=" + id;	
	else
	{
		var postData = "mode=mssave&id=" + id;
		el = document.getElementById('bmAttFile');
		alert (el.files);
		el = document.getElementById('mcName');
		var txt = el.value;
		postData += "&name=" + txt;
		el = document.getElementById('mcSubject');
		txt = el.value;
		postData += "&subject=" + txt;
		el = document.getElementById('mcFrom');
		txt = el.value;
		postData += "&sender=" + txt;
	}
	postData += "&dta=" + encodeURIComponent(sql);

	hAjax.open("POST","AjaxEditor.php",true);
	hAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	hAjax.send(postData);

}

// ------------------------------------------------
//	Validate New List form on submit
//
//	May not be needed after change to new list
// ------------------------------------------------
function validateList()
{
	var el = document.getElementById('rtable');
	if (el.value == ''){
		alert ('List table must be present');
		return false;
	}
	
	var hAjax = openAjax();
/*	hAjax.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
		    var httxt = hAjax.responseText;
		    alert (httxt);
		    if (httxt == "OK")
		    	return true;
		    else {
		    	alert (httxt);
		    	return false;
	    	}
		}
	}
*/
	el = document.getElementById('name');
	name = el.value;
	msg = "AjaxNewList.php?name=" + name;
	hAjax.open("GET", msg, false);
	hAjax.send();

    var httxt = hAjax.responseText;
    if (httxt == "OK")
    	return true;
    else {
    	alert (httxt);
    	return false;
	}
}

// ----------------------------------------
//	Create AJAX handle
// ----------------------------------------
function openAjax()
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{					// code for all but old IE
	  xmlhttp=new XMLHttpRequest();
	}
	else
	{					// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}

function ajax_response(hAjax)
{
	return(hAjax.readyState==4 && hAjax.status==200);
}


