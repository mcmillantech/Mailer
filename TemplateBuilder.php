<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	TemplateBuild.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Template Builder
//	Just provides a form to input the name and html of template
//	Posts to TemplateEdit
// --------------------------------------------------------------
/*
*/
?>
<!DOCTYPE html>
<head>
	<title>MT Mail Template Builder</title>
	<meta charset="utf-8">
	<script src="../../ckeditor/ckeditor.js"></script>
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
	<link rel="stylesheet" type="text/css" href="../../includes/Menus.css">
</head>
<body style='background-color: #eeeeee;'>
<h1>Template Builder</h1>
	<div style='position: absolute'>
		<form action='TemplateEdit.php?action=new' method='post'>
		<span class='prompt1'>Template name</span>
		<span class='input1'><input type='text' name='tpname'></span>
		<br><br>
		<span class='prompt1'>HTML</span>
		<span class='input1'><textarea rows='15' cols='40' name='htmlText'></textarea></span>
		<br>
		<div style='position:absolute; top:300px'>
			<span class='input1'><input type='submit' value='Go'></span>
		</div>
		</form>
	</div>
</html>
