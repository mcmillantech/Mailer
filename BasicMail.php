<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	BasicMail.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
// Compose and send an email
// --------------------------------------------------------------
/*
*/
	require_once "view.php";
	include "LogCheck.php";
	
	$dta = array();
	$dta['attachment'] = '';

	$ckpath = getenv('CKPATH');
	if ($ckpath=='')
		$ckpath = "../ckeditor/ckeditor.js";
	$dta['ckpath'] = $ckpath;

	showForm();
	showView("BasicMail.html", $dta);


// ---------------------------------------
//	Build the form
// ---------------------------------------
function showForm()
{
	global $dbConnection, $dta;

	$action='BasicMail2.php';
	if (array_key_exists('resend', $_GET))
	{
		$content = resendContent();
		$action .= "?resend=" . $_GET['resend'];;
	}
	else
		$content = "Dear {forename}";
	$dta['action'] = $action;
	$dta['content'] = $content;

	showRecipientList();
}

// -------------------------------------------
//	Build and show recipient lists drop down 
//
// -------------------------------------------
function showRecipientList()
{
	global $dbConnection, $dta;

	$items = array();

	$sql = "SELECT id, name FROM maillists";
	$result = mysqli_query($dbConnection, $sql)
		or die ("Error : $sql");
	while ($line = mysqli_fetch_array($result)) {
		array_push($items, $line);
	}
	$dta['items'] = $items;
}

// -----------------------------------------------
//	Fetch the existing content of the mail
//
//	Called when Resend is clicked in ViewLog.php
// -----------------------------------------------
function resendContent()
{
	global $dbConnection;
	
	$msg = $_GET['resend'];
	$sql = "SELECT htmltext FROM mailmessages WHERE id=$msg";
	$result = mysqli_query($dbConnection, $sql)
		or die (mysqli_error($dbConnection));
	$record = mysqli_fetch_array($result);
	$content = $record['htmltext'];
	mysqli_free_result($result);

	return $content;
}
?>
