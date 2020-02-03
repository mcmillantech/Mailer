<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	AjaxEditor.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Ajax server 
//	Receives data via POST method
//		mode	save
//				tpsave
//		id		message id
// --------------------------------------------------------------
/*
function doSave()
*/

	require_once "LogCheck.php";

	date_default_timezone_set("Europe/London");	// Today's date
	$dtSQL = date('Y-m-d G:i:s');
	// This is for mode=save
	$mode = $_POST['mode'];
	if ($mode == 'tpsave')
		saveTemplate();
	else
		doSave();
//
// -----------------------------------------
//	Save the message
//
//	Called from mode=save
// -----------------------------------------
function doSave()
{
	global $dbConnection;
	$id = $_POST['id'];

	date_default_timezone_set("Europe/London");	// Today's date
	$dtSQL = date('Y-m-d G:i:s');

	$name = $_POST['name'];
	$subject = $_POST['subject'];
	$sender = $_POST['sender'];
	$html = addslashes($_POST['dta']);
	
	$sql = "UPDATE mailmessages SET htmltext=\"$html\", modified='$dtSQL', "
		. "name='$name', subject='$subject', sender='$sender' "
		. "WHERE id=$id";
//	echo $sql;
	mysqli_query($dbConnection, $sql)
		or die("An error occured saving the message: " . mysqli_error($dbConnection));
	echo "Message updated";
}
	
// -----------------------------------------
//	Save a template
//
//	Called from mode=tpsave
// -----------------------------------------
function saveTemplate()
{
	global $dbConnection;
	$id = $_POST['id'];

	$html = addslashes($_POST['dta']);
	$sql = "UPDATE templates SET htmltext=\"$html\" WHERE id=$id";
//	echo $sql;
	mysqli_query($dbConnection, $sql)
		or die("An error occured saving the message: $sql");
	echo "Message updated";
}
	
?>
