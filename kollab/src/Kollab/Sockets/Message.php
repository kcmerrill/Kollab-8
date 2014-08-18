<?php
namespace Kollab\Sockets;

class Message {
    protected $fields = array('from','route','message','signature','channel');
    protected $message;
    protected $decoded;
    protected $error = false;
    protected $delimiter = '/';

    function __construct($message ){
       $this->message = $message;
       $this->decoded = json_decode($message, TRUE);
   }

    static function create($route, $message) {
        $new_message = array(
            'from'=>'_system',
            'route'=>$route,
            'message'=>$message,
            'signature'=>''
        );
        return new Message(json_encode($new_message));
    }

    public function getRawMessage(){
        return $this->message;
    }

    public function getMessage($field = false){
        /* Grab a portion of the message, or all of it */
        return isset($this->decoded[$field]) ? $this->decoded[$field] : NULL;
    }

    public function isValid(){
        $valid = true;
        foreach($this->fields as $field){
            $valid = is_null($this->$field()) ? false : $valid;
            $this->error = $valid ? 'Message missing ' . $field: $this->error;
        }
        return $valid;
    }

    public function channel(){
        $route = explode($this->delimiter, $this->route());
        return isset($route[0]) ? $route[0] : NULL;
    }

    public function action(){
        $route = explode($this->delimiter, $this->route());
        return isset($route[1]) ? $route[1] : NULL;
    }

    public function attribute($index = 2){
        $route = explode($this->delimiter, $this->route());
        return isset($route[$index]) ? $route[$index] : NULL;
    }

    public function lastAttribute(){
        $route = explode($this->delimiter, $this->route());
        return end($route);
    }

    public function typeOf(){
        $channel = $this->channel();
        return isset($channel[0]) ? $channel[0] : false;
    }

    public function error(){
        return $this->error;
    }

    public function __toString(){
        /* Dont send the user's credentials through */
        unset($this->decoded['signature']);
        $this->decoded = is_array($this->decoded) ? $this->decoded : array();
        $this->decoded['channel'] = $this->channel();
        $this->decoded['action'] = $this->action();
        $this->decoded['attribute'] = $this->attribute();
        $this->decoded['last_attribute'] = $this->lastAttribute();
        $this->decoded['date'] = time();
        return json_encode($this->decoded);
    }

    public function __call($method, $params){
        return $this->getMessage($method);
    }
}
