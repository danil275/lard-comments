<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

/** @var Logger */
$logger = require __DIR__ . '/../config/logger.php';
$pdo = require __DIR__ . '/../config/database.php';

$request = Request::createFromGlobals();

$routeHandler = require __DIR__ . '/../config/routes.php';
$routeHandler($request, $logger, $pdo);
