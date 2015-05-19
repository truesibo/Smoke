<?php

$loader = include __DIR__.'/../vendor/autoload.php';

$app = new \whm\CacheWatch\Cli\Application();
$app->run();