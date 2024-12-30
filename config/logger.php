<?php

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

$logger = new Logger('logger');

$logger->pushHandler(new StreamHandler(__DIR__ . '/../app.log', Level::Debug));
$logger->pushHandler(new FirePHPHandler());

return $logger;