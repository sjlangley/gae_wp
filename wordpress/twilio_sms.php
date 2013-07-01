<?php
/**
  *
 */

require_once 'google/appengine/api/taskqueue/PushTask.php';
use \google\appengine\api\taskqueue\PushTask;

require_once('wp-config.php');
require_once "TwilioServices/Twilio.php";

if (isset($_POST['message'])) {
	$message = $_POST['message'];
	syslog(LOG_DEBUG, $message);
	if (isset($_POST['people'])) {
		// TODO -- check for array
		$people = $_POST['people'];
		sendTwilioSMSs($message, $people);
	}
	elseif (isset($_POST['person'])) {
		$person = $_POST['person'];
		$number = $_POST['number'];
		sendTwilioSMS($message, $person, $number);

	}
	else {
		syslog(LOG_WARNING, "People/person information not set.");
	}
}
else {
	syslog(LOG_WARNING, "Message not sent.");
}

function sendTwilioSMSs($message, $people) {

    // Iterate over all elements in the $people array.
    foreach ($people as $number => $name) {
        // launch a task to send a new outgoing SMS to that person
    		$task = new PushTask('/twilio_sms.php',
    			['message' => "" . $message, 'person' => $name, 'number' => $number], ['method' => 'POST']);
   			$task_name = $task->add();
        // $body = "hello, $name.  $message";
        // $client->account->sms_messages->create($from, $to, $body);
        syslog(LOG_DEBUG, "started task to send twilio message to $name.");
    }
}

function sendTwilioSMS($message, $person, $number) {

    // Instantiate a new Twilio Rest Client
    $client = new Services_Twilio(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);

    /* Your Twilio Number or Outgoing Caller ID */
    $from = '+17852699001';

    // Send a new outgoing SMS
    $body = "hello, $person.\n" . $message;
    $client->account->sms_messages->create($from, $number, $body);
        syslog(LOG_DEBUG, "Sent twilio message to $name at $number: $body");
}










