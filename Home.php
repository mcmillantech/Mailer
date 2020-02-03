<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	Home.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Show the main menu 
// --------------------------------------------------------------
	require_once "view.php";
	require_once "LogCheck.php";

	$dta = showMenu();
	showView("Home.html", $dta);

// -----------------------------------------
//	Create array of display fields for the menu
//
//	Returns	Array of values
//
//	Three user levels are provided
//		1 - only allow basic mail
//		2 - all but templates
//		3 - all
// -----------------------------------------
function showMenu()
{
	$dta = array();				// For parameters to HTML
	$items = array();			// Holds one line of the menu

	$level = $_SESSION['userLevel'];
							// Options holds a record for each entry:
							// Text of entry
							// Action - always a function call
							// Minimum user level
	$options = array (
//		array("Write and send a basic email", "doBasic()", 1),
//		array("Compose and send a newsletter", "doCompose()", 2),
		array("Basic emails", "doBasic()", 1),
		array("Newsletters", "doNewsletter()", 2),
		array("Status of sent messages", "viewSend()", 2),
		array("Manage subscriber lists", "doLists()", 2),
		array("Manage email layouts", "templates()", 3)
	);
							// Loop through the entries, create a line array for each
							// and add that to the dta array
							// Disable ones below the user's level
	for ($i=0; $i<count($options); $i++) {
		$line = array();
		$line['text'] = $options[$i][0];
		$line['call'] = $options[$i][1];
		$line['level'] = $options[$i][2];
		if ($options[$i][2] > $level)
			$line['mode'] = "disabled";
		else
			$line['mode'] = "";
		array_push($items, $line);
	}
	
	$dta['items'] = $items;
//	print_r($dta);
	return $dta;
}

?>
