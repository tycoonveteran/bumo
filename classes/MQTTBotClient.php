<?php
use PhpMqtt\Client\Examples\Shared\SimpleLogger;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use Workerman\Worker;

class MQTTBotClient 
{
    const CLIENTNAME = "bumoclient";
    const MQTTSERVER = "192.168.0.251";
    const MQTTPORT = 1883;

    /** @var MqttClient */
    private $client; 
    /** @var Worker */
    private $worker;

    private function connect() : bool
    {
        try {
            $this->client = new MqttClient(self::MQTTSERVER, self::MQTTPORT);
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
                print $topic .':'. $message . PHP_EOL;
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
        print 'Start subscriceToTopic';
        $this->worker = new Worker();
        $this->worker->onWorkerStart = function() use ($topic, $callBackFunction) {
            print 'Run Workerman subscriceToTopic';
            $mqtt = new Workerman\Mqtt\Client('mqtt://'.self::MQTTSERVER.':'.self::MQTTPORT);
            $mqtt->onConnect = function($mqtt) use ($topic) {
                print 'Subscribe Workerman subscriceToTopic ' . $topic;
                $mqtt->subscribe($topic);
            };
            $mqtt->onMessage = $callBackFunction;
            $mqtt->connect();
        };
        $this->worker->run();
    }
}