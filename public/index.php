<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\Helpers\Session;
use Core\Router;

Session::start();
define("BASE_URL", '/');
$router = new Router();
