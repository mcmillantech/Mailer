<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
// -------------------------------------------------------------
//  Project	Emailer
//	File	MailEdit.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Editor for messages 
//	Container div holds instructpane and maineditpanel
//	The latter shows the html being edited
//	Also editview, a hidden panel for CKEditor
// --------------------------------------------------------------
/*
function openMessage()
function deleteMessage()
function newMessage()
function ckeditPanel()
function showInstructPanel()
*/
?>
<?php
	require_once "LogCheck.php";
	require_once "view.php";
	
	$dta = array();
	if (!array_key_exists('type', $_GET))
		$type = "news";
	else
		$type = $_GET['type'];
	if ($type == 'basic')
		$viewPage = "EmailEdit.html";
	else
		$viewPage = "NewsletterEdit.html";

	date_default_timezone_set("Europe/London");	// Today's date
	$dtSQL = date('Y-m-d G:i:s');

	$mode = '';
	$htmlText = '';
	$id = 0;
	if (array_key_exists('id', $_GET))
		$id = $_GET['id'];
	if (array_key_exists('action', $_GET))
		$mode = $_GET['action'];

	$record = array();
	$dta['attachment'] = '';

	switch ($mode)
	{
	case 'new':
		$record = newMessage($type);
//		$id = $record['id'];
		break;
	case 'edit':
		$record = openMessage();
		$dta['attachment'] = $record['attachment'];
		break;
	case 'delete':
		echo deleteMessage($type);
		return;
	}

	$htmltext = $record['htmltext'];
	$dta['msgname'] = $record['name'];
	$dta['id'] = $record['id'];
	$dta['htmltext'] = $htmltext;
	
	showHeaders($record);
	$dta['ckreplace'] = ckeditPanel();
//	$dta['action'] = "MessagePost.php";
	$dta['content'] = "";
//	print_r($dta);

	showView($viewPage, $dta);
//

// -------------------------------------
//	Open an existing message
//
//	Message id is passed as a parameter
//
//	Creates global $msgname
// -------------------------------------
function openMessage()
{
	global $dbConnection;
	
	$sql = "SELECT * FROM mailmessages WHERE id=" . $_GET['id'];
	$result = mysqli_query($dbConnection, $sql)
		or die($sql);
	$record = mysqli_fetch_array($result);
	mysqli_free_result($result);

	return $record;
}

// -------------------------------------
//	Delete a message
//
// -------------------------------------
function deleteMessage($type)
{
	global $dbConnection;
	
	$sql = "DELETE FROM mailmessages WHERE id=" . $_GET['id'];
	$result = mysqli_query($dbConnection, $sql)
		or die($sql);
	echo "Message deleted<br><br>";
	echo "<p><button type='button' onClick='document.location.assign(\"Home.php\")'>Home</button>";
	echo "&nbsp;&nbsp;<p><button type='button' onClick='document.location.assign";
	echo "(\"MessageList.php?type=$type\")'>Back to List</button></p>";
}

// -------------------------------------
//  Insert a new blank message into the
//	database
// -------------------------------------
function newMessage($type)
{
	global $dbConnection, $dtSQL, $msgname;

	$record = array();
	$record['name'] = $_GET['msgname'];
	$record['subject'] = $_GET['subject'];
	$record['sender'] =  $_GET['from'];
	$msgname = addslashes($_GET['msgname']);
	$subject = addslashes($_GET['subject']);
	$sender = addslashes($_GET['from']);
	if ($type == 'newsletter')
		$template = $_GET['tp'];
	else
		$template = 'null';
	
	$sql = "SELECT * FROM templates WHERE id=$template";
	$result = mysqli_query($dbConnection, $sql)
		or die ("SQL error " . mysqli_error($dbConnection));
	$tpRecord = mysqli_fetch_array($result);
	$htmltext = $tpRecord['htmlText'];
	$record['htmltext'] = $htmltext;
	
	mysqli_free_result($result);

	$sql = "INSERT INTO mailmessages (name, subject, sender, created, htmltext, template) "
		. "VALUES ('$msgname', '$subject', '$sender', '$dtSQL', '" 
		. addslashes($htmltext) . "', $template)";
	mysqli_query($dbConnection, $sql)
		or die ("SQL error " . mysqli_error($dbConnection) . $sql);
	$record['id'] = mysqli_insert_id($dbConnection);		// Get the id of the new message
//	echo "<br>New id $msgId";

	return $record;
}

// -------------------------------------
// -------------------------------------
function showHeaders($record)
{
	global $dta;

	$dta['name'] = $record['name'];
	$dta['subject'] = $record['subject'];
	$dta['sender'] = $record['sender'];
}

// -----------------------------------------
//	Fetch CSS formats for the implementaion
//  and generate html to set them into 
//	CKEditor
//
//	Returns html for CKEditor
// ----------------------------------------
function ckeditPanel()
{
	$formats = "";
	$hfile = fopen('formats.txt', 'r');

	if ($hfile)
	{
		$formats = fread($hfile, filesize("formats.txt"));
		fclose($hfile);
	}
                        // Tbis html connects a <textarea> called ed to CKEditor 
                        // In doing so, moves the formats into the edit
                        // $formats is a parameter for the view, as is $ckreplace
	$ckreplace = "<script>\n"
		. " CKEDITOR.replace( 'ed', { $formats } ) \n"
		. "</script>\n";

	return $ckreplace;
}

?>
