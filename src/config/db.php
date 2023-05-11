<?php
function establishDBConnection() {
    $dbConfig = [
        'host' => 'localhost:3306',
        'database' => 'power_poster',
        'username' => $_SERVER['DB_USERNAME'],
        'password' => $_SERVER['DB_PASSWORD'],
    ]; // For testing purposes only.

    $dsn = 'mysql:host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['database'];
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);

    return $pdo;
}
