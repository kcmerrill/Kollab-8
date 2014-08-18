<?php
use Kollab\Models\User as KUser;
use Kollab\Models\Channel as KChannel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;


$kollab->get('/channels', function() use ($kollab) {
    return $kollab->json(KChannel::where('type','=','public')->get());
});

$kollab->get('/channels/{channel}', function($channel) use ($kollab) {
    return $kollab->json(KChannel::where('type','=','public')->where('channel','=',$channel)->get());
});

$kollab->get('/channels/{channel}/users', function($channel) use ($kollab) {
    $channel = KChannel::where('channel','=', $channel)->first();
    $channel->users->each(function($user) use ($kollab) {
        foreach($user->toArray() as $key=>$value){
            $filtered = $kollab['extensions']->filter('user.' . $key, $user);
            if($filtered !== $user){
                $user->setAttribute($key, $filtered);
            }
        }
        return $user;
    });
    return $kollab->json($channel->users);
});
