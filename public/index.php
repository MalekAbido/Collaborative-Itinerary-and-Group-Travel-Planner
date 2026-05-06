<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
use App\Helpers\Session;
use Core\Router;

Session::start();
define("BASE_URL", '/');
$router = new Router();