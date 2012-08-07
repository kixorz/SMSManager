<?php

namespace SMSManager;

class HTTPRequest
{
	protected $params;
	
	public function __construct($username, $password)
	{
		$this->params = array
		(
			"username" => $username,
			"password" => $password
		);
	}
	
	public function get($method, array $params = array())
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