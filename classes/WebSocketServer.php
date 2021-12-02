<?php 

use Workerman\Worker;
use Workerman\Timer;
use PHPSocketIO\SocketIO;

class WebSocketServer {

    public ?GameController $gameController = null;
    private Player $player;
    private FileSystemMessageBroker $fsmb;

    private ?string $gameId;
    private bool $needsToJoin = false;

    private object $socket; 
    private $timer_id;

    private SocketIO $io;

    public function __construct($playerName, $playerId, $gameId = null, &$socket, &$io) 
    {
        // assign Socket
        $this->socket =& $socket;
        $this->io =& $io;

        // Create Player
        $this->player = new Player($playerName, $playerId);
        
        // assign gameid
        $this->gameId = $gameId;
    }

    public function initialize() 
    {
        if ($this->gameId == null) {
            // create new game as first player
            $this->gameController = new GameController();
            $this->gameController->initialize($this->player);

            $this->gameId = $this->gameController->getGameId();
            // publish game!
            // TODO: auslagern in publishNewGameState
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

        // initialize fsmb
        $this->fsmb = new FileSystemMessageBroker($this->gameId);
        // hello world
        $this->fsmb->publishMessage($message);
    }

    public function publishNewGameState() 
    {
        print '['.$this->player->getPlayerId().'] Publish New Game State! ' . PHP_EOL;
        $message['state'] = "PUBLISH";
        $message['gameController'] = serialize($this->gameController);
        $this->fsmb->publishMessage($message);
    }

    public function keepAlive() 
    {
        print '['.$this->player->getPlayerId().'] START LISTENING!';
        // ab hier kommt der ganze Schund:

        // TODO: Task als Attribut der Klasse implementieren 
        // und bei Beendigung oder wechsel Game ID neu initialisieren.
        if ($this->timer_id != null) {
            Timer::del($this->timer_id);
        }
        $task = new Worker();
        $task->onWorkerStart = function ($task) {
            // 2.5 seconds
            $time_interval = 1; 
            $this->timer_id = Timer::add($time_interval, function () {
                $message = $this->fsmb->getLatestMessage();
                if ($message !== false) {
                    // New message 
                    /*
                    $message['state'] = JOIN|PUBLISH
                    $message['gameController'] = 
                    $message['player']
                    */
                    switch ($message['state']) {
                        case 'JOIN': 
                            // Neuer Spieler joint, wir schicken Spielinfo raus!
                            if ($this->needsToJoin === false) {
        
                                print '['.$this->player->getPlayerId().']  Publish New Game! ' . PHP_EOL;
                                $responseMessage['state'] = "PUBLISH";
                                $receivedPlayer = unserialize($message['player']);
                                if ($this->gameController->getGameStatus() == GameController::GAME_INIT) {
                                    $this->gameController->joinGame($receivedPlayer);
                                }
                                $responseMessage['gameController'] = serialize($this->gameController);
        
                                $this->fsmb->publishMessage($responseMessage);
                            }
                            break;
        
                        case 'PUBLISH':
                            print '['.$this->player->getPlayerId().'] Published Case. unserialize GameController' . PHP_EOL;
                            /** @var $receivedGameController GameController */
                            $receivedGameController = unserialize($message['gameController']);
                            if ($receivedGameController instanceof GameController) {
                                $this->gameController = $receivedGameController; 
                            } else {
                                print "Warning! No instanceof GameController received!";
                            }

                            if ($this->needsToJoin) {
                                foreach ($receivedGameController->getPlayers() as $player) {
                                    print '['.$this->player->getPlayerId().'] Is ' . $player->getPlayerId() . '==' .  $this->player->getPlayerId() . PHP_EOL;
                                    if ($player->getPlayerId() == $this->player->getPlayerId()) {
                                        print '['.$this->player->getPlayerId().'] Succesfully joined!' . PHP_EOL;
                                        // erfolgreich join
                                        $this->needsToJoin = false;
                                    }
                                }
                            } 
                            break;
                    }

                    // wir teilen die Message allen Clients ebenfalls mit
                    // echo globally (all clients) that a person has connected
                    
                    // Todo: umbauen von broadcast auf Einzelnachricht, Abprüfung
                    // Der Player-ID, damit der Player nur seinen eigenne Kartenstapel
                    // in der Nachricht bekommt.
                    $preparedOutput = $this->gameController->prepareOutputForPlayerId ($this->player->getPlayerId());
                    
                    $this->socket->emit('gameState', array(
                        'gameController' => json_encode($preparedOutput)
                    ));

                    /*
                    if ($this->gameController != null && $this->gameController instanceof GameController) {
                        $this->socket->broadcast->emit('gameState', array(
                            'gameController' => json_encode($this->gameController)
                        ));
                    }
                    */
                }
            });
        };
        $task->run();
    }

    public function __destruct()
    {
        if ($this->timer_id != null) {
            Timer::del($this->timer_id);
        }
    }
}