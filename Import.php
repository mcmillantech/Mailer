<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	Import.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Form to import spreadsheet of contacts
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
<h1>Mailer: create / edit list</h1>

<?php
	echo "Hello";
	include "LogCheck.php";
	
	showForm();

// -------------------------------------
function showForm()
{
	$style = "top:130px; left:200px; padding:10px;";
	echo "<div class='mcform' style='$style'>";
	
	echo "\n<form action='Import2.php' method='post' enctype='multipart/form-data'>";

	echo "<span class='prompt1'>Import CSV file</span>";
	echo "\n<span class='input1'><input type='file' name='import' size=45 value='*.php'></span>";
	echo "<br><span class='prompt1'>List name</span>";
	echo "\n<span class='input1'><input type='text' name='name' size=45 ></span>";

	echo "<br><span class='prompt1'>Column maps</span><br>";
	echo "<span class='prompt1'>&nbsp;&nbsp;Email</span>";
	echo "\n<span class='input1'><input type='text' name='email' size=3 ></span>";
	echo "<br><span class='prompt1'>&nbsp;&nbsp;Forename</span>";
	echo "\n<span class='input1'><input type='text' name='forename' size=3 ></span>";
	echo "<br><span class='prompt1'>&nbsp;&nbsp;Surname</span>";
	echo "\n<span class='input1'><input type='text' name='surname' size=3 ></span>";
	echo "<br><span class='prompt1'>&nbsp;&nbsp;Business</span>";
	echo "\n<span class='input1'><input type='text' name='business' size=3 ></span>";
//	echo "<br><span class='prompt1'>&nbsp;&nbsp;Key field</span>";
//	echo "\n<span class='input1'><input type='text' name='ky' size=3 ></span>";

	echo "<br><br>";
	echo "<input type='submit' value='post'>";

	echo "</div>";
}
?>
</body>
</html>
