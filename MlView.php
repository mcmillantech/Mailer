<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	MlView.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Present and manage a mailing list 
//
//	Parameters	List name
//				Page (optional) - will be present after
//					page turner is clicked
// --------------------------------------------------------------
/*
PHP functions
function initialise()
function makeCol($record, $col)
function makeFilter($record)
function heading()
function showList($listControl)
function showTurners($listControl)

JavaScript functions
function turn(lst, page, op)
function mbvEdit(id)
function mbvDelete(rtable, id)
function mbvNewMember(mode)
*/
			// Check user is logged on
	require_once "LogCheck.php";
?>
<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
	<style>
		.mll0
		{
		  position: absolute;
		  left: 5px;
		  width: 250px;
		}
		.mll1
		{
		  position: absolute;
		  left: 255px;
		  width: 100px;
		}
		.mll2
		{
		  position: absolute;
		  left: 355px;
		  width: 100px;
		}
		.mll3
		{
		  position: absolute;
		  left:	 455px;
		  width: 140px;
		}
		.mll4
		{
		  position: absolute;
		  left: 615px;
		  width: 300px;
		}
	</style>
<?php
// --------------- This is the <head> section ---------------------

	if (array_key_exists('pg', $_GET))		// Entry from a page turner click
	{
		$listControl = $_SESSION['rtable'];
		$page = $_GET['pg'];
		$maxPage = ($listControl['count'] / 20) - 1;
		if ($page == 'end')
			$page = $maxPage;
		else if ($page > $maxPage)
			$page = $maxPage;
		$listControl['page'] = $page;
	}
	else									// Call from list open
		$listControl = initialise();

// ----------------------------------------------
//	This is the first call for a recip list
//
//	Identified by lack of a page parameter
//
//	Find the number of records, and create the
//	field map and filter
// ----------------------------------------------
function initialise()
{
	global $dbConnection;

	$list = $_GET['lst'];			// Fetch the table name
	$sql = "SELECT * FROM maillists WHERE name='$list'";
	$result = mysqli_query($dbConnection, $sql)
		or die ("Error : $sql");
	$lstRecord = mysqli_fetch_array($result);
	
	$listName = $lstRecord['name'];
	$rtable = $lstRecord['rtable'];
	$filter = makeFilter($lstRecord);
	$sqlCount = "SELECT COUNT(*) FROM $rtable $filter";
	$result = mysqli_query($dbConnection, $sqlCount)
		or die ("Error reading table : $sqlCount");
	$cr = mysqli_fetch_array($result);
	$count = $cr['COUNT(*)'];
	
	$listControl = array(
		'lst' => $list, 
		'name' => $lstRecord['name'],
		'rtable' => $rtable,
		'count' => $count,
		'page' => 0,
		'filter' => $filter,
		'ky' => makeCol($lstRecord, 'ky'),		// Custom column names
		'email' => makeCol($lstRecord, 'email'),
		'forename' => makeCol($lstRecord, 'forename'),
		'surname' => makeCol($lstRecord, 'surname'),
		'business' => makeCol($lstRecord, 'business')
	);
	
	$_SESSION['rtable'] = $listControl;
	mysqli_free_result($result);

	return $listControl;
}

	echo "<title>Email list: " . $listControl['name'] . "</title>";

// ----------------------------------------------
//	Make the columns mapping record
//
// ----------------------------------------------
function makeCol($record, $col)
{
	$c = $record[$col];
	if ($c == '')				// No entry - use the default
	{
		if ($col == 'ky')		// Default to the column name except for id
			$c = 'id';
		else
			$c = $col;
	}

	return $c;
}

// ----------------------------------------------
//	Make the filter clause
//
// ----------------------------------------------
function makeFilter($record)
{
	$fcol = $record['fcol'];
	if ($fcol == '')				// No filter specified
		return '';

	$value = $record['fval'];
	if (!is_numeric($value))		// Apply quotes to alphp value
		$value = "'$value'";
	$filter = " WHERE $fcol "
		. $record['fop']
		. $value;
//		. " '" . $record['fval'] . "'";
	return $filter;
}
?>
	
</head>
<body>
<?php
	echo "<h1>Mailing list"  . $listControl['name'] . "</h1>";

	echo "<p><button onClick='mbvNewMember(0)'>New recipient</button>&nbsp;&nbsp;";
	echo "<button onClick='home()'>Home</button>&nbsp;&nbsp;";
	showTurners($listControl);
	echo "</p>";
	echo "<p><button onClick='back()'>Back to list</button>&nbsp;&nbsp;";
	echo "<button onClick='home()'>Home</button></p>";

	echo "<div class='container'>";
	
	echo heading();
	echo "<br>";
	
	showList($listControl);
	
function heading()
{
	$hdg = "<b><span class='mll0'>Email</span>"
		. "<span class='mll1'>Forename</span>"
		. "<span class='mll2'>Surname</span>"
//		. "<span class='mll3'>status</span>"
		. "<span class='mll3'>Business</span></b><br>";
	return $hdg;
}

// ----------------------------------------------
//	Show the list of mailing lists
//
// ----------------------------------------------
function showList($listControl)
{
	global $dbConnection;
									// Pick up the fields we need to show
									// For an external file, the columns may not be
									// the same as internals
	$rtable = $listControl['rtable'];
	$filter = $listControl['filter'];
	$top = $listControl['page'] *20;

	$ky = $listControl['ky'];
	$em = $listControl['email'];
	$forename = $listControl['forename'];
	$surname = $listControl['surname'];
	$bus = $listControl['business'];
	$sql = "SELECT $ky, $em, $forename, $surname, $bus"
		. " FROM $rtable $filter LIMIT 20 OFFSET $top";
//	echo "$sql<br>";
	$result = mysqli_query($dbConnection, $sql)
		or die (mysqli_error($dbConnection) . ": " . $sql);

	while ($record = mysqli_fetch_array($result))
	{
		$kval = $record[$ky];
		echo "<div style='line-height:1.5'>";
		echo "<span class='mll0' id='mle$kval'>" . $record[$em] . "</span>";
		echo "<span class='mll1' id='mlf$kval'>" . $record[$forename] . "</span>";
		echo "<span class='mll2' id='mls$kval'>" . $record[$surname] . "</span>";
		echo "<span class='mll3' id='mlb$kval'>" . $record[$bus] . "</span>";
		
		echo "<span class='mll4'>";
		echo "<button onClick='mbvEdit($kval)'>Edit</button>&nbsp;&nbsp;";
		echo "<button onClick='mbvDelete(\"$rtable\", $kval)'>Delete</button>&nbsp;&nbsp;</span>";
		echo "<br><br></div>\n";

	}
	mysqli_free_result($result);
}

function showTurners($listControl)
{
	$page = $listControl['page'];
	$lst = '"' . $listControl['lst'] . '"';
	

	echo "<button onClick='turn($lst, $page, \"f\")'>First</button>";
	echo "<button onClick='turn($lst, $page, \"p\")'>Prev</button>";
	echo "<button onClick='turn($lst, $page, \"n\")'>Next</button>";
	echo "<button onClick='turn($lst, $page, \"l\")'>Last</button>";
}


// ------------------------------------------
//  The edit form follows
//
//	Note the record id is set in showManager
// ------------------------------------------
	$rtable = $listControl['rtable'];
?>
	<div id='messagecreate'>
		<div style="text-align: right" onClick='closeNewMailForm()'>X&nbsp;</div>
		<form action='RcpPost.php' method='post'>
			<b>Recipient</b>
			<br><br>
			First name <input type='text' class='mld' name='forename' id='forename'>
			<br>
			Last name <input type='text' class='mld' name='surname' id='surname'>
			<br>
			Email <input type='text' class='mld' name='email' id='email'>
			<br>
			Company <input type='text' class='mld' name='business' id='business'>
			<br><br>
			<input type='hidden' name='id' id='mid'>
			<input type='hidden' name='mode' id='mmode'>
			<input type='submit' value='Post'>
<?php
		 	echo"	<input type='hidden' name='list' value='" . $_GET['lst'] . "'>"; 
		 	echo"	<input type='text' name='table' value='$rtable'>"; 
?>
		 	<br><br>
		</form>
	</div>
	<br>
	<button onClick='back()'>Back to list</button>&nbsp;&nbsp;
	<button onClick='home()'>Home</button>

</div>  <!-- container -->
<script>
function back()
{
	document.location.assign('MailLists.php');
}

function home()
{
	document.location.assign('Home.php');
}

// ----------------------------------------
//	Page turners
//
// ----------------------------------------
function turn(lst, page, op)
{
	switch (op)
	{
	case 'f':
		page = 0;
		break;
	case 'p':
		page--;
		if (page < 0)
			page = 0;
		break
	case 'n':
		page++;
		break;
	case 'l':
		page='end';
		break;
	}
	var str = "MlView.php?lst=" + lst + "&pg=" + page;
	document.location.href = str;
}

// ----------------------------------------
//	Edit a mailing list member
//
//	Populate and show the edit form 
// ----------------------------------------
function mbvEdit(id)
{
	mbvMode = 'edit';
	var el = document.getElementById('mmode');
	el.value = 'edit';
	el = document.getElementById('mid');		// The hidded form field holds the id
	el.value = id;
												// Fetch data from the list row
	var x = 'mle' + id;
	el = document.getElementById('mle' + id);
	var el2 = document.getElementById('email');	// ...  and move into the edit form
	el2.value = el.innerHTML;
	el = document.getElementById('mlf' + id);
	el2 = document.getElementById('forename');
	el2.value = el.innerHTML;
	el = document.getElementById('mls' + id);
	el2 = document.getElementById('surname');
	el2.value = el.innerHTML;
	el = document.getElementById('mlb' + id);
	el2 = document.getElementById('business');
	el2.value = el.innerHTML;

	var el = document.getElementById('messagecreate');
	el.style.visibility = 'visible';
}

function mbvDelete(rtable, id)
{
	if (!confirm('Are you sure you want to delete this entry?'))
		return;

	var href = location.href;		// Locate the table from the calling href
	var psn = href.indexOf('lst') + 4;
	var list = href.substr(psn);
	
	var str = "RcpPost.php?action=delete&table=" + rtable + "&list=" + list + "&id=" + id;
	document.location.href = str;
}

// ----------------------------------------
//	Show the edit form when "new member"
//	is clicked
// ----------------------------------------
function mbvNewMember(mode)
{
	mbvMode = 'add';
	var el = document.getElementById('mmode');
	el.value = 'add';
	el = document.getElementById('mid');
	el.value = 0;
	el = document.getElementById('forename');
	el.value = '';
	el = document.getElementById('surname');
	el.value = '';
	el = document.getElementById('email');
	el.value = '';
	el = document.getElementById('business');
	el.value = '';
	el = document.getElementById('messagecreate');
	el.style.visibility = 'visible';
}

</script>
</body>
</html>
