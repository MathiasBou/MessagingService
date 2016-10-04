<?php

class NexmoSMSChannel extends Channel
{

	private $api_key;
	private $api_secret;

	function __construct($key, $secret) {
		$this->type = "SMS";
		$this->provider = "NEXMO";

		$this->httpUrl = "https://rest.nexmo.com/sms/json";
		$this->httpHeader = array("Content-type: application/x-www-form-urlencoded");
		$this->httpMethod = "POST";
		
		$this->api_key = $key;
		$this->api_secret = $secret;
	}

	public function getChannelBalance() {
		// GET /account/get-balance/{api_key}/{api_secret}
		$url = "https://rest.nexmo.com/account/get-balance/" . $this->api_key . "/" . $this->api_secret;
		$balanceResult = file_get_contents($url);
		if(!empty($balanceResult) && $balanceResult !== false) {
			$balanceObj = json_decode($balanceResult);
			return $balanceObj->value;
		} 
		return -1;
	}

	public function getMessageStatus($messageId) {
		// GET /search/message/{api_key}/{api_secret}/{id}
		$url = "https://rest.nexmo.com/search/message/" . $this->api_key . "/" . $this->api_secret . "/" . $messageId;
		return file_get_contents($url);
	}

	public function createMessage($recipient, $sender, $body, $subject = "") {

		if(empty($recipient)) {
			// throw exception
			throw new Exception('Recipient cannot be empty to send SMS.');
		}

		if(empty($body)) {
			// throw exception
			throw new Exception('Body cannot be empty to send SMS.');
		}

		if(empty($sender)) {
			$sender = "SAS Demo";
		}

		return parent::createMessage($recipient, $sender, $body, "");
	}

	public function sendMessage(Message $message) {
		$apiData = array("api_key" => $this->api_key, "api_secret" => $this->api_secret , "type" => "text" , "from" => $message->getSender(), "to" => $message->getRecipient(), "text" => $message->getBody() );
		$apiResult = parent::callMessageApi($apiData);

		$apiResultObj = json_decode($apiResult, true);

		// track result
		$messageId = "";
		$errorText = "";
		$statusCd = $apiResultObj["messages"][0]["status"];

		if($statusCd == "0") {
			$messageId = $apiResultObj["messages"][0]["message-id"];
		} else {
			$errorText = $apiResultObj["messages"][0]["error-text"];
		}

		parent::trackMessageResponse($message, $statusCd, $errorText, $messageId);
		
		return $statusCd == 0 ? "success" : $errorText;
	}

}



?>