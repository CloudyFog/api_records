<?php
require "config.php";
require "jwt.php";

$action = $_GET['action'] ?? null;
$data   = json_decode(file_get_contents("php://input"), true);

if (!$action) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "No action specified"
    ]);
    exit;
}

/* register */
if ($action === "register") {

    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "message" => "Date invalide"
        ]);
        exit;
    }

    $email = trim($data['email']);
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (email, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            "success" => true,
            "message" => "Utilizator inregistrat cu succes"
        ]);
    } else {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Adresa de email deja exista"
        ]);
    }

    exit;
}

/* login */
if ($action === "login") {

    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "message" => "Date invalide"
        ]);
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $data['email']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user || !password_verify($data['password'], $user['password'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Date de logare incorecte"
        ]);
        exit;
    }

    $token = generate_jwt([
        "user_id" => $user['id'],
        "email"   => $data['email']
    ]);

    echo json_encode([
        "success" => true,
        "token" => $token
    ]);

    exit;
}

/* actiune invalida */
http_response_code(404);
echo json_encode([
    "success" => false,
    "message" => "Actiune invalida"
]);

?>