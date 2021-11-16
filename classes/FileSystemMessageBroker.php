<?php


class FileSystemMessageBroker 
{

    const PATH = '../gamedata/';

    private $messagePath;

    private string $lastMessageId = "";

    private $lastMessage;

    public function __construct($gameId) 
    {
        $this->messagePath = self::PATH . $gameId . '/';
        if (!file_exists($this->messagePath)) {
            mkdir($this->messagePath);
        }
    }

    public function publishMessage($message) 
    {
        $this->deleteLastMessage();
        $parsedMessage = json_encode($message);
        
        $lastMessageId = uniqid() . '.txt';
        file_put_contents($this->messagePath . $lastMessageId, $parsedMessage);
    }

    private function decodeMessage($message) 
    {
        return json_decode($message, true);
    }

    private function deleteLastMessage() 
    {
        $messageFileName = $this->messagePath . $this->lastMessageId;
        if (!empty($this->lastMessageId) && file_exists($messageFileName))
        {
            unlink ($messageFileName);
        } elseif ($this->checkNewMessageArrived()) {
            // retrieve last MessageId
            $this->deleteLastMessage();
        }
    }

    private function checkNewMessageArrived() : bool
    {
        print 'opendir: ' . $this->messagePath . PHP_EOL;
        $directoryHandler = opendir($this->messagePath);
        while ($filename = readdir($directoryHandler)) {
            if (!in_array($filename, ['.', '..'])) {
                $lastMessageId = $filename;
            }
        }
        closedir($directoryHandler);

        if (isset($lastMessageId) && $lastMessageId != $this->lastMessageId)
        {
            $this->lastMessageId =$lastMessageId;
            return true;
        } 
        return false;
    }

    public function getLatestMessage() 
    {
        print 'getLatestMessageCall...' . PHP_EOL;
        
        if ($this->checkNewMessageArrived()) {
            $message = file_get_contents($this->messagePath . $this->lastMessageId);
            $this->lastMessage = $this->decodeMessage($message);
            return $this->lastMessage;
        } 
        return false;
    }
}