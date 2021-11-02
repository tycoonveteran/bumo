<?php 

use Workerman\Worker;
use Workerman\Timer;
use PHPSocketIO\SocketIO;

class WebSocketServer {

    private string $bindAddress = '0.0.0.0';
    private int $port = 12345;
    private $server, $client;

    public GameController $gameController;
    private Player $player;
    private MQTTBotClient $mqttClient;

    private ?string $gameId;
    private bool $needsToJoin = false;

    private object $socket; 

    public function __construct($playerName, $playerId, $gameId = null, &$socket) 
    {
        // assign Socket
        $this->socket =& $socket;

        // create mqtt Client
        $this->mqttClient = new MQTTBotClient($playerId);
        
        // Create Player
        $this->player = new Player($playerName, $playerId);
        
        // assign gameid
        $this->gameId = $gameId;
    }

    public function initialize() 
    {
        if ($this->gameId == null) {
            // create new game as first player
            $this->gameController = new GameController($this->player);
            $this->gameId = $this->gameController->getGameId();
            // publish game!
            print '['.$this->player->getPlayerId().'] Publish New Game! ' . PHP_EOL;
            $message['state'] = "PUBLISH";
            $message['gameController'] = serialize($this->gameController);
        } else {
            print '['.$this->player->getPlayerId().'] Join Game! ' . PHP_EOL;
            $this->gameId = $this->gameId;
            $this->needsToJoin = true;
            $message['state'] = "JOIN";
            $message['player'] = serialize($this->player);
        }

        $this->mqttClient->sendMessage(
            "bumo/".$this->gameId, 
            json_encode ($message)
        );
    }

    public function keepAlive() 
    {
        print '['.$this->player->getPlayerId().'] START LISTENING!';

        $this->mqttClient->subscribeToTopic(
            "bumo/".$this->gameId,
            $this->getTopicRefreshedEvent(),
            $this->socket
        );
    }

    public function getTopicRefreshedEvent () 
    {
        return (function ($topic, $message) {
            print '['.$this->player->getPlayerId().'] RECEIVED: ' . $topic . ':' . $message;
            $message = json_decode($message);

            // In Message ist immer ein serialized GameController
            switch ($message->state) {
                case 'JOIN': 
                    // Neuer Spieler joint, wir schicken Spielinfo raus!
                    if ($this->needsToJoin === false) {

                        print '['.$this->player->getPlayerId().']  Publish New Game! ' . PHP_EOL;
                        $responseMessage['state'] = "PUBLISH";
                        $receivedPlayer = unserialize($message->player);
                    
                        $this->gameController->joinGame($receivedPlayer);
                        $responseMessage['gameController'] = serialize($this->gameController);

                        $this->mqttClient->sendMessage(
                            "bumo/".$this->gameId, 
                            json_encode ($responseMessage)
                        );
                    }
                    break;

                case 'PUBLISH':
                    print '['.$this->player->getPlayerId().'] Published Case. unserialize GameController' . PHP_EOL;
                    /** @var $receivedGameController GameController */
                    $receivedGameController = unserialize($message->gameController);
                    if ($this->needsToJoin) {
                        foreach ($receivedGameController->getPlayers() as $player) {
                            print '['.$this->player->getPlayerId().'] Is ' . $player->getPlayerId() . '==' .  $this->player->getPlayerId() . PHP_EOL;
                            if ($player->getPlayerId() == $this->player->getPlayerId()) {
                                print '['.$this->player->getPlayerId().'] Succesfully joined!' . PHP_EOL;
                                // erfolgreich join
                                $this->needsToJoin = false;
                                $this->gameController = $receivedGameController;

                                print '['.$this->player->getPlayerId().'] Sending Success to Client!' . PHP_EOL;
                                $this->socket->emit(
                                    'Joined', 
                                    ['gameId' => $this->gameController->getGameId()]
                                );
                            }
                        }
                    }
                    break;
            }
        });
    }
}

/*
// Global array to save uid online data
$uidConnectionMap = array();
// Record the number of online users last broadcast
$last_online_count = 0;
  
  
// PHPSocketIO service
$sender_io = new SocketIO(2120);
// When the client initiates a connection event, it sets various event callbacks for the connection socket
  
// Listen to an http port when $sender GUI is started, through which you can push data to any uid or all UIDs
$sender_io->on('workerStart', function(){
 // Listening to an http port
 $inner_http_worker = new Worker('http://0.0.0.0:2121');
 // Triggered when the http client sends data
 $inner_http_worker->onMessage = function($http_connection, $data){
 global $uidConnectionMap;
 $_POST = $_POST ? $_POST : $_GET;
 // url format of push data type = publish & to = uid & content = XXXX
 switch(@$_POST['type']){
 case 'publish':
 global $sender_io;
 $to = @$_POST['to'];
 $_POST['content'] = htmlspecialchars(@$_POST['content']);
 // Send data to socket group where uid is specified
 if($to){
 $sender_io->to($to)->emit('new_msg', $_POST['content']);
 // Otherwise push data to all UIDs
 }else{
 $sender_io->emit('new_msg', @$_POST['content']);
 }
 // http interface returns. If the user is offline, socket returns fail
 if($to && !isset($uidConnectionMap[$to])){
 return $http_connection->send('offline');
 }else{
 return $http_connection->send('ok');
 }
 }
 return $http_connection->send('fail');
 };
  
});
  
if(!defined('GLOBAL_START'))
{
 Worker::runAll();
}
*/