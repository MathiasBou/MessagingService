<?php

include_once("../classes/ext/TwitterAPIExchange.php");

class TwitterChannel extends Channel
{

	private $auth_settings;


	function __construct($auth_settings) {
		$this->type = "TWITTER";
		$this->provider = "PUBLIC";

		$this->httpUrl = "";
		$this->httpHeader = "";
		$this->httpMethod = "";
		
		$this->auth_settings = $auth_settings;
	}

	public function getChannelBalance() {

		return -1;
	}

	public function getMessageStatus($messageId) {
		$serviceUrl = "https://api.twitter.com/1.1/statuses/lookup.json";

		$twitter = new TwitterAPIExchange($this->auth_settings);
		$apiResult = $twitter->setGetfield("?id=".$messageId)->buildOauth($serviceUrl, "GET")->performRequest();
		return $apiResult;
	}

	public function createMessage($recipient, $sender, $body, $subject = "") {

		if(empty($body)) {
			// throw exception
			throw new Exception('Body cannot be empty to send Tweets.');
		}

		return parent::createMessage($recipient, $sender, $body, "");
	}

	public function sendMessage(Message $message) {
		$serviceUrl = "";
		$apiData = null;


		if(!empty($message->getRecipient())) {
			$serviceUrl = 'https://api.twitter.com/1.1/direct_messages/new.json';
			$apiData = array("screen_name" => $message->getRecipient(), "text" => $message->getBody());
		} else {
			$serviceUrl = 'https://api.twitter.com/1.1/statuses/update.json'; 
			$apiData = array("status" => $message->getBody());
		}


		$twitter = new TwitterAPIExchange($this->auth_settings);
		$apiResult = $twitter->setPostfields($apiData)->buildOauth($serviceUrl, "POST")->performRequest();
		$apiResultObj = json_decode($apiResult, true);

		// track result
		$messageId = "";
		$errorText = "";
		// check errors
		if(array_key_exists("errors" , $apiResultObj)) {
			$statusCd = $apiResultObj["errors"][0]["code"];
			$errorText = $apiResultObj["errors"][0]["message"];
		} else {
			$statusCd = 0;
			$messageId = $apiResultObj["id_str"];
		}
		
		parent::trackMessageResponse($message, $statusCd, $errorText, $messageId);
		
		return $statusCd == 0 ? "success" : $errorText;
	}

}



?>