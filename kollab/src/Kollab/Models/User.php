<?php
namespace Kollab\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent {
    protected $fillable = array('username','email','password','gender','title','name','department');
    protected $hidden = array('id','password');
    protected $appends = array('status_code');

    public function channels(){
        return $this->belongsToMany('Kollab\Models\Channel');
    }

    public function getStatusCodeAttribute() {
        $status_codes = array('online'=>1, 'away'=>2,'offline'=>3);
        return $this->attributes['status_code'] = $status_codes[strtolower($this->attributes['status'])];
    }
}
