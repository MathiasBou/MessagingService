<?php


class DatabaseLogger
{
	private $user;
	private $password;
	private $host;

	private $link;

	function __construct($host, $user, $pass) {
		$this->host = $host;
		$this->user = $user;
		$this->password = $pass;

		$this->link = null;
	}

	public function connect() {

		$dblink = new mysqli($this->host, $this->user, $this->password, $this->user);
		if($dblink->connect_errno) {
			// log connect_errno and 

		} else {
			$this->link = $dblink;
		}

	}

	public function isConnected() {
		return ($this->link != null);
	}

	public function getLink() {
		return $this->link;
	}


	public function logMessage(Message $message) {

		$userHost = !empty(@$_POST['remoteUser']) ? $_POST['remoteUser'] . ";" . gethostbyaddr($_SERVER['REMOTE_ADDR']) :  gethostbyaddr($_SERVER['REMOTE_ADDR']);

		$sqlInsert = "INSERT INTO `message_svc`.`message` " . "(`entryId`, `messageId`, `statusCd`, `errorText`, `channel`, `provider`, `subject`, `sender`, `recipient`, `body`, `bodyTemplate`, `bodyData`, `createdBy`) VALUES " 	. "(NULL, '{$message->getMessageId()}', '{$message->getStatusCd()}', '{$this->getLink()->real_escape_string($message->getErrorText())}', '{$message->getChannel()}', '{$message->getProvider()}', '{$this->getLink()->real_escape_string($message->getSubject())}', '{$this->getLink()->real_escape_string($message->getSender())}', '{$this->getLink()->real_escape_string($message->getRecipient())}', '{$this->getLink()->real_escape_string($message->getBody())}', '{$this->getLink()->real_escape_string($message->getBodyTemplate())}', '{$this->getLink()->real_escape_string($message->getBodyData())}','{$userHost}');";
		if($this->isConnected()) {
			$this->getLink()->query($sqlInsert);
		}
	}

}


















?>