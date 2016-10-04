<?php

include_once("../classes/ext/PHPMailerAutoload.php");


class SampleEmailChannel extends Channel 
{

	private $phpMailer;

	function __construct($useHTML) {
		$this->type = "EMAIL";
		$this->provider = $useHTML ? "HTML" : "TEXT";

		// do not use HTTP API
		$this->httpUrl = "";
		$this->httpHeader = "";
		$this->httpMethod = "";

		// setup PHP Mailer
		$this->phpMailer = new PHPMailer;
		//$this->phpMailer->SMTPDebug = 3;
		$this->phpMailer->CharSet = 'UTF-8';
		$this->phpMailer->Host = 'ssl://smtp.strato.de:465';

		$this->phpMailer->isSMTP();                                      // Set mailer to use SMTP
		$this->phpMailer->SMTPAuth = true;                               // Enable SMTP authentication
		$this->phpMailer->Username = 'system@klaudnabli.com';            // SMTP username
		$this->phpMailer->Password = 'CXfmZqwVzDpfzTKJ999';              // SMTP password
		$this->phpMailer->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
		$this->phpMailer->isHTML($useHTML);								 // Set email format to HTML

		$this->phpMailer->From = 'sas_demo@klaudnabli.com';
		
		$this->phpMailer->addReplyTo('khaled.nabli@sas.com', 'Khaled Nabli');
		$this->phpMailer->addReplyTo('mathias.bouten@sas.com', 'Mathias Bouten');
		$this->phpMailer->addReplyTo('rob.sneath@sas.com', 'Rob Sneath');
	}

	public function createMessage($recipient, $sender, $body, $subject) {


		if(empty($recipient)) {
			// throw exception
			throw new Exception('Recipient cannot be empty to send EMAIL.');
		}

		if(empty($body)) {
			// throw exception
			throw new Exception('Body cannot be empty to send EMAIL.');
		}
		

		if(empty($sender)) {
			$sender = "SAS GPCI";
		}

		return parent::createMessage($recipient, $sender, $body, $subject);
	}

	public function sendMessage(Message $message) {

		
		$this->phpMailer->FromName = $message->getSender();
		$this->phpMailer->addAddress($message->getRecipient());               // Name is optional
		
		$this->phpMailer->Subject = $message->getSubject();
		$this->phpMailer->Body    = $message->getBody();

		$statusCd = 0;
		$errorText = "";

		if(!$this->phpMailer->send()) {
		    $statusCd = 1;
		    $errorText = $this->phpMailer->ErrorInfo;
		}
		
		parent::trackMessageResponse($message, $statusCd, $errorText, "");

		return $statusCd == 0 ? "success" : $errorText;
	}

}



?>