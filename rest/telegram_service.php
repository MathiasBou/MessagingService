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


$action = @$_POST['action'];


// check parameters
if(empty($action)) {
	// print usage!
	echo json_encode(array("message" => "wrong usage. Missing required parameter: action"));
	return;
}

try {

	switch ($action) {
			case 'getUpdates':
				
				echo json_encode(sendRequest($action));
				
				break;
				
			default:
				// invalid channel
				echo json_encode(array("message" => "Action not valid. Please use getUpdates"));
			break;
	}
} catch (Exception $e) {
	echo json_encode(array("message" => $e->getMessage()));
}

return;



function sendRequest($action) {

	$url = "https://api.telegram.org/bot"."243675596:AAFCxV_kqq6tpLCOjCfzifFij6ML81ZbmsQ"."/getUpdates";

			//$apiData = array("chat_id" => "1234");
			
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $apiData); 			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);			
			$apiResultObj = json_decode($result, true);

			if(curl_errno($ch)){
				echo 'Curl error: ' . curl_error($ch);
				echo $result;
			}

			$index = sizeof($apiResultObj["result"])-1;
			$lastMessage = $apiResultObj["result"][$index];
			$message    = "MessageID: " . $apiResultObj["result"][$index]["message"]["message_id"]
						. " \n\nfrom: " . $apiResultObj["result"][$index]["message"]["from"]["id"] 
						. " \n    firstname: " . $apiResultObj["result"][$index]["message"]["from"]["first_name"]
						//. " \n    lastname: " . $apiResultObj["result"][$index]["message"]["from"]["last_name"]
						. " \n\nto chat id: " . $apiResultObj["result"][$index]["message"]["chat"]["id"]
						. " \n    firstname: " . $apiResultObj["result"][$index]["message"]["chat"]["first_name"]
						//. " \n    lastname: " . $apiResultObj["result"][$index]["message"]["chat"]["last_name"]
						. " \n\ntext: " . $apiResultObj["result"][$index]["message"]["text"];

			$jsonResponse = array(
				    "message" => $message, 
				    "chat_id" => $apiResultObj["result"][$index]["message"]["chat"]["id"]
				    );

			curl_close($ch);

	return $jsonResponse;
}



?>