<?php

$kollab->get('/', function() use ($kollab) {
    return file_get_contents(KOLLAB_WWW_DIR . 'views/index.html');
});
