<?php
namespace Kollab\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Channel extends Eloquent {
    protected $fillable = array('channel','title','description');

    public function users(){
        return $this->belongsToMany('Kollab\Models\User')->select(array('username'));
    }
}
