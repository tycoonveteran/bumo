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

        $socket->webSocketHandler->keepAlive();
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

        $socket->webSocketHandler->keepAlive();
    });

    $socket->on('run', function ($data) use($socket){
        global $usernames, $numUsers;
        // Vorhandenem Spiel beitreten
        $username = $data[0];
        $gameId = $data[1];
        
        if ($socket->webSocketHandler->gameController->canStartNewGame()) {
            $socket->webSocketHandler->gameController->initNewGame();
            $socket->webSocketHandler->publishNewGameState();

        } else {
            // echo globally (all clients) that a person has connected
            $socket->broadcast->emit('NoStartAllowed', array(
                'numUsers' => $numUsers
            ));
        }
    });

    $socket->on('playCard', function ($data) use($socket){
        global $usernames, $numUsers;
        // Vorhandenem Spiel beitreten
        $username = $data[0];
        $cardIndex = $data[1];
        
        if ($socket->webSocketHandler->gameController->getNextPlayerId() == $socket->userid &&
            $socket->webSocketHandler->gameController->makePlayerMove($cardIndex)
        ) {
            // Zug war erfolgreich! 
            $socket->webSocketHandler->publishNewGameState();
        } else {
            // echo globally (all clients) that a person has connected
            $socket->broadcast->emit('NoPlayCardAllowed', array(
                'numUsers' => $numUsers
            ));
        }
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