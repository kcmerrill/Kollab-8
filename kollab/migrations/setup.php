<?php
require_once __DIR__ . '/../bootstrap.php';

use Kollab\Models\User as KUser;
use Kollab\Models\Channel as KChannel;

/* Drop the users table */
if($kollab['eloquent']::schema()->hasTable('users')){
    $kollab['eloquent']::schema()->drop('users');
}

/* Drop the users table */
if($kollab['eloquent']::schema()->hasTable('channels')){
    $kollab['eloquent']::schema()->drop('channels');
}

/* Drop the users table */
if($kollab['eloquent']::schema()->hasTable('channel_user')){
    $kollab['eloquent']::schema()->drop('channel_user');
}

/* Drop the messages table */
if($kollab['eloquent']::schema()->hasTable('messages')){
    $kollab['eloquent']::schema()->drop('messages');
}


/* Creat the users table */
$kollab['eloquent']::schema()->create('users', function($table){
    $table->increments('id');
    $table->string('name');
    $table->string('username');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('avatar')->nullable();
    $table->string('department')->nullable();
    $table->string('title');
    $table->char('gender',1);
    $table->string('status')->default('offline');
    $table->timestamps();
});

/* Creat the users table */
$kollab['eloquent']::schema()->create('channels', function($table){
    $table->increments('id');
    $table->string('channel');
    $table->string('title');
    $table->string('description');
    $table->string('type')->default('public');
    $table->timestamps();
});

/* Create the pivot table */
$kollab['eloquent']::schema()->create('channel_user', function($table){
    $table->increments('id');
    $table->integer('channel_id');
    $table->integer('user_id');
    $table->timestamps();
});

/* Creat the messages table */
$kollab['eloquent']::schema()->create('messages', function($table){
    $table->increments('id');
    $table->string('from');
    $table->string('channel');
    $table->string('route');
    $table->string('message');
    $table->timestamps();
});

/* Create a basic channel */
KChannel::create(array('channel'=>'rpall','title'=>'RPALL','description'=>''));
KChannel::create(array('channel'=>'watercooler','title'=>'watercooler','description'=>' the description for watercooler should go here'));

$users_str = file_get_contents(KOLLAB_DATA_DIR . '/rpusers.txt');
$users = json_decode(trim($users_str), TRUE);
foreach($users as $user){
    $user['gender'] = isset($user['gender'][0]) ? $user['gender'] : '?';
    $u = KUser::create(array('name'=>$user['firstName'] . ' ' . $user['lastName'], 'department'=>$user['department'],'title'=>$user['title'],'gender'=>$user['gender'][0],'username'=>$user['alias'],'password'=>md5('Dr0wssap!'),'email'=>$user['email']));
    $u->channels()->attach(1);
}

$u = KUser::where('username','=','casey.merrill')->first();
$u->channels()->attach(2);

