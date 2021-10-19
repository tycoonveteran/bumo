<?php

require_once '../vendor/autoload.php';

#$mqttClient = new MQTTBotClient();
#$mqttClient->sendMessage("hello/world", "Hello World!");

$webSocketServer = new WebSocketServer(
    "Testspieler", 
    uniqid(), 
    $_GET['gameId'] ?? null
);
$webSocketServer->keepAlive();