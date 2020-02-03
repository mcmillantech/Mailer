<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	AjaxNewFile.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//  Ajax server to create new recipient table
//	
// --------------------------------------------------------------
	require_once "LogCheck.php";

	$sql = "SELECT * FROM mailsystem";			// Fetch next table number
	$result = mysqli_query($dbConnection, $sql);
	$sys = mysqli_fetch_array($result);
	$nextNum = $sys['nexttable'];
	mysqli_free_result($result);

	$table = "rcp_$nextNum";					// Make table name
	$newSql = "CREATE TABLE `$table` ("
		. "`id` int(10) unsigned NOT NULL auto_increment,"
		. "`list` int(10) unsigned default NULL,"
		. "`email` varchar(45) default NULL,"
		. "`business` varchar(45) default NULL,"
		. "`forename` varchar(45) default NULL,"
		. "`surname` varchar(45) default NULL,"
		. "`classes` int(10) unsigned default NULL,"
		. "`status` smallint(5) unsigned default NULL,"
		. "`joindate` datetime default NULL,"
		. "`lastmessage` datetime default NULL,"
		. "PRIMARY KEY  (`id`)"
		. ") ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;";

	mysqli_query($dbConnection, $newSql)		// ... and create the table
		or die ("Failed to create new table");

	$nextNum++;
	$sql = "UPDATE mailsystem SET nextTable = $nextNum";

	mysqli_query($dbConnection, $sql)
		or die ("Failed to update system table");

echo $table;
?>
