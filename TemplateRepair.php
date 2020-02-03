<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	TemplateRepair.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Receives an update to the HTML and posts it
//	Returns to the template editor
// --------------------------------------------------------------
?>
<!DOCTYPE html>
<head>
	<title>MT Template Repair</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
</head>
<body>
<?php
	include "LogCheck.php";
	echo "<h1>Template Repair</h1>";

	$id = $_POST['id'];
	$html = addSlashes($_POST['ed']);
	$sql = "UPDATE templates SET htmlText='$html' WHERE id=$id";

	if (mysqli_query($dbConnection, $sql))
		echo "<p>Record updated";
	else
		echo "<p>Failed to update template: " . mysqli_error($dbConnection);

	echo "</p>";
	echo "<button onClick='goBack($id)'>Return to template</button>";
?>
<script>
//----------------------------------------
//	Handler for Return to template button
//
//----------------------------------------
function goBack(id)
{
	document.location.href="TemplateEdit.php?action=edit&id=" + id;
}

</script>
</body>
</html>
