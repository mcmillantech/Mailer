<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	Newsletters.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  List newsletters that have been created / sent
//	Called from main menu
// --------------------------------------------------------------
/*
function showMessageList()
function showRecord($record)
function showManager($id)
function formatSQLDate($dt)
*/
?>
<!DOCTYPE html>
<html>
<head>
<title>Email Messages</title>
	<meta charset="utf-8">
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">

</head>

<body>
<h1>Mailer: Newsletters</h1>
<?php
	include "LogCheck.php";
?>
<!-- Show the page  -->
<p><button onClick='newMail()'>Create message</button></p>
<p><button type='button' onClick='home()'>Home</button></p>

<div class='container'>
	<div id='messagecreate'>
		<div style="text-align: right" onClick='closeNewMailForm()'>X&nbsp;</div>
		<form action='MailEdit.php?action=new' method='post'>
		<b>Message Details</b>
		<br><br>
		Message name <input type='text' name='msgname'>
		<br><br>
		Subject <input type='text' name='subject'>
		<br><br>
		From <input type='text' name='from'>
		<br><br>
		** Pick up a template **
		<br><br>
		<input type='submit' value='Next'>
		</form>
	</div>
	
	<div id='messagelist'>
		<h3>Messages</h3>
		<?php showMessageList();
			  makeManageMenu();
		?>
	</div>
</div>

<?php

// ----------------------------------------------
//	The manage button drop down menu
//
//	This is in div mbvmanagedd
// ----------------------------------------------
function makeManageMenu()
{
	echo "<br><div class='mslmanagedd' id='mslmanagedd'>";
		echo "<div onClick='mslSend()'>Send</div>";
		echo "<div onClick='mslEdit()'>Edit</div>";
		echo "<div onClick='mslDelete()'>Delete</div>";
	echo "</div>";
}

// -------------------------------------
//	Show the list of messages
//
// -------------------------------------
function showMessageList()
{
	global $dbConnection;

	echo "\n<b>Id";
	echo "<span class='mslname'>Name</span>";
	echo "<span class='mslsubject'>Subject</span>";
	echo "<span class='msllast'>Last sent</span>";
	echo "<span class='mslnsent'>Number sent</span>";
	echo "</b><br><br>";

	$sql = "SELECT * FROM mailmessages WHERE template <>0 ORDER BY id DESC";
	$result = mysqli_query($dbConnection, $sql)
		or die ("Error : $sql");
	while ($record = mysqli_fetch_array($result))
		showRecord($record);
	
	mysqli_free_result($result);
}

// -------------------------------------
//	Show details of one message
//
// -------------------------------------
function showRecord($record)
{
	echo "\n<div class='mslmessage'>";
		echo $record['id'];
		echo "<span class='mslname'>" . $record['name'] . "</span>";
		$subject = substr($record['subject'], 0, 32);
		echo "<span class='mslsubject'>$subject</span>";
	
		echo "<br>";
		$dt = formatSQLDate($record['created']);
		echo "<span class='mslname'>Created $dt</span>";
		$dt = formatSQLDate($record['modified']);
		if ($dt != '')
			echo "<span class='mslsubject'>Modified $dt</span>";
		$dt = formatSQLDate($record['lastsend']);
		echo "<span class='msllast'>$dt</span>";
		echo "<span class='mslnsent'>" . $record['numbersent'] . "</span>";
		showManager($record['id']);
	echo "</div>";
	echo "&nbsp;<br><br><br><hr>";
	
}

// -------------------------------------
//	The manage button and drop down
//
//	The button calls mslManage
//	The drop down is in div mslmanagedd
// -------------------------------------
function showManager($id)
{
	echo "<div class='mslmanage'>";
									// The manage button
		echo "<div class='mslmanagebtn' onClick='mslManage(this, $id)'>&nbsp;Manage&nbsp;</div>";
	echo "</div>";
}

function formatSQLDate($dt)
{
	if ($dt != '')
		$dt = substr($dt, 8, 2) . '/' . substr($dt, 5, 2) . '/' . substr($dt, 0, 4);
	return $dt;
}

?>

<script>

function newMail()
{
	document.location.href = "MailCreate.php?mode=newsletter";
}

// ----------------------------------------
//	Handler for Message 'edit' click
//
//	Called from Messages Manage list
// ----------------------------------------
function mslEdit()
{
	id = mslId;
	var str = "MailEdit.php?action=edit&id=" + id;
	document.location.href = str;
}

// ----------------------------------------
//	Handler for Messages 'Send' click
// ----------------------------------------
function mslSend()
{
	id = mslId;
	var str = "SendNews.php?action=msg&id=" + id;
	document.location.href = str;
}

function mslDelete(id)
{
	id = mslId;
	if (confirm("Are you sure?"))
	{
		var str = "MailEdit.php?action=delete&id=" + id;
		document.location.href = str;
	}
}

// ----------------------------------------
//	Handler for "Manage" button
//
//	Show the drop down list
//
//	Paramerers	Calling button
//				id of message
//	Its hierarchy is container > message list
//	> mslmessage.
// ----------------------------------------
function mslManage(obj, id)
{
	mslId = id;
							// Position the menu relative to the mslmessage
							// Locate the top of the calling button
	var o2 = obj.parentElement;			// The msl message class )
	var top = o2.parentElement.offsetTop +30;
	top += 'px';

	var dd = 'mslmanagedd';
	var el = document.getElementById(dd);
	el.style.top = top;

	var v = el.style.visibility;
	if (v == 'visible')
		el.style.visibility = 'hidden';
	else
		el.style.visibility = 'visible';
}

</script>

</body>
</html>
