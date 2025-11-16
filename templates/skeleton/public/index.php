<?php

require __DIR__ . '/../vendor/autoload.php';

use Kodomo\Core\App;

$app = new App();

// Load route files
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

$app->run();