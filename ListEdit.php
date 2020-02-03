<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	ListEdit.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Create or edit new mail list
//
//	Parameters are mode - newman, newdb, delete or edit
//				   list - name of list, or "new"
// --------------------------------------------------------------
/*
function createNewValues()
function fetchList()
function deleteList($mode)
function showForm($record, $list)
function showDbFields($record)
function nonDbFields($record)
function showLine($record, $column, $size)
function fixedField($record, $column)
function showOperators($record)
function createManualTable()

JS function back()
*/
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit mail list entry</title>
	<meta charset="utf-8">
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
</head>

<body style='background-color: #eeeeee;'>
<h1>Mailer: create / edit list</h1>
<?php
	include "LogCheck.php";

	$mode = $_GET['mode'];
	$list = $_GET['list'];

	if ($mode=='newman' || $mode == 'newdb')	// Set up data for a new list
		$record = createNewValues($mode);
	if ($mode =='newman') 
		$record ['rtable'] = createManualTable();

	else if ($mode=='edit')
		$record = fetchList();
	
	else if ($mode == 'delete')
		deleteList();

	if ($mode != 'delete') {						// For create or edit, show the form in a div
		echo "<div id='container'>";
			showForm($record, $list);
		echo "</div>";
		echo "<button onClick='back()'>Back to list</button>";
	}

// -------------------------------------
//	Set up data for new table
//
// -------------------------------------
function createNewValues($mode)
{
	$record = array (
		'name' => '',
		'type' => 1,				// This is default for db table
		'rtable' => '',
		'forename' => 'forename',
		'surname' => 'surname',
		'email' => 'email',
		'ky' => 'id',
		'business' => '',
		'fcol' => '',
		'fop' => '',
		'fval' => '',
		'ky' => 'id'
	);
	
	if ($mode == 'newman')			// Set type for manual list
		$record['type'] = 2;
	else
		$record['type'] = 1;
	
	return $record;
}

// ----------------------------------------
//	Fetch the record for list to be edited
//
// ----------------------------------------
function fetchList()
{
	global $dbConnection;

	$sql="SELECT * FROM maillists WHERE name='" . $_GET['list'] . "'";
	$result = mysqli_query($dbConnection, $sql)
		or die("Failed to fetch list " . "$sql " . mysqli_error($dbConnection));
	$record = mysqli_fetch_array($result);
	mysqli_free_result($result);
	
	return $record;
}

// -------------------------------------
//	Delete a list
//
// -------------------------------------
function deleteList()
{
	global $dbConnection;

	removeTable();
	$sql="DELETE FROM maillists WHERE name='" . $_GET['list'] . "'";
	if (!mysqli_query($dbConnection, $sql))
		die("Failed to delete list " . "$sql " . mysqli_error($dbConnection));
	echo "<button onClick='back()'>Back to list</button>";
}

// ----------------------------------------
//	Remove non DB table on delete of list
//
// ----------------------------------------
function removeTable()
{
	global $dbConnection;

	$sql="SELECT * FROM maillists WHERE name='" . $_GET['list'] . "'";
	$result = mysqli_query($dbConnection, $sql)
		or die("Failed to fetch list " . "$sql " . mysqli_error($dbConnection));
	$record = mysqli_fetch_array($result);
	mysqli_free_result($result);
	
	$type = $record['type'];				// Do NOT remove database table
	if ($type == 1)
		return;

	$rtable = $record['rtable'];
	$sql = "DROP TABLE $rtable";
	echo $sql;
	$result = mysqli_query($dbConnection, $sql)
		or die("Failed to drop table $rtable" . mysqli_error($dbConnection));
}

// -------------------------------------
//	Show the form
//
//	Parameters
//			record - from lists table
//					or defaults for new
//			list table or 'new'
// -------------------------------------
function showForm($record, $list)
{
	global $mode;

	$type = $record['type'];
	$style = "top:130px; left:200px; padding:10px;";
	echo "<div class='mcform' style='$style'>";
	
	echo "\n<form onsubmit='return validateList()' action='ListPost.php?list=$list&type=$type' method='post'>";

	echo "<span class='prompt1'>List name</span>";
	showLine($record, 'name', 45);
	if ($record['type'] == 1) 
		showdbFields($record);					// Show table name and map fields
	else
		nonDbFields($record);

	echo "<br><span class='prompt1'>Filter</span><br>";
	echo "<span class='prompt1'>&nbsp;&nbsp;Columns</span>";
	showLine($record, 'fcol', 45);
	showOperators($record);
	echo "<span class='prompt1'>&nbsp;&nbsp;Value</span><br>";

	showLine($record, 'fval', 45);
	
	echo "<br><br>";
	echo "<input type='submit' value='post'>";
//	echo "<span class='input1'><button type='button' onClick='genTable()'>Generate table name</button></span>";
	echo "</form>";
	echo "</div>";
}

// -------------------------------------
//	Present fields that are only used
//	for database lists
//
// -------------------------------------
function showDbFields($record)
{
	echo "<span class='prompt1'>Database table</span>";
	showLine($record, 'rtable', 45);
	
	echo "<br><span class='prompt1'>Column maps</span><br>";
	echo "<span class='prompt1'>&nbsp;&nbsp;Email</span>";
	showLine($record, 'email', 45);
	echo "<span class='prompt1'>&nbsp;&nbsp;Forename</span>";
	showLine($record, 'forename', 45);
	echo "<span class='prompt1'>&nbsp;&nbsp;Surname</span>";
	showLine($record, 'surname', 45);
	echo "<span class='prompt1'>&nbsp;&nbsp;Business</span>";
	showLine($record, 'business', 45);
	echo "<span class='prompt1'>&nbsp;&nbsp;Key field</span>";
	showLine($record, 'ky', 45);
	fixedField($record, 'type');

}

// -------------------------------------
//	Hold fixed data for non DB lists
//	This causes them to be posted
//
// -------------------------------------
function nonDbFields($record)
{
	fixedField($record, 'rtable');
	fixedField($record, 'type');
	fixedField($record, 'forename');
	fixedField($record, 'surname');
	fixedField($record, 'business');
	fixedField($record, 'email');
	fixedField($record, 'ky');
}

// -------------------------------------
//	Build and show drop down of 
//  comparison operators
//
// -------------------------------------
function showOperators($record)
{
	$selected = $record['fop'];

	$values = array (
		"=" => "Equals",
		">" => "Greater than",
		"<" => "Less than",
		">=" => "At least",
		"<=" => "At most",
		"<>" => "Not equal to"
	);

	echo "<span class='prompt1'>&nbsp;&nbsp;Operator</span>";
	echo "\n<span class='input1'><select name='fop' id='fop'>";
		foreach ($values as $op => $text)
		{
			echo "\n<option value='$op'";
			if ($op == $selected)
				echo " selected";
			echo ">$text</option>";
		}
		echo "</select>";
	echo "</span>";
	echo "<br>";
}

// ----------------------------------------
//	Show one entry field
//
//	Parameters
//			Record holding data
//			This column
//			Field size
// ----------------------------------------
function showLine($record, $column, $size)
{
	$val = $record[$column];
	echo "\n<span class='input1'><input type='text' id='$column' name=$column size='$size' value='$val'></span>";
	echo "<br>";
}

// ----------------------------------------
//	Generate a hidden field
//
// ----------------------------------------
function fixedField($record, $column)
{
	$val = $record[$column];
	echo "\n<input type='hidden' name='$column' value='$val'>";
}

// ----------------------------------------
//	Create manual table
//
//	Generates table name and creates it
//
//	Returns table name
// ----------------------------------------
function createManualTable()
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

	$nextNum++;
	$sql = "UPDATE mailsystem SET nextTable = $nextNum";

	mysqli_query($dbConnection, $sql)
		or die ("Failed to update system table");

	return $table;
}

?>

<script>

// ------------------------------------------------
//	JS generate new table
//
//	Use AJAX to create the table and fetch its name
//	REMOVE THIS
// -------------------------------------------------
function genTable()
{
	var hAjax;
	hAjax = new XMLHttpRequest();
	
	hAjax.onreadystatechange=function()	
	{
		if (hAjax.readyState==4 && hAjax.status==200)
		{
			var el = document.getElementById('rtable');
		    var httxt = hAjax.responseText;
		    el.value = httxt;
							//	Need to disable the generate button
	    }
    }
	hAjax.open("GET","AjaxNewFile.php",true);
	hAjax.send();
}

function back()
{
	document.location.assign('MailLists.php');
}

</script>
</body>
</html>
