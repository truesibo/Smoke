#!/usr/bin/env php
<?php

$loader = include __DIR__ . '/../vendor/autoload.php';

define ("SMOKE_VERSION", "1.0.0 RC3");

$app = new \whm\Smoke\Cli\Application();
$app->run();
