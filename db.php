<?php
$host = "localhost";
$db = "lights";
$user = "root";
$pass = "";


$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>