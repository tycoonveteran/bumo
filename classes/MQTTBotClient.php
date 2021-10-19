<?php
use PhpMqtt\Client\Examples\Shared\SimpleLogger;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;

class MQTTBotClient 
{
    const CLIENTNAME = "bumoclient";
    const MQTTSERVER = "192.168.0.251";

    /** @var MqttClient */
    private $client; 
    
    private function connect() : bool
    {
        try {
            $this->client = new MqttClient(self::MQTTSERVER, 1883);
            $this->client->connect(null, true);
            return true;
        } catch (Throwable $e) {
            print $e->getMessage() . PHP_EOL;
            print $e->getTraceAsString();
            return false;
        }
    }

    public function sendMessage($topic, $message) 
    {
        try {
            if ($this->connect()) {
                print $topic .':'. $message;
                $this->client->publish($topic, $message, 2);    
                // Since QoS 2 requires the publisher to await confirmation and resend the message if no confirmation is received,
                // we need to start the client loop which takes care of that. By passing `true` as second parameter,
                // we allow the loop to exit as soon as all confirmations have been received.
                $this->client->loop(true, true);
                #$this->client->disconnect();
            } else {
                throw new Exception("Could not connect!");
            }
        } catch (Throwable $e) {
            print $e->getMessage() . PHP_EOL;
            print $e->getTraceAsString();
        }
    }

    public function subscribeToTopic ($topic, $callBackFunction) 
    {
        try {
            if ($this->connect()) {
                // Wir wollen aus einer Topic den aktuellsten Stand haben
                // TODO: Keine wirkliche Callback-Function, der Code pausiert,
                // bis eine Topic kommt! 
                $this->client->subscribe($topic, $callBackFunction, 0);
                $this->client->loop(true, true);
            } else {
                throw new Exception("Could not connect!");
            }
        } catch (Throwable $e) {
            print $e->getMessage() . PHP_EOL;
            print $e->getTraceAsString();
        }
    }

    public function __destruct() 
    {
        #$this->client->disconnect();
    }
}