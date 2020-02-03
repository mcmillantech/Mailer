<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	AjaxLogOn.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Ajax server for log on
//
//	Checks the user name and password and if OK, sets
//	both the user and mysqli connection in the session
// --------------------------------------------------------------

	global $name;
	$name = $_GET['name'];
	$config = setConfig();
	$user = $config['dbpw'];

	$dbConnection = mysqli_connect ('localhost', $config['dbuser'], $config['dbpw'])
		or die("Could not connect : " . mysqli_connect_error());

	mysqli_select_db($dbConnection, $config['dbname']) 
		or die("Could not select database : " . mysqli_error($dbConnection));

	$sql = "SELECT * FROM mailusers WHERE name='$name'";
	$result = mysqli_query($dbConnection, $sql)
		or die("Select error : " . mysqli_error($dbConnection));
	if (mysqli_num_rows($result) == 0)
		echo "Error: user not found";
	else checkPassword($result);
		
	mysqli_free_result($result);

function checkPassword($result)
{
	$pw = $_GET['pw'];
	$user = mysqli_fetch_array($result);
	if ($user['password'] == $pw)
	{
		logOn();
		$_SESSION['userLevel'] = $user['level'];
	}
	else
		echo "Error: wrong password";
}

function logOn()
{
	session_start();
	global $name;
	$_SESSION['mailuser'] = $name;
	setConfig();

	$tm = time();
	$_SESSION['lastAccess'] = $tm;
	echo 'OK';
}

// ----------------------------------------------
//	Read the database access parameters from
//	the cofig file
//
// ----------------------------------------------
function setConfig()
{
	$hfile = fopen('config.txt', 'r');
	if (!$hfile)
		die ("Could not open config file");
	$config = array();
	while (!feof($hfile))
	{
		$str = fgets($hfile);
		sscanf($str, '%s %s', $ky, $val);
		$config[$ky] = $val;
	}
	fclose ($hfile);
	$_SESSION['config'] = $config;
	return $config;
}

?>
