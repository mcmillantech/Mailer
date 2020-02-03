<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	Import2.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Post spreadsheet import 
// --------------------------------------------------------------
/*
*/
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit mail list import</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
</head>

<body style='background-color: #eeeeee;'>
<h1>Mailer: Process Import</h1>

<?php
	include "LogCheck.php";

								// Fetch the import file
	$csvFile = $_FILES['import']['tmp_name'];
	$file = fopen($csvFile, 'r')
		or die ("$csvFile not found");

	$table = createTable();
	$map = makeMap();
	while ($line = fgetcsv($file))
		insertLine($table, $map, $line);

	echo "<button type='button' onClick='goList()'>Back to List</button></p>";

// --------------------------------------
//	Insert line into rcp table
//
//	Parameters	table name
//				column map
//				csv line from input file
// --------------------------------------
function insertLine($table, $map, $line)
{
	global $dbConnection;
							// Fetch the import fields
	$email = $line[$map['email']];
	$forename = $line[$map['forename']];
	$surname = $line[$map['surname']];
	$business = $line[$map['business']];

	$sql = "INSERT INTO $table (email, forename, surname, business, member) "
		. "VALUES ('$email', '$forename', '$surname', '$business', 2)";
	echo "<br>$sql<br>";
	mysqli_query($dbConnection, $sql)
		or die("Failed to insert line " . "$sql " . mysqli_error($dbConnection));
}

// --------------------------------------
//	Create map of columns
//
//	Returns array holding the mao
// --------------------------------------
function makeMap()
{
	$map = array();
	$map = addToMap($map, 'email');
	$map = addToMap($map, 'forename');
	$map = addToMap($map, 'surname');
	$map = addToMap($map, 'business');

	return $map;
}

// --------------------------------------
//	Map one columns
//
//	Parameters
//		Map array to date
//		Name of database column
//			This is same as $POST entry
//
//	Returns array with this column added
// --------------------------------------
function addToMap($map, $column)
{
							// Convert the column letter into an integer
	$sCol = strtoupper($_POST[$column]);
	$nCol = ord($sCol) - ord('A');
							// ... and add to the map
	$map[$column] = $nCol;
							// Error check needed - back to form
	return $map;
}

// --------------------------------------
//	Create the rcp table and add to the
//	maillist table
//
//	Returns	name of rcp table
// --------------------------------------
function createTable()
{
	global $dbConnection;

	$sql = "SELECT * FROM mailsystem";			// Fetch next table number
	$result = mysqli_query($dbConnection, $sql);
	$sys = mysqli_fetch_array($result);
	$nextNum = $sys['nexttable'];
	mysqli_free_result($result);

	$table = "rcp_$nextNum";					// Make table name
	$newSql = "CREATE TABLE `$table` ("
		. "`id` int(10) unsigned NOT NULL auto_increment,"
		. "`list` int(10) unsigned default NULL,"
		. "`email` varchar(45) default NULL,"
		. "`business` varchar(45) default NULL,"
		. "`forename` varchar(45) default NULL,"
		. "`surname` varchar(45) default NULL,"
		. "`classes` int(10) unsigned default NULL,"
		. "`status` smallint(5) unsigned default NULL,"
		. "`joindate` datetime default NULL,"
		. "`lastmessage` datetime default NULL,"
		. "PRIMARY KEY  (`id`)"
		. ") ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;";

	mysqli_query($dbConnection, $newSql)		// ... and create the table
		or die ("Failed to create new table");

	$nextNum++;									// Update the system table
	$sql = "UPDATE mailsystem SET nextTable = $nextNum";
	mysqli_query($dbConnection, $sql)
		or die ("Failed to update system table");
												// Update mail lists
	$name = $_POST['name'];
	$sql = "INSERT INTO maillists (name, rtable, email, forename, surname, business)"
		. " VALUES ('$name', '$table', 'email', 'forename', 'surname', 'business')";
	mysqli_query($dbConnection, $sql)
		or die("Failed to insert list " . "$sql " . mysqli_error($dbConnection));
	echo "<br>Record added<br><br>";

	return $table;
}
?>

<script>
// -------------------------------------
//	Handler for 'Back to list'
// -------------------------------------
function goList()
{
	document.location.assign("MailLists.php");
}

</script>

</body>
</html>
