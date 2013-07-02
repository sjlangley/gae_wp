<?php
/**
  * Task handler
 */

require_once 'google/appengine/api/taskqueue/PushTask.php';
use \google\appengine\api\taskqueue\PushTask;

require_once "TwilioServices/Twilio.php";
require_once('tw-config.php');

if (isset($_POST['message'])) {
	$message = $_POST['message'];
	syslog(LOG_DEBUG, $message);
	// if this handler was called with an array of phone/name entries
	if (isset($_POST['people'])) {
		// TODO -- check for array
		$people = $_POST['people'];
		sendTwilioSMSs($message, $people);
	}
	elseif (isset($_POST['person'])) { // else, individual person's info
		$person = $_POST['person'];
		// TODO -- validity check on vars
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

//launch an SMS notification task for each person in the given $people array
function sendTwilioSMSs($message, $people) {

    // Iterate over all elements in the $people array.
	  // iterate multiple times for temp larger-scale task deployment demo
	  $annoying_count = 2;
		for ( $i = 0; $i < $annoying_count; $i++ ) {
	    foreach ($people as $number => $name) {
	    		$cname = $name . " $i";
	        // launch a task to send a new outgoing SMS to that person
	    		$task = new PushTask('/twilio_sms.php',
	    			['message' => $message, 'person' => $cname, 'number' => $number],
	    			['method' => 'POST']);
	   			$task_name = $task->add();
	        syslog(LOG_DEBUG, "started task to send twilio message to $cname.");
	    }
	  }
}

// send an outgoing SMS to the given name at the given number.
function sendTwilioSMS($message, $person, $number) {

    // Instantiate a new Twilio Rest Client
    $client = new Services_Twilio(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);

    /* Your Twilio Number or Outgoing Caller ID */
    $from = '+17852699001';

    // Send a new outgoing SMS
    $body = "hello, $person.\n" . $message;
    $client->account->sms_messages->create($from, $number, $body);
    syslog(LOG_DEBUG, "Sent twilio message to $person at $number: $body");
}










