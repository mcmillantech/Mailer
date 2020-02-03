<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	ViewLog.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  View messages sent from the queue
// --------------------------------------------------------------
/*
*/
	require_once "LogCheck.php";

?>
<!DOCTYPE html>
<html>
<head>
<title>Sent messages</title>
	<meta charset="utf-8">
</head>
<body>
<?php
	echo "<h1>Sent messages</h1>";

	echo "<button onClick='home()'>Home</button>";
	
	echo "<div id='table' style='position:absolute'>";
		echo "<p><b>";					// Headings
		echo "Sent ";
		echo "<span style='position:absolute; left:130px; width:200px'>";
		echo "Status</span>";
		echo "<span style='position:absolute; left:200px; width:200px'>";
		echo "No.</span>";
		echo "<span style='position:absolute; left:250px; width:250px'>";
		echo "Subject</span>";
		echo "<span style='position:absolute; left:500px; width:150px'>";
		echo "List</span>";
		echo "<span style='position:absolute; left:650px; width:150px'>";
		echo "Template</span>";
		echo "</b></p>";
									// Detail rows
		$sql = "SELECT m.* , l.name, g.archive, g.template FROM mailqueue m "
			. "JOIN maillists l ON l.id=m.listid "
			. "JOIN mailmessages g ON g.id=m.messageid "
			. "WHERE g.archive=0 "
			. "ORDER BY m.inx DESC";
	//SELECT * FROM mailqueue ORDER BY inx DESC";

		$result = mysqli_query($dbConnection, $sql);
		while ($record = mysqli_fetch_array($result))
		{
			echo "<p>";
			$dt = $record['queuetime'];
			$dt = substr($dt, 8, 2) . '/' . substr($dt, 5, 2) . '/' . substr($dt, 0, 4)
				. ' ' . substr($dt, 11,5);
	
			echo $dt . ' ';
			showField('status', 130);
			showField('totalsent', 200);
			showField('subject', 250);
			showField('name', 500);
			$msg = $record['messageid'];
			$tp = showTemplate($msg);
			showResendButton($msg, $tp);
			echo "</p>\n";
		}
	echo "</div>";
	mysqli_free_result($result);

// ---------------------------------
//	If there's a template, show it
//
//	Parameter	Message
// ---------------------------------
function showTemplate($messageid)
{
	global $dbConnection;

	$sql = "SELECT template FROM mailmessages WHERE id=$messageid";
	$result = mysqli_query($dbConnection, $sql)
		or die (mysqli_error($dbConnection));
	$row = mysqli_fetch_array($result);
	$tp = $row['template'];
	if ($tp == '')					// No template - this is a basic mail
		return '';

	$sql = "SELECT name FROM templates WHERE id=$tp";
	$result = mysqli_query($dbConnection, $sql)
		or die (mysqli_error($dbConnection));
	$row = mysqli_fetch_array($result);
	$name = $row['name'];
	echo "<span style='position:absolute; left:650px; width:200px;'>";
	echo $name;
	echo "</span>";
	mysqli_free_result($result);
	
	return $tp;
}

function showField($column, $left)
{
	global $record;

	echo "<span style='position:absolute; left:$left" . "px; width:200px'>";
	echo $record[$column];
	echo "</span>";
}

//
function showResendButton($msg, $tp)
{
	echo "<span style='position:absolute; left:800px;'>";
	if ($tp == '')
		$tp = '""';
	echo "<button onClick='resend($msg, $tp)'>Resend</button>";
	echo "<span>";
}

?>
<script>
function home()
{
	document.location.assign('Home.php');
}

function resend(msg, tp)
{
	if (tp == '')
		var str = "BasicMail.php?resend=" + msg;
	else
		var str = "MailEdit.php?action=edit&id=" + msg;
	document.location.href = str;
}

</script>
</body>
</html>
