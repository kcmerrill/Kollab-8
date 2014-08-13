<?php
namespace Kollab;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use kcmerrill\utility\config;

class Router implements MessageComponentInterface {
    protected $connections;
    protected $config;

    public function __construct(config $config) {
        $this->connections = new Connections;
        $this->config = $config;
    }

    public function onOpen(ConnectionInterface $connection) {
        $user = new User($connection);
        $this->connections->connect($user);
    }

    public function onMessage(ConnectionInterface $connection, $message) {
        $message = new message($message);
        $current_user = $this->connections->find($connection);
        if(!$message->valid()) {
            foreach($this->clients->connections as $user){
                if($user->isSubscribed($message->channel())){
                    $user->message($message);
                }
            }
        }
   }

    public function onClose(ConnectionInterface $connection) {}

    public function onError(ConnectionInterface $connection, \Exception $e) {}
}
