<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use kcmerrill\utility\config;

require_once __DIR__ . '/../vendor/autoload.php';

/* CONSTANTS! */
define('ROOT_DIR', dirname(__DIR__) . '/');
define('KOLLAB_ROOT_DIR', __DIR__ . '/');
define('KOLLAB_LOG_DIR', KOLLAB_ROOT_DIR . 'logs/');
define('KOLLAB_CONFIG_DIR', KOLLAB_ROOT_DIR . 'config/');

$kollab = new Silex\Application();

$kollab['config'] = $kollab->share(function() {
    return new config(KOLLAB_CONFIG_DIR);
});

$kollab['WsServer'] = $kollab->share(function() use($kollab) {
     return IoServer::factory(
        new HttpServer(
            new WsServer(
                new Kollab\Router($kollab['config'])
            )
        ),
        $kollab['config']->c('kollab.server.port')
    );
});
