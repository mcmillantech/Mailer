<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	RcpPost.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//	Post a recipient
// --------------------------------------------------------------

	require_once "LogCheck.php";

	if (array_key_exists('action', $_GET))
		doDelete();
	$mode = $_POST['mode'];
	if ($mode == 'edit')
		doEdit();
	// This is for mode=save
	else
		doAdd();

// -----------------------------------------
function doEdit()
{
	global $dbConnection;

	$table = $_POST['table'];
	$list = $_POST['list'];
	$id = $_POST['id'];

	$sql = "UPDATE $table SET "
		. "email='" . addslashes($_POST['email']) . "',"
		. "forename='" . addslashes($_POST['forename']) . "',"
		. "surname='" . addslashes($_POST['surname']) . "',"
		. "business='" . addslashes($_POST['business']) . "'"
		. " WHERE id=$id";

	mysqli_query($dbConnection, $sql) 
		or die("Update error $sql");
	header("Location: MlView.php?lst=$list");
		exit;
}

// -----------------------------------------
function doAdd()
{
	global $dbConnection;

	$table = $_POST['table'];
	$list = $_POST['list'];
	$sql = "INSERT INTO $table ("
		. "email, forename, surname, business) "
		. "VALUES ('". addslashes($_POST['email']) . "',"
		. "'" . addslashes($_POST['forename']) . "',"
		. "'" . addslashes($_POST['surname']) . "',"
		. "'" . addslashes($_POST['business']) . "')";
//	echo $sql;
	mysqli_query($dbConnection, $sql) 
		or die("Update error $sql");
	header("Location: MlView.php?lst=$list");
		exit;
}

// -----------------------------------------
function doDelete()
{
	global $dbConnection;

	$table = $_GET['table'];
	$list = $_GET['list'];
	$id = $_GET['id'];
	$sql = "DELETE FROM $table WHERE id=$id";
	mysqli_query($dbConnection, $sql) 
		or die("Update error $sql");
	header("Location: MlView.php?lst=$list");
	exit;
}
?>
