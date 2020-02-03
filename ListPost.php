<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	ListPost.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Create a new mail list
// --------------------------------------------------------------
/*
*/
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit mail list entry</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
</head>

<body style='background-color: #eeeeee;'>
<?php
	require_once "LogCheck.php";

	$list = $_GET['list'];
	if ($list=='new')
	{
		echo"<h1>Mailer: Create list</h1>";
		doNew();
	}
	else 
	{
		echo "<h1>Mailer: Update list</h1>";
		doUpdate();
	}

// -------------------------------------
//	New record
//
// -------------------------------------
function doNew()
{
	global	$dbConnection;

	$value = $_POST['fval'];
	if (!is_numeric($value))		// Apply quotes to alpha value
		$value = "'$value'";

	$sql = "INSERT INTO maillists ("
		. "name, rtable, type, email, forename, surname, business, ky, fcol, fop, fval)"
		. " VALUES ("
		. val('name')
		. val('rtable')
		. val('type')
		. val('email')
		. val('forename')
		. val('surname')
		. val('business')
		. val('ky')
		. val('fcol')
		. val('fop')
		. "$value)";

	mysqli_query($dbConnection, $sql)
		or die("Failed to insert list " . "$sql " . mysqli_error($dbConnection));
	echo "<br>Record added<br><br>";
	echo "<button onClick='back()'>Return to list</button>";
}

// -------------------------------------
//	Perform update
//
// -------------------------------------
function doUpdate()
{
	global	$dbConnection, $list;

	$type = $_GET['type'];

	$fval = addslashes ($_POST['fval']);
	$sql = "UPDATE maillists SET "
		. "name=" . val('name');
											// Following fields only used for DB lists
	if ($type <> 2) {
		$sql = $sql
		. "rtable=" . val('rtable')
		. "email=" . val('email')
		. "forename=" . val('forename')
		. "surname=" . val('surname')
		. "business=". val('business')
		. "ky=" . val('ky');
	}
	$sql = $sql								// These used for all lists
		. "fcol=" . val('fcol')
		. "fop=" . val('fop')
		. "fval='$fval' " 
		. "WHERE name='$list'";

	mysqli_query($dbConnection, $sql)
		or die("Failed to update list " . "$sql " . mysqli_error($dbConnection));
	echo "<br>Record updated<br><br>";
	echo "<button onClick='back()'>Return to list</button>";
}

// -------------------------------------
//	Take a posted value and quote it
// -------------------------------------
function val($col)
{
	$val = addslashes ($_POST[$col]);
	return "'$val',";
}

?>

<script>

// -------------------------------------
//	Return to list
//
// -------------------------------------
function back()
{
	document.location.assign('MailLists.php');
}

</script>
</body>
</html>