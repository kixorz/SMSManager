<?php

namespace SMSManager;

require("Config.php");
require("HTTPRequest.php");

class SMSManagerException extends \Exception
{
	function __construct($message, $code = 0)
	{
		parent::__construct($message, $code);
		
		if(Config::logging)
		{
			error_log((string)$self);
		}
	}
}

class SMSManager
{
	protected $request;
	protected $username;
	protected $password;
	
	function __construct($username = Config::username, $password = Config::password)
	{
		$this->username = $username;
		$this->password = sha1($password);
		
		$this->request = new HTTPRequest($this->username, $this->password);
	}
	
	public function prepareMessage(Array $numbers, $text, $type = "lowcost")
	{
		$message = new \stdClass;
		
		$message->numbers = $numbers;
		$message->text = $text;
		$message->type = $type;
		
		return $message;
	}
	
	public function send($messages)
	{
		if(!is_array($messages))
		{
			$messages = array($messages);
		}
		
		//who the hell uses XML these days... :-(
		$xml = new \DOMDocument("1.0", "utf-8");
		
		$requestDocument = $xml->appendChild($xml->createElement("RequestDocument"));
		
		//RequestHeader
		$requestHeader = $requestDocument->appendChild($xml->createElement("RequestHeader"));
		$requestHeader->appendChild($xml->createElement("Username", $this->username));
		$requestHeader->appendChild($xml->createElement("Password", $this->password));
		
		//RequestList
		$requestList = $xml->createElement("RequestList");
		
		//for each message create new request
		foreach($messages as $message)
		{			
			//request type
			$request = $xml->createElement("Request");
			$request->setAttribute("Type", $message->type);
			
			//message
			$msg = $xml->createElement("Message", $message->text);
			$msg->setAttribute("Type", "Text");
			$request->appendChild($msg);
			
			//numbers
			$numbersList = $xml->createElement("NumbersList");
			
			//for each number in the message
			foreach($message->numbers as $number)
			{
				$num = $xml->createElement("Number", $number);
				$numbersList->appendChild($num);
			}
			
			$request->appendChild($numbersList);
			$requestList->appendChild($request);
		}
		
		$requestDocument->appendChild($requestList);
		
		$responseXML = $this->request->post("Send", $xml->saveXML());
		
		$response = simplexml_load_string($responseXML);
		
		$attr = (array)$response->Response;
		if($attr["@attributes"]["Type"] != "OK")
		{
			throw new SMSManagerException("API responded with error.");
		}
		
		$out = array();
		
		$responseRequests = $response->ResponseRequestList;
		foreach($responseRequests->ResponseRequest as $responseRequest)
		{
			$o = new \stdClass;
			
			$o->id = (string)$responseRequest->RequestID;
			
			$numbers = $responseRequest->ResponseNumbersList;
			foreach($numbers->Number as $number)
			{
				$o->numbers[] = (string)$number;
			}
			
			$out[] = $o;
		}
		
		return $out;
	}
	
	public function requestList()
	{
		$responses = $this->request->get("RequestList");
		
		$out = array();
		foreach($responses as $response)
		{
			$o = new \stdClass;
			list(
				$o->id,
				$o->gateway,
				$o->sent,
				$o->expires,
				$o->sender,
				$o->remainingRecipients,
				$o->requestState
			) = $response;
			
			$out[] = $o;
		}
		
		return $out;
	}
	
	public function requestStatus($requestId)
	{
		$response = $this->request->get("RequestStatus", array("requestID" => $requestId));
		
		var_dump($response);
		
		$o = new \stdClass;
		list(
			$o->number,
			$o->sentState,
			$o->remainingConfirmations,
			$o->receivedState
		) = $response[0];
		
		return $o;
	}
	
	public function getUserInfo()
	{
		$response = $this->request->get("GetUserInfo", array("requestID" => $requestId));
		
		$o = new \stdClass;
		list(
			$o->credit,
			$o->sender,
			$o->gateway
		) = $response[0];
		
		$o->credit = floatval($o->credit);
		
		return $o;
	}
}

?>