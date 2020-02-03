<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	AjaxPreviewList.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Ajax server to generate a preview of recipient list 
//
//	Called BasicMail.php, from onLoad, showForm and 
//	showRecipientList > MailEdit.js:previewList
// --------------------------------------------------------------
	require_once "LogCheck.php";

	$list = $_GET['lst'];				// Look up the list
	$sql = makeListSQL($list);

	if (!$result = mysqli_query($dbConnection, $sql))
	{
		echo "An error has occured: table '$table' is missing<br><br>";
		die();
	}
	while ($recip = mysqli_fetch_array($result))
		echo $recip[$emailMap] . '<br>';
		
	mysqli_free_result($result);

// ----------------------------------------------
//	Build the SQL to fetch a short list
//
//	Also sets the table name global
//	(bit messy I know, but ...
// ----------------------------------------------
function makeListSQL($list)
{
	global $dbConnection, $emailMap, $table;

	$sql = "SELECT * FROM maillists WHERE id='$list'";
	if (!$result = mysqli_query($dbConnection, $sql))
	{
		echo ("An error has occured reading table maillists<br><br>");
		die();
	}
	$dta = mysqli_fetch_array($result);
	mysqli_free_result($result);

	$table = $dta['rtable'];
	$emailMap = $dta['email'];
	$col = $dta['fcol'];
	$op = $dta['fop'];
	$val = $dta['fval'];
	if ($col == '')				// Build the filter
		$where = '';
	else
	{
		$where = " WHERE $col $op '$val'";		// I did have quotes around $val - could fail
	}
	$sql = "SELECT * FROM $table $where LIMIT 10";
	return $sql;
}

?>
