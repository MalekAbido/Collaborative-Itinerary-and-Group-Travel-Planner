<?php

require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Database.php';

define("BASE_URL", '/' . basename(dirname(__DIR__)) . '/public/');

$app = new App();
