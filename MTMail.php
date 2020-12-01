<?php
// -------------------------------------------------------------
//  Project	Emailer
//	File	MTMail.php
//
//	Author	John McMillan
//  McMillan Technology Ltd
//
//	Class to implement mail via Pear mail
//
//	There are 3 main call points
//	sendToOneAddress simply generates the message from the 
//		class properties and sends it
//	sendMailToQueue is the normal call from the application.
//		It takes the message content, subject etc and places in 
//		the queue table for queued sending
//	sendQueued is called by the CRON job. This checks for 
//		incomplete jobs. For each, it fetches the send list
//		and processes a batch of recipients. For each, it
//		merges the recipient fields, and generates and sends
//		the message
// --------------------------------------------------------------
/*
function __construct($dbConnection)
public function sender($sender)
public function messageName($name)
public function subject($subject)

public function sendMailToQueue($listId)	// Call points
public function sendToOneAddress($email)
public function sendQueued($record)

public function lst()						// Properties
public function text()
public function headers()

public function setMessage($id, $html)
public function sender($sender)
public function messageName($name)
public function subject($subject)
public function attach($attFile)
public function setPearMail($pearMail)
public function setMap($recipient)
public function setList($queue)
public function setQueueData($html, $subject)

private function generate()					// Private functions
private function sendOn($email)
private function merge($htxt, $recipient)
private function makeLines($text)
private function plainText($htxt)
private function mergeField($htxt, $pt1, $pt2, $recipient)
private function makeHeaders($dbConnection)
private function writeSentMessage()
private function lookUpMessageName()
*/
echo "<br>MTMail 12 \n";

define ("LOCAL", 0);
if (LOCAL == 0)
{
    require_once "/usr/share/pear/Mail.php";
    require "/usr/share/pear/Mail/mail.php";
    require "/usr/share/pear/Mail/mime.php";
}


class MTMail
{
    private	$id;
    private     $list;
    private	$htmlLines;
    private	$plainLines;
    private	$subject;
    private	$sender;
    private     $messageName;
    private	$rawHtml;	// This is the html from the app. It includes template content
    private	$attFile;
    private	$text;		// Final body text to be sent
    private	$headers;
    private	$pearMail;
    private	$boundary;
    private	$mergedHtml;
    private	$mergedPlain;

    // ----------------------------------------------

    function __construct($dbConnection)
    {
        global	$dbConnection;

        $list = array();
        $this->boundary = uniqid('np');		//create a boundary for the email - is this needed?
        $this->makeHeaders($dbConnection);
    }

//	----------  Set attributes
    public function sender($sender)
    {
        $this->sender = $sender;
    }

    public function messageName($name)
    {
        $this->messageName = $name;
    }

    public function subject($subject)
    {
        $this->subject = $subject;
        $headers['Subject'] = $subject;
    }

//	----------  Return attributes
    public function lst()
    {
        return $this->list;
    }
    public function text()
    {
        return $this->text;
    }
    public function headers()
    {
        return $this->headers;
    }
///	
    public function setMessage($id, $html)
    {
        global	$dbConnection;

        $this->id = $id;
        $this->rawHtml = $html;
    }

// --------------------------------------------
//	Set an attachment
//
//	Called by MessageSend
// --------------------------------------------
    public function attach($attFile)
    {
        $this->attFile = $attFile;
    }

// ----------------------------------------------------
//	Map the sending field names (e.g. email)
//
//	Parameter	Database record for recipient
//
//	Returns		Record modified for actual column names
//
//	The list (table) holds the names of the columns in 
//	the data table (e.g. contacts, rcp.. to be merged
//	Map the merge records to these columns
// ----------------------------------------------------
    public function setMap($recipient)
    {
        $em = $this->list['email'];
        $fname = $this->list['forename'];
        $sname = $this->list['surname'];
        $co  = $this->list['business'];

        $recipient['email'] = $recipient[$em];
        if ($co != '') {
            $recipient['business'] = $recipient[$co];
        }
        $recipient['forename'] = $recipient[$fname];
        $recipient['surname'] = $recipient[$sname];

        return $recipient;
    }
    // --------------------------------------------------------
    //  Fetch the maillist record for the send list
    //	This holds the map of user's column names to ours
    //
    //	Store the maillist record in the class list property
    // --------------------------------------------------------
    public function setList($queue)
    {
        global	$dbConnection;

        $sqlm = "SELECT * FROM maillists WHERE id='" . $queue['listid'] . "'";
        $resultm = mysqli_query($dbConnection, $sqlm)
            or die("Mapping error " . mysqli_error($dbConnection) . $sqlm);
        $this->list = mysqli_fetch_array($resultm);
        mysqli_free_result($resultm);
    }

    // ----------------------------------------------
    //	This is called by the CRON job
    //
    //	Set the body and subject
    // ----------------------------------------------
    public function setQueueData($html, $subject)
    {
            $this->rawHtml = $html;

            $this->subject = $subject;
            $this->headers['Subject'] = $subject;
    }

// --------------------------------------------
//	Send mail to named recipient by email addr
//
//	Paramter	recipient's email
//
//	Note - merge isn't used here - there
//	are no merge fields.
// --------------------------------------------
    public function sendToOneAddress($email)
    {
//            echo "Sending message to $email ";
        $cleanedHtml = $this->stripMTEditHandlers($this->rawHtml);
        $this->mergedHtml = $this->merge($cleanedHtml, $record);
        $this->mergedPlain = $this->plainText($this->mergedHtml);
//		$this->mergedHtml = $this->htmlLines;
//		$this->mergedPlain = $this->htmlLines;
            $this->generate();
            $this->headers['From'] = $this->sender;
            $this->sendOn($email);
    }


// --------------------------------------------
//	Call point for Send mail to a list
//
//	Insert the details into the mail queue
//
//	called from BasicMail2 and GenerateNews
// --------------------------------------------
    public function sendMailToQueue($listId)
    {
        global	$dbConnection;

        $t=time();
        $dt = date('Y-m-d G:i:s');

        $sql = "INSERT INTO mailqueue "
            . "(messageid, listid, queuetime, lastrow, subject, sender, html, status, attachment) "
            . "VALUES ("
            . "$this->id,"
            . "'$listId',"
            . "'$dt',"
            . "0,"
            . "'" . addslashes($this->subject) . "',"
            . "'" . addslashes($this->sender) . "',"
            . "'" . addslashes($this->rawHtml) . "',"
            . "'new',"
            . "'" . $this->attFile . "')";
//echo "$sql<br>";
        mysqli_query($dbConnection, $sql)
            or die("Error in MTMail: $sql" . mysqli_error($dbConnection));

        $this->lookUpMessageName();
        $this->writeSentMessage();

        echo "Thank you. Your email has been scheduled for sending";
    }

// -----------------------------------------------
//	Send one message from the queue
//	This is the call point from the CRON job
//
//	Parameter   Record for the recipient
//                  i.e. his email and merge data
// -----------------------------------------------
    public function sendQueued($record)
    {
        $to = $record['email'];
        if ($to == '')	// This deals with entries with missing email addresses
            return;

        $cleanedHtml = $this->stripMTEditHandlers($this->rawHtml);
        $this->mergedHtml = $this->merge($cleanedHtml, $record);
        $this->mergedPlain = $this->plainText($this->mergedHtml);

        $this->generate($record);
        $this->headers['To'] = $to;
        $this->headers['From'] = $this->sender;
//echo "SendQueued<br>\n";
        if (LOCAL == 0) {
//                echo "Not local";
            $crlf = "\n";
            $mime = new Mail_mime($crlf);
            $mime->setHTMLBody($this->mergedHtml);
            $mime->setTXTBody($this->mergedPlain);
            if ($this->attFile != '') {
                $this->addAttachments($mime);
            }
            $body = $mime->get();
            $headers = $mime->headers($this->headers);
//	print_r($headers);
//                $mail =& Mail::factory('sendmail', $this->params);
            $mail = & Mail::factory('mail');
            $result = $mail->send($to, $headers, $body);
    if ($result != TRUE) 
        echo "Mail failed ";
    
            echo "MTMail sendQueued success<br>";
        }
        else {			// Development mode - running on local PC
            echo "\n<br>$to<br>";
    echo "Local<br>\n";
            if ($this->attFile != '') {
                $this->addAttachments('', true);
            }
            echo $this->mergedHtml;
            echo "Sender " . $this->headers['From'];
            echo "----------------<br>\n";
        }
    }
	
// -----------------------------------------------
//	Add the attachments to the MIME object
//
//	Attachments are held in a comma separated list
//
//	Parameter	The MIME object
// -----------------------------------------------
    private function addAttachments($mime, $test=false)
    {
        $attachStr = $this->attFile;
        if ($attachStr == "")				// No attachments
                return;

        $attFiles = explode(',', $attachStr);
        $count = count($attFiles) - 1;
//        echo "count $count ";
//$test=TRUE;
//		foreach ($attFiles as $file) {
        $path = getcwd() . "/public_html/mail/Uploads/";
        for ($i=0; $i<$count; $i++) {
            $upFile = $attFiles[$i];
            $upFile = $path . $attFiles[$i];
            if (!$test) {
                $rp = $mime->addAttachment($upFile);
//        echo " Att reply $rp, $upFile\n";
            }
            else echo " File $upFile\n";
        }
    }

// -------------------------------------------------
// Look up message name
// -------------------------------------------------
	private function lookUpMessageName()
	{
		global	$dbConnection;

		$sql = "SELECT name FROM mailmessages WHERE id=$this->id";
		$result = mysqli_query($dbConnection, $sql)
			or die("Look up message error " . mysqli_error($dbConnection) . " $sql");
		$record = mysqli_fetch_array($result);
		$this->messageName = $record['name'];
		mysqli_free_result($result);
	}

// ------------------------------------------------------
//	Send one actual email
//
//	Parameter	Recipient address
//
//	The headers must be set up before this is called
//	Construct a Pear Mail object and call its send method
// -------------------------------------------------------
	private function sendOn($email)
	{
		$this->headers['To'] = $email;
		$this->headers['Subject'] = $this->subject;
		$to = $email;

		$params['sendmail_path'] = '/usr/lib/sendmail';
		if ($_SERVER['SERVER_NAME'] != 'localhost')
		{
			$mail =& Mail::factory('sendmail', $params);
			$result = $mail->send($to, $this->headers, $this->text);
//			echo "MTMail sendOn dump<br>";
//			var_dump($result);
		}
		else echo "Send on $this->text";			// For local debugging
	}

// --------------------------------------------
//	Generate the message 
//
//	Merge data into both plain and html lines
//	Set up the encoding etc
// --------------------------------------------
	private function generate()
	{
		global	$dbConnection;
		
		$message = "This is a MIME encoded message.";
		$message .= "\r\n\r\n--" . $this->boundary . "\r\n";
		$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
		$message .= $this->mergedPlain;

		$message .= "\r\n\r\n--" . $this->boundary . "\r\n";
		$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
	
		$message .= $this->mergedHtml . "\r\n\r\n--" . $this->boundary . "--";
		$this->text = $message;
	}

// --------------------------------------------
//	Process the merge
//
//	Returns the merged HTML
// --------------------------------------------
    private function merge($htxt, $recipient)
    {
        $ptr = strpos($htxt, '{', 0);		// Start of 1st placeholder
        if (!$ptr)									// There aren't any - use the raw text
            return $htxt;

        $result = substr($htxt, 0, $ptr);	// Text before 1st ph

        $pt2 = strpos($htxt, '}', $ptr) + 1;	// End of ph + 1 psn
        $result .= $this->mergeField($htxt, $ptr, $pt2, $recipient);

        while ($ptr != 0)
        {
            $ptr = strpos($htxt, '{', $pt2);	// Start of next section
            if (!$ptr)								// No more tags
            {
                $result .= substr($htxt, $pt2);
                break;
            }
            $len = $ptr - $pt2;
            $result .= substr($htxt, $pt2, $len);
            $pt2 = strpos($htxt, '}', $ptr) + 1;	// End of ph + 1 psn
            $result .= $this->mergeField($htxt, $ptr, $pt2, $recipient);
        }
        return $result;
	}

// -----------------------------------------------
// 	Build lines < 70 characters long
//
//	Returns string with long lines broken up
// -----------------------------------------------
	private function makeLines($text)
	{
		$lines = explode("\n", $text);			// Start by breaking message into lines

		$splitText = '';						// This is the output string
		foreach ($lines as $str)				// Now process each line
		{
			if (strlen($str) > 70)				// Process lines longer than 70 characters
			{
				$tr = 0;
				while (strlen($str) > 70)		// Loop through long lines
				{
					$tstStr = substr($str, 0, 70);
					$psn = strrpos($tstStr, ' ');	// Break each line on a space
					if ($psn == false)
						$psn = strrpos(substr($str, 0, 70), '>');	// ... or tag
					if ($psn == false)
						$psn = strlen($str);		// Give up and hope
													// Append this text and a line break
					$splitText .= substr($str, 0, $psn) . "\r\n"; 
					$str = substr($str, $psn);
					if ($tr++ > 10)
						die ("Looping making lines: $str ");
				}
				$splitText .= "$str\r\n";					// Finish the lines as RFC...
			}
			else
				$splitText .= "$str\r\n";					// OK, this line was < 70 characters
		}
		return $splitText;
	}

// --------------------------------------------
//	Generate plain text
//
// --------------------------------------------
	private function plainText($htxt)
	{
		$ptr = strpos($htxt, '<', 0);			// Start of 1st tag
		$out = substr($htxt, 0, $ptr);			// Text before 1st tag
		$pt2 = strpos($htxt, '>', $ptr) + 1;	// End of tag + 1 psn
		$len = strlen($htxt);

		do
		{
			if ($pt2 > $len)
				break;
			$ptr = strpos($htxt, '<', $pt2);		// Start of next section
			$thisLen = $ptr - $pt2;
			$out .= substr($htxt, $pt2, $thisLen);
			$pt2 = strpos($htxt, '>', $ptr) + 1;	// End of tag + 1 psn
		} while ($ptr != 0);
		return $out;
	}

// --------------------------------------------
//	Set one merged field
//
//	Parameters 
//		the raw text string
//		pt1 and p2t point to the merge tag,
//			i.e. between braces
//		the record from the recipient table
//
//	Extract the tag name.
//	If the tag is invalid, ignore
//	Else, return the field from the table
// --------------------------------------------
	private function mergeField($htxt, $pt1, $pt2, $recipient)
	{
		$len = $pt2 - $pt1 - 2;
		$key = substr($htxt, $pt1+1, $len);

		if (!array_key_exists($key, $recipient))
			return '';
		return $recipient[$key];
	}

// --------------------------------------------
//	Build the mail headers
//
// --------------------------------------------
	private function makeHeaders($dbConnection)
	{
		$headers['From'] = $this->sender;
		$headers['Subject'] = $this->subject;
		$headers['MIME-Version'] = "1.0";
		$headers['Content-Type'] = "multipart/alternative;boundary=" 
			. $this->boundary;

		$this->headers = $headers;
	}

// ------------------------------------------
//  Strip out the handlers used to edit the
//	sections (onClick, onMouse...)
// ------------------------------------------
	private function stripMTEditHandlers($html)
	{
								// Locate each edit block: between $block0 and $blockn
		$psn = 0;
		$block0 = strpos($html, 'editblock', $psn);
		while ($block0 != false)
		{
			$blockn = strpos($html, '>', $block0);
			$len = $blockn-$block0+1;
			$psn = $blockn;
		
			$editBlock = substr($html, $block0, $len);
			$editBlock = $this->removeHandler($editBlock, 'onclick', 12);
			$editBlock = $this->removeHandler($editBlock, 'onmouse', 20);
			$editBlock = $this->removeHandler($editBlock, 'onmouse', 20);
			$html = substr_replace($html, $editBlock, $block0, $len);
	
			$block0 = strpos($html, 'editblock', $psn);
		}
		return $html;
	}

// --------------------------------------------------------
//	Remove one handler from the template html
//
//	Parameters
//		The contents of the td tag from 'editBlock'
//		The event
//		Number of chars after event before checking for "
//	Returns
//		The input, with the handler removed
// --------------------------------------------------------
    private function removeHandler($block, $event, $chars)
    {
                                    // Locate handler: from $h0 to $hn	
        $h0 = strpos($block, $event, 0);
        if (!$h0)
                return $block;

        $hn = strpos($block, '"', $h0+$chars);
        $len = $hn - $h0 + 1;		// start and length of handler

        $htm2 = substr_replace($block, '', $h0, $len);	// Remove it
        return $htm2;
    }


// -------------------------------------------------
// Write to messages table
// -------------------------------------------------
    private function writeSentMessage()
    {
        global	$dbConnection;

        $t=time();
        $dt = date('Y-m-d G:i:s');

        $sql = "INSERT INTO sentmessages "
            . "(name, sender, subject, lastsend, htmltext) VALUES ("
            . "'$this->messageName',"
            . "'$this->sender',"
            . "'" . addslashes($this->subject) . "',"
            . "'$dt',"
            . "'" . addslashes($this->htmlLines) . "')";
        mysqli_query($dbConnection, $sql)
            or die("writeSentMessage error " 
                . mysqli_error($dbConnection) . " $sql");
	}

} 

?>
