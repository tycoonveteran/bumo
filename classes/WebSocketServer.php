<?php 

use Workerman\Worker;
use Workerman\Timer;

class WebSocketServer {

    private string $bindAddress = '0.0.0.0';
    private int $port = 12345;
    private $server, $client;

    public GameController $gameController;
    private Player $player;
    private MQTTBotClient $mqttClient;

    private string $gameId;
    private bool $needsToJoin = false;

    public function __construct($playerName, $playerId, $gameId = null) 
    {
        // create mqtt Client
        $this->mqttClient = new MQTTBotClient();
        
        // Create Player
        $this->player = new Player($playerName, $playerId);
        
        if ($gameId == null) 
        {
            // create new game as first player
            $this->gameController = new GameController($this->player, $gameId);
            $this->gameId = $this->gameController->getGameId();
            // publish game!
            $task = new Worker();
            $task->onWorkerStart = function ($task) {
                // 2.5 seconds
                $time_interval = 2.5; 
                $timer_id = Timer::add($time_interval, function () {
                    print "Publish New Game! " . PHP_EOL;
                    $this->mqttClient->sendMessage(
                        "bumo/game", 
                        $this->gameController->getGameId()
                    );
                });
            };
            $task->run();
        } else {
            $this->gameId = $gameId;
            $this->needsToJoin = true;
        }
    }

    public function getGameList($callBackFunction) 
    {
        print 'call getGameList';
        $this->mqttClient->subscribeToTopic(
            "bumo/game",
            $callBackFunction
        );
    }

    public function keepAlive() 
    {
        
        $this->mqttClient->subscribeToTopic(
            "bumo/".$this->gameId,
            $this->getTopicRefreshedEvent()
        );

        /*
        $buf = '';
        if (false !== ($bytes = socket_recv($this->client, $buf, 2048, MSG_DONTWAIT)))
        {
            print "Got $bytes from client!";
            print $buf;
        }
        */
    }

    private function getTopicRefreshedEvent () 
    {
        return function ($topic, $message) {
            print $topic . ':' . $message;

            // In Message ist immer ein serialized GameController
            $receivedGameController = unserialize($message);
            if ($receivedGameController instanceof GameController) {
                $this->gameController = $receivedGameController;

                if ($this->needsToJoin) {
                    $this->gameController->joinGame($this->player);
                    $this->mqttClient->sendMessage(
                        "bumo/".$this->gameId,
                        serialize($this->gameController)
                    );
                }

                //socket_write($this->client, json_encode($this->gameController));
            }
        };
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