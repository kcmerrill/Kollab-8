<?php
namespace Kollab\Sockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use kcmerrill\utility\config;
use Kollab\Models\User as KUser;
use Kollab\Models\Channel as KChannel;
use Kollab\Models\Message as KMessage;


class Router implements MessageComponentInterface {
    protected $connections;
    protected $config;

    public function __construct(config $config) {
        $this->connections = new Connections;
        $this->config = $config;
    }

    public function onOpen(ConnectionInterface $connection) {
        $user = new User($connection);
        $user->subscribe('_system');
        $this->connections->connect($user);
    }

    public function onMessage(ConnectionInterface $connection, $message) {
        $message = new Message($message);
        $current_user = $this->connections->find($connection);
        if($message->isValid() && $message->from() != '_system') {
            echo $message->from() . '[' . $message->route() . ']->action(' . $message->action() . ')->message(' . $message->message() . ')' . PHP_EOL;
            $authenticated_user = KUser::where('username','=', $message->from())->where('password','=',$message->signature())->first();
            if($authenticated_user){
               if($message->lastAttribute() != 'ignore'){
                    KMessage::create(array('from'=>$message->from(),'channel'=>$message->channel(), 'route'=>$message->route(),'message'=>$message->message()));
               }
               $this->distribute($current_user, $message);
               $this->actUpon($current_user, $message);
           }
        } else {
            echo 'ERROR: ' . $message->error() . PHP_EOL;
        }
    }

    public function onClose(ConnectionInterface $connection) {
        $current_user = $this->connections->find($connection);
    }

    public function onError(ConnectionInterface $connection, \Exception $e) {}

    private function actUpon($current_user, $message){
        if($message->action() == 'subscribe'){
            $this->connections->disconnect($current_user);
            $current_user->subscribe($message->attribute());
            $this->connections->connect($current_user);
            echo $message->from() . ' subscribed to ' . $message->attribute() . PHP_EOL;
        }
    }

    public function distribute($user, $message){
        foreach($this->connections->users as $user){
            if($user->isSubscribed($message->channel())){
                $user->message($message);
            }
        }
    }
}
