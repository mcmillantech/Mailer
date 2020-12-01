<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	MessagePost.php
//
//	Author	John McMillan
//  McMillan Technology 
//
//  Send and/or save a message
//
//	Parameters	id of message
//				Source - editor or list
// --------------------------------------------------------------
/*
function saveMessage($attachStr)
function showList()
function showRecord($record)
function attachments()
*/
//ini_set("display_errors", "1");
//error_reporting(E_ALL);
	require_once "view.php";
	include "LogCheck.php";
	$source = $_GET['src'];
	$dta = array();

	if ($source == 'editor')
		doFromEditor();
	else if ($source == 'list')
		doFromList();

// ----------------------------------------------
//	Call from editor - either from Save or Send
//
//	Always save, anyway
// ----------------------------------------------
function doFromEditor()
{
	global $dta;

	$dta = getPostedData($dta);

	attachments();
	$attachStr = $_POST['mcAttach'];

	saveMessage($attachStr);

	if (array_key_exists('btnSend', $_POST)) {
								// Panel for recipient list preview
		showList();
		showView("MessagePost.html", $dta);
	}

	else {
		echo "Message saved <br><br>";
		buttons();
	}
}

// ----------------------------------------------
//	Show the buttons after save from editor
//
// ----------------------------------------------
function buttons()
{
//	$headers = getallheaders();	// Find the type of message from the referrer
//	$caller = $headers['Referer'];
	$caller = $_SERVER['HTTP_REFERER'];
    	$str = strstr($caller, "type=");
	$type = substr($str, 5, 5);

	if ($type != "email")
		$type = "newsletter";
	echo "<p><button type='button' onClick='document.location.assign";
	echo "(\"MessageList.php?type=$type\")'>Back to List</button>";
	echo "&nbsp;&nbsp;&nbsp;";
	echo "<button type='button' onClick='document.location.assign(\"Home.php\")'>Home</button>";
	echo "<p>";
}

// ----------------------------------------------
//	Call from the send option in the "Manage"
//	link in one of the message lists
//
// ----------------------------------------------
function doFromList()
{
	global $dta;

	$dta = getDataFromTable($dta);
	showList();
	showView("MessagePost.html", $dta);
	echo "<p><button type='button' onClick='document.location.assign(\"Home.php\")'>Home</button>";
}

// ----------------------------------------------
//	Source is editor. Set fields from post data
//
//	Updates array $dta
// ----------------------------------------------
function getPostedData($dta)
{
	$dta['head'] = $_POST['mcName'];
	$dta['subject'] = $_POST['mcSubject'];
	$dta['from'] = $_POST['mcFrom'];
	$dta['msgId'] = $_GET['id'];
	$dta['msgname'] = $_POST['mcName'];
	$dta['attachStr'] = $_POST['mcAttach'];
	
	return $dta;
}

// ----------------------------------------------
//	Fetch the message data from the database
//
// ----------------------------------------------
function getDataFromTable($dta)
{
	global $dbConnection;
	$id = $_GET['id'];

	$sql = "SELECT * FROM mailmessages WHERE id=$id";
	$result = mysqli_query($dbConnection, $sql);
	$record = mysqli_fetch_array($result);
	
	$dta['head'] = $record['name'];
	$dta['subject'] = $record['subject'];
	$dta['from'] = $record['sender'];
	$dta['msgId'] = $id;
	$dta['msgname'] = $record['name'];
	$dta['attachStr'] = $record['attachment'];

	mysqli_free_result($result);

	return $dta;
}

// ----------------------------------------------
// ----------------------------------------------
function saveMessage($attachStr)
{
	global $dbConnection;
	$id = $_GET['id'];

	date_default_timezone_set("Europe/London");	// Today's date
	$dtSQL = date('Y-m-d G:i:s');

	$name = $_POST['mcName'];
	$subject = $_POST['mcSubject'];
	$sender = $_POST['mcFrom'];
	$html = addslashes($_POST['html']);
	
	$sql = "UPDATE mailmessages SET htmltext=\"$html\", modified='$dtSQL', "
		. "name='$name', subject='$subject', sender='$sender', attachment='$attachStr' "
		. "WHERE id=$id";
//	echo $sql;
	mysqli_query($dbConnection, $sql)
		or die("An error occured saving the message: " . mysqli_error($dbConnection));
}

// ----------------------------------------------
//	Show the recipient lists (Preview)
//
// ----------------------------------------------
function showList()
{
	global $dbConnection, $dta;

	$rList = array();

	$sql = "SELECT * FROM maillists ORDER BY id DESC";
	$result = mysqli_query($dbConnection, $sql);
	while ($record = mysqli_fetch_array($result)) {
		$rItem = showRecord($record);
		array_push($rList, $rItem);
	}
	$dta['rList'] = $rList;
}

// ----------------------------------------------
//	Show the mail list record
//
// ----------------------------------------------
function showRecord($record)
{
	$rItem = array();

	$id = $record['id'];
	$rItem['id'] = $id;
	$rItem['spId'] = 'sr' . $id;

	$rItem['name'] = $record['name'];
	return $rItem;
}

// --------------------------------------------
// 	Process attachments
//
//	If attachments have been selected, upload
//	them and return a comma separated string
//	holding their nams.
//	Otherwise return an empty string.
// --------------------------------------------
function attachments()
{
    $attachStr = "";
//  print_r($_FILES);
    $num = count($_FILES["mcAttFile"]["name"]);
    if ($num == 0)			// Not sure if this has an effect
            return "";

    $files = $_FILES["mcAttFile"];
            // Check the 1st error code - only way to see if there's an upload
    if ($files['error'][0] == UPLOAD_ERR_NO_FILE) {
        echo "Nothing uploaded<br>";
        return "";
    }

    for ($i=0; $i<$num; $i++) {
        $fileName = $files["name"][$i];
        $tmpName =  $files["tmp_name"][$i];
        $upload = "Uploads/$fileName";
        if ($fileName == '')
                break;
        $reply = move_uploaded_file($tmpName, $upload);
        if ($reply == false)
                die ("MessagePost: Uploading attachment failed");
        $attachStr .= "$fileName,";
    }
echo "String $attachStr ";
    return $attachStr;
}

