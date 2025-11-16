<?php

require __DIR__ . '/../vendor/autoload.php';

use Tiny\Core\App;

$app = new App();

$app->router->get('/', [\Tiny\Controllers\HomeController::class, 'index']);

$app->run();