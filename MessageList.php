<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	MessageList.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  List messages that have been created 
//	Called from main menu
// --------------------------------------------------------------
/*
function showMessageList()
function formatSQLDate($dt)
*/

	require_once "view.php";
	include "LogCheck.php";

	$dta = array();
	if (!array_key_exists('type', $_GET))
		$type = "basic";
	else
		$type = $_GET['type'];
	$dta['type'] = $type;

	$dta = showMessageList($type, $dta);
	showView("MessageList.html", $dta);

// -------------------------------------
//	Show the list of messages
//
// -------------------------------------
function showMessageList($type, $dta)
{
	global $dbConnection;

	if ($type == "newsletter")
		$where = "is not null";
	else
		$where = "is null";

	$sql = "SELECT * FROM mailmessages WHERE template $where ORDER BY id DESC";
	$result = mysqli_query($dbConnection, $sql)
		or die ("Error showing message list: $sql");
	
	$list = array();		// Create a sub array for a list
	while ($record = mysqli_fetch_array($result)) {
		$item = array();
		$item['id'] = $record['id'];
		$item['name'] = $record['name'];
		$subject = substr($record['subject'], 0, 32);
		$item['subject'] = $subject;
		$item['sender'] = $record['sender'];
		$item['numbersent'] = $record['numbersent'];
		$lastsend = formatSQLDate($record['lastsend']);
		$item['lastsend'] = $lastsend;
		$dt = formatSQLDate($record['created']);
		$item['created'] = $dt;
		$dt = formatSQLDate($record['modified']);
		if ($dt != '')
			$dt = "Modified $dt";
		$item['modified'] = $dt;
		array_push($list, $item);
	}
        $dta["list"] = $list;

	mysqli_free_result($result);
	return $dta;
}

function formatSQLDate($dt)
{
	if ($dt != '')
		$dt = substr($dt, 8, 2) . '/' . substr($dt, 5, 2) . '/' . substr($dt, 0, 4);
	return $dt;
}

?>
