<?php
$host = "localhost";
$db = "lights";
$user = "root";
$pass = "";

// Creăm conexiunea MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Verificăm dacă sunt erori de conectare
if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

// Setăm setul de caractere la utf8
$conn->set_charset("utf8");
?>