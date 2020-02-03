<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	TemplateEdit.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Editor for templates 
//	Container div holds instructpane and maineditpanel
//	The latter shows the html being edited
//	Also editview, a hidden panel for CKEditor
// --------------------------------------------------------------
/*
function opentemplate()
function deletetemplate()
function newTemplate()
function ckeditPanel()
function showInstructPanel()
function loadTemplate($file)
function makeLine($buffer)
function processTableDataCell($buffer, $st)
*/
?>
<!DOCTYPE html>
<head>
	<title>MT Template Editor</title>
	<meta charset="utf-8">
	<script src="../../ckeditor/ckeditor.js"></script>
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
</head>
<body style='background-color: #eeeeee;'>
<br>

<?php
	include "LogCheck.php";

	$mode = '';
	$htmlText = '';
	$templateN = 1;							// This is the section - start from 1st
	$id = 0;
	$msgname = "(new template)";			// For new case

	if (array_key_exists('id', $_GET))
		$id = $_GET['id'];
	if (array_key_exists('action', $_GET))
		$mode = $_GET['action'];

	echo "<h1>Template Editor</h1>";
	switch ($mode)
	{
	case 'new':
		$htmltext = newTemplate();
		break;
	case 'edit':
		$htmltext = opentemplate();
		break;
	case 'delete':
		echo deletetemplate();
		return;
	}

	echo "<h3>Editing template $msgname</h3>";
	echo "<p><button onClick='home()'>Home</button>";
	echo "&nbsp;&nbsp;";
	echo "<button type='button' onClick='tplist()'>Back to List</button></p>";
	
	echo "<div class='container'>";
		showInstructPanel($htmltext);
		echo "\n <!--   Template data -->\n";			// For view source 
		echo "\n<div class='maineditpanel' id='maineditpanel'>";
		echo $htmltext;
		echo "</div>";
		ckeditPanel();
		repairPanel();
	echo "</div>";
//

// -------------------------------------
//	Open an existing template
//
//	template id is passed as a parameter
//
//	Creates global $msgname
// -------------------------------------
function opentemplate()
{
	global $dbConnection, $msgname, $id;
	
	$sql = "SELECT * FROM templates WHERE id=" . $id;
	$result = mysqli_query($dbConnection, $sql)
		or die($sql);
	$record = mysqli_fetch_array($result);
	mysqli_free_result($result);
	$msgname = $record['name'];

	return $record['htmlText'];
}

// -------------------------------------
//	Delete a template
//
// -------------------------------------
function deletetemplate()
{
	global $dbConnection, $id;
	
	$sql = "DELETE FROM templates WHERE id=" . $id;
	$result = mysqli_query($dbConnection, $sql)
		or die($sql);
	return "Deleted";
}

// -------------------------------------
//  Construct a new template
//  Insert a new skeleton template into the
//	database
// -------------------------------------
function newTemplate()
{
	global $dbConnection, $id;

	$name = addslashes($_POST['tpname']);

	$html = buildTemplate();
	$htmls = "'" . addslashes($html) . "'";

	$sql = "INSERT INTO templates (name, htmltext) "
		. "VALUES ('$name', $htmls)";

	mysqli_query($dbConnection, $sql)
		or die (mysqli_error($dbConnection) . "<br>$sql");
	$id = $dbConnection->insert_id;

	return $html;
}

// -------------------------------------
//	Present a panel for ckEditor 
//
// -------------------------------------
function ckeditPanel()
{
	echo "<div class='editorpanel' id='editview'>";
		echo "<div style='text-align: right' onClick='closeEditor()'>X&nbsp;&nbsp; </div>";
	echo "<textarea name='ed' id='editTA' rows='15' cols='60'>aaa</textarea>";
	echo "<script>";
	echo "  CKEDITOR.replace( 'ed' );";
	echo "  </script>";
	echo "<button onClick='editPost()'>Done</button>";

	echo "</div>";
}

// ------------------------------------------
//	Present a panel for repair 
//
//	This allows the user to edit the raw HTML
// ------------------------------------------
function repairPanel()
{
	global $id;

	echo "<div class='editorpanel' id='repairPanel'>";
		echo "<div style='text-align: right' onClick='closeRPanel()'>X&nbsp;&nbsp; </div>";

	echo "<form action='TemplateRepair.php' method='post'>";
		echo "<p><input type='text' name='id' value='$id' readonly></p>";
		echo "<textarea name='ed' id='editRp' rows='20' cols='80'></textarea>";
		echo "<p><input type='submit' value='Post'></p>";
	echo "</div>";
}

// -------------------------------------
//	The left hand, instruction, panel
//
//	Prompt and a Save button
// -------------------------------------
function showInstructPanel($htmltext)
{
	global $id;

	echo "\n<div class='infopanel'>";
	echo "Click on a section to edit it";
	echo "<br>";
	echo "<p><button onClick='doSave(\"template\", $id)'>Save Edits</button><p>";

	echo "\n<p><button onClick='doRepair($id)'>Repair</button><p>";
	echo "\n</div>";
}

// ------------------------------------------------------
//	Build new template
//
// Process each line of html from the edit box
//
// Returns html with modified <td> elements
// ------------------------------------------------------
function buildTemplate()
{

	$html = $_POST['htmlText'];
	$out = '';
	$ar = explode("\n", $html);

	foreach ($ar as $line)
		$out .= makeLine($line);
	return $out;
}

// ------------------------------------------------------
//	Process one line from the input html
//
//	Input	One line of input
//
// Returns html with modified <td> elements
// ------------------------------------------------------
function makeLine($buffer)
{

	$out = $buffer;		// Default process: use the whole template line

	$st = strpos($buffer, '<');
	if ($st !== FALSE)						// Find next html tag
	{
		$tag = substr($buffer, $st+1, 2);
		if ($tag == 'td')			// Process the table data cell
			$out = processTableDataCell($buffer, $st);
	}
	
	return $out;
}

// -------------------------------------
//	Make an editable block
//
//	$st points to the <
//
//	Returns the modified <td> tag
// -------------------------------------
function processTableDataCell($buffer, $st)
{
	global $templateN;

	$id = "MCTEdit$templateN";
	$click = "onClick='editCell($templateN)'";
	$mouse = "onMouseOver='meMouseOver($templateN)' onMouseOut='meMouseOut($templateN)'";
	$ptr = $st+3;							// Point to <td
	$out = substr($buffer, 0, $ptr);

	$out .= " id='$id' ";					// Write the id
	$out .= " class='editblock'";
	$out .= " $click $mouse";
	
	$close = strpos($buffer, '>');
	$length = $close - $ptr + 1;
	$out .= substr($buffer, $ptr, $length);		// Up to 1st closing tag
//	$out .= "Section $templateN";			// This for new - use the content for edit
	$templateN++;
	
	return $out;
}

?>

<script>
function tplist()
{
	document.location.assign('Templates.php');
}

function closeRPanel()
{
	var el = document.getElementById('repairPanel');
	el.style.visibility = 'hidden';
}

function doRepair(id)
{
	var el = document.getElementById('maineditpanel');
	var htmltext = el.innerHTML;
	el = document.getElementById('editRp');
	el.value = htmltext;
	el = document.getElementById('repairPanel');
	el.style.visibility = 'visible';

}

</script>
</body>
</html>
