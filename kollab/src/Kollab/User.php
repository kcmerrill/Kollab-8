<?php
namespace Kollab;

class User{
    public $connection;
    protected $name = false;
    protected $connected_timestamp;
    protected $channels = [];

    function __construct($connection){
        $this->connection = $connection;
        $this->connected_timestamp = time();
    }

    function isAuthenticated(){
        return is_string($this->name);
    }

    function subscribe($channel){
        $channel = strtolower($channel);
        if(!in_array($channel, $this->channels)){
            $this->channels[] = $channel;
        }
    }

    function isSubscribed($channel){
        return in_array(strtolower($channel), $this->channels);
    }

    function message(Message $message){
        $this->connection->send($message);
    }

    function connection(){
        return $this->connection;
    }

    function name(){
        return $this->name ? $this->name : 'Unknown';
    }
}
