<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');  


include_once('../classes/channels.php');
include_once("../classes/nexmo_sms.php");
include_once("../classes/clickatell_sms.php");
include_once("../classes/sample_email.php");
include_once("../classes/twitter.php");
include_once('../classes/telegram.php');
include_once('../classes/slack.php');


include_once("../classes/database_logger.php");
include_once("../classes/message.php");
include_once("credentials.php");


$channel 			= @$_POST['channel'];
$provider 			= @$_POST['provider'];
$subject 			= @$_POST['subject'];
$sender 			= @$_POST['sender'];
$recipient 			= @$_POST['recipient'];
$body 				= @$_POST['body'];
$bodyVariablesCSV	= @$_POST['bodyVariablesCSV'];
$bodyVariablesJSON	= @$_POST['bodyVariablesJSON'];
$clientIpAddress	= $_SERVER['REMOTE_ADDR'];


// check parameters
if(empty($channel) || empty($body)) {
	// print usage!
	echo json_encode(array("errorText" => "wrong usage. Missing required parameters: channel, provider, recipient and body."));
	return;
}

try {

	$bodyDataArray = parseContextData($bodyVariablesCSV);

	switch ($channel) {
			case 'SMS':
				if($provider == "NEXMO") {
					echo json_encode(sendMessage($channelObjs["SMS_NEXMO"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				}
				else if($provider == "CLICKATELL") {
					echo json_encode(sendMessage($channelObjs["SMS_CLICKATELL"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				}
				else {
					// if us Number than route through CLICKATELL
					if(substr($recipient, 0, 1) == "1" ||  substr($recipient, 0, 2) == "+1") {
						echo json_encode(sendMessage($channelObjs["SMS_CLICKATELL"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
					} else {
						echo json_encode(sendMessage($channelObjs["SMS_NEXMO"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
					}
				}
				break;

			case 'TELEGRAM':
				
				if ($clientIpAddress == "::1") {
					//echo " send from localhost ";
				} else {
					$subject = str_replace("sasbap.demo.sas.com",$clientIpAddress,$subject);
				}

				echo json_encode(sendMessage($channelObjs["TELEGRAM_MESSAGE"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				break;

			case 'SLACK':
				echo json_encode(sendMessage($channelObjs["SLACK_MESSAGE"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				break;

			case 'TWITTER':
				// AUTO Provider
				echo json_encode(sendMessage($channelObjs["TWITTER_PUBLIC"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				break;

			case 'EMAIL':
				if($provider == "HTML") {
					echo json_encode(sendMessage($channelObjs["EMAIL_HTML"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				}
				else if($provider == "TEXT") {
					echo json_encode(sendMessage($channelObjs["EMAIL_TEXT"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				}
				else {
					// AUTO provider
					echo json_encode(sendMessage($channelObjs["EMAIL_HTML"], $recipient, $sender, $body, $subject, $bodyDataArray, true));
				}
				break;
				
			default:
				// invalid channel
				echo json_encode(array("errorText" => "Channel not valid. Please use EMAIL or SMS instead."));
			break;
	}
} catch (Exception $e) {
	echo json_encode(array("errorText" => $e->getMessage()));
}

return;



function sendMessage($channelObj, $recipient, $sender, $body, $subject, $bodyDataArray, $logUsage) {
	global $dbLogger;
	$myMessage = $channelObj->createMessage($recipient, $sender, $body, $subject);
	$myMessage->personalizeBody($body, $bodyDataArray);
	$result = $channelObj->sendMessage($myMessage);
	$dbLogger->logMessage($myMessage);

	return $myMessage;
}


function parseContextData ($contextDataCSV) {
	$resultDataArray = array();
	$contextDataJunks = explode(";", $contextDataCSV);
	for($i = 0; $i < count($contextDataJunks); $i++) {
		$contextData = explode("=", $contextDataJunks[$i]);

		if(count($contextData) == 1) {
			$index = $i+1;
			$resultDataArray[''.$index] = $contextData[0];
		}

		else if(count($contextData) == 2) {
			$resultDataArray[$contextData[0]] = $contextData[1];
		} 

	}

	return $resultDataArray;
}




?>