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

    // when the client emits 'add user', this listens and executes
    $socket->on('join', function ($username) use($socket){
        global $usernames, $numUsers;
        // we store the username in the socket session for this client
        $socket->username = $username;
        $socket->userid = uniqid();

        $socket->webSocketHandler = new WebSocketServer(
            $socket->username, 
            $socket->userid,
            null
        );

        $socket->webSocketHandler->getGameList(function($topic, $message) use ($socket){
            print 'sending game id: ' . $message;
            $socket->emit($message);
        });
        
        // add the client's username to the global list
        $usernames[$username] = $username;
        ++$numUsers;
        $socket->addedUser = true;

        $socket->emit('game', array( 
            'gameId' => $socket->webSocketHandler->gameController->getGameId(),
            'gameController' => serialize( $socket->webSocketHandler->gameController )
        ));

        // echo globally (all clients) that a person has connected
        $socket->broadcast->emit('user joined', array(
            'numUsers' => $numUsers
        ));
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