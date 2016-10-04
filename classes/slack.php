<?php

class SlackChannel extends Channel
{

	private $webhook_token;

	function __construct($token) {
		$this->type = "SLACK";
		$this->provider = "SLACK";

		## special Slack Webhook
		$this->httpUrl = "https://hooks.slack.com/services/".$token;
		$this->httpHeader = array("Content-type: application/json");
		$this->httpMethod = "POST";
		
		$this->webhook_token = $token;
	}

	public function getChannelBalance() {
		return -1;
	}

	public function getMessageStatus($messageId) {
		return -1;
	}

	public function createMessage($recipient, $sender, $body, $subject) {

		if(empty($recipient)) {
			// throw exception
			throw new Exception('Recipient cannot be empty to send Slack Message.');
		}

		if(empty($body)) {
			// throw exception
			throw new Exception('Body cannot be empty to send Slack Message.');
		}

		if(empty($sender)) {
			$sender = "SAS Demo";
		}

		return parent::createMessage($recipient, $sender, $body, $subject);
	}

	public function sendMessage(Message $message) {
		

		/*$attachment = array(
						array( 	"fallback" => "fallback message",
								"title" => "photo title",
								"title_link" => "link to something",
								"text" => "some text",
								"image_url" => "http://www.w3schools.com/html/html5.gif",
								"color" => "#764FA5"
						)
		);*/

		$apiData = array( 
			"channel" => $message->getRecipient(),
			"text" => $message->getBody(),
			"username" => $message->getSender()
    		//"attachments" => $attachment
		);
		
		$url = $this->httpUrl;
			//Initiate cURL.
			$ch = curl_init($url);			
			 
			//Encode the array into JSON.
			$jsonDataEncoded = json_encode($apiData);
			//echo $jsonDataEncoded;
			 
			//Tell cURL that we want to send a POST request.
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			 
			//Attach our encoded JSON string to the POST fields.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
			 
			//Set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

			//Execute the request
			$result = curl_exec($ch);
			$apiResultObj = json_decode($result, true);
			$curl_error_no = curl_errno($ch);
			$curl_error_text = curl_error($ch);
			//echo " curl_errno: ".$curl_error_no. " curl_error: ".$curl_error_text;
			//echo " result: " .$result. " result decoded: ". $apiResultObj;

			if($curl_error_no){
			    echo 'Curl error: ' . curl_error($ch);
			    echo "result: " .$result;
			}
			
			// close curl
			curl_close($ch);

		// track result
		$messageId = "";
		$errorText = "";
		$statusCd = "";
		if ($curl_error_no != 0) {
			$statusCd = -1;
			$errorText = $curl_error_text;
			$messageId = 999;
		} else if ($result != "ok") {
			$statusCd = 1;
			$messageId = "";
			$errorText = $result;
		} else {
			$statusCd = 0;
			$messageId = "";
			$errorText = $result;
		}


		parent::trackMessageResponse($message, $statusCd, $errorText, $messageId);
		
		return $statusCd == 0 ? "0 success" : $errorText;
	}

}



?>