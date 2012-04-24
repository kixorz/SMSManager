<?php

namespace SMSManager;

class HTTPRequestException extends SMSManagerException
{
	function __construct($code)
	{
		if(intval($code) > 900)
		{
			$code = 900;
		}
		
		$codes = array
		(
			"101" => "Neexistuj�c� data po�adavku (chyb� XMLDATA parametr u XML API)",
			"102" => "Metoda neexistuje",
			"103" => "Neplatn� u�ivatelsk� jm�no nebo heslo",
			"104" => "Neplatn� parametr gateway",
			"105" => "Nedostatek kreditu pro prepaid",
			"201" => "��dn� platn� telefonn� ��sla v po�adavku",
			"202" => "Text zpr�vy neexistuje nebo je p��li� dlouh�",
			"203" => "Neplatn� parametr sender (odes�latele nejprve nastavte ve webov�m rozhran�)",
			"900" => "Syst�mov� chyba (informujte se na support@smsmanager.cz)"
		);
		
		if(empty($codes[$code]))
		{
			parent::__construct("SMSManager API vr�tilo nezn�mou chybu", $code);
		}
		
		parent::__construct("SMSManager API chyba: ".$codes[$code], $code);
	}
}

class HTTPRequest
{
	public function __construct($username, $password)
	{
		$this->params = array
		(
			"username" => $username,
			"password" => $password
		);
	}
	
	public function get($method, $params = array())
	{
		$getParams = array_merge($params, $this->params);
		
		$endpoint = Config::api_protocol."://".Config::api_http."/";
		$url = $endpoint.$method."?".http_build_query($getParams);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		
		$response = curl_exec($ch); 
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if($code != 200)
		{
			throw new \Exception("Request to {$url} failed.");
		}
		
		curl_close($ch);
		
		$out = array();
		
		$lines = explode("\n", trim($response));
		foreach($lines as $line)
		{
			if(!empty($line))
			{
				$out[] = explode("|", trim($line));
			}
		}
		
		return $out;
	}
	
	public function post($method, $document)
	{
		$endpoint = Config::api_protocol."://".Config::api_xml."/";
		$url = $endpoint.$method;
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "&XMLDATA=".urlencode($document));
		
		$response = curl_exec($ch); 
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if($code != 200)
		{
			throw new \Exception("Request to {$url} failed.");
		}
		
		curl_close($ch);
		
		return $response;
	}
}

?>