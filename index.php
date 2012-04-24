<?php

namespace SMSManager;

require("SMSManager.php");

$s = new SMSManager();

var_dump($s->requestList());
echo "<pre>";
var_dump($s->requestStatus(123456));
echo "</pre><pre>";
var_dump($s->getUserInfo());
echo "</pre><pre>";
var_dump($s->send(array($s->prepareMessage(array("00420776123456"), "testovaci sms"))));
echo "</pre>";


?>