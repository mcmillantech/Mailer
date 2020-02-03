<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	MessageSend.php
//
//	Author	John McMillan
//  McMillan Technology
//
//	Perform the actual send
//
//	Called from messagePost
//
//	Creates an MTMail object
// --------------------------------------------------------------
/*
*/
?>
<!DOCTYPE html>
<html>
<head>
<title>Mailer send messages</title>
	<meta charset="utf-8">
	<script src="MailEdit.js"></script>
	<link rel="stylesheet" type="text/css" href="MailEdit.css">
	<link rel="stylesheet" type="text/css" href="../../includes/Menus.css">
</head>

<body>
<h1>Mailer: Send Message</h1>
<?php
	include "LogCheck.php";
	include "MTMail.php";

	$msgId = $_GET['id'];
	$subject = $_POST['subject'];
	$sender = $_POST['sender'];
	$lstid = $_POST['rlist'];
	$attachStr = $_POST['attachStr'];

	date_default_timezone_set("Europe/London");	// Today's date
	$dtSQL = date('Y-m-d G:i:s');

	$sql = "SELECT name, htmltext FROM mailmessages WHERE id=$msgId";
	$result = mysqli_query($dbConnection, $sql)
		or die ("SQL error " . mysqli_error($dbConnection) . $sql);
	$stdMail = mysqli_fetch_array($result);
	$htmltext = $stdMail['htmltext'];
	$name = $stdMail['name'];
	mysqli_free_result($result);

	$mailer = new MTmail($dbConnection);
	$mailer->setMessage($msgId, $htmltext);
	$mailer->sender($sender);
	$mailer->subject($subject);
	$mailer->attach($attachStr);

	$sendTo = $_POST['bmto'];
	if ($sendTo == 'Single')
		$mailer->sendToOneAddress($_POST['bmOne']);
	else if ($sendTo == 'List')
		$mailer->sendMailToQueue($lstid);

	echo "<br><button onClick='goHome()'>Home</button>";

?>
<script>
function goHome()
{
	document.location.href="Home.php";
}

</script>
</body>
</html>