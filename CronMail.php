<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	CronMail.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//	Send queued messages
// --------------------------------------------------------------
/*
function openConnection()
function makeMTMailObject($dbConnection, $queue)
function sendMails($queue)
function makeListSQL($dta, $row)
function endOfRun($queue, $stats)
*/

ini_set('display_errors', 1);
error_reporting(E_ALL);
    $dbConnection = openConnection();

    include "MTMail.php";			// Mailer class
    $batchSize = 50;
                    // Fetch the oldest incompleted record from the queue table
    $sql = "SELECT * FROM mailqueue WHERE status != 'complete'";
    $result = mysqli_query($dbConnection, $sql);
	if (mysqli_num_rows($result) == 0)					// Nothing in the queue
        exit;
    $queue = mysqli_fetch_array($result);
    $mtMail = makeMTMailObject($dbConnection, $queue);

    $stats = sendMails($queue, $mtMail);
    print_r($stats);
    endOfRun($queue, $stats);

    mysqli_free_result($result);

// ----------------------------------------
//	Connect to the database
//
//	Returns mysqli connection
// ----------------------------------------
function openConnection()
{
    $hfile = fopen('config.txt', 'r');
    if (!$hfile)
            die ("Could not open config file");
    $config = array();

    while (!feof($hfile))		// config.txt holds DB name, password and user
    {
            $str = fgets($hfile);
            sscanf($str, '%s %s', $ky, $val);
            $config[$ky] = $val;
    }
    fclose ($hfile);
    $dbConnection = mysqli_connect 
        ($config['dbhost'], $config['dbuser'], $config['dbpw'], $config['dbname'])
        or die("Could not connect : " . $mysqli -> error);

    return $dbConnection;
}

// ----------------------------------------
//	Construct an MTMail object and 
//	populate it with data from the
//	queue record
// ----------------------------------------
function makeMTMailObject($dbConnection, $queue)
{
    $msg = $queue['messageid'];
    $mailer = new MTmail($dbConnection);

    $stats = array (		// Data to be posted at the end of run
        'nStart' => 0,
        'nSent' => 0,
        'emStart' => '',
        'emEnd' => ''
    );

    $html = $queue['html'];	// Distribute the data from the record
    $sender = $queue['sender'];
    $subject = $queue['subject'];
    $attFile = $queue['attachment'];

    $mailer->setMessage($msg, $html);
    $mailer->setList($queue);
    $mailer->sender($sender);
    $mailer->attach($attFile);

    $mailer->setQueueData($html, $subject);

    return $mailer;
}

// ----------------------------------------------------
//	This is the workhorse
//
//	Use an MTmail object to build and send the message
//
//	Parameter	queue table record
//				MTMail object
// ----------------------------------------------------
function sendMails($queue, $mailer)
{
    global $dbConnection, $batchSize;

    $count = 0;

    $row = $queue['lastrow'];
    $list = $queue['listid'];
                        // Read the next batch of recipients from the queue
    $sql = makeListSQL($mailer->lst(), $row);

    $resultr = mysqli_query($dbConnection, $sql)
            or die("Error reading queued list " . $sql);
    while ($recipient = mysqli_fetch_array($resultr)) {	// One pass per recipient
        $recipient = $mailer->setMap($recipient);

        if ($count == 0) {  	// Set the start stats in the 1st pass
            $stats['nStart'] = $queue['lastrow'];
            $stats['emStart'] = $recipient['email'];
        }

        $mailer->sendQueued($recipient);
        $count++;
        $stats['emEnd'] = $recipient['email'];
        $stats['nSent'] = $count;
    }
    return $stats;

    mysqli_free_result($resultr);
}

// ----------------------------------------------------
//	Build the SQL to read the next batch of records
//	from the list
//
//	Parameters	Fields from the list table record
//				Last row sent from the queue
//
//	Returns the SQL string
// ----------------------------------------------------
function makeListSQL($dta, $row)
{
	global $batchSize;

	$table = $dta['rtable'];
	$col = $dta['fcol'];
	$op = $dta['fop'];
	$val = $dta['fval'];
	if ($dta['fcol'] == '')				// Build the filter
		$where = '';
	else
	{
		$where = " WHERE $col $op $val";		// I did have quotes around $val - could fail
	}
	$sql = "SELECT * FROM $table $where LIMIT $batchSize OFFSET $row";

	return $sql;
}

// ----------------------------------------------------
//	End of run
//	Update the queue and list table
//	Write the log file
//
//	Parameters	Queue record
//				Array of data
// ----------------------------------------------------
function endOfRun($queue, $stats)
{
	global $dbConnection, $batchSize;

	$endOfJob = false;
	$status = 'sending';
	$nSent = $stats['nSent'];

	if ($nSent < $batchSize)				// Test for end of this job
	{
		$endOfJob = true;
		$status = 'complete';
	}
	
	$nEnd = $stats['nStart'] + $nSent;			// Update the queue table
	$inx = $queue['inx'];
	$totalSent = $queue['totalsent'] + $nSent;
	date_default_timezone_set("Europe/London");
	
	$sDate = date('Y-m-d G:i:s');
	$sqlQ = "UPDATE mailqueue SET status='$status', totalsent=$totalSent";
	if (!$endOfJob)
		$sqlQ .= ", lastrow=$nEnd";
	$sqlQ .= " WHERE inx=$inx";
	mysqli_query($dbConnection, $sqlQ);

	if ($endOfJob)								// When job finished, update the list table
	{
		echo "----------\nEnd of Job\n";

		$list = $queue['listid'];
		$sqlL = "UPDATE maillists SET nSent=$totalSent, lastSend='$sDate' WHERE id=$list";
		mysqli_query($dbConnection, $sqlL);
		$msg = $queue['messageid'];
		$sqlM = "UPDATE mailmessages SET numbersent=$nSent, lastSend='$sDate' WHERE id=$msg";
		mysqli_query($dbConnection, $sqlM);
	}
	
	date_default_timezone_set("Europe/London");	// Write log file
	$sDate = date('d-m-Y G:i');

	$hFile = fopen('sendlog.txt', 'a');			// Finally write to the log file
	fprintf($hFile, "%16s %3d %3d %20s %3d %s\r\n",
		$sDate, $queue['listid'], $stats['nStart'], $stats['emStart'], $nEnd, $stats['emEnd']);
	fclose ($hFile);
}

?>
