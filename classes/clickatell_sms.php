<?php

class ClickatellSMSChannel extends Channel
{

	private $auth_token;


	function __construct($auth_token) {
		$this->type = "SMS";
		$this->provider = "CLICKATELL";

		$this->httpUrl = "https://api.clickatell.com/rest/message";
		$this->httpHeader = array("X-Version: 1", "Content-type: application/json", "Authorization: Bearer $auth_token", "Accept: application/json" );
		$this->httpMethod = "POST";
		
		$this->auth_token = $auth_token;
	}

	public function getChannelBalance() {
		$url = "https://api.clickatell.com/rest/account/balance";
		$options = array(
		    'http' => array(
		        'header'  => array("X-Version: 1", "Authorization: Bearer $this->auth_token", "Accept: application/json" ),
		        'method'  => "GET",
		    ),
		);
		$context  = stream_context_create($options);
		$balanceResult = file_get_contents($url, false, $context);

		if(!empty($balanceResult) && $balanceResult !== false) {
			$balanceObj = json_decode($balanceResult, true);
			return $balanceObj["data"]["balance"];
		} 

		return -1;
	}



	public function getMessageStatus($messageId) {
		// GET https://api.clickatell.com/rest/message/[message ID]
		$url = "https://api.clickatell.com/rest/message/" . $messageId;
		$options = array(
		    'http' => array(
		        'header'  => array("X-Version: 1", "Authorization: Bearer $this->auth_token", "Accept: application/json" ),
		        'method'  => "GET",
		    ),
		);

		$context  = stream_context_create($options);
		$messageStatus = file_get_contents($url, false, $context);

		return $messageStatus;
	}


	public function createMessage($recipient, $sender, $body, $subject = "") {
		// TODO: validate inputs!
		
		if(empty($recipient)) {
			// throw exception
			throw new Exception('Recipient cannot be empty to send SMS.');
		}

		if(empty($body)) {
			// throw exception
			throw new Exception('Body cannot be empty to send SMS.');
		}
		

		return parent::createMessage($recipient, "Clickatell Number", $body, "");
	}

	public function sendMessage(Message $message) {
		$apiData = json_encode(array("to" => array($message->getRecipient()), "text" => $message->getBody() ));

		$apiResult = parent::callMessageApi($apiData);

		$apiResultObj = json_decode($apiResult, true);

		// track result
		$messageId = "";
		$errorText = "";
		$statusCd = $apiResultObj["data"]["message"][0]["accepted"] ? 0 : 1;
		
		if($statusCd == 0) {
			$messageId = $apiResultObj["data"]["message"][0]["apiMessageId"];
		} else {
			$errorText = "Message was not accepted by CLICKATELL";
		}
		
		parent::trackMessageResponse($message, $statusCd, $errorText, $messageId);
		
		return $statusCd == 0 ? "success" : $errorText;
	}

}



?>