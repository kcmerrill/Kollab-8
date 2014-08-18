<?php

$kollab['extensions']->register('user.avatar', function($user){
    return 'http://rpfaces.returnpath.net/profile/images/profile/' . $user->getAttribute('username') . '.jpg';
});
