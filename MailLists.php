<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	MailLists.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Presesnt the list of mail lists 
//	Called from Guest Lists > Mail Lists menu
//
//	"New list" needs to post and refresh the list - easiest to 
//	do here
// --------------------------------------------------------------
/*
function showList()
function showRecord($record)
function formatSQLDate($dt)
*/
			// Check user is logged on
	require_once "LogCheck.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage mailing lists</title>
	<meta charset="utf-8">
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
<style>
.mslname
{
	position:		absolute;
	left:			50px;
	width:			300px;
}

.msllast
{
	position:		absolute;
	left:			450px;
	width:			60px;
}

.mslnsent
{
	position:		absolute;
	left:			550px;
	width:			60px;
}
</style>
</head>

<body>
<h1>Mailer: Manage mailing lists</h1>
<?php
	if (array_key_exists('newlistname', $_POST))
		doNewList();
?>
<!-- Show the page  -->
<!-- <p><button onClick='newList()'>New List</button> -->
<p><button onClick='newList("newdb")'>New Database List</button>
&nbsp;&nbsp;<button onClick='newList("newman")'>New Manual List</button>
&nbsp;&nbsp;<button onClick='doImport()'>Import</button>
&nbsp;&nbsp;<button onClick='home()'>Home</button></p>

<div class='container'>
	<div id='messagelist'>
		<h3>Lists</h3>
		<?php showLists()
 		?>
		<br>
		<button onClick='home()'>Home</button>
	</div>
</div>

<?php
// -------------------------------------
//	Insert new list
//
// -------------------------------------
function doNewList()
{
	global $dbConnection;

	$name = addslashes($_POST['newlistname']);
	$sql = "INSERT INTO maillists (name) VALUES ('$name')";
	mysqli_query($dbConnection, $sql);
}

// -------------------------------------
//	Show the lists
//
// -------------------------------------
function showLists()
{
	global $dbConnection;

	echo "<b>";
	echo "<span class='mslname'>Name</span>";
	echo "<span class='msllast'>Last sent</span>";
	echo "<span class='mslnsent'>Number sent</span>";
	echo "</b><br><br>";

	$sql = "SELECT * FROM maillists";
	$result = mysqli_query($dbConnection, $sql)
		or die ("Error : $sql");
	while ($record = mysqli_fetch_array($result))
		showRecord($record);
	
	mysqli_free_result($result);
}

// -------------------------------------
//	Show details of one list
//
// -------------------------------------
function showRecord($record)
{
	$id = $record['id'];
	$rtable = $record['rtable'];
	$list = $record['name'];

	echo "<div class='mslmessage'>";
		if ($rtable == '')
		{
			echo "Error: table $rtable (list " . $record['name'] . "does not exist";
			return;
		}
		echo "<span class='mslname'>" . $record['name'] . "</span>";
		$dt = $record['lastsend'];
		$nSent = $record['nSent'];
		echo "<span class='msllast'>$dt</span>";
		echo "<span class='mslnsent'>$nSent</span>";
	
//		echo "<span class='mslsubject'>Modified $dt</span>";
		echo "<span class='lstmanage' onClick='lstOpen(\"$list\")'>&nbsp;Open&nbsp;</span>";
		echo "<span class='lstmanage' style='left: 650px' onClick='lstEdit(\"$list\")'>&nbsp;Edit&nbsp;</span>";
		echo "<span class='lstmanage' style='left: 700px' onClick='lstDelete(\"$list\")'>&nbsp;Delete&nbsp;</span>";

	echo "</div>";
	echo "&nbsp;<br><br><hr>\n";
	
}

function formatSQLDate($dt)
{
	if ($dt != '')
		$dt = substr($dt, 8, 2) . '/' . substr($dt, 5, 2) . '/' . substr($dt, 0, 4);
	return $dt;
}

?>

</body>
<script>
function newList(mode)
{
	document.location.href = "ListEdit.php?mode=" + mode + "&list=new";
}

function doImport()
{
	document.location.href = "Import.php";
}

function lstEdit(list)
{
	document.location.href = "ListEdit.php?mode=edit&list=" + list;
}

function lstDelete(list)
{
	if (!confirm ("Delete " + list + "?"))
		return;
	document.location.href = "ListEdit.php?mode=delete&list=" + list;
}

// ----------------------------------------
//	Open a mailing list
// ----------------------------------------
function lstOpen(list)
{
	document.location.href = "MlView.php?lst=" + list;
}

</script>

</html>
