<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	MailCreate.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Create a new newaletter 

// --------------------------------------------------------------
/*
*/
	require_once "view.php";
	include "LogCheck.php";

	$type = $_GET['type'];
	
	$dta = showSelector();
	switch ($type)
	{
	case 'basic':
		showView("MailCreateB.html", $dta);
		break;
	case 'newsletter':
		showView("MailCreateM.html", $dta);
		break;
	}


function showSelector()
{
	global $dbConnection;
	$dta = array();

	$lines = array();
	$sql = "SELECT * FROM templates";
	$result = mysqli_query($dbConnection, $sql);
	while ($record = mysqli_fetch_array($result)) {
		array_push($lines, $record);
	}
	
	$dta['lines'] = $lines;
	return $dta;
}

// ----------------------------------------------
//	Show the template record
//
// ----------------------------------------------
function showRecord($record)
{
	$id = $record['id'];
	$name = $record['name'];
}

?>

