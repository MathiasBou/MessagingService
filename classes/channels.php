<?php
abstract class Channel 
{

	protected $type;
	protected $provider;

	protected $httpHeader;
	protected $httpMethod;
	protected $httpUrl;

	public function createMessage($recipient, $sender, $body, $subject) {
		return new Message($recipient, $sender, $body, $subject, $this->type);
	}

	//public abstract function getChannelBalance();

	public abstract function sendMessage(Message $message);

	protected function callMessageApi($apiData) {

		$content = is_array($apiData) ? http_build_query($apiData) : $apiData;
		$options = array(
		    'http' => array(
		        'header'  => $this->httpHeader,
		        'method'  => $this->httpMethod,
		        'content' => $content,
		    ),
		);

		$context  = stream_context_create($options);
		// add error handling here
		return file_get_contents($this->httpUrl, false, $context);
	}

	protected function trackMessageResponse(Message $message, $statusCd, $errorText, $messageId) {
		$message->setMessageResult($statusCd, $errorText, $messageId, $this->provider);
		return $message;
	}
}








?>