<?php
require "config.php";
require "jwt.php";

/* autentificare cu JWT */
$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Lipseste tokenul de autentificare"
    ]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);
$user  = verify_jwt($token);

if (!$user) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Autentificare esuata"
    ]);
    exit;
}

$action = $_GET['action'] ?? null;
$data   = json_decode(file_get_contents("php://input"), true);

/* status becuri */
if ($action === "status") {

    $res = mysqli_query($conn, "SELECT id, name, status FROM bulbs");
    $bulbs = mysqli_fetch_all($res, MYSQLI_ASSOC);

    echo json_encode([
        "success" => true,
        "bulbs" => $bulbs
    ]);
    exit;
}

/* control becuri */
if ($action === "toggle") {

    if (!isset($data['bulbs'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Nu ai specificat becurile"
        ]);
        exit;
    }

    $now = date("Y-m-d H:i:s");

    /* actionam toate becurile */
    if ($data['bulbs'] === "all") {

        if (!isset($data['status'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Pentru 'all' trebuie status (0 sau 1)"
            ]);
            exit;
        }

        $newStatus = (int)$data['status'];

        /* selectam doar becurile care se schimba */
        $bulbsRes = mysqli_query($conn, "SELECT id FROM bulbs WHERE status != $newStatus");
        $bulbs = mysqli_fetch_all($bulbsRes, MYSQLI_ASSOC);

        if ($bulbs) {
            /* update status */
            mysqli_query($conn, "UPDATE bulbs SET status = $newStatus");

            if ($newStatus === 1) {
                /* din OFF in ON */
                foreach ($bulbs as $bulb) {
                    mysqli_query($conn, "INSERT INTO bulb_usage (bulb_id, turned_on_at)VALUES ({$bulb['id']}, '$now')
                    ");
                }
            } else {
                /* din ON in OFF */
                mysqli_query($conn, "UPDATE bulb_usage SET turned_off_at = '$now' WHERE turned_off_at IS NULL
                ");
            }
        }
    }

    /* toggle unul sau mai multe becuri */
    else {
        $ids = implode(",", array_map("intval", $data['bulbs']));
        /* selecteaza starea curenta */
        $bulbsRes = mysqli_query($conn, "SELECT id, status FROM bulbs WHERE id IN ($ids)");
        $bulbs = mysqli_fetch_all($bulbsRes, MYSQLI_ASSOC);

        foreach ($bulbs as $bulb) {
            if ($bulb['status'] == 1) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }
            mysqli_query($conn, "UPDATE bulbs SET status = $newStatus WHERE id = {$bulb['id']}");

            if ($newStatus === 1) {
                mysqli_query($conn, "INSERT INTO bulb_usage (bulb_id, turned_on_at) VALUES ({$bulb['id']}, '$now')");
            } else {
                mysqli_query($conn, "UPDATE bulb_usage SET turned_off_at = '$now' WHERE bulb_id = {$bulb['id']} AND turned_off_at IS NULL");
            }
        }
    }

    /* return statusuri */
    $res = mysqli_query($conn, "SELECT id, name, status FROM bulbs");
    $updatedBulbs = mysqli_fetch_all($res, MYSQLI_ASSOC);

    echo json_encode([
        "success" => true,
        "message" => "Operatiunea a fost efectuata cu succes",
        "bulbs" => $updatedBulbs
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