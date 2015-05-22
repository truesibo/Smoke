#!/usr/bin/env php
<?php

$loader = include __DIR__ . '/../vendor/autoload.php';

$app = new \whm\Smoke\Cli\Application();
$app->run();
