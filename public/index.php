<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
use Core\Router;

define("BASE_URL", '/');
$router = new Router();