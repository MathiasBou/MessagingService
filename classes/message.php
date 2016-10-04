<?php

class Message implements JsonSerializable
{
	private $messageId;
	private $statusCd;
	private $errorText;

	private $channel;
	private $provider;

	private $subject;
	private $recipient;
	private $sender;
	private $body;

	private $bodyTemplate; // only for logging
	private $bodyData;		// only for logging
	

	function __construct($recipient, $sender, $body, $subject, $channel) {
		$this->subject = $subject;
		$this->recipient = $recipient;
		$this->sender = $sender;
		$this->body = $body;
		$this->channel = $channel;
	}

	public function setMessageResult($statusCd, $errorText, $messageId, $provider) {
		$this->messageId = $messageId;
		$this->statusCd = $statusCd;
		$this->errorText = $errorText;
		$this->provider = $provider;
	}

	public function getMessageId() {
		return $this->messageId;
	}

	public function getStatusCd() {
		return $this->statusCd;
	}

	public function getErrorText() {
		return $this->errorText;
	}

	public function getChannel() {
		return $this->channel;
	}

	public function getProvider() {
		return $this->provider;
	}


	public function getSubject() {
		return $this->subject;
	}


	public function getRecipient() {
		return $this->recipient;
	}


	public function getSender() {
		return $this->sender;
	}


	public function getBody() {
		return $this->body;
	}

	public function getBodyTemplate() {
		return $this->bodyTemplate;
	}

	public function getBodyData() {
		return $this->bodyData;
	}

	public function personalizeBody($template, $dataArray) {
		$this->bodyTemplate = $template;
		$this->bodyData = json_encode($dataArray);

		foreach($dataArray as $key=>$value)
		{
			$searchFor = "{{" . $key . "}}";
			$replaceWith = $value;
			$template = str_replace($searchFor, $replaceWith, $template);
		}

		$this->body = $template;
	}


	public function jsonSerialize() {
		$jsonArray = array();
	    foreach($this as $key => $value) {
	        $jsonArray[$key] = $value;
	    }
    	return $jsonArray;
	}


}











?>