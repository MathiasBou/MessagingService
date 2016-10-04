<?php

class TelegramChannel extends Channel
{

	private $bot_token;

	function __construct($token) {
		$this->type = "TELEGRAM";
		$this->provider = "TELEGRAM";

		## special Telegram SAS CI Bot
		$this->httpUrl = "https://api.telegram.org/bot".$token;
		$this->httpHeader = array("Content-type: application/json");
		$this->httpMethod = "POST";
		
		$this->bot_token = $token;
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
			throw new Exception('Recipient cannot be empty to send Telegram Message.');
		}

		if(empty($body)) {
			// throw exception
			throw new Exception('Body cannot be empty to send Telegram Message.');
		}

		if(empty($sender)) {
			$sender = "SAS Demo";
		}

		return parent::createMessage($recipient, $sender, $body, $subject);
	}

	public function sendMessage(Message $message) {
		$messageId = "";
		$errorText = "";
		$statusCd = "";

		$apiData = array(
			"chat_id" => $message->getRecipient(), 
			"text" => $message->getBody() 
		);

		$url = $this->httpUrl ."/sendMessage";
		$header = $this->httpHeader;
		$apiResultObj = "";


		// check the subject for sending a photo
		if($message->getSubject() != "") {
			$url = $this->httpUrl ."/sendPhoto";
			$urlToImage = $message->getSubject();

			// download file first from urlToImage location
			$pictureFileName = "../download/" . $message->getRecipient() . "_" . uniqid() . ".png";
			
			if ( !@getimagesize($urlToImage) ) {
				$statusCd = -1;
				$errorText = "Image not found - check url: " . $urlToImage;
				//return new Exception('Image file not found. Check Url in subject');
      		} else {
				$fileOpen = fopen($urlToImage, 'rb');

				file_put_contents($pictureFileName, $fileOpen);

				$pathToDownloadedImage = str_replace('\\','/',getcwd()) . "/" . $pictureFileName;

				$apiData = array(
					"chat_id" => $message->getRecipient(),
					"photo"   => new CURLFile($pathToDownloadedImage),
					"caption" => $message->getBody()
				);
				$header = array("Content-type: multipart/form-data");

				
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt($ch, CURLOPT_POSTFIELDS, $apiData); 			
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$result = curl_exec($ch);			
				$apiResultObj = json_decode($result, true);

				//delete downloaded picture
				unlink($pictureFileName);

				// check if curl returned an error
				if(curl_errno($ch)){
					echo 'Curl error: ' . curl_error($ch);
					echo $result;
				}
				
				curl_close($ch);
			}
		} else {  //if subject is not set then only send text message

			$ch = curl_init($url);
				 
			//Encode the array into JSON.
			$jsonDataEncoded = json_encode($apiData);
			//echo $jsonDataEncoded;
				 
			//Tell cURL that we want to send a POST request.
			curl_setopt($ch, CURLOPT_POST, 1);
				 
			//Attach our encoded JSON string to the POST fields.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
				 
			//Set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

			//Execute the request
			$result = curl_exec($ch);
			$apiResultObj = json_decode($result, true);

			if(curl_errno($ch)){
				echo 'Curl error: ' . curl_error($ch);
				echo " result: " . $result;
			}
			// close curl
			curl_close($ch);
		}	

		// track result
		
		if (isset($apiResultObj["ok"]) &&  $apiResultObj["ok"] == false) {
			$statusCd = $apiResultObj["error_code"];
			$errorText = $apiResultObj["description"];
			$messageId = 999;
		} else if (isset($apiResultObj["ok"]) &&  $apiResultObj["ok"] == true) {
			$statusCd = 0;
			$messageId = $apiResultObj["result"]["message_id"];
			$errorText = "- Sent to " . $apiResultObj["result"]["chat"]["first_name"];
			if (isset($apiResultObj["result"]["chat"]["last_name"])) {
				$errorText = $errorText." ".$apiResultObj["result"]["chat"]["last_name"];
			} 
						 
			if (isset($apiResultObj["result"]["photo"])) {
				$errorText = $errorText . " with photo " . $message->getSubject();
			} 			 
		}


		parent::trackMessageResponse($message, $statusCd, $errorText, $messageId);
		
		return $statusCd == 0 ? "0 success" : $errorText;
	}

}



?>