<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use kcmerrill\utility\config;
use kcmerrill\utility\events;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Eloquent;

require_once __DIR__ . '/../vendor/autoload.php';

/* CONSTANTS! */
define('ROOT_DIR', dirname(__DIR__) . '/');
define('KOLLAB_ROOT_DIR', __DIR__ . '/');
define('KOLLAB_LOG_DIR', KOLLAB_ROOT_DIR . 'logs/');
define('KOLLAB_DATA_DIR', KOLLAB_ROOT_DIR . 'data/');
define('KOLLAB_WWW_DIR', ROOT_DIR . 'www/');
define('KOLLAB_CONFIG_DIR', KOLLAB_ROOT_DIR . 'config/');

date_default_timezone_set('America/Denver');

$kollab = new Silex\Application();
$kollab['debug'] = true;
$kollab->register(new Silex\Provider\SessionServiceProvider());

$kollab['config'] = $kollab->share(function() {
    $config = new config(KOLLAB_CONFIG_DIR);
    return $config;
});

$kollab['extensions'] = $kollab->share(function() use ($kollab) {
    return new events;
});

$kollab['eloquent'] = $kollab->share(function() use ($kollab){
    $capsule = new Capsule;
    $capsule->addConnection(array(
        'driver' => 'mysql',
        'database' => 'kollab',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    ));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
});

$kollab['WsServer'] = $kollab->share(function() use($kollab) {
     return IoServer::factory(
        new HttpServer(
            new WsServer(
                new Kollab\Sockets\Router($kollab['config'])
            )
        ),
        $kollab['config']->c('kollab.server.port')
    );
});

$kollab['user'] = $kollab->share(function() use ($kollab) {

});

$kollab['eloquent'];

foreach (glob(KOLLAB_ROOT_DIR . "extensions/enabled/*.php") as $extension){
    include $extension;
}
