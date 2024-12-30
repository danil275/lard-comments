<?php

$host = '127.0.0.1';
$db   = 'lard';
$user = 'root';
$pass = '';
$charset = 'utf8';
$password = '123123';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $password);
    return $pdo;
} catch (PDOException $e) {
    $logger->critical('Подключение не удалось: ' . $e->getMessage());
    die('Подключение не удалось: ' . $e->getMessage());
}