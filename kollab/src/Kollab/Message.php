<?php
namespace Kollab;

class Message {
    protected $fields = array('from','route','message','signature','channel');
    protected $message;
    protected $decoded;
    protected $error = false;

    function __construct($message){
       $this->message = $message;
       $this->decoded = json_decode($message, TRUE);
   }

    function getRawMessage(){
        return $this->message;
    }

    function getMessage($field = false){
        /* Grab a portion of the message, or all of it */
        return isset($this->decoded[$field]) ? $this->decoded[$field] : NULL;
    }

    function isValid(){
        $valid = false;
        foreach($this->fields as $field){
            $valid = is_null($this->$field()) ? true : $valid;
            $this->error = $valid ? 'Message missing ' . $field: $this->error;
        }
        return $valid;
    }

    function channel(){
        $route = explode('.', $this->route());
        return isset($route[0]) ? $route[0] : NULL;
    }

    function action(){
        $route = explode('.', $this->route());
        return isset($route[1]) ? $route[1] : NULL;
    }

    function subaction(){
        $route = explode('.', $this->route());
        return isset($route[2]) ? $route[2] : NULL;
    }

    function error(){
        return $this->error;
    }

    function __toString(){
        return $this->message;
    }

    function __call($method, $params){
        return $this->getMessage($method);
    }
}
