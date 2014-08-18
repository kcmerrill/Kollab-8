<?php
include dirname(__DIR__) . '/kollab/bootstrap.php';

include KOLLAB_ROOT_DIR . 'routes/index.php';
include KOLLAB_ROOT_DIR . 'routes/users.php';
include KOLLAB_ROOT_DIR . 'routes/channels.php';

$kollab->run();
