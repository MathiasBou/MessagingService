<?php

header('Content-type: application/json');

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




$action = @$_GET["action"];
$channel = @$_GET["channel"];
$provider = @$_GET["provider"];
$messageId = @$_GET["message"];

if($action == "messages") {
	echo json_encode(getLastMessages());
}

else if($action == "message") {
	if($channel == "SMS" && $provider == "NEXMO") {
		echo json_encode(array("messageDetails" => json_decode($channelObjs["SMS_NEXMO"]->getMessageStatus($messageId))));
	}
	else if($channel == "SMS" && $provider == "CLICKATELL") {
		echo json_encode(array("messageDetails" => json_decode($channelObjs["SMS_CLICKATELL"]->getMessageStatus($messageId),true)["data"] ));
	}
	else if($channel == "TWITTER") {
		echo json_encode(array("messageDetails" => json_decode($channelObjs["TWITTER"]->getMessageStatus($messageId))));	
	} 
	else if($channel == "TELEGRAM") {
		echo json_encode(array("messageDetails" => json_decode($channelObjs["TELEGRAM_MESSAGE"]->getMessageStatus($messageId))));	
	}
	else {
		echo json_encode(array("messageDetails" => array("info" => "Not supported by this channel / provider.")));
	}
}


else if($action == "balance") {
	if($provider == "NEXMO")
		echo json_encode($channelObjs["SMS_NEXMO"]->getChannelBalance());
	else if($provider == "CLICKATELL") {
		echo json_encode($channelObjs["SMS_CLICKATELL"]->getChannelBalance());
	}
}

else {
	echo json_encode(getCurrentStatus());
}

return;




function getCurrentStatus() {
	global $channelObjs;


	$currentStatus = array();
	$currentStatus["nexmo_balance"] = $channelObjs["SMS_NEXMO"]->getChannelBalance();
	$currentStatus["clickatel_balance"] = $channelObjs["SMS_CLICKATELL"]->getChannelBalance();
	$currentStatus["lastMessages"] = getLastMessages() ;

	return $currentStatus;
}

function getLastMessages() {
	global $dbLogger;
	$lastMessages = array();

	$selectMsgQuery = "SELECT entryId, messageId, statusCd, errorText, channel, provider, subject, sender, recipient, body, bodyTemplate, bodyData, createDttm FROM `message` WHERE createDttm > (NOW() - INTERVAL 1 DAY) ORDER BY createDttm desc";
	$selectMsgResult = $dbLogger->getLink()->query($selectMsgQuery);

	while($messageItem = $selectMsgResult->fetch_array(MYSQLI_ASSOC))
	{
		$lastMessages[] = $messageItem;
	}

	return $lastMessages;
}


?>