<?php
// src/db.php
require_once 'load_env.php';
loadEnv(__DIR__ . '/../.env');

$host     = getenv('DB_HOST');
$user     = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=UTF8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$MyJawnQuery = "SELECT * from users;";
$test        = $pdo->query($MyJawnQuery);

while ($ids = $test->fetch()) {
    echo "<br>";
    echo $ids["username"];
    echo "<br>";
}

echo "<br>";

echo "<br>";
echo "<br>";
echo "<br>";