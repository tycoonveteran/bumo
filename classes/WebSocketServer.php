<?php 

class WebSocketServer {

    private string $bindAddress = '0.0.0.0';
    private int $port = 12345;
    private $server, $client;

    private GameController $gameController;
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
            print "Publish New Game! " . PHP_EOL;

            $this->mqttClient->sendMessage(
                "bumo/".$this->gameController->getGameId(), 
                serialize($this->gameController)
            );
        } else {
            $this->gameId = $gameId;
            $this->needsToJoin = true;
        }

        // Create WebSocket.
        $this->server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->server, $this->bindAddress, $this->port);
        socket_listen($this->server);
        $this->client = socket_accept($this->server);

        // Send WebSocket handshake headers.
        $request = socket_read($this->client, 5000);
        preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
        $key = base64_encode(pack(
            'H*',
            sha1($matches[1] . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11")
        ));

        $headers = "HTTP/1.1 101 Switching Protocols\r\n";
        $headers .= "Upgrade: websocket\r\n";
        $headers .= "Connection: Upgrade\r\n";
        $headers .= "Sec-WebSocket-Version: 13\r\n";
        $headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
        socket_write($this->client, $headers, strlen($headers));
    }

    public function keepAlive() 
    {
        
        while (true) {
            sleep(1);

            $this->mqttClient->sendMessage(
                "bumo/".$this->gameController->getGameId(), 
                serialize($this->gameController)
            );

            $response = chr(129) . chr(strlen($this->gameId)) .  $this->gameId;
            socket_write($this->client, $response);

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

                socket_write($this->client, json_encode($this->gameController));
            }
        };
    }

}