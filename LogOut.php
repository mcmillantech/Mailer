<?php
// -------------------------------------------------------------
//	Project	MTMail
//	File	LogOut.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//	Logs out. What did you expect?
//	Clear the user's session
// -------------------------------------------------------------

session_start();
session_unset();
session_destroy();

echo "You have been logged out<br><br>";

// require_once "LogCheck.php";

?>
