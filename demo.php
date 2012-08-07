<?php

require("SMSManager.php");

$s = new SMSManager\SMSManager();

var_dump($s->requestList());

echo "<pre>";
var_dump($s->requestStatus(123456));
echo "</pre>";

echo "<pre>";
var_dump($s->getUserInfo());
echo "</pre>";

echo "<pre>";
var_dump(
	$s->send(
		array(
			$s->prepareMessage("00420776123456", "testovaci sms")
		)
	)
);
echo "</pre>";


?>