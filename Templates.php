<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	Templates.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Template Builder
// --------------------------------------------------------------
/*
function showList()
function showRecord($record)
*/
?>
<!DOCTYPE html>
<head>
	<title>MT Mail Templates</title>
	<meta charset="utf-8">
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
</head>
<body style='background-color: #eeeeee;'>
<h1>Templates</h1>

<?php

	include "LogCheck.php";
	echo "<p><button onClick='home()'>Home</button>";
	$ref = '"TemplateBuilder.php"';
	echo "&nbsp;&nbsp;<button onClick='location.href=$ref'>New template</button></p>";
	showList();

function showList()
{
	global $dbConnection;

	echo "<div id='container'>";
		echo "<div class='selector' style='top:100px; left:200px; padding-left:5px'>";
		echo "Select template to edit<br><br>\n";
		$sql = "SELECT * FROM templates";
		$result = mysqli_query($dbConnection, $sql);
		while ($record = mysqli_fetch_array($result))
			showRecord($record);
		echo "</div>";
	echo "</div>";
}

// ----------------------------------------------
//	Show the template record
//
// ----------------------------------------------
function showRecord($record)
{
	$id = $record['id'];
	echo "<span style='position:absolute; width: 300px'>";
	echo $record['name'];
	echo "</span>";
	echo "<span style='position:absolute; left:400px'>";
		echo "<a href='TemplateEdit.php?action=edit&id=$id'>Edit</a></span>";
	echo '<br>';
}

?>
</body>
</html>
