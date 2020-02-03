<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	AjaxNewList.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Ajax server to check the name of a new list is unique
//	Receives data via POST method
//		name	Name of new list
// --------------------------------------------------------------
/*
*/

	require_once "LogCheck.php";

	$name = $_GET['name'];
	$sql = "SELECT * FROM mailmessages WHERE name='$name'";
	$result = mysqli_query($dbConnection, $sql)
		or die("An error occurred: AjaxNewList " . mysqli_error($dbConnection));
	$rows = mysqli_num_rows($result);
	if ($rows == 0)
		echo "OK";
	else
		echo "Error: List $name already exists";
		
	mysqli_free_result($result);
?>
