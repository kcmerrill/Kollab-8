<?php
namespace Kollab;

class Connections {
    function __construct(){
        $this->users = new \SplObjectStorage;
    }

    function connect(User $user){
        $this->users->attach($user, false);
    }

    function register(User $user, $as_who){
        $this->users[$user] = $as_who;
    }

    function disconnect(User $user){
        $this->users->detach($user);
    }

    function isRegistered($connection){
        return $this->users->offsetGet($this->find($connection));
    }

    function registeredAs($conneciton){
        return $this->users->offsetGet($this->find($connection));
    }

    function find($connection){
        foreach($this->users as $user){
            if($user->connection() == $connection){
                return $user;
            }
        }
        return false;
    }
}
