<?php

header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "api_records";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed"
    ]);
    exit;
}

mysqli_set_charset($conn, "utf8");