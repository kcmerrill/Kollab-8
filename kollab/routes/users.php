<?php
use Kollab\Models\User as KUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

$kollab->get('/users', function() use ($kollab){
    $users = KUser::all();
    $users->each(function($user) use ($kollab) {
        foreach($user->toArray() as $key=>$value){
            $filtered = $kollab['extensions']->filter('user.' . $key, $user);
            if($filtered !== $user){
                $user->setAttribute($key, $filtered);
            }
        }
        return $user;
    });
    return $users->toJson();
});

$kollab->get('/user/_logout', function() use($kollab) {
    $logged_in_as = $kollab['session']->get('logged_in_as');
    $kollab['session']->clear();

    if($logged_in_as){
        $user = KUser::where('username','=',$logged_in_as)->first();
        $user->status = 'offline';
        $user->save();
        return $kollab->json('Succesfully logged '. $logged_in_as . 'out!', 200);
    } else {
        return $kollab->json(array(), 401);
    }
});

$kollab->get('/user/_current', function() use($kollab) {
    $logged_in_as = $kollab['session']->get('logged_in_as');
    $current_user = KUser::where('username','=',$logged_in_as)->first();
        if(!is_null($current_user)){
            foreach($current_user->toArray() as $key=>$value){
                $filtered = $kollab['extensions']->filter('user.' . $key, $current_user);
                if($filtered !== $current_user){
                    $current_user->setAttribute($key, $filtered);
                }
            }
        $current_user->setHidden(array());
        return $kollab->json($current_user->toArray());
    } else {
        return $kollab->json(new \stdClass, 404);
    }
});

$kollab->get('/user/_channels', function() use ($kollab) {
    $logged_in_as = $kollab['session']->get('logged_in_as');
    if($logged_in_as){
        $user = KUser::where('username','=',$logged_in_as)->first();
        return $kollab->json($user->channels()->get());
    } else {
        return $kollab->json(array(), 401);
    }
});

/* TODO: Fix this, kind of shady */
$kollab->get('/user/{username}/login/{password}', function($username, $password) use ($kollab) {
    $user = KUser::where('username','=',$username)->where('password','=', md5($password))->first();
    if(!is_null($user)){
        $kollab['session']->set('logged_in_as', $username);
        return $kollab->json($user->toArray());
    } else {
        return $kollab->json( new \stdClass, 404);
    }
});



$kollab->get('/users/{username}', function($username) use ($kollab){
    $user = KUser::where('username','=',$username)->first();
    return $user->toJson();
});
