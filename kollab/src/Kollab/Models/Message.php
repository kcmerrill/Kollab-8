<?php
namespace Kollab\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Message extends Eloquent {
    protected $fillable = array('from','channel', 'route','message');
}
