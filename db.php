<?php
$host = "localhost";
$db   = "lights";
$user = "root";
$pass = ""; 

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Eroare DB: " . $e->getMessage());
}
