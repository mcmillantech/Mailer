<?php
// -------------------------------------------------------------
//	Project	MTMail
//	File	LogCheck.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//	Check a user is logged on and stop unauthorised access
//	It also connects to the database
//
//	Called by most pages
//	A session variable is used to track timeout
// -------------------------------------------------------------

	session_start();
                                                // See if a user is logged on
                                                // If not, request log on
	if (!array_key_exists('mailuser', $_SESSION)) {
		header('Location: Logon.php');
	}

                            // Now there should be a session - process timeout
	$tm = time();		// Fetch current time, compare with last access
	$lastTime = 0;
	if (array_key_exists('lastAccess', $_SESSION))
		$lastTime = $_SESSION['lastAccess'];
                                // Timeout is hard set to 1 hour (parametrise?)
	if (($tm - $lastTime) > 3600) {
		session_unset();
		session_destroy();
		header('Location: Logon.php');
	}

	$_SESSION['lastAccess'] = $tm;
                    // Yes. As this is a common file, its' a good place to open 
                    // the database connection. Details are in the config file
                    // which is read by AjaxLogon
	$config = $_SESSION['config'];
    $dbConnection = mysqli_connect 
        ($config['dbhost'], $config['dbuser'], $config['dbpw'], $config['dbname'])
        or die("Could not connect : " . $mysqli -> error);

?>
