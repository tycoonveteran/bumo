<?php

use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;

// composer autoload
require_once "../vendor/autoload.php";

$io = new SocketIO(2020);
$io->on('connection', function($socket){
    $socket->addedUser = false;

    // when the client emits 'create', this listens and executes
    $socket->on('create', function ($username) use($socket){
        global $usernames, $numUsers;
        // we store the username in the socket session for this client
        $socket->username = $username;
        $socket->userid = uniqid();

        $socket->webSocketHandler = new WebSocketServer(
            $socket->username, 
            $socket->userid,
            null,
            $socket
        );

        $socket->webSocketHandler->initialize();
        
        // add the client's username to the global list
        $usernames[$username] = $username;
        ++$numUsers;
        $socket->addedUser = true;

        $socket->emit('NewGame', array( 
            'gameId' => $socket->webSocketHandler->gameController->getGameId(),
            'gameController' => serialize( $socket->webSocketHandler->gameController )
        ));

        // echo globally (all clients) that a person has connected
        $socket->broadcast->emit('user joined', array(
            'numUsers' => $numUsers
        ));
        
        $gameId = $socket->webSocketHandler->gameController->getGameId();
        $topic = 'bumo/'.$gameId;
        $callBackFunction = $socket->webSocketHandler->getTopicRefreshedEvent();

        print 'Start subscriceToTopic';
        $socket->innerWorkerHost = new Worker();

        $socket->innerWorkerHost->onWorkerStart = function() 
            use ($socket, $topic, $callBackFunction) {

            print 'Run Workerman subscriceToTopic' . PHP_EOL;
            $mqtt = new Workerman\Mqtt\Client(
                'mqtt://'.MQTTBotClient::MQTTSERVER.'?token='.$socket->userid.':'.MQTTBotClient::MQTTPORT,
                ['client_id' => $socket->userid, 
                 'debug' => true] 
            );

            $mqtt->onConnect = function($mqtt) use ($topic) {
                print 'Subscribe Workerman subscriceToTopic ' . $topic . PHP_EOL;
                $mqtt->subscribe($topic);
            };
            $mqtt->onMessage = $callBackFunction;
            $mqtt->connect();
        };

        $socket->innerWorkerHost->run();
    });
    
    $socket->on('join', function ($data) use($socket){
        global $usernames, $numUsers;
        // Vorhandenem Spiel beitreten
        $username = $data[0];
        $gameId = $data[1];
        
        // we store the username in the socket session for this client
        $socket->username = $username;
        $socket->userid = uniqid();

        $socket->webSocketHandler = new WebSocketServer(
            $socket->username, 
            $socket->userid,
            $gameId,
            $socket
        );
        
        $socket->webSocketHandler->initialize();

        // add the client's username to the global list
        $usernames[$username] = $username;
        ++$numUsers;
        $socket->addedUser = true;

        // echo globally (all clients) that a person has connected
        $socket->broadcast->emit('user joined', array(
            'numUsers' => $numUsers
        ));

        $topic = 'bumo/'.$gameId;
        $callBackFunction = $socket->webSocketHandler->getTopicRefreshedEvent();

        print 'Start subscriceToTopic';
        $socket->innerWorkerJoiner = new Worker();

        $socket->innerWorkerJoiner->onWorkerStart = function() 
            use ($socket, $topic, $callBackFunction) {

            print 'Run Workerman subscriceToTopic' . PHP_EOL;
            $mqtt = new Workerman\Mqtt\Client(
                'mqtt://'.MQTTBotClient::MQTTSERVER.'?token='.$socket->userid.':'.MQTTBotClient::MQTTPORT,
                ['client_id' => $socket->userid, 
                 'debug' => true] 
            );

            $mqtt->onConnect = function($mqtt) use ($topic) {
                print 'Subscribe Workerman subscriceToTopic ' . $topic . PHP_EOL;
                $mqtt->subscribe($topic);
            };
            $mqtt->onMessage = $callBackFunction;
            $mqtt->connect();
        };

        $socket->innerWorkerJoiner->run();
    });

    // when the user disconnects.. perform this
    $socket->on('disconnect', function () use($socket) {
        global $usernames, $numUsers;
        // remove the username from global usernames list
        if($socket->addedUser) {
            unset($usernames[$socket->username]);
            --$numUsers;

           // echo globally that this client has left
           $socket->broadcast->emit('user left', array(
               'username' => $socket->username,
               'numUsers' => $numUsers
            ));
        }
   });

   
});

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

Worker::runAll();