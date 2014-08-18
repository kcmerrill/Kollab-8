<?php

$kollab['extensions']->register('user.avatar', function($user){
    return 'http://www.gravatar.com/avatar/' . md5(strtolower($user->getAttribute('email'))) . '?r=r&d=identicon';
});
